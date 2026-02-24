<?php
	include(__DIR__ . '/../connection/config.php');
	include(__DIR__ . '/../models/TaiKhoan.php');
	
	$database = new Database();
	$conn = $database->getConnection();
	$taiKhoan = new TaiKhoan($conn);

	// Cập nhật thông tin cá nhân
	if (isset($_POST['update-ca-nhan'])) {
		$data = [
			'id' => $_POST['id'],
			'ho' => trim($_POST['ho']),
			'ten' => trim($_POST['ten']),
			'sodt' => trim($_POST['sodt'])
		];
		$file = $_FILES['hinhanh'] ?? null;

		$result = $taiKhoan->updateInfo($data, $file);
		if ($result['success']) {
			// refresh session
			$updated = $taiKhoan->getById($data['id']);
			if ($updated) {
				$_SESSION['user']['ho'] = $updated['ho'];
				$_SESSION['user']['ten'] = $updated['ten'];
				$_SESSION['user']['sodt'] = $updated['sodt'];
				if (!empty($updated['hinhanh'])) $_SESSION['user']['hinhanh'] = $updated['hinhanh'];
			}
		}

		header('Location: ../tai-khoan-ca-nhan.php?status=' . ($result['success'] ? 'success' : 'fail') . '&msg=' . urlencode($result['message']));
		exit;
	}
	/* ========================
   🔹 2. Đổi mật khẩu
	======================== */
	if (isset($_POST['updateChangePassword'])) {
		$id = (int)($_POST['id'] ?? 0);
		$mk_cu = trim($_POST['mk_cu'] ?? '');
		$mk_cu_xacnhan = trim($_POST['mk_cu_xacnhan'] ?? '');
		$mk_moi = trim($_POST['mk_moi'] ?? '');
		$mk_moi_xacnhan = trim($_POST['mk_moi_xacnhan'] ?? '');

		// Kiểm tra mật khẩu cũ nhập lại
		if ($mk_cu !== $mk_cu_xacnhan) {
			header('Location: ../tai-khoan-ca-nhan.php?status=fail&msg=' . urlencode('❌ Mật khẩu cũ và xác nhận mật khẩu cũ không trùng khớp.'));
			exit;
		}

		// Kiểm tra mật khẩu mới nhập lại
		if ($mk_moi !== $mk_moi_xacnhan) {
			header('Location: ../tai-khoan-ca-nhan.php?status=fail&msg=' . urlencode('❌ Mật khẩu mới và xác nhận mật khẩu mới không trùng khớp.'));
			exit;
		}

		// Thực hiện đổi mật khẩu
		$result = $taiKhoan->updatePassword($id, $mk_cu, $mk_moi);
		$status = $result['success'] ? 'success' : 'fail';

		header('Location: ../tai-khoan-ca-nhan.php?status=' . $status . '&msg=' . urlencode($result['message']));
		exit;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$isEdit = isset($_POST['isEdit']) && $_POST['isEdit'] == 1;
		$file = $_FILES['hinhanh'] ?? null;

		// Chuẩn bị dữ liệu
		$data = [
			'ho' => trim($_POST['ho'] ?? ''),
			'ten' => trim($_POST['ten'] ?? ''),
			'email' => trim($_POST['email'] ?? ''),
			'sodt' => trim($_POST['sodt'] ?? ''),
			'quyen' => $_POST['quyen'] ?? 0,
			'trangthai' => $_POST['trangthai'] ?? 1,
		];

		// ✅ Upload hình ảnh nếu có
		if ($file && $file['error'] === UPLOAD_ERR_OK) {
			$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
			$allow = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
			if (!in_array($ext, $allow)) {
				header('Location: ../add-tai-khoan.php?status=fail&msg=Định dạng ảnh không hợp lệ');
				exit;
			}

			$newFileName = time() . '_' . uniqid() . '.' . $ext;
			$uploadDir = __DIR__ . '/../uploads/anh/';
			if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
			$targetPath = $uploadDir . $newFileName;

			if (move_uploaded_file($file['tmp_name'], $targetPath)) {
				$data['hinhanh'] = $newFileName;
			} else {
				header('Location: ../add-tai-khoan.php?status=fail&msg=Tải lên ảnh thất bại');
				exit;
			}
		}

		// ✅ Thêm mới
		if (!$isEdit) {
			$data['mk'] = $_POST['mk'] ?? '';
			$result = $taiKhoan->add($data);

			if ($result['success']) {
				header('Location: ../ds-tai-khoan.php?status=success&msg=Thêm tài khoản thành công');
			} elseif (($result['error'] ?? '') === 'duplicate_email') {
				header('Location: ../add-tai-khoan.php?status=fail&msg=Email này đã tồn tại. Vui lòng chọn email khác.');
			} else {
				header('Location: ../add-tai-khoan.php?status=fail&msg=Thêm tài khoản thất bại.');
			}
			exit;
		}

		// ✅ Cập nhật
		else {
			$data['id'] = $_POST['id'];
			$result = $taiKhoan->update($data);

			if ($result) {
				header('Location: ../ds-tai-khoan.php?status=success&msg=Cập nhật tài khoản thành công');
			} else {
				header('Location: ../add-tai-khoan.php?id=' . $data['id'] . '&status=fail&msg=Cập nhật tài khoản thất bại');
			}
			exit;
		}
	}

	// ✅ Xóa tài khoản
	if (isset($_GET['delete_id'])) {
		$id = (int)$_GET['delete_id'];
		$result = $taiKhoan->delete($id);

		if ($result['success']) {
			header('Location: ../ds-tai-khoan.php?status=success&msg=Xóa tài khoản thành công');
		} else {
			$msg = 'Xóa tài khoản thất bại';
			if ($result['error'] === 'constraint') {
				$msg = 'Tài khoản này đã sử dụng để thêm dữ liệu, không thể xóa.';
			}
			header('Location: ../ds-tai-khoan.php?status=fail&msg=' . urlencode($msg));
		}
		exit;
	}

?>
