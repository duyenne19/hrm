<?php
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/LoaiNhanVien.php');

if (session_status() === PHP_SESSION_NONE) session_start();

$database = new Database();
$conn = $database->getConnection();
$loaiNV = new LoaiNhanVien($conn);

// Thêm mới
if (isset($_POST['add'])) {
    $ma_lnv = $_POST['ma_lnv'];
    $ten_lnv = trim($_POST['ten_lnv']);
    $mota = $_POST['mota'];
    $id_nguoitao = $_SESSION['user']['id'] ?? 0;

    if ($loaiNV->existsByName($ten_lnv)) {
        header("Location: ../loai-nhanvien.php?status=fail&msg=" . urlencode("Tên loại nhân viên đã tồn tại."));
        exit;
    }

    $ok = $loaiNV->add($ma_lnv, $ten_lnv, $mota, $id_nguoitao);
    header("Location: ../loai-nhanvien.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Thêm mới thành công!" : "Không thể thêm mới."));
    exit;
}

// Cập nhật
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $ten_lnv = trim($_POST['ten_lnv']);
    $mota = $_POST['mota'];
    $ok = $loaiNV->update($id, $ten_lnv, $mota);
    header("Location: ../loai-nhanvien.php?status=" . ($ok ? 'success' : 'fail') . "&msg=" . urlencode($ok ? "Cập nhật thành công!" : "Cập nhật thất bại."));
    exit;
}

// Xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $loaiNV->delete($id);

    if ($result['success']) {
        header("Location: ../loai-nhanvien.php?status=success&msg=" . urlencode("Xóa loại nhân viên thành công!"));
    } else {
        $msg = $result['error'] === 'constraint'
            ? "Loại nhân viên đang được sử dụng, không thể xóa."
            : "Không thể xóa loại nhân viên.";
        header("Location: ../loai-nhanvien.php?status=fail&msg=" . urlencode($msg));
    }
    exit;
}
?>
