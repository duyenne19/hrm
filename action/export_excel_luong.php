<?php
	// File: export_excel_luong.php 

	// 1. Cấu hình và Tải Thư viện
	require __DIR__ . '/../vendor/autoload.php'; 

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\Style\Border;
	use PhpOffice\PhpSpreadsheet\Style\Alignment;

	// Tải các file Model và Kết nối CSDL
	include_once __DIR__ . '/../connection/config.php';
	include_once __DIR__ . '/../models/Luong.php';

	// Khởi tạo kết nối
	$database = new Database();
	$conn = $database->getConnection();
	$luong_model = new Luong($conn);

	// 2. Lấy và Chuẩn hóa Tham số Lọc từ GET
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

	if (empty($arrLuong)) {
		die("Không tìm thấy dữ liệu lương để xuất theo điều kiện lọc.");
	}

	// 4. Chuẩn bị Tiêu đề và Dữ liệu để Xuất
	// HÀNG 1: Tiêu đề chung (Cho phép Merge)
	$headerRow1 = [
		'STT', 'Mã nhân viên', 'Họ tên', 'Phòng ban', 'chức vụ', 
		'Lương cơ bản', 'Hệ số lương', 'Hệ số phụ cấp', 'Ngày công','Phụ Cấp', 
		'Các khoản trừ', // Bắt đầu ô Merge (Cột L)
		NULL, NULL, NULL, NULL, // Các ô còn lại của khối Merge
		'Thực Lãnh', 'Kỳ lương'
	];

	// HÀNG 2: Tiêu đề chi tiết
	$headerRow2 = [
		'STT', 'Mã nhân viên', 'Họ tên', 'Phòng ban', 'chức vụ', 
		'Lương cơ bản', 'Hệ số lương', 'Hệ số phụ cấp', 'Ngày công','Phụ Cấp', 
		'Tạm ứng', 'BHXH', 'BHYT', 'BHTN', 'Thuế TNCN', // Chi tiết Khoản Trừ
		'Thực Lãnh', 'Kỳ lương'
	];
	
	$exportData = [];
	$stt = 1; 

	foreach ($arrLuong as $luong) {
		$luong_coban = $luong['luong_coban'] ?? 0;
		$he_so_luong_goc = $luong['he_so_luong_goc'] ?? 0;
		$ky_luong_format = date('m/Y', strtotime($luong['ky_luong'] ?? 'now'));

		$exportData[] = [
			$stt,
			$luong['ma_nv'] ?? '',
			$luong['hoten'] ?? '',
			$luong['phongban'] ?? '',
			$luong['chucvu'] ?? '',
			
			$luong_coban,
			$he_so_luong_goc,
			$luong['he_so_phu_cap_goc'] ?? 0,
			$luong['ngay_cong'] ?? 0,
			$luong['phu_cap'] ?? 0,
			$luong['tam_ung'] ?? 0,
			$luong['bhxh'] ?? 0,
			$luong['bhyt'] ?? 0,
			$luong['bhtn'] ?? 0,
			$luong['thue_tncn'] ?? 0,
			$luong['thuc_lanh'] ?? 0,
			$ky_luong_format,
		];
		$stt++;
	}

	// 5. Xử lý PHPSpreadsheet
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();

	// Ghi tiêu đề (Hàng 1 & 2) và dữ liệu (Bắt đầu từ Hàng 3)
	$sheet->fromArray($headerRow1, NULL, 'A1'); 
	$sheet->fromArray($headerRow2, NULL, 'A2'); 
	$sheet->fromArray($exportData, NULL, 'A3'); 

	$lastCol = $sheet->getHighestColumn();
	$lastRow = $sheet->getHighestRow();

	// ============== HỢP NHẤT VÀ ĐỊNH DẠNG TIÊU ĐỀ ==============
	
	// 1. Hợp nhất ô "Các khoản trừ" (L1 đến P1)
	$sheet->mergeCells('L1:P1'); 
	
	// 2. Hợp nhất các cột lẻ từ hàng 1 đến hàng 2 (Cột A đến K, và cột Q)
	$colsToMerge = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'Q']; 
	foreach ($colsToMerge as $col) {
		$sheet->mergeCells($col . '1:' . $col . '2');
	}

	// 3. Định dạng Tiêu đề (Áp dụng cho Hàng 1 và Hàng 2)
	$sheet->getStyle('A1:' . $lastCol . '2')->getFont()->setBold(true);
	$sheet->getStyle('A1:' . $lastCol . '2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
	$sheet->getStyle('A1:' . $lastCol . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
	$sheet->getStyle('L1:P1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER); 

	// 4. Định dạng Số (Phạm vi từ G3 đến Q cuối cùng)
	$sheet->getStyle('F3:P' . $lastRow)->getNumberFormat()->setFormatCode('#,##0'); 
	$sheet->getStyle('Q3:Q' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
	// Tự động điều chỉnh độ rộng cột
	foreach (range('A', $lastCol) as $col) {
		$sheet->getColumnDimension($col)->setAutoSize(true);
	}


	// 6. Cài đặt Header để tải file Excel và Xuất (ĐẶT TÊN THEO LỌC)
	$filename = "Danh_sach_luong_tong_hop.xlsx";

	if ($from_month && $to_month) {
		$from_month_clean = str_replace('/', '_', $from_month);
		$to_month_clean = str_replace('/', '_', $to_month);

		if ($from_month === $to_month) {
			$filename = "Danh_sach_luong_{$from_month_clean}.xlsx";
		} else {
			$filename = "Danh_sach_luong_tu_{$from_month_clean}_den_{$to_month_clean}.xlsx";
		}
	} 

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="' . $filename . '"');
	header('Cache-Control: max-age=0');
	header('Cache-Control: max-age=1'); 

	$writer = new Xlsx($spreadsheet);
	$writer->save('php://output');
	exit;
?>