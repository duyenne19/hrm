<?php
	//echo 'sao khong hien ra gi';
	//echo password_hash('123456', PASSWORD_DEFAULT);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Quản lý nhân sự</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/css/pages/auth.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
<div id="auth" class="d-flex justify-content-center align-items-center vh-100">
    <div class="col-lg-4 col-md-6 col-10">
        <div id="auth-left" class="p-4 shadow rounded-4 bg-white">
            <div class="text-center mb-4">
                <img src="assets/images/logo/logo.png" alt="Logo" style="height: 60px;">
                <h2 class="auth-title mt-3">Đăng nhập hệ thống</h2>
                <p class="auth-subtitle text-muted">Quản lý nhân sự chuyên nghiệp</p>
            </div>

            <form method="POST" action="action/login-action.php" id="formLogin" class="form">
                <div class="form-group position-relative has-icon-left mb-4">
                    <input type="email" name="email" class="form-control form-control-xl" placeholder="Nhập email" required>
                    <div class="form-control-icon"><i class="bi bi-person"></i></div>
                </div>
                <div class="form-group position-relative has-icon-left mb-4">
                    <input type="password" name="password" class="form-control form-control-xl" placeholder="Nhập mật khẩu" required>
                    <div class="form-control-icon"><i class="bi bi-shield-lock"></i></div>
                </div>
                <button type="submit" name="login" class="btn btn-primary btn-block btn-lg shadow-lg mt-4 w-100">Đăng nhập</button>
            </form>
        </div>
    </div>
</div>

<!-- ✅ SweetAlert hiển thị thông báo -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get("error");
    const msg = urlParams.get("msg");

    if (error) {
        Swal.fire({
            icon: 'error',
            title: 'Đăng nhập thất bại',
            text: decodeURIComponent(error),
            confirmButtonText: 'Thử lại'
        });
    }
    if (msg) {
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: decodeURIComponent(msg),
            showConfirmButton: false,
            timer: 1500
        });
    }
});
</script>
</body>
</html>
