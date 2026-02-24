<?php
	// File: export_excel_nhanvien.php
	require __DIR__ . '/../vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\Style\Border;
	use PhpOffice\PhpSpreadsheet\Style\Alignment;
	use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Color;

	// Tải các file Model và Kết nối CSDL
	include_once __DIR__ . '/../connection/config.php';
	include(__DIR__ . '/../models/NhanVien.php');
	$database = new Database();
	$conn = $database->getConnection();
    $nhanvien_model = new NhanVien($conn); 	
    
    $filter_id_pb = $_GET['filter_id_pb'] ?? '0';
    $stmt = $nhanvien_model->getFilter_NV_PB($filter_id_pb);
    $arrNhanVien = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($arrNhanVien)) {
		die("Không tìm thấy dữ liệu nhân viên để xuất theo điều kiện lọc.");
	}

    // --- 4. ĐỊNH NGHĨA TIÊU ĐỀ, HEADER & LOGIC LỌC ---
    
    $is_filtered_by_pb = !empty($filter_id_pb) && $filter_id_pb !== '0';
    $stt = 1;
    $exportData = [];
    $ten_pb_for_title = '';

    // Mảng Tiêu đề (Keys) và Tiêu đề Tiếng Việt (Values) - Đã sắp xếp lại theo yêu cầu
    $full_headers_map = [
		'stt'             => 'STT',
		'ma_nv'           => 'Mã nhân viên',
		'hoten'           => 'Họ tên',
		'phong_ban'       => 'Phòng ban', // Vị trí 4
		'chuc_vu'         => 'Chức vụ',
		'loai_nv'         => 'Loại nhân viên',
		'chuyen_mon'      => 'Chuyên môn',
		'sodt'            => 'Số điện thoại',
		'email'           => 'Email',
		'gtinh'           => 'Giới tính',
		'ngsinh'          => 'Ngày sinh',
		'noisinh'         => 'Nơi sinh',
		'so_cccd'         => 'Số CCCD',
		'noicap_cccd'     => 'Nơi cấp CCCD',
		'ngaycap_cccd'    => 'Ngày cấp CCCD',
		'hokhau'          => 'Hộ khẩu',
		'tamtru'          => 'Tạm trú',
		'quoc_tich'       => 'Quốc tịch',
		'ton_giao'        => 'Tôn giáo',
		'dan_toc'         => 'Dân tộc',
		'hon_nhan'        => 'Hôn nhân',
		'trinh_do'        => 'Trình độ',
		'trangthai'       => 'Trạng thái', // Vị trí cuối cùng
    ];
    
    // Tạo tiêu đề cuối cùng dựa trên lọc
    if ($is_filtered_by_pb) {
        // Trường hợp 2: CÓ chọn phòng ban -> BỎ cột 'Phòng ban' khỏi header
        $final_headers_keys = array_keys($full_headers_map);
        $ten_pb_for_title = $arrNhanVien[0]['phong_ban'] ?? 'Không xác định'; 
        $title = "DANH SÁCH NHÂN VIÊN THUỘC PHÒNG BAN " . strtoupper($ten_pb_for_title);
        
        // Loại bỏ 'phong_ban' khỏi danh sách key header
        $final_headers_keys = array_diff($final_headers_keys, ['phong_ban']);

    } else {
        // Trường hợp 1: KHÔNG chọn phòng ban -> Giữ nguyên tất cả
        $title = "DANH SÁCH TẤT CẢ NHÂN VIÊN TRONG CÔNG TY";
        $final_headers_keys = array_keys($full_headers_map);
    }
    
    // Lấy tên Tiêu đề tiếng Việt cuối cùng
    $final_headers = array_intersect_key($full_headers_map, array_flip($final_headers_keys));
    $final_headers_values = array_values($final_headers);

    // --- 5. CHUẨN BỊ DỮ LIỆU ---

    foreach ($arrNhanVien as $row) {
        $data_row = [];
        
        // Bắt đầu với cột STT
        $data_row[] = $stt; 

        // Lặp qua các key đã lọc để lấy dữ liệu theo đúng thứ tự
        foreach ($final_headers_keys as $key) {
            
            if ($key === 'stt') {
                continue; // Bỏ qua STT vì đã thêm ở trên
            }
            
            $value = $row[$key] ?? ''; 
            
            // Định dạng lại cột 'trangthai'
            if ($key === 'trangthai') {
                $value = ($value == 1) ? 'Đang làm việc' : 'Đã nghỉ việc'; // Đã sửa từ 'Đã nghỉ' sang 'Đã nghỉ việc'
            }
            
            // Thêm giá trị (trừ cột 'phong_ban' nếu đang lọc)
            if ($key !== 'phong_ban' || !$is_filtered_by_pb) {
                $data_row[] = $value;
            }
        }

        $exportData[] = $data_row;
        $stt++;
    }

	// 6. Xử lý PHPSpreadsheet
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();

    // 6.1. Ghi Tiêu đề Chính (Hàng 1)
    $sheet->setCellValue('A1', $title);
    
    $header_start_row = 2; // Bắt đầu ghi Header từ Hàng 2

    // 6.2. Ghi Header (Hàng 2)
    $sheet->fromArray($final_headers_values, NULL, 'A' . $header_start_row); 

    // 6.3. Ghi Dữ liệu (Bắt đầu từ Hàng 3)
	$sheet->fromArray($exportData, NULL, 'A' . ($header_start_row + 1)); 

    $lastCol = $sheet->getHighestColumn();
	$lastRow = $sheet->getHighestRow();

	// ============== ĐỊNH DẠNG ==============
    
    // 1. Hợp nhất Tiêu đề Chính (Hàng 1)
    $sheet->mergeCells('A1:' . $lastCol . '1'); 
    
    // 2. Định dạng Tiêu đề Chính (Hàng 1)
    $sheet->getStyle('A1')->applyFromArray([
        'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0000FF']], 
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    ]);
    
    // 3. Định dạng Header (Hàng 2)
    $sheet->getStyle('A' . $header_start_row . ':' . $lastCol . $header_start_row)->applyFromArray([
		'font' => [
			'bold' => true, 
			'color' => ['rgb' => 'FFFFFF'] 
		],
		'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '008080']],
		'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
		'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
	]);

    // 4. Định dạng chung cho Bảng dữ liệu (Viền)
	$sheet->getStyle('A' . $header_start_row . ':' . $lastCol . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    
    // 5. Định dạng Căn giữa cho cột STT
    $sheet->getStyle('A' . ($header_start_row + 1) . ':A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

	// 6. Tự động điều chỉnh độ rộng cột
	foreach (range('A', $lastCol) as $col) {
		$sheet->getColumnDimension($col)->setAutoSize(true);
	}


	// 7. Cài đặt Header để tải file Excel và Xuất (ĐẶT TÊN THEO LỌC)
    $filename = "Danh_sach_nhan_vien_tong_hop.xlsx";
    
    if ($is_filtered_by_pb) {
        $filename = "DS_NV_" . str_replace(' ', '_', $ten_pb_for_title) . ".xlsx";
    }

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="' . $filename . '"');
	header('Cache-Control: max-age=0');
	header('Cache-Control: max-age=1'); 

	$writer = new Xlsx($spreadsheet);
	$writer->save('php://output');
	exit;
?>