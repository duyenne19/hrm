<?php
// action/khen-thuong-ky-luat-action.php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/KhenThuongKyLuat.php');

$database = new Database();
$conn = $database->getConnection();
$model = new KhenThuongKyLuat($conn);


// XỬ LÝ THÊM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
	
    $id_nguoitao = $_SESSION['user']['id'] ?? 1;

    // Chuẩn hóa số tiền: nếu UI gửi đã format (ví dụ "1,000,000") -> strip commas
    $so_tien_raw = $_POST['so_tien'] ?? '';
    $so_tien = is_numeric($so_tien_raw) ? $so_tien_raw : str_replace(',', '', $so_tien_raw);

    $data = [
        'ma_ktkl'        => $_POST['ma_ktkl'] ?? '',
        'ten_ktkl'       => trim($_POST['ten_ktkl'] ?? ''),
        'id_nv'          => (int)($_POST['id_nv'] ?? 0),
        'so_tien'        => $so_tien,
        'hinh_thuc'      => trim($_POST['hinh_thuc'] ?? ''),
        'ngayqd'         => $_POST['ngayqd'] ?? null,
        'noidung'        => trim($_POST['noidung'] ?? ''),
        'ck_khenthuong'  => (int)($_POST['ck_khenthuong'] ?? $ck_khenthuong),
        'id_nguoitao'    => $id_nguoitao
    ];

    // Kiểm tra: id_nv bắt buộc và NV phải đang làm việc
    if (empty($data['id_nv'])) {
        header("Location: ../khen-thuong-ky-luat.php?ck_khenthuong={$data['ck_khenthuong']}&status=fail&msg=" . urlencode("Vui lòng chọn nhân viên."));
        exit;
    }

    $result = $model->add($data);
    if ($result['success']) {
        header("Location: ../khen-thuong-ky-luat.php?ck_khenthuong={$data['ck_khenthuong']}&status=success&msg=" . urlencode("Thêm mới thành công."));
    } else {
        $msg = isset($result['error']) ? $result['error'] : 'Thêm mới thất bại.';
        header("Location: ../khen-thuong-ky-luat.php?ck_khenthuong={$data['ck_khenthuong']}&status=fail&msg=" . urlencode($msg));
    }
    exit;
}

// XỬ LÝ CẬP NHẬT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = (int)($_POST['id'] ?? 0);
    $so_tien_raw = $_POST['so_tien'] ?? '';
    $so_tien = is_numeric($so_tien_raw) ? $so_tien_raw : str_replace(',', '', $so_tien_raw);

    $data = [
        'ten_ktkl'       => trim($_POST['ten_ktkl'] ?? ''),
        'id_nv'          => (int)($_POST['id_nv'] ?? 0),
        'so_tien'        => $so_tien,
        'hinh_thuc'      => trim($_POST['hinh_thuc'] ?? ''),
        'ngayqd'         => $_POST['ngayqd'] ?? null,
        'noidung'        => trim($_POST['noidung'] ?? ''),
        'ck_khenthuong'  => (int)($_POST['ck_khenthuong'] ?? $ck_khenthuong)
    ];

    if (empty($data['id_nv'])) {
        header("Location: ../khen-thuong-ky-luat.php?ck_khenthuong={$data['ck_khenthuong']}&status=fail&msg=" . urlencode("Vui lòng chọn nhân viên."));
        exit;
    }

    $ok = $model->update($id, $data);
    if ($ok) {
        header("Location: ../khen-thuong-ky-luat.php?ck_khenthuong={$data['ck_khenthuong']}&status=success&msg=" . urlencode("Cập nhật thành công."));
    } else {
        header("Location: ../khen-thuong-ky-luat.php?ck_khenthuong={$data['ck_khenthuong']}&idEdit={$id}&status=fail&msg=" . urlencode("Cập nhật thất bại."));
    }
    exit;
}

// XỬ LÝ XÓA
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
	$ck_khenthuong = (int)$_GET['ck_khenthuong'];
    $result = $model->delete($id);
	
    if ($result['success']) {
        header("Location: ../khen-thuong-ky-luat.php?ck_khenthuong={$ck_khenthuong}&status=success&msg=" . urlencode("Xóa thành công."));
    } elseif (isset($result['error']) && $result['error'] === 'constraint') {
        header("Location: ../khen-thuong-ky-luat.php?ck_khenthuong={$ck_khenthuong}&status=fail&msg=" . urlencode("Dữ liệu đang được sử dụng, không thể xóa."));
    } else {
        header("Location: ../khen-thuong-ky-luat.php?ck_khenthuong={$ck_khenthuong}&status=fail&msg=" . urlencode("Xóa thất bại."));
    }
    exit;
}

?>
