<?php
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/ChiTietNhom.php');

$database = new Database();
$conn = $database->getConnection();
$model = new ChiTietNhom($conn);

session_start();
$id_nguoitao = $_SESSION['user']['id'] ?? 1;

// 🔹 Thêm nhân viên vào nhóm
if (isset($_POST['addMember'])) {
    $id_nhom = (int)$_POST['id_nhom'];
    $id_nv = (int)$_POST['id_nv'];

    $ok = $model->addMember($id_nhom, $id_nv, $id_nguoitao);
    if ($ok) {
        header("Location: ../ds-nhom-nhan-vien.php?id=$id_nhom&status=success&msg=" . urlencode("Thêm nhân viên vào nhóm thành công."));
    } else {
        header("Location: ../ds-nhom-nhan-vien.php?id=$id_nhom&status=fail&msg=" . urlencode("Không thể thêm nhân viên vào nhóm."));
    }
    exit;
}

// 🔹 Xóa nhân viên khỏi nhóm
if (isset($_GET['delete'])) {
    $id_nhom = (int)$_GET['id_nhom'];
    $id_nv = (int)$_GET['id_nv'];

    $ok = $model->deleteMember($id_nhom, $id_nv);
    if ($ok) {
        header("Location: ../ds-nhom-nhan-vien.php?id=$id_nhom&status=success&msg=" . urlencode("Đã xóa nhân viên khỏi nhóm."));
    } else {
        header("Location: ../ds-nhom-nhan-vien.php?id=$id_nhom&status=fail&msg=" . urlencode("Không thể xóa nhân viên khỏi nhóm."));
    }
    exit;
}
?>
