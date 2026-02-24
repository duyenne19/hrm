<?php
// File: action/fetch-chinh-luong-history.php

header('Content-Type: application/json');

// Tải các file cần thiết
// Đảm bảo đường dẫn include này đúng
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/ChinhLuong.php');

$response = ['success' => false, 'data' => null, 'nhanvien_info' => null, 'message' => ''];

if (isset($_GET['id_nv'])) {
    $id_nv = intval($_GET['id_nv']);

    $database = new Database();
    $conn = $database->getConnection();
    $chinhluong = new ChinhLuong($conn);

    if ($id_nv > 0) {
        // 1. Lấy thông tin nhân viên (Phòng ban, Chức vụ)
        $nhanvien_info = $chinhluong->getNhanVienDetails($id_nv);
        
        // 2. Lấy lịch sử chỉnh lương
        $history = $chinhluong->getHistoryByNhanVienId($id_nv);
        
        $response['success'] = true;
        $response['data'] = $history;
        $response['nhanvien_info'] = $nhanvien_info; // Gửi thông tin NV về client
    } else {
        $response['message'] = 'ID Nhân viên không hợp lệ.';
    }
} else {
    $response['message'] = 'Không tìm thấy ID Nhân viên.';
}

echo json_encode($response);
exit;
?>