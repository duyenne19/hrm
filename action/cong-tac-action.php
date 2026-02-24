<?php
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/CongTac.php');

$database = new Database();
$conn = $database->getConnection();
$nhom = new CongTac($conn);

// ===================
// Thêm mới
// ===================
if (isset($_POST['add'])) {
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
    $id_nguoitao = $_SESSION['user']['id'];
    $data = [
        'ma_ctac'       => $_POST['ma_ctac'],
        'id_nv'         => $_POST['id_nv'],
        'bdau_ctac'     => $_POST['bdau_ctac'],
        'kthuc_ctac'    => $_POST['kthuc_ctac'],
        'dd_ctac'       => $_POST['dd_ctac'],
        'mucdich_ctac'  => $_POST['mucdich_ctac'],
        'id_nguoitao'   => $id_nguoitao
    ];
	if ($nhom->kiem_tra_ngay($data['id_nv'], $data['bdau_ctac'], $data['kthuc_ctac'], null)) {
		header("Location: ../them-cong-tac.php?status=fail&msg=" . urlencode("Thêm lịch công tác thất bại. Vì ngày công tác đang bị trùng lịch đang có."));
	}else{
		$ok = $nhom->add($data);
		if ($ok) {
			header("Location: ../cong-tac.php?status=success&msg=" . urlencode("Thêm mới nhóm công tác thành công."));
		} else {
			header("Location: ../them-cong-tac.php?status=fail&msg=" . urlencode("Thêm mới nhóm công tác thất bại."));
		}
	}
    exit;
}

// ===================
// Cập nhật
// ===================
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $data = [
        'id_nv'         => $_POST['id_nv'],
        'bdau_ctac'     => $_POST['bdau_ctac'],
        'kthuc_ctac'    => $_POST['kthuc_ctac'],
        'dd_ctac'       => $_POST['dd_ctac'],
        'mucdich_ctac'  => $_POST['mucdich_ctac']
    ];
	if ($nhom->kiem_tra_ngay($data['id_nv'], $data['bdau_ctac'], $data['kthuc_ctac'], $id)) {
		header("Location: ../add-nhom-cong-tac.php?idEdit=$id&status=fail&msg=" . urlencode("Cập nhật lịch công tác thất bại. Vì ngày công tác đang bị trùng lịch đang có."));
	}else{
		$ok = $nhom->update($id, $data);	
		if ($ok) {
			header("Location: ../cong-tac.php?status=success&msg=" . urlencode("Cập nhật lịch công tác thành công."));
		} else {
			header("Location: ../them-cong-tac.php?idEdit=$id&status=fail&msg=" . urlencode("Cập nhật lịch công tác thất bại."));
		}
	}
    exit;
}

// ===================
// Xóa
// ===================
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $result = $nhom->delete($id);

    if ($result['success']) {
        header("Location: ../cong-tac.php?status=success&msg=" . urlencode("Xóa nhóm công tác thành công."));
    } elseif ($result['error'] === 'constraint') {
        header("Location: ../cong-tac.php?status=fail&msg=" . urlencode("Nhóm công tác này đang được sử dụng, không thể xóa."));
    } else {
        header("Location: ../cong-tac.php?status=fail&msg=" . urlencode("Không thể xóa nhóm công tác."));
    }
    exit;
}
?>
