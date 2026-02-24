<?php
session_start();
include(__DIR__ . '/../connection/config.php');
include(__DIR__ . '/../models/TaiKhoan.php');

$database = new Database();
$conn = $database->getConnection();
$tai_khoan = new TaiKhoan($conn);

// ==========================
// PHÂN NHÁNH CHÍNH
// ==========================
if (isset($_GET['logout'])) {
    // ----- ĐĂNG XUẤT -----
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit;

} elseif (isset($_POST['login'])) {
    // ----- ĐĂNG NHẬP -----
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Gọi model login()
    $result = $tai_khoan->login($email, $password);

    if ($result['success']) {
			$_SESSION['user'] = $result['user'];
			header("Location: ../tong-quan.php");
        exit;
    } else {
        $msg = $result['error'] === 'email'
            ? 'Email không tồn tại.'
            : 'Sai mật khẩu.';
		$status = 'fail';
        header("Location: ../login.php?status=$status&msg=" . urlencode($msg));
        exit;
    }

} else {
    // ----- KHÔNG CÓ YÊU CẦU HỢP LỆ -----
    header("Location: ../login.php");
    exit;
}
?>
