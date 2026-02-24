<?php
	session_start();
	include(__DIR__ . '/../connection/config.php');
	include(__DIR__ . '/../models/TaiKhoan.php');
	
	$database = new Database();
	$conn = $database->getConnection();
	$tai_khoan = new TaiKhoan($conn);
	
	if(isset($_POST['submit']))
	{
		$tai_khoan->id = $_POST['id'];
		$tai_khoan->ho = $_POST['ho'];
		$tai_khoan->ten = $_POST['ten'];
		$tai_khoan->sodt = $_POST['sodt'];

		// Kiểm tra nếu có upload ảnh
		$file = isset($_FILES['hinhanh']) ? $_FILES['hinhanh'] : null;

		$result = $tai_khoan->update($file);
		if ($result['success']) {
			$_SESSION['user']['ho'] = $tai_khoan->ho;
			$_SESSION['user']['ten'] = $tai_khoan->ten;
			$_SESSION['user']['sodt'] = $tai_khoan->sodt;
			if (!empty($result['hinhanh'])) {
				$_SESSION['user']['hinhanh'] = $result['hinhanh'];
			}
		}
		//echo json_encode($result, JSON_UNESCAPED_UNICODE);
		header("Location: ../taikhoan-canhan.php?error=".$result["message"]);
	}
	
?>