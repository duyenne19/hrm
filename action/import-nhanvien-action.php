<?php
session_start();
require_once __DIR__ . '/../connection/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

$database = new Database();
$conn = $database->getConnection();

if (isset($_POST['import'])) {
    
    // 1. Kiểm tra File
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "Vui lòng chọn file hợp lệ!";
        header("Location: ../import-nhanvien-tu-file.php");
        exit;
    }

    $fileMimes = [
        'text/x-comma-separated-values', 
        'text/comma-separated-values', 
        'application/octet-stream', 
        'application/vnd.ms-excel', 
        'application/x-csv', 
        'text/x-csv', 
        'text/csv', 
        'application/csv', 
        'application/excel', 
        'application/vnd.msexcel', 
        'text/plain', 
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (!in_array($_FILES['file']['type'], $fileMimes)) {
        $_SESSION['error'] = "Định dạng file không hỗ trợ. Chỉ chấp nhận .xlsx, .xls, .csv";
        header("Location: ../import-nhanvien-tu-file.php");
        exit;
    }

    $fileName = $_FILES['file']['tmp_name'];

    try {
        // 2. Đọc File Excel
        $spreadsheet = IOFactory::load($fileName);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Bỏ qua dòng tiêu đề (Dòng 1)
        array_shift($rows);

        $countSuccess = 0;
        $countFail = 0;

        // Câu lệnh INSERT chuẩn theo tên bảng và cột thực tế
        $sql = "INSERT INTO nhan_vien (
            ma_nv, hoten, anhdaidien, gtinh, ngsinh, noisinh, 
            sodt, email, hokhau, tamtru, so_cccd, 
            ngaycap_cccd, noicap_cccd, 
            id_dantoc, id_tongiao, id_quoctich, id_honnhan, 
            id_trinhdo, id_chuyenmon, id_phongban, id_chucvu, id_loainv, 
            trangthai, ngaytao, id_nguoitao
        ) VALUES (
            ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, 
            ?, ?, 
            ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, 
            ?, NOW(), ?
        )";
        
        // Lưu ý: $this->conn không tồn tại trong scope này vì không nằm trong class. Dùng $conn.
        $stmt = $conn->prepare($sql);

        // Lấy ID người tạo từ Session (nếu có)
        $id_nguoitao = $_SESSION['user']['id'] ?? 1;

        // 3. Duyệt từng dòng và Insert
        $rowIndex = 1; // Để tracking lỗi
        foreach ($rows as $row) {
            $rowIndex++;
            // Mapping theo file mẫu: 
            // 0: A - Mã NV
            // 1: B - Họ Tên
            // 2: C - Giới Tính
            // 3: D - Ngày Sinh
            // 4: E - Nơi Sinh
            // 5: F - SĐT
            // 6: G - Email
            // 7: H - Địa Chỉ (-> Hộ khẩu & Tạm trú)
            // 8: I - CCCD

            $ma_nv = trim($row[0] ?? '');
            $hoten = trim($row[1] ?? '');
            
            // Bỏ qua nếu thiếu mã hoặc tên
            if (empty($ma_nv) || empty($hoten)) {
                // $countFail++; // Bỏ qua dòng rỗng hoàn toàn thì không tính là Fail
                continue;
            }

            // Kiểm tra trùng mã nhân viên
            $check = $conn->prepare("SELECT id FROM nhan_vien WHERE ma_nv = ?");
            $check->execute([$ma_nv]);
            if ($check->rowCount() > 0) {
                // Đã tồn tại -> Bỏ qua hoặc Update (ở đây chọn bỏ qua)
                $_SESSION['debug_error'] = "Mã NV $ma_nv tại dòng $rowIndex đã tồn tại.";
                $countFail++;
                continue;
            }

            // Xử lý Giới tính
            $gioitinh_raw = trim($row[2] ?? '');
            $gtinh = (stripos($gioitinh_raw, 'Nam') !== false) ? 1 : 0; // 1: Nam, 0: Nữ

            // Xử lý Ngày sinh (Excel Date -> Y-m-d)
            $ngsinh = null;
            $rawDate = $row[3] ?? null;
            if (!empty($rawDate)) {
                if (is_numeric($rawDate)) {
                    // Dạng số Excel
                    $ngsinh = Date::excelToDateTimeObject($rawDate)->format('Y-m-d');
                } else {
                    // Dạng chuỗi (thử parse)
                    $timestamp = strtotime(str_replace('/', '-', $rawDate));
                    if ($timestamp) {
                        $ngsinh = date('Y-m-d', $timestamp);
                    }
                }
            }

            $noisinh = trim($row[4] ?? '');
            $sodt    = trim($row[5] ?? '');
            $email   = trim($row[6] ?? '');
            $diachi  = trim($row[7] ?? ''); // Dùng cho hokhau và tamtru
            $cccd    = trim($row[8] ?? '');
            $anhdaidien = ''; // Mặc định rỗng hoặc 'avatar-default.png'

            // Các trường mặc định (ID = 1) - Cần đảm bảo CSDL có ID 1
            // CẢI TIẾN: Lấy ID đầu tiên tìm thấy trong bảng tương ứng để tránh lỗi Khóa ngoại (FK)
            $id_dantoc    = $conn->query("SELECT id FROM dan_toc LIMIT 1")->fetchColumn() ?: 1; 
            $id_tongiao   = $conn->query("SELECT id FROM ton_giao LIMIT 1")->fetchColumn() ?: 1;
            $id_quoctich  = $conn->query("SELECT id FROM quoc_tich LIMIT 1")->fetchColumn() ?: 1;
            $id_honnhan   = $conn->query("SELECT id FROM tt_hon_nhan LIMIT 1")->fetchColumn() ?: 1;
            $id_trinhdo   = $conn->query("SELECT id FROM trinh_do LIMIT 1")->fetchColumn() ?: 1;
            $id_chuyenmon = $conn->query("SELECT id FROM chuyen_mon LIMIT 1")->fetchColumn() ?: 1;
            $id_phongban  = $conn->query("SELECT id FROM phong_ban LIMIT 1")->fetchColumn() ?: 1; 
            $id_chucvu    = $conn->query("SELECT id FROM chuc_vu LIMIT 1")->fetchColumn() ?: 1;
            $id_loainv    = $conn->query("SELECT id FROM loai_nhanvien LIMIT 1")->fetchColumn() ?: 1;
            
            $trangthai = 1; // 1: Đang làm việc

            // Thực thi INSERT
            try {
                $params = [
                    $ma_nv, 
                    $hoten, 
                    $anhdaidien, // Mới thêm
                    $gtinh, 
                    $ngsinh, 
                    $noisinh,
                    $sodt, 
                    $email, 
                    $diachi, // hokhau
                    $diachi, // tamtru
                    $cccd,
                    null, // ngaycap_cccd (chưa có trong file mẫu) - Nếu DB bắt buộc -> lỗi. Nên sửa thành NULL nếu DB cho phép.
                    '',   // noicap_cccd
                    $id_dantoc, $id_tongiao, $id_quoctich, $id_honnhan,
                    $id_trinhdo, $id_chuyenmon, $id_phongban, $id_chucvu, $id_loainv,
                    $trangthai,
                    $id_nguoitao
                ];

                if ($stmt->execute($params)) {
                    $countSuccess++;
                } else {
                    $errInfo = $stmt->errorInfo();
                    $_SESSION['debug_error'] = "Lỗi SQL dòng $rowIndex: " . $errInfo[2];
                    $countFail++;
                }
            } catch (PDOException $ex) {
                // Lỗi SQL (VD: constraint violation)
                // Lưu lỗi cụ thể để debug nếu cần
                $_SESSION['debug_error'] = "Lỗi Exception dòng $rowIndex: " . $ex->getMessage();
                $countFail++;
            }
        }

        if ($countSuccess > 0) {
            $_SESSION['success'] = "Đã nhập thành công $countSuccess nhân viên. (Thất bại/Trùng: $countFail)";
            if($countFail > 0 && isset($_SESSION['debug_error'])) {
                 $_SESSION['success'] .= " Lỗi mẫu: " . $_SESSION['debug_error'];
            }
        } else {
            $_SESSION['error'] = "Không có nhân viên nào được nhập.";
            if ($countFail > 0) {
                 $_SESSION['error'] .= " Thất bại $countFail dòng. Lỗi gần nhất: " . ($_SESSION['debug_error'] ?? 'Không rõ');
            } else {
                 $_SESSION['error'] .= " (File rỗng hoặc mã nhân viên đã tồn tại hết)";
            }
        }

    } catch (Exception $e) {
        $_SESSION['error'] = "Lỗi xử lý file Import: " . $e->getMessage();
    }
}

header("Location: ../ds-nhan-vien.php");
exit;
?>
