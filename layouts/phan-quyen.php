<?php
	//include_once(__DIR__ . '/phan-quyen.php');
	// LẤY VAI TRÒ TỪ SESSION (GIỮ NGUYÊN NHƯ PHIÊN BẢN TRƯỚC)
	$id_quyen = $_SESSION['user']["quyen"] ?? 0;

	$ten_quyen = "Khách";
	$admin = false;
	$hr = false;
	$ke_toan = false;

	// Thiết lập các biến Boolean cho vai trò
	if ($id_quyen == 1) {
		$ten_quyen = "Admin";
		$admin = true;
	} else if ($id_quyen == 2) {
		$ten_quyen = "HR";
		$hr = true;
	} else if ($id_quyen == 3) {
		$ten_quyen = "Kế toán";
		$ke_toan = true;
	}
	function getTen_Quyen($quyen){
		switch ($quyen) {
			case 1:
					return "Admin";
				break;
			case 2:
				return "HR";
				break;
			case 3:
				return  "Kế toán";
				break;
			default:
				return   "Khách";
		}
	}
	
	
	$current_page = basename($_SERVER['PHP_SELF']); 

	// Hàm kiểm tra và in ra class 'active' cho các thẻ <li> con
	function is_active_page($page) {
		global $current_page;
		return $current_page == $page ? 'active' : '';
	}
	// ******* Thiết lập active menu ***********************************
	
	
	// 1. Kiểm tra trang con thuộc "Thiết lập nhân sự"
	$thiet_lap_ns_subpages = [
		'phong-ban.php', 'chuc-vu.php', 'trinh-do.php', 'chuyen-mon.php', 
		'loai-nhanvien.php', 'quoc-tich.php', 'ton-giao.php', 'dan-toc.php', 'hon-nhan.php'
	];
	$is_thiet_lap_ns_active = in_array($current_page, $thiet_lap_ns_subpages);


	// 2. Kiểm tra trang con thuộc "Công tác"
	$cong_tac_subpages = ['them-nhom-cong-tac.php', 'nhom-cong-tac.php'];
	$is_cong_tac_active = in_array($current_page, $cong_tac_subpages);


	// 3. Danh sách các trang con thuộc mục cha "Nhân viên" (bao gồm cả các cấp con)
	$nhanvien_subpages = array_merge(
		['them-nhan-vien.php', 'ds-nhan-vien.php', 'nhom-nhan-vien.php','xem-nhan-vien.php','ds-nhom-nhan-vien.php'], 
		$cong_tac_subpages
	);
	$is_nhanvien_active = in_array($current_page, $nhanvien_subpages);

	// 4. Các mục cha khác
	$luong_subpages = ['luong.php', 'chinh-luong.php'];
	$is_luong_active = in_array($current_page, $luong_subpages);

	$is_khenthuong_parent_active = $current_page == 'khen-thuong-ky-luat.php';

	$taikhoan_subpages = ['them-tai-khoan.php', 'ds-tai-khoan.php','tai-khoan.php'];
	$is_taikhoan_active = in_array($current_page, $taikhoan_subpages);
	// ******* Kết thúc thiết lập active menu ***********************************
	// 5. Nếu tự ý truy cập trang không được phép. chủ động đẩy về tong-quan.php
	
	if ($hr) {
		// HR không được phép vào Lương và Tài khoản
		$hr_check = ['luong.php','add-tai-khoan.php', 'ds-tai-khoan.php'];        
        if (in_array($current_page, $hr_check)) {
            header('Location: tong-quan.php');
        }
    }
	
	if ($ke_toan) {
		// HR chỉ được vào những trang này
		$kt_check = ['luong.php','ds-nhan-vien.php', 'tong-quan.php','xem-nhan-vien.php','khen-thuong-ky-luat.php'];        
        if (!in_array($current_page, $kt_check)) {
            header('Location: tong-quan.php');
        }
    } 
	
?>