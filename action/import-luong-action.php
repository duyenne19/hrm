<?php
session_start();
require_once __DIR__ . '/../connection/config.php';
require_once __DIR__ . '/../models/Luong.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

$database = new Database();
$db = $database->getConnection();
$luongModel = new Luong($db);

if (isset($_POST['import'])) {

    // 1. Validate File
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "Vui lòng chọn file hợp lệ!";
        header("Location: ../import-luong-tu-file.php");
        exit;
    }

    $fileMimes = [
        'application/vnd.ms-excel', 
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/excel', 
        'application/x-excel', 
        'application/x-msexcel'
    ];

    if (!in_array($_FILES['file']['type'], $fileMimes)) {
        // Fallback check extension
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($ext), ['xls', 'xlsx'])) {
            $_SESSION['error'] = "Định dạng file không hỗ trợ. Chỉ chấp nhận .xlsx, .xls";
            header("Location: ../import-luong-tu-file.php");
            exit;
        }
    }

    $fileName = $_FILES['file']['tmp_name'];

    try {
        // 2. Read Excel
        $spreadsheet = IOFactory::load($fileName);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        // Remove header row
        array_shift($rows);

        $countSuccess = 0;
        $countSkip = 0;
        $countFail = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowIndex = $index + 2; // Excel row index
            
            // Excel columns: 
            // 0: Mã NV
            // 1: Kỳ Lương (MM-YYYY)
            // 2: Ngày Công
            // 3: Tạm Ứng

            $ma_nv = trim($row[0] ?? '');
            $ky_luong_raw = trim($row[1] ?? ''); // MM-YYYY hoặc MM/YYYY
            $ngay_cong = floatval($row[2] ?? 0);
            $tam_ung = intval(str_replace([',', '.'], '', $row[3] ?? 0)); // Remove formatting

            if (empty($ma_nv) || empty($ky_luong_raw)) {
                continue; // Skip empty rows
            }

            // --- XỬ LÝ ĐỊNH DẠNG KỲ LƯƠNG (MM-YYYY -> YYYY-MM-01) ---
            $ky_luong_db = '';
            
            // Xử lý Excel Date Serial (Số nguyên)
            if (is_numeric($ky_luong_raw) && $ky_luong_raw > 1000) { 
                try {
                    $dt = ExcelDate::excelToDateTimeObject($ky_luong_raw);
                    $ky_luong_db = $dt->format('Y-m-01');
                } catch (Exception $e) {
                     // Nếu convert lỗi thì thôi, fallback xuống dưới
                }
            }
            
            // Nếu chưa parse được, thử parse string
            if (empty($ky_luong_db)) {
                // Chuẩn hóa dấu phân cách
                $ky_luong_raw = str_replace(['/', '.'], '-', $ky_luong_raw); 
                $parts = explode('-', $ky_luong_raw);
                
                if (count($parts) === 2) {
                    $part1 = trim($parts[0]);
                    $part2 = trim($parts[1]);
                    
                    // Logic nhận diện YYYY-MM hay MM-YYYY
                    if (strlen($part1) == 4 && is_numeric($part1)) {
                        // YYYY-MM
                        $nam = $part1;
                        $thang = str_pad($part2, 2, '0', STR_PAD_LEFT);
                    } elseif (strlen($part2) == 4 && is_numeric($part2)) {
                        // MM-YYYY
                        $thang = str_pad($part1, 2, '0', STR_PAD_LEFT);
                        $nam = $part2;
                    } else {
                        // Mặc định MM-YY (không khuyến khích) hoặc lỗi
                        // Giả sử MM-YYYY
                         $thang = str_pad($part1, 2, '0', STR_PAD_LEFT);
                         $nam = (strlen($part2) == 2) ? '20'.$part2 : $part2;
                    }
                    
                    $ky_luong_db = "$nam-$thang-01"; // YYYY-MM-01 format
                    
                } elseif (count($parts) === 3) {
                     // Trường hợp dd-mm-yyyy hoặc yyyy-mm-dd
                    // Ta chỉ quan tâm tháng và năm, lấy ngày 01
                     $ts = strtotime($ky_luong_raw);
                     if ($ts) {
                         $ky_luong_db = date('Y-m-01', $ts);
                     }
                }
            }
            
            // Validate lại lần cuối
            $validateDate = DateTime::createFromFormat('Y-m-d', $ky_luong_db);
            if (!$validateDate || $validateDate->format('Y-m-d') !== $ky_luong_db) {
                // Thử cách an toàn
                if ($validateDate) {
                     $ky_luong_db = $validateDate->format('Y-m-01');
                } else {
                    $countFail++;
                    $errors[] = "Dòng $rowIndex: Định dạng kỳ lương '$ky_luong_raw' không hợp lệ.";
                    continue;
                }
            }

            // 3. Get ID NV from Ma NV
            // Note: Should optimize this query inside a loop but for now it's okay for < 1000 rows
            $stmt = $db->prepare("SELECT id FROM nhan_vien WHERE ma_nv = :ma_nv LIMIT 1");
            $stmt->bindParam(':ma_nv', $ma_nv);
            $stmt->execute();
            $nv = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$nv) {
                $countFail++;
                $errors[] = "Dòng $rowIndex: Không tìm thấy nhân viên mã $ma_nv";
                continue;
            }
            $id_nv = $nv['id'];

            // 4. Validate Duplicate (Constraint: "tự động loại trừ")
            // Check if salary record already exists for this staff and period
            $stmtCheck = $db->prepare("SELECT id FROM luong WHERE id_nv = :id_nv AND ky_luong = :ky_luong LIMIT 1");
            $stmtCheck->bindParam(':id_nv', $id_nv);
            $stmtCheck->bindParam(':ky_luong', $ky_luong_db); // Sử dụng ky_luong_db
            $stmtCheck->execute();

            if ($stmtCheck->rowCount() > 0) {
                $countSkip++;
                // Skip silently or log? User said "tự động loại trừ" so silently skipping or just counting is fine.
                continue;
            }

            // 5. Get Salary Components
            $salaryComponents = $luongModel->getLcb_Hsl_Hspc($id_nv);
            
            // Xử lý trường hợp có thể null (Ví dụ nhân viên chưa có bảng lương)
            $salaryComponents = $salaryComponents ?? [
                'luong_co_ban' => 0,
                'he_so_luong' => 1,
                'he_so_phu_cap' => 0
            ];
            
            // 6. Construct Data Array for addLuong
            // Generate ma_luong
            $cleanKyLuong = str_replace('-', '', $ky_luong_db); // YYYYMM01
            $ma_luong = 'L' . $id_nv . $cleanKyLuong . rand(10, 99);
            
            $data = [
                'ma_luong' => $ma_luong,
                'id_nv' => $id_nv,
                'ky_luong' => $ky_luong_db, // Sử dụng ky_luong_db
                'ngay_cong' => $ngay_cong,
                'tam_ung' => $tam_ung,
                // Components from DB
                'luong_co_ban_goc' => $salaryComponents['luong_co_ban'], // Notice key mapping
                'he_so_luong_goc' => $salaryComponents['he_so_luong'],
                'he_so_phu_cap_goc' => $salaryComponents['he_so_phu_cap'],
                
                // Helper mapping for calculation inside addLuong
                'luong_co_ban' => $salaryComponents['luong_co_ban'], 
                'he_so_luong' => $salaryComponents['he_so_luong'],
                'he_so_phu_cap' => $salaryComponents['he_so_phu_cap'],
            ];

            // 7. Insert
            if ($luongModel->addLuong($data)) {
                $countSuccess++;
            } else {
                $countFail++;
                $errors[] = "Dòng $rowIndex: Lỗi khi lưu vào CSDL cho $ma_nv";
            }
        }

        // Summary message
        $msg = "Đã nhập thành công: $countSuccess. Bỏ qua (trùng): $countSkip. Lỗi: $countFail.";
        if (count($errors) > 0) {
            $msg .= " Chi tiết lỗi: " . implode('; ', array_slice($errors, 0, 5)) . (count($errors)>5 ? "..." : "");
        }

        if ($countSuccess > 0) {
            $_SESSION['success'] = $msg;
        } elseif ($countSkip > 0 && $countSuccess == 0) {
             $_SESSION['success'] = "Không có bản ghi mới. " . $msg;
        } else {
            $_SESSION['error'] = "Nhập dữ liệu thất bại. " . $msg;
        }

    } catch (Exception $e) {
        $_SESSION['error'] = "Lỗi xử lý file Import: " . $e->getMessage();
    }

    header("Location: ../import-luong-tu-file.php");
    exit;
}
