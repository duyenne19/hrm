<?php
session_start();
	if(!isset($_SESSION['user']))
	{
		header('Location: login.php');
		exit();
	}
    // File: action/print_luong.php
    include_once __DIR__ . '/../connection/config.php';
    include_once __DIR__ . '/../models/Luong.php';
    
    // Khởi tạo kết nối và Model (Giữ nguyên logic này)
    $database = new Database();
    $conn = $database->getConnection();
    $luong_model = new Luong($conn);

    // 2. Lấy và Chuẩn hóa Tham số Lọc
	$from_month = $_GET['from_month'] ?? null;
	$to_month = $_GET['to_month'] ?? null;
	$id_pb = $_GET['id_pb'] ?? 0;

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
    
    // Thiết lập tiêu đề cho bảng (dựa trên mẫu của bạn)
    $headerRow2 = [
		'STT', 'Mã nhân viên', 'Họ tên', 'Phòng ban', 'Chức vụ','Lương cơ bản', 'Hệ số lương', 'Hệ số phụ cấp', 'Ngày công','Phụ Cấp',
		'Tạm ứng', 'BHXH', 'BHYT', 'BHTN', 'Thuế TNCN',
		'Thực Lãnh', 'Kỳ lương'
		];
?>
<!DOCTYPE html>
<html>
<head>
     <h2>BẢNG LƯƠNG NHÂN VIÊN <?php echo $tieu_de_thang; ?></h2>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            font-size: 10pt;
        }
        h2 { text-align: center; margin-bottom: 20px; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            page-break-inside: auto; /* Cho phép bảng ngắt trang khi in */
        }
        th, td { 
            border: 1px solid #000; 
            padding: 5px 3px; 
            text-align: left;
            vertical-align: middle;
            font-size: 9pt;
        }
        thead { display: table-header-group; } /* Lặp lại tiêu đề khi ngắt trang */
        
        /* CĂN CHỈNH TIÊU ĐỀ */
        thead th {
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
        }
        /* CĂN GIỮA cột Kỳ Lương (Cột thứ 17) */
        table tr td:nth-child(17) { text-align: center; } 
        /* CĂN PHẢI các cột tiền tệ/số (Từ Lương CB đến Thuế TNCN: Cột 6 đến 15) và Thực Lãnh (Cột 16) */
        table tr td:nth-child(n+6):nth-child(-n+16) { text-align: right; } 
    </style>
</head>
<body onload="window.print()">
    <?php
        $tieu_de_thang = $from_month;
        if ($from_month && $to_month && $from_month !== $to_month) {
            $tieu_de_thang = "TỪ $from_month ĐẾN $to_month";
        }
    ?>
    <h2>BẢNG LƯƠNG NHÂN VIÊN</h2>
    
    <table>
        <thead>
            <tr>
                <th rowspan="2">STT</th>
                <th rowspan="2">Mã NV</th>
                <th rowspan="2">Họ tên</th>
                <th rowspan="2">Phòng ban</th>
                <th rowspan="2">Chức vụ</th>
                <th rowspan="2">Lương cơ bản</th>
                <th rowspan="2">Hệ số lương</th>
                <th rowspan="2">Hệ số phụ cấp</th>
                <th rowspan="2">Ngày công</th>
                <th rowspan="2">Phụ Cấp</th>
                <th colspan="5">Các khoản trừ</th> <th rowspan="2">Thực Lãnh</th>
                <th rowspan="2">Kỳ lương</th>
				<th rowspan="2">Ký nhận</th>
            </tr>
            <tr>
                <th>Tạm ứng</th>
                <th>BHXH</th>
                <th>BHYT</th>
                <th>BHTN</th>
                <th>Thuế TNCN</th>
            </tr>
        </thead>
        <tbody>
            <?php $stt = 1; foreach ($arrLuong as $luong): 
                $luong_coban = $luong['luong_coban'] ?? 0;
                $he_so_luong_goc = $luong['he_so_luong_goc'] ?? 0;
                // $tong_luong_goc = $luong_coban * $he_so_luong_goc; // Không cần thiết để hiển thị
                $ky_luong_format = date('m/Y', strtotime($luong['ky_luong'] ?? 'now'));
            ?>
            <tr>
                <td><?php echo $stt++; ?></td>
                <td><?php echo $luong['ma_nv'] ?? ''; ?></td>
                <td><?php echo $luong['hoten'] ?? ''; ?></td>
                <td><?php echo $luong['phongban'] ?? ''; ?></td>
                <td><?php echo $luong['chucvu'] ?? ''; ?></td>
                <td><?php echo number_format($luong_coban); ?></td>
                <td><?php echo number_format($he_so_luong_goc, 2); ?></td>
                <td><?php echo number_format($luong['he_so_phu_cap_goc'] ?? 0, 2); ?></td>
                <td><?php echo number_format($luong['ngay_cong'] ?? 0); ?></td>
                <td><?php echo number_format($luong['phu_cap'] ?? 0); ?></td>
                <td><?php echo number_format($luong['tam_ung'] ?? 0); ?></td>
                <td><?php echo number_format($luong['bhxh'] ?? 0); ?></td>
                <td><?php echo number_format($luong['bhyt'] ?? 0); ?></td>
                <td><?php echo number_format($luong['bhtn'] ?? 0); ?></td>
                <td><?php echo number_format($luong['thue_tncn'] ?? 0); ?></td>
                <td><?php echo number_format($luong['thuc_lanh'] ?? 0); ?></td>
                <td><?php echo $ky_luong_format; ?></td>
				<td></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
</html>