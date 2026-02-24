<?php
    // File: action/print_luong.php - Phiên bản Tách Tạm ứng và Khấu trừ

    // 1. Kiểm tra Session và Tải thư viện
    session_start();
    
    // Kiểm tra session
    if (!isset($_SESSION['user'])) {
        header("Location: ../login.php"); 
        exit;
    }
    
    include_once __DIR__ . '/../connection/config.php';
    include_once __DIR__ . '/../models/Luong.php';
    
    // Khởi tạo kết nối và Model
    $database = new Database();
    $conn = $database->getConnection();
    $luong_model = new Luong($conn);

    // 2. Lấy và Chuẩn hóa Tham số Lọc
    $from_month = $_GET['from_month'] ?? null;
    $to_month = $_GET['to_month'] ?? null;
    $id_pb = $_GET['filter_id_pb'] ?? 0;

    $from_date = null;
    $to_date = null;

    // Chuyển đổi MM/YYYY sang YYYY-MM-DD
    if ($from_month) {
        $from_date = date('Y-m-01', strtotime(str_replace('/', '-', '01/' . $from_month)));
    }
    if ($to_month) {
        $to_date = date('Y-m-t', strtotime(str_replace('/', '-', '01/' . $to_month)));
    }

    // 3. Truy vấn Dữ liệu Lương đã Lọc
    $arrLuong = $luong_model->getSalaryList($from_date, $to_date, $id_pb); 

    // =============================================================
    // * LOGIC MỚI: KIỂM TRA ĐIỀU KIỆN ẨN/HIỆN CỘT KỲ LƯƠNG
    // =============================================================
    $show_ky_luong_col = ($from_month !== $to_month);
    
    $tieu_de_thang = $from_month;
    if ($show_ky_luong_col) {
        $tieu_de_thang = "TỪ $from_month ĐẾN $to_month";
    }

    // Khởi tạo biến Tổng cộng
    $total_ngay_cong = 0;
    $total_luong_coban = 0;
    $total_phu_cap = 0;
    $total_tam_ung = 0;
    $total_khau_tru = 0;
    $total_thuc_lanh = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>In bảng lương nhân viên</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            font-size: 10pt;
        }
        h2 { 
            text-align: center; 
            margin-bottom: 20px; 
            text-transform: capitalize;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            page-break-inside: auto; 
            margin-bottom: 50px; /* Thêm khoảng cách trước phần chữ ký */
        }
        th, td { 
            border: 1px solid #000; 
            padding: 5px 3px; 
            text-align: left;
            vertical-align: middle;
            font-size: 9pt;
        }
        thead th, tfoot td {
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
        }
        
        /* CĂN CHỈNH DỮ LIỆU CÁC CỘT: */
        
        /* CỘT KỲ LƯƠNG và CỘT CHỮ KÝ */
        <?php if ($show_ky_luong_col): ?>
            /* Nếu có cột Kỳ lương (12) */
            table tr td:nth-child(12) { text-align: center; } 
            /* Cột Chữ ký là cột 13 */
            table tr td:nth-child(13) { width: 100px; }
        <?php else: ?>
            /* Nếu KHÔNG có cột Kỳ lương (12) */
            /* Cột Chữ ký là cột 12 */
            table tr td:nth-child(12) { width: 100px; }
        <?php endif; ?>
        
        /* CĂN PHẢI các cột tiền tệ/số (Từ Ngày công: Cột 6 đến Thực Lãnh: Cột 11) */
        table tr td:nth-child(n+6):nth-child(-n+11) { text-align: right; }
        
        tfoot td { font-weight: bold; }

        /* ============================================================= */
        /* * CSS MỚI CHO CHỮ KÝ */
        /* ============================================================= */
        .signature-section {
            width: 100%;
            display: flex; /* Dùng flexbox để chia cột */
            justify-content: space-between; /* Căn đều 2 đầu */
            margin-top: 40px;
        }
        .signature-group {
            width: 45%; /* Chia đều khoảng 45% cho mỗi bên */
            text-align: center;
        }
        .signature-group p {
            margin: 0;
            line-height: 1.5;
        }
        .date-location {
            width: 100%;
            text-align: right;
            margin-bottom: 20px;
            font-style: italic;
        }
        .signature-title {
            font-weight: bold;
            text-transform: uppercase; /* Giữ in hoa cho tiêu đề chức danh */
            margin-top: 50px; /* Khoảng trống cho chữ ký */
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
    </div>
    <h2>BẢNG LƯƠNG NHÂN VIÊN <?php echo $tieu_de_thang; ?></h2>
    
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã nhân viên</th>
                <th>Họ tên</th>
                <th>Phòng ban</th>
                <th>Chức vụ</th>
                <th>Ngày công</th>
                <th>Lương cơ bản</th> 
                <th>Phụ cấp</th> 
                <th>Tạm ứng</th> <th>Khấu trừ</th> <th>Thực Lãnh</th>
                
                <?php if ($show_ky_luong_col): ?>
                    <th>Kỳ lương</th>
                <?php endif; ?>
                
                <th>Chữ ký</th>
            </tr>
        </thead>
        <tbody>
            <?php $stt = 1; foreach ($arrLuong as $luong): 
                
                // Lấy các giá trị cần thiết
                $ngay_cong = $luong['ngay_cong'] ?? 0;
                $luong_coban = $luong['luong_coban'] ?? 0;
                $phu_cap = $luong['phu_cap'] ?? 0;
                $tam_ung = $luong['tam_ung'] ?? 0;

                // TÍNH TOÁN TỔNG KHẤU TRỪ
                $khau_tru = ($luong['bhxh'] ?? 0) + ($luong['bhtn'] ?? 0) + ($luong['bhyt'] ?? 0) + ($luong['thue_tncn'] ?? 0);
                $thuc_lanh = $luong['thuc_lanh'] ?? 0;

                $ky_luong_format = date('m/Y', strtotime($luong['ky_luong'] ?? 'now'));

                // CỘNG DỒN VÀO TỔNG CỘNG
                $total_ngay_cong += $ngay_cong;
                $total_luong_coban += $luong_coban;
                $total_phu_cap += $phu_cap;
                $total_tam_ung += $tam_ung;
                $total_khau_tru += $khau_tru;
                $total_thuc_lanh += $thuc_lanh;
            ?>
            <tr>
                <td><?php echo $stt++; ?></td>
                <td><?php echo $luong['ma_nv'] ?? ''; ?></td>
                <td><?php echo $luong['hoten'] ?? ''; ?></td>
                <td><?php echo $luong['phongban'] ?? ''; ?></td>
                <td><?php echo $luong['chucvu'] ?? ''; ?></td>
                
                <td><?php echo number_format($ngay_cong); ?></td>
                <td><?php echo number_format($luong_coban); ?></td>
                <td><?php echo number_format($phu_cap); ?></td>
                <td><?php echo number_format($tam_ung); ?></td> 
                <td><?php echo number_format($khau_tru); ?></td> 
                <td><?php echo number_format($thuc_lanh); ?></td>
                
                <?php if ($show_ky_luong_col): ?>
                    <td><?php echo $ky_luong_format; ?></td>
                <?php endif; ?>
                
                <td></td> 
            </tr>
            <?php endforeach; ?>
        </tbody>
        
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: center;">TỔNG CỘNG:</td>
                <td><?php echo number_format($total_ngay_cong); ?></td>
                <td><?php echo number_format($total_luong_coban); ?></td>
                <td><?php echo number_format($total_phu_cap); ?></td>
                <td><?php echo number_format($total_tam_ung); ?></td>
                <td><?php echo number_format($total_khau_tru); ?></td>
                <td><?php echo number_format($total_thuc_lanh); ?></td>
                
                <?php if ($show_ky_luong_col): ?>
                    <td colspan="2"></td>
                <?php else: ?>
                    <td colspan="1"></td>
                <?php endif; ?>
            </tr>
        </tfoot>
    </table>
	<div class="date-location">
        Ngày... tháng... năm...
    </div>

    <div class="signature-section">
        <div class="signature-group">
            <p><b>Kế toán</b></p>
            <p>(Ký, họ tên)</p>
        </div>

        <div class="signature-group">
            <p><b>Giám đốc</b></p>
            <p>(Ký, họ tên)</p>
        </div>
    </div>
</body>
</html>