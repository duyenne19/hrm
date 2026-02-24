<?php //file: them-nhan-vien-view-action.php


	include(__DIR__ . '/../connection/config.php');
	include(__DIR__ . '/../models/NhanVien.php');

	$database = new Database();
	$conn = $database->getConnection();

	// Include các model để load select
	include(__DIR__ . '/../models/QuocTich.php');
	include(__DIR__ . '/../models/TonGiao.php');
	include(__DIR__ . '/../models/DanToc.php');
	include(__DIR__ . '/../models/LoaiNhanVien.php');
	include(__DIR__ . '/../models/TrinhDo.php');
	include(__DIR__ . '/../models/ChuyenMon.php');

	include(__DIR__ . '/../models/PhongBan.php');
	include(__DIR__ . '/../models/ChucVu.php');
	include(__DIR__ . '/../models/HonNhan.php');

	// Lấy danh sách hiển thị select
	$ds_quoc_tich  = (new QuocTich($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
	$ds_ton_giao   = (new TonGiao($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
	$ds_dan_toc    = (new DanToc($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
	$ds_loai_nv    = (new LoaiNhanVien($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
	$ds_trinh_do   = (new TrinhDo($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
	$ds_chuyen_mon = (new ChuyenMon($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);

	$ds_phong_ban  = (new PhongBan($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
	$ds_chuc_vu    = (new ChucVu($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
	$ds_hon_nhan   = (new HonNhan($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);

	$nhanvien = new NhanVien($conn);
	$nhanvienInfo = null;
	if (isset($_GET['id'])) {
		$id = intval($_GET['id']);
		$nhanvienInfo = $nhanvien->getById($id);
	}
	
	$isEdit = isset($_GET['id']) && !empty($nhanvienInfo);
	$row_acc = $_SESSION['user'] ?? null;
	$ma_nv_default = "MNV" . time();

?>