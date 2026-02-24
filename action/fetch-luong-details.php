<?php
// File: action/fetch-luong-details.php (Đã cập nhật tên hàm và sửa lỗi tên trường)

header('Content-Type: application/json');

// Khuyên dùng: Tắt hiển thị lỗi NOTICE/WARNING nếu môi trường đã sẵn sàng
error_reporting(E_ERROR | E_PARSE);

// Kiểm tra và khởi tạo session nếu cần
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Bao gồm các file cần thiết (Đường dẫn giả định là đúng theo cấu trúc của bạn)
include_once __DIR__ . '/../connection/config.php';
include_once __DIR__ . '/../models/Luong.php'; 

$response = [
    'success' => false,
    'message' => 'Lỗi không xác định.',
    'data' => null,
    'nhanvien_info' => null
];

// 2. Lấy ID lương từ request
$id_luong = $_GET['id'] ?? null;

if (empty($id_luong) || !is_numeric($id_luong)) {
    $response['message'] = 'ID bảng lương không hợp lệ.';
    echo json_encode($response);
    exit;
}

try {
    // 3. Khởi tạo Database và Model
    $database = new Database();
    $conn = $database->getConnection();
    $luong_model = new Luong($conn);

    // 4. GỌI HÀM VỚI TÊN MỚI
    $data = $luong_model->xemLuongNhanVien(intval($id_luong));
    
    if ($data) {
        // ... (Định dạng dữ liệu) ...
        // Định dạng lại dữ liệu cho hiển thị trong JS
		$data['ky_luong_display'] = date('m/Y', strtotime($data['ky_luong']));
		$data['ngaytao_display'] = date('d/m/Y', strtotime($data['ngaytao']));
		$data['ngaysua_display'] = date('d/m/Y', strtotime($data['ngaysua']));
		$data['anh_url'] = !empty($data['anhdaidien']) ? 
		'uploads/nhanvien/' . $data['anhdaidien'] : 
		'assets/images/default-avatar.png';
        $response['success'] = true;
        $response['message'] = 'Tải dữ liệu thành công.';
        
        // Tách thông tin Nhân viên và Lương ra riêng để dễ xử lý trong JS
        $response['data'] = $data;
        $response['nhanvien_info'] = [
            'hoten' => $data['hoten'] ?? 'N/A',
            'ma_nv' => $data['ma_nv'] ?? 'N/A',
            'phongban' => $data['ten_pb'] ?? 'N/A',
            'chucvu' => $data['ten_cv'] ?? 'N/A',
            'sodt' => $data['sodt'] ?? 'N/A',
            'email' => $data['email'] ?? 'N/A',
            // Đã sửa lỗi tên trường tại đây: $data['gtinh']
            'gioitinh' => $data['gtinh'] ?? 'N/A', 
        ];

    } else {
        $response['message'] = 'Không tìm thấy chi tiết bảng lương.';
    }
} catch (Exception $e) {
    // Thông báo lỗi khi có lỗi kết nối/truy vấn
    // Khuyến nghị: KHÔNG HIỂN THỊ $e->getMessage() trên môi trường production
    $response['message'] = 'Lỗi truy vấn cơ sở dữ liệu: ' . $e->getMessage(); 
}

// 5. Trả về JSON
echo json_encode($response);
exit;

// KHÔNG CÓ KHOẢNG TRẮNG HOẶC KÝ TỰ NÀO SAU DÒNG NÀY