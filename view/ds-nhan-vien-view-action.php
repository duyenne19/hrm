<?php
	include(__DIR__ . '/../connection/config.php');
	include(__DIR__ . '/../models/NhanVien.php');
	include_once __DIR__ . '/../models/PhongBan.php';

	$database = new Database();
	$conn = $database->getConnection();
	
	// ******************** Lọc theo phòng ban *******************************
		$phong_ban_model = new PhongBan($conn);
		
		$arrPhongBan = $phong_ban_model->getAll();
	// ********************  *******************************
	$filter_id_pb = $_GET['filter_id_pb'] ?? '0';
	$nhanvien = new NhanVien($conn);	
	$stmt = $nhanvien->getFilter_NV_PB($filter_id_pb);
	$arrNhanVien = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
