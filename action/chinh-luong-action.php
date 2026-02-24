<?php
// Tải các file cần thiết
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/ChinhLuong.php');
include(__DIR__ . '/../models/NhanVien.php'); // Cần để lấy danh sách nhân viên cho dropdown

// Khởi tạo đối tượng CSDL và Model
$database = new Database();
$conn = $database->getConnection();
$chinhluong = new ChinhLuong($conn);

// ******* XỬ LÝ POST (Thêm mới/Cập nhật) *******
if (isset($_POST['add']) || isset($_POST['update'])) {
    session_start();
    
    // Nhận dữ liệu chung
    $id_nv = $_POST['id_nv'];
    $he_so_cu = $_POST['he_so_cu'];
    $he_so_moi = $_POST['he_so_moi'];
    $so_quyet_dinh = $_POST['so_quyet_dinh'];
    $ngay_ky_ket = $_POST['ngay_ky_ket'];
    $ngay_hieu_luc = $_POST['ngay_hieu_luc'];
    
    if (isset($_POST['add'])) {
        // Thêm mới
        $ma_cl = 'MCL' . time();
        $id_nguoitao = $_SESSION['user']['id'];
        
        $ok = $chinhluong->add($ma_cl, $id_nv, $he_so_cu, $he_so_moi, $so_quyet_dinh, $ngay_ky_ket, $ngay_hieu_luc, $id_nguoitao);
        
        if ($ok) {
            header("Location: ../chinh-luong.php?status=success&msg=" . urlencode("Thêm mới Quyết định chỉnh lương thành công."));
        } else {
            header("Location: ../chinh-luong.php?status=fail&msg=" . urlencode("Thêm mới Quyết định chỉnh lương thất bại."));
        }
        exit;
        
    } elseif (isset($_POST['update'])) {
        // Cập nhật
        $id_cl = $_POST['id'];
        
        $ok = $chinhluong->update($id_cl, $he_so_cu, $he_so_moi, $so_quyet_dinh, $ngay_ky_ket, $ngay_hieu_luc);
        
        if ($ok) {
            header("Location: ../chinh-luong.php?status=success&msg=" . urlencode("Cập nhật Quyết định chỉnh lương thành công."));
        } else {
            header("Location: ../chinh-luong.php?status=fail&msg=" . urlencode("Cập nhật Quyết định chỉnh lương thất bại."));
        }
        exit;
    }
}

// ******* XỬ LÝ GET (Xóa) *******
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $chinhluong->delete($id);

    if ($result['success']) {
        header("Location: ../chinh-luong.php?status=success&msg=" . urlencode("Xóa Quyết định chỉnh lương thành công."));
    } else {
        header("Location: ../chinh-luong.php?status=fail&msg=" . urlencode("Không thể xóa Quyết định chỉnh lương."));
    }
    exit;
}
?>