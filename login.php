<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Quản lý nhân sự</title>
    
<link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">

    <link rel="stylesheet" href="assets/vendors/iconly/bold.css">
	
    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon">
	<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
	<link rel="stylesheet" href="assets/css/bootstrap-datepicker.min.css">
    <style>
/* Tổng thể nền */
body {
    margin: 0;
    padding: 0;
    height: 100vh;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #d0f0f5, #f6ffff, #dff7f9);
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Khung ngoài cùng */
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}

/* Khung chính chia 2 nửa */
.login-box {
    display: flex;
    width: 900px;
    height: 520px;
    border: 2px solid rgba(255, 255, 255, 0.7);
    border-radius: 20px;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.1);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* Nửa trái */
.login-left {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: #004d61;
    padding: 40px;
}

.login-left img {
    width: 150px;
    height: 150px;
    margin-bottom: 20px;
    object-fit: cover;
    border-radius: 50%;
    overflow: hidden;
    background-color: white;
}

.login-left h2 {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 10px;
}

/* Phần nội dung bên trái (welcome) */
.login-left .subtitle {
    font-size: 17px;
    color: rgba(0, 0, 0, 0.75);
    margin: 15px 0 25px;
    line-height: 1.6;
    max-width: 300px;
}

/* Câu châm ngôn hoặc lời chào */
.login-left .quote {
    font-style: italic;
    color: rgba(0, 0, 0, 0.6);
    font-size: 15px;
    line-height: 1.6;
    max-width: 320px;
}

.login-left .quote span {
    display: block;
    margin-top: 8px;
    font-weight: 600;
    color: #006978;
    font-style: normal;
}

/* Nửa phải (Form) */
.login-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 50px;
}

.login-right h3 {
    text-align: center;
    font-size: 24px;
    margin-bottom: 30px;
    color: #005b6b;
}

/* Input */
.login-right input {
    width: 100%;
    padding: 12px 14px;
    margin: 10px 0;
    border-radius: 8px;
    border: 1px solid rgba(0, 150, 136, 0.4);
    background: rgba(255, 255, 255, 0.6);
    font-size: 15px;
    outline: none;
    transition: 0.3s;
}

.login-right input:focus {
    border-color: #00acc1;
    box-shadow: 0 0 8px rgba(0, 172, 193, 0.3);
}

/* Nút đăng nhập */
.login-right button {
    width: 40%;
    margin: 25px auto 0 auto;
    display: block;
    padding: 12px;
    font-size: 16px;
    font-weight: 600;
    color: white;
    background: linear-gradient(90deg, #00acc1, #00838f);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}

.login-right button:hover {
    transform: translateY(-2px);
    background: linear-gradient(90deg, #0097a7, #006978);
}

/* Responsive */
@media (max-width: 768px) {
    .login-box {
        flex-direction: column;
        width: 90%;
        height: auto;
    }

    .login-left, .login-right {
        flex: none;
        width: 100%;
        padding: 30px 20px;
    }
}
.copyright {
    text-align: center;
    margin-top: 30px;
    font-size: 13px;
    color: rgba(0, 0, 0, 0.6);
    font-style: italic;
}

    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-box">
            <!-- Nửa trái -->
            <div class="login-left">
                <img src="./assets/images/logo/logohrm.png" alt="Logo">
                <h2>Welcome to HRM</h2>
                <p class="subtitle">Giải pháp quản lý nhân sự toàn diện cho doanh nghiệp hiện đại.</p>
                <p class="quote">“Tài sản quý giá nhất của doanh nghiệp chính là con người.”</p>
            </div>

            <!-- Nửa phải -->
            <div class="login-right">
                <h3>Đăng nhập hệ thống</h3>
                <form action="action/login-action.php" method="POST">
                    <input type="email" name="email" placeholder="Nhập email" required>
                    <input type="password" name="password" placeholder="Nhập mật khẩu" required>
                    <button type="submit" name="login">Đăng nhập</button>
					<p class="copyright">©2025 - Hệ thống quản lý nhân sự (HRM)</p>
                </form>
            </div>
        </div>
    </div>

	
	<script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendors/apexcharts/apexcharts.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>
    <script src="assets/js/main.js"></script>
	<script src="assets/vendors/simple-datatables/simple-datatables.js"></script>
	<script src="assets/js/sweetalert2.js"></script>
	<script src="assets/js/alert-handler.js"></script>
	<script src="assets/js/validator-tooltip.js"></script>
</body>
</html>
