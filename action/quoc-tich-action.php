<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/QuocTich.php');


$database = new Database();
$conn = $database->getConnection();
$quoctich = new QuocTich($conn);


// 🔹 Thêm mới
if (isset($_POST['add'])) {
    $ma_qt = $_POST['ma_qt'] ?? '';
    $ten_qt = trim($_POST['ten_qt'] ?? '');
    $id_nguoitao = $_SESSION['user']['id'] ?? 0;

    if ($quoctich->existsByName($ten_qt)) {
        header("Location: ../quoc-tich.php?status=fail&msg=" . urlencode("Tên quốc tịch đã tồn tại."));
        exit;
    }

    $ok = $quoctich->add($ma_qt, $ten_qt, $id_nguoitao);
    header("Location: ../quoc-tich.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Thêm quốc tịch thành công." : "Thêm quốc tịch thất bại."));
    exit;
}

// 🔹 Cập nhật
if (isset($_POST['update'])) {
    $id = intval($_POST['id'] ?? 0);
    $ten_qt = trim($_POST['ten_qt'] ?? '');

    $ok = $quoctich->update($id, $ten_qt);
    header("Location: ../quoc-tich.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Cập nhật quốc tịch thành công." : "Cập nhật quốc tịch thất bại."));
    exit;
}

// 🔹 Xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $quoctich->delete($id);

    if ($result['success']) {
        header("Location: ../quoc-tich.php?status=success&msg=" . urlencode("Xóa quốc tịch thành công."));
    } else {
        if ($result['error'] === 'constraint') {
            header("Location: ../quoc-tich.php?status=fail&msg=" . urlencode("Quốc tịch này đang được sử dụng, không thể xóa."));
        } else {
            header("Location: ../quoc-tich.php?status=fail&msg=" . urlencode("Không thể xóa quốc tịch."));
        }
    }
    exit;
}
?>
