<?php
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/BangCap.php');

if (session_status() === PHP_SESSION_NONE) session_start();

$database = new Database();
$conn = $database->getConnection();
$bangcap = new BangCap($conn);

// 🔹 Lấy danh sách
$stmt = $bangcap->getAll();
$arrShow = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Lấy chi tiết khi sửa
$bangcapInfo = null;
if (isset($_GET['idEdit'])) {
    $idEdit = intval($_GET['idEdit']);
    $bangcapInfo = $bangcap->getById($idEdit);
}

// 🔹 Thêm mới
if (isset($_POST['add'])) {
    $ma_bcap = $_POST['ma_bcap'] ?? '';
    $ten_bcap = trim($_POST['ten_bcap'] ?? '');
    $mota_bcap = $_POST['mota_bcap'] ?? '';
    $id_nguoitao = $_SESSION['user']['id'] ?? 0;

    if ($bangcap->existsByName($ten_bcap)) {
        header("Location: ../bang-cap.php?status=fail&msg=" . urlencode("Tên bằng cấp đã tồn tại."));
        exit;
    }

    $ok = $bangcap->add($ma_bcap, $ten_bcap, $mota_bcap, $id_nguoitao);
    header("Location: ../bang-cap.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Thêm bằng cấp thành công." : "Thêm bằng cấp thất bại."));
    exit;
}

// 🔹 Cập nhật
if (isset($_POST['update'])) {
    $id = intval($_POST['id'] ?? 0);
    $ten_bcap = trim($_POST['ten_bcap'] ?? '');
    $mota_bcap = $_POST['mota_bcap'] ?? '';

    $ok = $bangcap->update($id, $ten_bcap, $mota_bcap);
    header("Location: ../bang-cap.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Cập nhật bằng cấp thành công." : "Cập nhật bằng cấp thất bại."));
    exit;
}

// 🔹 Xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $bangcap->delete($id);

    if ($result['success']) {
        header("Location: ../bang-cap.php?status=success&msg=" . urlencode("Xóa bằng cấp thành công."));
    } else {
        if ($result['error'] === 'constraint') {
            header("Location: ../bang-cap.php?status=fail&msg=" . urlencode("Bằng cấp này đang được sử dụng, không thể xóa."));
        } else {
            header("Location: ../bang-cap.php?status=fail&msg=" . urlencode("Không thể xóa bằng cấp."));
        }
    }
    exit;
}
?>
