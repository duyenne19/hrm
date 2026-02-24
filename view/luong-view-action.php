<?php
	// ĐÃ SỬA: Thêm include Database.php để tránh lỗi "Class 'Database' not found"
	include_once __DIR__ . '/../connection/config.php';
	
	include_once __DIR__ . '/../models/Luong.php';
	include_once __DIR__ . '/../models/PhongBan.php';

	
	
	$database = new Database();
	$conn = $database->getConnection();
	$phong_ban_model = new PhongBan($conn);
	$luong_model = new Luong($conn);
	$maLuong = $maLuong ?? "ML" . time();
	
	// --- Lấy toàn bộ Phòng Ban cho selecttion *******************************************************************
	$arrPhongBan = $phong_ban_model->getAll();
	$filter_arrPhongBan = $phong_ban_model->getAll();
	$selected_id_pb = ($_GET['id_pb'] ?? 0);
	// *******************************************************************

	// Tiếp tục sử lý nếu chọn nhân viên => sẽ thêm hiển thị lương cơ bản + phụ cấp:
	$selected_id_nv = $_GET['id_nv'] ?? null;
	$idEdit = $_GET['idEdit'] ?? null;
	$luongComponents = $luongComponents ?? [];
	
	
	$default_month_display = date('m/Y', strtotime('-1 month'));	
	$thangTinhLuongValue = $_GET['ky_luong_chon'] ?? $default_month_display;
	$ky_luong_chon_db = null; // Biến dùng để truyền vào Model (YYYY-MM-01)

	$thang_arr = explode('/', $thangTinhLuongValue);
	if (count($thang_arr) === 2) {
		// Chuyển MM/YYYY sang YYYY-MM-01
		$ky_luong_chon_db = $thang_arr[1] . '-' . $thang_arr[0] . '-01';
	}

	// Thiết lập giá trị Tháng Tính Lương hiển thị trên form ($thangTinhLuongValue)
	
	if ($selected_id_nv && !$idEdit) {
		$luongComponents = $luong_model->getLcb_Hsl_Hspc(intval($selected_id_nv));
	}
	// Nếu sửa thì sẽ truy xuất bảng lương để lấy dữ liệu cũ.
	$luongInfo = [];
	// --- Lấy toàn bộ nhân viên theo phòng ban cho selecttion  *******************************************************************
	$arrNhanVien = [];
	if ($idEdit) {
		// Logic lấy thông tin lương để sửa
		$luongInfo = $luong_model->getLuongNhanVien(intval($idEdit));
		// Cập nhật lại các giá trị gốc cho Form nếu sửa
		$luongComponents['luong_co_ban'] = $luongInfo['luong_co_ban_goc'] ?? 0;
		$luongComponents['he_so_luong'] = $luongInfo['he_so_luong_goc'] ?? 0;
		$luongComponents['he_so_phu_cap'] = $luongInfo['he_so_phu_cap_goc'] ?? 0;
		
		$selected_id_nv = $luongInfo['id_nv'] ?? $selected_id_nv; 
		$selected_id_pb = $luongInfo['id_phongban'] ?? $selected_id_pb; 
		
		$thangTinhLuongValue = date('m/Y', strtotime($luongInfo['ky_luong']));
		
			$arrNhanVien = [
				[
					// Cần đảm bảo các key này khớp với vòng lặp foreach trong luong.php
					'id'       => $luongInfo['id_nv'],
					'hoten'    => $luongInfo['hoten'],      
					'chucvu'   => $luongInfo['chucvu'],    
					'phongban' => $luongInfo['phongban'],
					// Thêm các trường khác của NV nếu cần
				]
			];
		
	}elseif ($selected_id_pb) {
		// CHẾ ĐỘ THÊM MỚI VÀ ĐÃ CHỌN PHÒNG BAN: Lọc nhân viên chưa có lương		
		// Sử dụng hàm getAllNhanVien_PhongBan đã được sửa để lọc nhân viên
		$arrNhanVien = $luong_model->getAllNhanVien_PhongBan(
			$selected_id_pb, 
			$ky_luong_chon_db 
		);
	}
	

		// Lưu giá trị của lương cơ bản, hệ số để hiển thị lên form cho dễ.
	$luongCoBan = $luongComponents['luong_co_ban'] ?? $luongInfo['luong_co_ban_goc'] ?? 0;
	$heSoLuong = $luongComponents['he_so_luong'] ?? $luongInfo['he_so_luong_goc'] ?? 0;
	$heSoPhuCap = $luongComponents['he_so_phu_cap'] ?? $luongInfo['he_so_phu_cap_goc'] ?? 0;
	
	// Tìm kiếm từ tháng XX -> đến thang XX *******************************************************************
	
	$default_from_month = date('Y-m', strtotime('-1 month'));
	$default_to_month = date('Y-m', strtotime('-1 month'));
	$from_date = $default_from_month . '-01';
	$to_date = date('Y-m-t', strtotime($default_to_month . '-01'));
	$filter_id_pb = $_GET['filter_id_pb'] ?? 0;
	
	if (isset($_GET['from_month']) && isset($_GET['to_month'])) {
		$from_date = date('Y-m-01', strtotime(str_replace('/', '-', '01/' . $_GET['from_month'])));
		$to_date = date('Y-m-t', strtotime(str_replace('/', '-', '01/' . $_GET['to_month'])));
	}
	
	$arrLuong = $luong_model->getSalaryList($from_date, $to_date,$filter_id_pb);
	// Kết thúc từ tháng XX -> đến thang XX *******************************************************************
	// Tạo biến hiển thị tháng mặc định (Dùng cho tiêu đề Bảng)	
	if (isset($_GET['from_month']) && isset($_GET['to_month'])) {
		$start_month = $_GET['from_month']; // Ví dụ: 05/2025
		$end_month = $_GET['to_month'];     // Ví dụ: 10/2025
		
		if ($start_month === $end_month) {
			// Nếu tháng bắt đầu và kết thúc giống nhau, chỉ hiển thị một tháng
			$display_month = $start_month;
		} else {
			// Nếu lọc theo khoảng thời gian, hiển thị "từ A đến B"
			$display_month = "từ $start_month đến $end_month";
		}
	} else {
		// Trường hợp mặc định (không có lọc), giữ nguyên tháng bắt đầu
		$display_month = date('m/Y', strtotime($from_date));
	}
	// *******************************************************************

	// --- ******************************************************************* ---
	$filter_params = [];
	if (isset($_GET['from_month'])) {
		$filter_params['from_month'] = $_GET['from_month'];
	}
	if (isset($_GET['to_month'])) {
		$filter_params['to_month'] = $_GET['to_month'];
	}
	// Chuyển mảng tham số thành chuỗi URL: ?from_month=...&to_month=...
	$redirect_query_string = http_build_query($filter_params);

	// Thêm ký tự '&' nếu chuỗi không rỗng, để dễ dàng nối với status/msg
	if (!empty($redirect_query_string)) {
		$redirect_query_string = '&' . $redirect_query_string;
	}	
	
	// ***************************
	
?>