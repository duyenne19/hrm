<?php
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/ChucVu.php');

$database = new Database();
$conn = $database->getConnection();
$chucvu = new ChucVu($conn);

// Thêm mới
if (isset($_POST['add'])) {
    session_start();
    $macv = $_POST['macv'];
    $tencv = trim($_POST['tencv']);
	
	$luong_coban = (int)str_replace(',', '', trim($_POST['luong_coban']));
	$he_so_luong = trim($_POST['he_so_luong']);
	$he_so_phu_cap = trim($_POST['he_so_phu_cap']);
	
    $mota = $_POST['mota'] ?? '';
    $id_nguoitao = $_SESSION['user']['id'];

    if ($chucvu->existsByName($tencv)) {
        header("Location: ../chuc-vu.php?status=fail&msg=" . urlencode("Chức vụ này đã tồn tại trong danh sách."));
        exit;
    }

    $ok = $chucvu->add($macv, $tencv, $luong_coban,$he_so_luong,$he_so_phu_cap, $mota, $id_nguoitao);
    if ($ok) {
        header("Location: ../chuc-vu.php?status=success&msg=" . urlencode("Thêm mới chức vụ thành công."));
    } else {
        header("Location: ../chuc-vu.php?status=fail&msg=" . urlencode("Thêm mới chức vụ thất bại."));
    }
    exit;
}

// Cập nhật
if (isset($_POST['update'])) {
	
	$id_chucvu = trim($_POST['id']);
    $tencv = trim($_POST['tencv']);
	
	$luong_coban = (int)str_replace(',', '', trim($_POST['luong_coban']));
	$he_so_luong = trim($_POST['he_so_luong']);
	$he_so_phu_cap = trim($_POST['he_so_phu_cap']);
	
    $ok = $chucvu->update($id_chucvu,$tencv, $luong_coban,$he_so_luong,$he_so_phu_cap, $mota);
    if ($ok) {
        header("Location: ../chuc-vu.php?status=success&msg=" . urlencode("Cập nhật chức vụ thành công."));
    } else {
        header("Location: ../chuc-vu.php?status=fail&msg=" . urlencode("Cập nhật chức vụ thất bại."));
    }
    exit;
}

// Xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $chucvu->delete($id);

    if ($result['success']) {
        header("Location: ../chuc-vu.php?status=success&msg=" . urlencode("Xóa chức vụ thành công."));
    } elseif ($result['error'] === 'constraint') {
        header("Location: ../chuc-vu.php?status=fail&msg=" . urlencode("Chức vụ này đang được sử dụng và không thể xóa."));
    } else {
        header("Location: ../chuc-vu.php?status=fail&msg=" . urlencode("Không thể xóa chức vụ."));
    }
    exit;
}
?>
