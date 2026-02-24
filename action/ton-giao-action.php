<?php
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/TonGiao.php');

if (session_status() === PHP_SESSION_NONE) session_start();

$database = new Database();
$conn = $database->getConnection();
$tongiao = new TonGiao($conn);

// 🔹 Thêm mới
if (isset($_POST['add'])) {
    $ma_tg = $_POST['ma_tg'] ?? '';
    $ten_tg = trim($_POST['ten_tg'] ?? '');
    $id_nguoitao = $_SESSION['user']['id'] ?? 0;

    if ($tongiao->existsByName($ten_tg)) {
        header("Location: ../ton-giao.php?status=fail&msg=" . urlencode("Tên tôn giáo đã tồn tại."));
        exit;
    }

    $ok = $tongiao->add($ma_tg, $ten_tg, $id_nguoitao);
    header("Location: ../ton-giao.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Thêm tôn giáo thành công." : "Thêm tôn giáo thất bại."));
    exit;
}

// 🔹 Cập nhật
if (isset($_POST['update'])) {
    $id = intval($_POST['id'] ?? 0);
    $ten_tg = trim($_POST['ten_tg'] ?? '');
    $ok = $tongiao->update($id, $ten_tg);
    header("Location: ../ton-giao.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Cập nhật tôn giáo thành công." : "Cập nhật thất bại."));
    exit;
}

// 🔹 Xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $tongiao->delete($id);

    if ($result['success']) {
        header("Location: ../ton-giao.php?status=success&msg=" . urlencode("Xóa tôn giáo thành công."));
    } else {
        if ($result['error'] === 'constraint') {
            header("Location: ../ton-giao.php?status=fail&msg=" . urlencode("Tôn giáo này đang được sử dụng, không thể xóa."));
        } else {
            header("Location: ../ton-giao.php?status=fail&msg=" . urlencode("Không thể xóa tôn giáo."));
        }
    }
    exit;
}
?>
