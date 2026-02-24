<?php
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/PhongBan.php');

$database = new Database();
$conn = $database->getConnection();
$phongban = new PhongBan($conn);

// Thêm mới
if (isset($_POST['add'])) {
    $ma_bp = $_POST['ma_bp'];
    $ten_bp = trim($_POST['ten_bp']);
    $mota = $_POST['mota'] ?? '';
	session_start();
    $id_nguoitao = $_SESSION['user']['id'];

    if ($phongban->existsByName($ten_bp)) {
        header("Location: ../phong-ban.php?status=fail&msg=" . urlencode("Phòng ban này đã tồn tại trong danh sách."));
        exit;
    }

    $ok = $phongban->add($ma_bp, $ten_bp, $mota, $id_nguoitao);
    if ($ok) {
        header("Location: ../phong-ban.php?status=success&msg=" . urlencode("Thêm mới phòng ban thành công."));
    } else {
        header("Location: ../phong-ban.php?status=fail&msg=" . urlencode("Thêm mới phòng ban thất bại."));
    }
    exit;
}

// Cập nhật
if (isset($_POST['update'])) {
    $ok = $phongban->update($_POST['id'], $_POST['ten_bp'], $_POST['mota']);
    if ($ok) {
        header("Location: ../phong-ban.php?status=success&msg=" . urlencode("Cập nhật phòng ban thành công."));
    } else {
        header("Location: ../phong-ban.php?status=fail&msg=" . urlencode("Cập nhật phòng ban thất bại."));
    }
    exit;
}

// Xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $phongban->delete($id);

    if ($result['success']) {
        header("Location: ../phong-ban.php?status=success&msg=" . urlencode("Xóa phòng ban thành công."));
    } elseif ($result['error'] === 'constraint') {
        header("Location: ../phong-ban.php?status=fail&msg=" . urlencode("Phòng ban này đang có nhân viên nên không thể xóa."));
    } else {
        header("Location: ../phong-ban.php?status=fail&msg=" . urlencode("Không thể xóa phòng ban."));
    }
    exit;
}
?>
