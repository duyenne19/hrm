<!-- header ======== -->
<?php
	session_start();
	if(!isset($_SESSION['user']))
	{
		header('Location: login.php');
		exit();
	}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhân sự</title>

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
    /* ================================================= */
    /* KHẮC PHỤC LỖI TRIỆT ĐỂ BẰNG !important VÀ 300PX */
    /* ================================================= */

    /* 1. Sidebar: Giữ cố định và Z-index cao nhất (300px theo app.css) */
    #sidebar {
        width: 300px !important; 
        z-index: 9999 !important; 
    }
    .sidebar-wrapper {
         z-index: 9999 !important; 
    }

    /* 2. KHỐI MỚI #main2: Vị trí mặc định cho màn hình lớn */
    #main2 {
        /* VỊ TRÍ CHÍNH: Đẩy #main2 ra khỏi Sidebar (300px) */
        margin-left: 300px !important; 
        transition: margin-left 0.3s;
        
        /* Đảm bảo chiều cao tối thiểu và layout Flex cho nội dung */
        display: flex;
        flex-direction: column;
        min-height: 100vh !important;
        
        /* Đảm bảo #main2 không có padding ngoài */
        padding: 0 !important; 
        width: calc(100% - 300px);
    }

    /* 3. Original #main: Giữ nguyên flex-grow và bỏ mọi margin/padding */
    #main {
        flex-grow: 1;
        padding: 0 !important; 
        margin: 0 !important;
        display: block; 
    }
    
    /* 4. Đảm bảo Topbar nằm dưới Sidebar */
    .header-top {
        padding: 1rem 1.5rem;
        background-color: #ffffff;
        border-bottom: 1px solid #e9e9e9;
        position: sticky;
        top: 0;
        z-index: 1000 !important; 
    }
    
    .content-wrapper {
        flex-grow: 1;
        padding: 2rem 1.5rem; 
    }

    footer {
        margin-top: auto; 
        padding: 1rem 1.5rem;
        background-color: #ffffff; 
        border-top: 1px solid #f0f0f0;
    }
    
    .mb-3 {
        margin-bottom: 0 !important;
    }

    /* ================================================= */
    /* MEDIA QUERY: KHẮC PHỤC LỖI KHI MÀN HÌNH NHỎ (<1200px) */
    /* ================================================= */
    @media screen and (max-width: 1199px) {
        /* Khi Sidebar ẩn, ép #main2 không còn margin-left */
        #main2 {
            margin-left: 0 !important;
            width: 100% !important; /* Đảm bảo nó chiếm toàn bộ chiều rộng */
        }
        
        /* Sidebar bị ẩn mặc định, nhưng nếu nó có class 'active' (đã mở) thì nó hiện ra.
           CSS này đảm bảo không gian không bị chiếm khi nó ẩn. */
    }
	
/* Avatar style */
.avatar-glow {
    object-fit: cover;
    object-position: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}
.avatar-glow:hover {
    box-shadow: 0 0 15px rgba(59,130,246,0.6);
    transform: scale(1.08);
}

/* Dropdown animation */
@keyframes dropdownFade {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
/* Hover effect cho tiêu đề */
.hover-glow:hover {
    text-shadow: 0 0 10px rgba(59,130,246,0.4);
    transition: 0.3s ease;
}

/* Gradient tên admin */
.topbar-name {
    font-weight: 700;
    font-size: 1rem;
    background: linear-gradient(90deg, #facc15, #fb923c, #ef4444, #9333EA, #3B82F6);
    background-size: 300% 300%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: colorShift 5s linear infinite;
    text-shadow: 0 0 8px rgba(249,115,22,0.3);
}

/* Hiệu ứng gradient chạy liên tục */
@keyframes colorShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Hiệu ứng ánh sáng quét qua chữ */
.shimmer-text {
    position: relative;
    overflow: hidden;
}
.shimmer-text::after {
    content: "";
    position: absolute;
    top: 0; left: -75%;
    width: 50%; height: 100%;
    background: linear-gradient(120deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.6) 50%, rgba(255,255,255,0) 100%);
    animation: shimmerMove 4s infinite;
}
@keyframes shimmerMove {
    0% { left: -75%; }
    100% { left: 125%; }
}
</style>
</head>

<body>
    <div id="app">
	<!-- End header --------- -->
	<!-- Đến Menu --------- -->
	<?php include_once('./layouts/menu.php'); ?>
	<!-- Hết Menu -->
	<!-- Bat đầu Main -->
	<div id="main2">
		<!-- Bắt đầu Topbar -->
		<?php include_once('./layouts/topbar.php'); ?>
		<!-- Hết Topbar -->
		<!-- Bắt đầu conten -->
			<div id="main">
                <!-- Bắt đầu conten -->
				<!-- Hết conten -->
  
	<?php include_once('./layouts/footer.php'); ?>