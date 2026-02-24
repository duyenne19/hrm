<?php
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/ChuyenMon.php');

if (session_status() === PHP_SESSION_NONE) session_start();

$database = new Database();
$conn = $database->getConnection();
$chuyenmon = new ChuyenMon($conn);

// 🔹 Lấy danh sách
$stmt = $chuyenmon->getAll();
$arrShow = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Lấy chi tiết khi sửa
$chuyenmonInfo = null;
if (isset($_GET['idEdit'])) {
    $idEdit = intval($_GET['idEdit']);
    $chuyenmonInfo = $chuyenmon->getById($idEdit);
}

// 🔹 Thêm mới
if (isset($_POST['add'])) {
    $ma_cm = $_POST['ma_cm'] ?? '';
    $ten_cm = trim($_POST['ten_cm'] ?? '');
    $mota = $_POST['mota'] ?? '';
    $id_nguoitao = $_SESSION['user']['id'] ?? 0;

    if ($chuyenmon->existsByName($ten_cm)) {
        header("Location: ../chuyen-mon.php?status=fail&msg=" . urlencode("Tên chuyên môn đã tồn tại."));
        exit;
    }

    $ok = $chuyenmon->add($ma_cm, $ten_cm, $mota, $id_nguoitao);
    header("Location: ../chuyen-mon.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Thêm chuyên môn thành công." : "Thêm chuyên môn thất bại."));
    exit;
}

// 🔹 Cập nhật
if (isset($_POST['update'])) {
    $id = intval($_POST['id'] ?? 0);
    $ten_cm = trim($_POST['ten_cm'] ?? '');
    $mota = $_POST['mota'] ?? '';

    $ok = $chuyenmon->update($id, $ten_cm, $mota);
    header("Location: ../chuyen-mon.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Cập nhật chuyên môn thành công." : "Cập nhật chuyên môn thất bại."));
    exit;
}

// 🔹 Xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $chuyenmon->delete($id);

    if ($result['success']) {
        header("Location: ../chuyen-mon.php?status=success&msg=" . urlencode("Xóa chuyên môn thành công."));
    } else {
        if ($result['error'] === 'constraint') {
            header("Location: ../chuyen-mon.php?status=fail&msg=" . urlencode("Chuyên môn này đang được sử dụng, không thể xóa."));
        } else {
            header("Location: ../chuyen-mon.php?status=fail&msg=" . urlencode("Không thể xóa chuyên môn."));
        }
    }
    exit;
}
?>
