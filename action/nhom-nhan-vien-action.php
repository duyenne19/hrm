<?php

include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/NhomNV.php');

$database = new Database();
$conn = $database->getConnection();
$nhomnv = new NhomNV($conn);





// Xử lý thêm
if (isset($_POST['add'])) {
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
    $data = [
        'manhom' => $_POST['manhom'],
        'tennhom' => trim($_POST['tennhom']),
        'mota' => trim($_POST['mota']),
        'id_nguoitao' => $_SESSION['user']['id'] ?? 1
    ];

    if ($nhomnv->existsByName($data['tennhom'])) {
        header("Location: ../nhom-nhan-vien.php?status=fail&msg=Tên nhóm đã tồn tại!");
        exit;
    }

    $result = $nhomnv->add($data);
    $status = ($result) ? 'success' : 'fail';
    $msg = ($result) ? 'Thêm nhóm thành công!' : 'Không thể thêm nhóm.';
    header("Location: ../nhom-nhan-vien.php?status=$status&msg=" . urlencode($msg));
    exit;
}

// Cập nhật
if (isset($_POST['update'])) {
    $data = [
        'id' => $_POST['id'],
        'tennhom' => trim($_POST['tennhom']),
        'mota' => trim($_POST['mota'])
    ];

    if ($nhomnv->existsByName($data['tennhom'], $data['id'])) {
        header("Location: ../nhom-nhan-vien.php?idEdit={$data['id']}&status=fail&msg=Tên nhóm đã tồn tại!");
        exit;
    }

    $ok = $nhomnv->update($data);
    header("Location: ../nhom-nhan-vien.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? 'Cập nhật thành công!' : 'Cập nhật thất bại!'));
    exit;
}

// Xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $nhomnv->delete($id);
    if ($result['success']) {
        header("Location: ../nhom-nhan-vien.php?status=success&msg=" . urlencode('Xóa nhóm thành công!'));
    } else {
        $msg = ($result['error'] === 'constraint') ? 'Nhóm này đang được sử dụng, không thể xóa.' : 'Xóa nhóm thất bại.';
        header("Location: ../nhom-nhan-vien.php?status=fail&msg=" . urlencode($msg));
    }
    exit;
}
?>
