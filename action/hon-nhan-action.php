<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/HonNhan.php');



$database = new Database();
$conn = $database->getConnection();
$honnhan = new HonNhan($conn);



// 🔹 Thêm mới
if (isset($_POST['add'])) {
    $ma_hn = $_POST['ma_hn'];
    $ten_hn = trim($_POST['ten_hn']);
    $id_nguoitao = $_SESSION['user']['id'] ?? 0;

    if ($honnhan->existsByName($ten_hn)) {
        header("Location: ../hon-nhan.php?status=fail&msg=" . urlencode("Tên tình trạng hôn nhân đã tồn tại."));
        exit;
    }

    $ok = $honnhan->add($ma_hn, $ten_hn, $id_nguoitao);
    header("Location: ../hon-nhan.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Thêm mới thành công!" : "Thêm mới thất bại."));
    exit;
}

// 🔹 Cập nhật
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $ten_hn = trim($_POST['ten_hn']);
    $ok = $honnhan->update($id, $ten_hn);
    header("Location: ../hon-nhan.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Cập nhật thành công!" : "Cập nhật thất bại."));
    exit;
}

// 🔹 Xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $honnhan->delete($id);

    if ($result['success']) {
        header("Location: ../hon-nhan.php?status=success&msg=" . urlencode("Xóa tình trạng hôn nhân thành công!"));
    } else {
        if ($result['error'] === 'constraint') {
            header("Location: ../hon-nhan.php?status=fail&msg=" . urlencode("Tình trạng này đang được sử dụng, không thể xóa."));
        } else {
            header("Location: ../hon-nhan.php?status=fail&msg=" . urlencode("Không thể xóa tình trạng hôn nhân."));
        }
    }
    exit;
}
?>
