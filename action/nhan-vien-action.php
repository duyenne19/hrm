<?php //file them-nhan-vien-action.php
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	include(__DIR__ . '/../connection/config.php');
	include(__DIR__ . '/../models/NhanVien.php');
	$database = new Database();
	$conn = $database->getConnection();
	$nhanvien = new NhanVien($conn);
	// ===============================
	// Xử lý thêm hoặc sửa nhân viên
	// ===============================

	$id_nguoitao = $_SESSION['user']['id'] ?? 1;
	
	$nhanvienInfo = null;
	$is_update_mode = isset($_POST['update']);
	$id_can_sua = $is_update_mode ? intval($_POST['id']) : null;
	if ($is_update_mode && $id_can_sua) {
		// Gọi hàm lấy thông tin nhân viên cũ bằng ID
		$nhanvienInfo = $nhanvien->getById($id_can_sua); 
	}
	
	$uploadDir = __DIR__ . '/../uploads/nhanvien/';
	if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

	// Xử lý upload ảnh (chung cho cả thêm & sửa)
	$anhdaidien = $_FILES['anhdaidien']['name'] ?? null;
	if ($anhdaidien) {
		$fileName = time() . '_' . basename($_FILES['anhdaidien']['name']);
		$targetPath = $uploadDir . $fileName;
		if (move_uploaded_file($_FILES['anhdaidien']['tmp_name'], $targetPath)) {
			// Nếu upload mới thành công và có ảnh cũ → xóa ảnh cũ (tránh rác)
			if (!empty($nhanvienInfo['anhdaidien']) && file_exists($uploadDir . $nhanvienInfo['anhdaidien'])) {
				unlink($uploadDir . $nhanvienInfo['anhdaidien']);
			}
			$anhdaidien = $fileName;
		} else {
			$anhdaidien = $nhanvienInfo['anhdaidien'] ?? null;
		}
	} else {
		$anhdaidien = $nhanvienInfo['anhdaidien'] ?? null;
	}
	// ===================
	// Thêm mới
	// ===================
	if (isset($_POST['add'])) {
		$data = [        
			'anhdaidien'   => $anhdaidien,
			'hoten'        => $_POST['ten_nv'],
			'sodt'         => $_POST['sodt'],
			'email'        => $_POST['email'],
			'gtinh'        => $_POST['gioi_tinh'],
			'ngsinh'       => $_POST['ngaysinh'],
			'noisinh'      => $_POST['noisinh'],
			//'que_quan'     => $_POST['nguyen_quan'],
			'id_honnhan'   => $_POST['hon_nhan'],
			'so_cccd'      => $_POST['cmnd'],
			'noicap_cccd'  => $_POST['noicap'],
			'ngaycap_cccd' => $_POST['ngaycap'],
			'id_quoctich'  => $_POST['quoc_tich'],
			'id_tongiao'   => $_POST['ton_giao'],
			'id_dantoc'    => $_POST['dan_toc'],
			'hokhau'       => $_POST['hokhau'],
			'tamtru'       => $_POST['tamtru'],
			'id_loainv'    => $_POST['loai_nv'],
			'id_trinhdo'   => $_POST['trinh_do'],
			'id_chuyenmon' => $_POST['chuyen_mon'],        
			'id_phongban'  => $_POST['phong_ban'],
			'id_chucvu'    => $_POST['chuc_vu'],
			'trangthai'    => 1,
			'id_nguoitao'  => $id_nguoitao
		];
		$ok = $nhanvien->add($data);
		if ($ok) {
			header("Location: ../ds-nhan-vien.php?status=success&msg=" . urlencode("Thêm mới nhân viên thành công."));
		} else {
			header("Location: ../add-nhanvien.php?status=fail&msg=" . urlencode("Thêm mới nhân viên thất bại."));
		}
		
		exit;
	}

	// ===================
	// Cập nhật
	// ===================
	if (isset($_POST['update'])) {
		$id = intval($_POST['id']);

		// Helper lấy giá trị null nếu rỗng
		$getOpt = function($key) {
			return (isset($_POST[$key]) && $_POST[$key] !== '') ? $_POST[$key] : null;
		};

		$data = [
			
			'anhdaidien'   => $anhdaidien,
			'hoten'        => $_POST['ten_nv'],
			'sodt'         => $_POST['sodt'],
			'email'        => $_POST['email'],
			'gtinh'        => $_POST['gioi_tinh'],
			'ngsinh'       => $_POST['ngaysinh'],
			'noisinh'      => $getOpt('noisinh'),
			
			'id_honnhan'   => $getOpt('hon_nhan'),
			'so_cccd'      => $_POST['cmnd'],
			'noicap_cccd'  => $getOpt('noicap'),
			'ngaycap_cccd' => $getOpt('ngaycap'),
			'id_quoctich'  => $getOpt('quoc_tich'),
			'id_tongiao'   => $getOpt('ton_giao'),
			'id_dantoc'    => $getOpt('dan_toc'),
			'hokhau'       => $getOpt('hokhau'),
			'tamtru'       => $getOpt('tamtru'),
			'id_loainv'    => $_POST['loai_nv'],
			'id_trinhdo'   => $getOpt('trinh_do'),
			'id_chuyenmon' => $getOpt('chuyen_mon'),
			
			'id_phongban'  => $_POST['phong_ban'],
			'id_chucvu'    => $_POST['chuc_vu'],
			'trangthai'    => $_POST['trang_thai']
		];
		$ok = $nhanvien->update($id, $data);
		if ($ok) {
			header('Location: ../ds-nhan-vien.php?status=success&msg=' . urlencode('Cập nhật nhân viên thành công.'));
		} else {
			header('Location: ../them-nhan-vien.php?id=' . $id . '&status=fail&msg=' . urlencode('Cập nhật nhân viên thất bại.'));
		}
		exit;
	}
	// ==========================
	// XÓA NHÂN VIÊN
	// ==========================
	if (isset($_GET['delete_id'])) {
		$id = intval($_GET['delete_id']);
		$result = $nhanvien->delete($id);

		$status = $result['success'] ? 'success' : 'fail';
		$msg = urlencode($result['message']);

		header("Location: ../ds-nhan-vien.php?status={$status}&msg={$msg}");
		exit;
	}
?>
