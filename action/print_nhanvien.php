<?php
// File: action/print_nhanvien.php - In danh sách nhân viên

// 1. Kiểm tra Session và Tải thư viện
session_start();
 
// Kiểm tra session (giả định)
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php"); 
    exit;
}
 
// Giả định các file này tồn tại và chứa class Database, NhanVienModel
include_once __DIR__ . '/../connection/config.php';
include_once __DIR__ . '/../models/NhanVien.php';
 
// Khởi tạo kết nối và Model (giả định)
$database = new Database();
$conn = $database->getConnection();
$nhanVienModel = new NhanVien($conn);

// 2. Lấy và Chuẩn hóa Tham số Lọc
// Giả định tham số lọc phòng ban được truyền qua URL là 'filter_id_pb'
$filter_id_pb = $_GET['filter_id_pb'] ?? '0';

// 3. Truy vấn Dữ liệu Nhân viên đã Lọc
$stmt = $nhanVienModel->getFilter_NV_PB($filter_id_pb);
$arrNhanVien = $stmt->fetchAll(PDO::FETCH_ASSOC); 

// =============================================================
// * LOGIC ẨN/HIỆN CỘT PHÒNG BAN VÀ TIÊU ĐỀ
// =============================================================
$is_filtering_by_pb = ($filter_id_pb !== '0' && !empty($filter_id_pb));

$ten_phong_ban_hien_tai = 'TẤT CẢ PHÒNG BAN';
if ($is_filtering_by_pb && !empty($arrNhanVien)) {
    // Lấy tên phòng ban từ hàng dữ liệu đầu tiên (do đã được JOIN trong Model)
    $ten_phong_ban_hien_tai = $arrNhanVien[0]['phong_ban'] ?? 'PHÒNG BAN KHÔNG XÁC ĐỊNH';
}

$ngay_in = date('H:i:s - d/m/Y');
?>
<!DOCTYPE html>
<html>
<head>
    <title>In Danh Sách Nhân Viên</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            padding: 0;
            font-size: 10pt;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .company-info {
            line-height: 1.5;
            font-size: 9pt;
        }
        .filter-info {
            line-height: 1.5;
            text-align: right;
            font-size: 10pt;
            font-weight: bold;
        }
        h2 { 
            text-align: center; 
            margin: 10px 0 20px 0; 
            text-transform: uppercase;
            font-size: 14pt;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            page-break-inside: auto; 
            margin-bottom: 50px; 
        }
        th, td { 
            border: 1px solid #000; 
            padding: 6px 8px; 
            text-align: left;
            vertical-align: middle;
            font-size: 9pt;
        }
        th {
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
        }
        td:nth-child(1) { width: 30px; text-align: center; } /* STT */
        td:nth-child(2) { width: 70px; } /* Mã NV */
        td:nth-child(3) { width: 150px; } /* Tên nhân viên */
        td:nth-child(5) { width: 70px; } /* Ngày sinh */
        
        /* Chữ ký */
        .date-location {
            width: 100%;
            text-align: right;
            margin-bottom: 20px;
            font-style: italic;
        }
        .signature-section {
            width: 100%;
            display: flex; 
            justify-content: space-around; 
            margin-top: 40px;
            page-break-before: auto; /* Ngăn phân trang giữa bảng và chữ ký */
        }
        .signature-group {
            width: 30%; 
            text-align: center;
        }
        .signature-group p {
            margin: 0;
            line-height: 1.5;
        }
        .signature-group .name {
            margin-top: 70px; /* Khoảng trống cho chữ ký */
            font-weight: bold;
        }
        /* Ẩn nút khi in */
        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header-section">
        <div class="company-info">
            <strong>[Tên công ty]</strong><br>
            Địa chỉ: [Địa chỉ công ty]<br>
            Hotline: [SĐT - Hotline]
        </div>
        <div class="filter-info">
            PHÒNG BAN: <?= htmlspecialchars($ten_phong_ban_hien_tai); ?>
        </div>
    </div>
    
    <h2>DANH SÁCH NHÂN VIÊN</h2>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã nhân viên</th>
                <th>Họ tên</th>
                <th>Giới tính</th>
                <th>Ngày sinh</th>
                <th>Nơi sinh</th>
                <th>Số CCCD</th>
                <th>Số điện thoại</th>
                <th>Chức vụ</th>
                <?php if (!$is_filtering_by_pb): ?>
                    <th>Phòng ban</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            $stt = 1;
            if (!empty($arrNhanVien)):
                foreach ($arrNhanVien as $nv): 
            ?>
                <tr>
                    <td><?= $stt; ?></td>
                    <td><?= htmlspecialchars($nv['ma_nv']); ?></td>
                    <td><?= htmlspecialchars($nv['hoten']); ?></td>
                    <td><?= htmlspecialchars($nv['gtinh']); ?></td>
                    <td><?= htmlspecialchars($nv['ngsinh']); ?></td>
                    <td><?= htmlspecialchars($nv['noisinh']); ?></td>
                    <td><?= htmlspecialchars($nv['so_cccd']); ?></td>
                    <td><?= htmlspecialchars($nv['sodt']); ?></td>
                    <td><?= htmlspecialchars($nv['chuc_vu']); ?></td>
                    
                    <?php if (!$is_filtering_by_pb): ?>
                        <td><?= htmlspecialchars($nv['phong_ban']); ?></td>
                    <?php endif; ?>
                </tr>
            <?php 
                $stt++;
                endforeach; 
            else:
            ?>
                <tr>
                    <td colspan="<?= $is_filtering_by_pb ? 9 : 10; ?>" style="text-align: center;">Không tìm thấy nhân viên nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="date-location">
        Ngày... tháng... năm... (In lúc: <?= $ngay_in; ?>)
    </div>

</body>
</html>
</html>