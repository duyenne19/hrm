<?php
// File: action/luong-action.php (ĐÃ FIX LỖI CÚ PHÁP VÀ LỖI INCLUDE)

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}

	// ĐÃ SỬA: Thêm include Database.php để tránh lỗi "Class 'Database' not found"
	include_once __DIR__ . '/../connection/config.php';
	
	include_once __DIR__ . '/../models/Luong.php';
	include_once __DIR__ . '/../models/PhongBan.php';

	
	
	$database = new Database();
	$conn = $database->getConnection();
	
	$luong_model = new Luong($conn);
	
	
	// ***************************
	if (isset($_POST['add']) || isset($_POST['update'])){
		
		
		$data = $_POST;
		$data['luong_co_ban'] = str_replace(',', '', $data['luong_co_ban_input'] ?? 0);
		$data['he_so_luong'] = $data['he_so_luong_input'] ?? 0;
		$data['he_so_phu_cap'] = $data['he_so_phu_cap_input'] ?? 0;

		
		$data['id_nv'] = $data['id_nv'] ?? $luongInfo['id_nv'] ?? null;
		
		// ĐÃ LÀM SẠCH KÝ TỰ LỖI
		$data['tam_ung'] = str_replace(',', '', $data['tam_ung'] ?? 0);
		$data['ngay_cong'] = floatval($data['ngay_cong']);		
		// Chuyển MM/YYYY sang YYYY-MM-01 cho cột ky_luong
		$thang_arr = explode('/', $data['ky_luong_display']);
		// ĐÃ LÀM SẠCH KÝ TỰ LỖI
		$data['ky_luong'] = $thang_arr[1] . '-' . $thang_arr[0] . '-01';
		
		// Tự động tạo Mã Lương nếu thêm mới chưa có mã
		$data['ma_luong'] = $data['ma_luong'] ?? "ML" . time();

			
		if (isset($data['add'])) {
			
			$result = $luong_model->addLuong($data);
			$status = ($result) ? 'success' : 'fail';
			$msg = ($result) ? 'Tính lương thành công!' : 'Tính lương thất bại.';
			header("Location: ../luong.php?status=$status&msg=" . urlencode($msg));
			exit();
			
		} elseif (isset($data['update'])) {
			// ĐÃ LÀM SẠCH KÝ TỰ LỖI
			$id_luong_update = $data['id'];
			
			$result = $luong_model->updateLuong($id_luong_update, $data);
			
			$status = ($result) ? 'success' : 'fail';
			$msg = ($result) ? 'Cập nhật lương thành công!' : 'Cập nhật lương thất bại.';
			header("Location: ../luong.php?status=$status&msg=" . urlencode($msg));
			exit();
		}


	} else{		
		if (isset($_GET['delete'])) {
			
			$id_luong_xoa = $_GET['delete'];
			
			$result = $luong_model->deleteLuong($id_luong_xoa);

			$status = ($result) ? 'success' : 'fail';
			$msg = ($result) ? 'Xóa lương nhân viên thành công!' : 'Xóa lương nhân viên thất bại.';
			header("Location: ../luong.php?status=$status&msg=" . urlencode($msg));
			
			exit();
		}
	}

?>