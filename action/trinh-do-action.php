<?php
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/TrinhDo.php');



$database = new Database();
$conn = $database->getConnection();
$trinhdo = new TrinhDo($conn);


// Thêm mới
if (isset($_POST['add'])) {
	if (session_status() === PHP_SESSION_NONE) session_start();
    $ma_td = $_POST['ma_td'] ?? '';
    $ten_td = trim($_POST['ten_td'] ?? '');
	$mota_td = trim($_POST['mota_td'] ?? '');
    $id_nguoitao = $_SESSION['user']['id'] ?? 0;

    // kiểm tra trùng tên (nếu bạn đã thêm existsByName trong TrinhDo.php)
    if (method_exists($trinhdo, 'existsByName') && $trinhdo->existsByName($ten_td)) {
        header("Location: ../trinh-do.php?status=fail&msg=" . urlencode("Trình độ này đã tồn tại trong danh sách."));
        exit;
    }

    $ok = $trinhdo->add($ma_td, $ten_td, $mota_td, $id_nguoitao);
    if ($ok) {
        header("Location: ../trinh-do.php?status=success&msg=" . urlencode("Thêm mới trình độ thành công."));
    } else {
        header("Location: ../trinh-do.php?status=fail&msg=" . urlencode("Thêm mới trình độ thất bại."));
    }
    exit;
}

// Cập nhật
if (isset($_POST['update'])) {
    $id = intval($_POST['id'] ?? 0);
    $ten_td = trim($_POST['ten_td'] ?? '');
	$mota_td = trim($_POST['mota_td'] ?? '');
    $ok = $trinhdo->update($id, $ten_td, $mota_td);
    if ($ok) {
        header("Location: ../trinh-do.php?status=success&msg=" . urlencode("Cập nhật trình độ thành công."));
    } else {
        header("Location: ../trinh-do.php?status=fail&msg=" . urlencode("Cập nhật trình độ thất bại."));
    }
    exit;
}

// Xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $trinhdo->delete($id);

    if (is_array($result) && isset($result['success'])) {
        if ($result['success']) {
            header("Location: ../trinh-do.php?status=success&msg=" . urlencode("Xóa trình độ thành công."));
        } else {
            if (isset($result['error']) && $result['error'] === 'constraint') {
                header("Location: ../trinh-do.php?status=fail&msg=" . urlencode("Trình độ này đang được sử dụng nên không thể xóa."));
            } else {
                header("Location: ../trinh-do.php?status=fail&msg=" . urlencode("Không thể xóa trình độ."));
            }
        }
    } else {
        // nếu delete trả về boolean cũ
        if ($result === true) {
            header("Location: ../trinh-do.php?status=success&msg=" . urlencode("Xóa trình độ thành công."));
        } else {
            header("Location: ../trinh-do.php?status=fail&msg=" . urlencode("Không thể xóa trình độ."));
        }
    }
    exit;
}
