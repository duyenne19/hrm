<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/DanToc.php');



$database = new Database();
$conn = $database->getConnection();
$dantoc = new DanToc($conn);



// 🔹 Thêm mới
if (isset($_POST['add'])) {
    $ma_dt = $_POST['ma_dt'];
    $ten_dt = trim($_POST['ten_dt']);
    $id_nguoitao = $_SESSION['user']['id'] ?? 0;

    if ($dantoc->existsByName($ten_dt)) {
        header("Location: ../dan-toc.php?status=fail&msg=" . urlencode("Tên dân tộc đã tồn tại."));
        exit;
    }

    $ok = $dantoc->add($ma_dt, $ten_dt, $id_nguoitao);
    header("Location: ../dan-toc.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Thêm dân tộc thành công!" : "Thêm dân tộc thất bại."));
    exit;
}

// 🔹 Cập nhật
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $ten_dt = trim($_POST['ten_dt']);
    $ok = $dantoc->update($id, $ten_dt);
    header("Location: ../dan-toc.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Cập nhật dân tộc thành công!" : "Cập nhật thất bại."));
    exit;
}

// 🔹 Xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $dantoc->delete($id);

    if ($result['success']) {
        header("Location: ../dan-toc.php?status=success&msg=" . urlencode("Xóa dân tộc thành công!"));
    } else {
        if ($result['error'] === 'constraint') {
            header("Location: ../dan-toc.php?status=fail&msg=" . urlencode("Dân tộc này đang được sử dụng, không thể xóa."));
        } else {
            header("Location: ../dan-toc.php?status=fail&msg=" . urlencode("Không thể xóa dân tộc."));
        }
    }
    exit;
}
?>
