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
</style>
</head>

<body>
    <div id="app">
	<!-- End header --------- -->
	<!-- Đến Menu --------- -->
		<?php 
				// Khối PHP để xác định trang hiện tại và kiểm tra trạng thái active

				$current_page = basename($_SERVER['PHP_SELF']); 

				// Hàm kiểm tra và in ra class 'active' cho các thẻ <li> con
				function is_active_page($page) {
					global $current_page;
					return $current_page == $page ? 'active' : '';
				}

				// 1. Kiểm tra trang con thuộc "Thiết lập nhân sự"
				$thiet_lap_ns_subpages = [
					'phong-ban.php', 'chuc-vu.php', 'trinh-do.php', 'chuyen-mon.php', 
					'loai-nhanvien.php', 'quoc-tich.php', 'ton-giao.php', 'dan-toc.php', 'hon-nhan.php'
				];
				$is_thiet_lap_ns_active = in_array($current_page, $thiet_lap_ns_subpages);


				// 2. Kiểm tra trang con thuộc "Công tác"
				$cong_tac_subpages = ['add-nhom-cong-tac.php', 'nhom-cong-tac.php'];
				$is_cong_tac_active = in_array($current_page, $cong_tac_subpages);


				// 3. Danh sách các trang con thuộc mục cha "Nhân viên" (bao gồm cả các cấp con)
				$nhanvien_subpages = array_merge(
					['add-nhanvien.php', 'ds-nhan-vien.php', 'nhom-vn.php','xem-nhan-vien.php'], 
					$cong_tac_subpages, 
					$thiet_lap_ns_subpages
				);
				$is_nhanvien_active = in_array($current_page, $nhanvien_subpages);


				// 4. Các mục cha khác
				$luong_subpages = ['luong.php', 'chinh-luong.php'];
				$is_luong_active = in_array($current_page, $luong_subpages);

				$is_khenthuong_parent_active = $current_page == 'khen-thuong-ky-luat.php';

				$taikhoan_subpages = ['add-tai-khoan.php', 'ds-tai-khoan.php','tai-khoan.php'];
				$is_taikhoan_active = in_array($current_page, $taikhoan_subpages);
				?>

				<div id="sidebar" class="active">
					<div class="sidebar-wrapper active">
						<div class="sidebar-header d-flex justify-content-center">
					<div class="logo">
						<a href="trangchu.php">
							<img src="./assets/images/logo/logohrm.png" alt="Logo">
						</a>
					</div>
				</div>

						<div class="sidebar-menu">
							<ul class="menu">
								<li class="sidebar-title">Menu</li>

								<!-- 1. Tổng quan -->
								<li class="sidebar-item">
									<a href="#" class='sidebar-link'>
										<i class="bi bi-speedometer2"></i>
										<span><b>Tổng quan</b></span>
									</a>
								</li>

								<!-- 2. Nhân viên -->
								<li class="sidebar-item has-sub ">
									<a href="#" class='sidebar-link'>
										<i class="bi bi-people-fill"></i>
										<span><b>Nhân viên</b></span>
									</a>
									<ul class="submenu <?= $is_nhanvien_active ? 'active' : ''; ?>">
									
										
										<!-- 2.1. Thêm nhân viên -->
										<li class="sidebar-item no-submenu">
											<a href="add-nhanvien.php" class='sidebar-link'>
												<i class="bi bi-person-plus"></i>
												<span>Thêm nhân viên</span>
											</a>
										</li>
										<!-- 2.2. Danh sách nhân viên -->
										<li class="sidebar-item no-submenu">
											<a href="ds-nhan-vien.php" class='sidebar-link'>
												<i class="bi bi-list-ul"></i>
												<span>Danh sách nhân viên</span>
											</a>
										</li>
										<!-- 2.3. Nhóm nhân viên -->
										<li class="sidebar-item no-submenu">
											<a href="nhom-vn.php" class='sidebar-link'>
												<i class="bi bi-people"></i>
												<span>Nhóm nhân viên</span>
											</a>
										</li> 
										

										<!-- 2.4 Công tác -->
										
										<li class="sidebar-item has-sub">
											<a href="#" class="sidebar-link">
												<i class="bi bi-briefcase-fill"></i>
												<span>Công tác</span>
											</a>
											<ul class="submenu <?= $is_cong_tac_active ? 'active' : ''; ?>">
												<li class="submenu-item">
													<a href="add-nhom-cong-tac.php"><i class="bi bi-plus-circle"></i> Tạo công tác</a>
												</li>
												<li class="submenu-item">
													<a href="nhom-cong-tac.php"><i class="bi bi-list-check"></i> Danh sách công tác</a>
												</li>
											</ul>
										</li>

										<!-- 2.5 Thiết lập nhân sự -->
									   
										<li class="sidebar-item has-sub">
											<a href="#" class="sidebar-link">
												<i class="bi bi-gear-fill"></i>
												<span>Thiết lập nhân sự</span>
											</a>
											<ul class="submenu <?= $thiet_lap_ns_subpages ? 'active' : ''; ?>">
												<li class="submenu-item"><a href="phong-ban.php"><i class="bi bi-building"></i> Phòng ban</a></li>
												<li class="submenu-item"><a href="chuc-vu.php"><i class="bi bi-award"></i> Chức vụ</a></li>
												<li class="submenu-item"><a href="trinh-do.php"><i class="bi bi-journal-bookmark-fill"></i> Trình độ</a></li>
												<li class="submenu-item"><a href="chuyen-mon.php"><i class="bi bi-book"></i> Chuyên môn</a></li>
												<li class="submenu-item"><a href="loai-nhanvien.php"><i class="bi bi-person-badge-fill"></i> Loại nhân viên</a></li>
												<li class="submenu-item"><a href="quoc-tich.php"><i class="bi bi-flag-fill"></i> Quốc tịch</a></li>
												<li class="submenu-item"><a href="ton-giao.php"><i class="bi bi-journal"></i> Tôn giáo</a></li>
												<li class="submenu-item"><a href="dan-toc.php"><i class="bi bi-people"></i> Dân tộc</a></li>
												<li class="submenu-item"><a href="hon-nhan.php"><i class="bi bi-heart-fill"></i> Hôn nhân</a></li>
											</ul>
										</li>
										</li>
									</ul>
								</li>

								<!-- 3. Quản lý lương -->
								<li class="sidebar-item has-sub">
									<a href="#" class='sidebar-link'>
										<i class="bi bi-cash-stack"></i>
										<span><b>Quản lý lương</b></span>
									</a>
									<ul class="submenu <?= $is_luong_active ? 'active' : ''; ?>">
										<li class="submenu-item"><a href="luong.php"><i class="bi bi-calculator-fill"></i> Tính lương</a></li>
										
										<li class="submenu-item"><a href="chinh-luong.php"><i class="bi bi-receipt"></i> Quản lý chỉnh lương</a></li>
									</ul>
								</li>

								<!-- 4. Khen thưởng – Kỷ luật -->
								<li class="sidebar-item has-sub">
									<a href="#" class='sidebar-link'>
										<i class="bi bi-trophy-fill"></i>
										<span><b>Khen thưởng–Kỷ luật</b></span>
									</a>
									<ul class="submenu <?= $is_khenthuong_parent_active ? 'active' : ''; ?>">
										<li class="submenu-item"><a href="khen-thuong-ky-luat.php?ck_khenthuong=1"><i class="bi bi-star-fill"></i> Khen thưởng</a></li>
										<li class="submenu-item"><a href="khen-thuong-ky-luat.php?ck_khenthuong=0"><i class="bi bi-exclamation-triangle-fill"></i> Kỷ luật</a></li>
									</ul>
								</li>

								<!-- 5. Tài khoản -->
								<li class="sidebar-item has-sub">
									<a href="#" class='sidebar-link'>
										<i class="bi bi-person-circle"></i>
										<span><b>Tài khoản</b></span>
									</a>
									<ul class="submenu <?= $is_taikhoan_active ? 'active' : ''; ?>">
										<li class="submenu-item"><a href="add-tai-khoan.php"><i class="bi bi-person-plus-fill"></i> Tạo tài khoản</a></li>
										<li class="submenu-item"><a href="ds-tai-khoan.php"><i class="bi bi-person-lines-fill"></i> Danh sách tài khoản</a></li>
									</ul>
								</li>

							</ul>
						</div>
						<button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
					</div>
				</div>

				<style>
				/* Sidebar cơ bản */
				#sidebar {
					background: #1b1b2f;
					color: #fff;
					width: 250px;
					transition: 0.3s;
				}
				.sidebar-wrapper .sidebar-link {
					display: flex;
					align-items: center;
					padding: 10px 20px;
					color: #fff;
					font-weight: 500;
					transition: 0.3s;
				}
				.sidebar-wrapper .sidebar-link i {
					margin-right: 10px;
				}
				.sidebar-wrapper .sidebar-item:hover > .sidebar-link {
					background: transparent;
					border-radius: 8px;
				}
				.sidebar-wrapper .submenu {
					display: block; /* Luôn mở submenu */
					padding-left: 20px;
				}
				.submenu-item a {
					padding: 8px 20px;
					display: block;
					color: #cfd2f0;
					transition: 0.3s;
				}
				.submenu-item a:hover {
					background: transparent; /* không đổi nền */
					border-radius: 6px;
					color: #fff;
				}
				.has-sub > a::after {
					content: "\f107";
					font-family: "FontAwesome";
					float: right;
					transition: 0.3s;
				}
				.submenu .has-sub > a::after {
					transform: rotate(90deg);
				}
				/* Ẩn mũi tên cho các mục con trong submenu */
				.sidebar-wrapper .submenu .sidebar-item.no-submenu > .sidebar-link::after {
					display: none !important;
					content: none !important;
				}

				.sidebar-header .logo img {
					width: 120px;       /* tăng kích thước ngang */
					height: 120px;      /* tăng kích thước dọc */
					object-fit: cover;  /* giữ tỉ lệ và cắt vừa khung */
					border-radius: 50%; /* hình tròn */
					display: block;
				}

				</style>
	<!-- Hết Menu -->
	<!-- Bat đầu Main -->
	<div id="main2">
		<!-- Bắt đầu Topbar -->
		
            <header class="mb-3 header-top">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="#" class="burger-btn d-block d-xl-none me-3">
                        <i class="bi bi-justify fs-3"></i>
                    </a>
                    <h5 class="mb-0 me-auto">Trang Chủ / Dashboard</h5>
                    
                    <div class="dropdown">
                        <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="dropdown-toggle">
                            <div class="user-menu d-flex align-items-center">
                                <div class="user-img d-flex align-items-center">
                                    <div class="avatar avatar-md">
                                        <img src="assets/images/faces/1.jpg" alt="User Avatar">
                                    </div>
                                    <div class="d-none d-md-block ms-3">
                                        <h6 class="font-bold mb-0">John Doe</h6>
                                        <p class="text-xs mb-0 text-muted">Administrator</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" style="min-width: 11rem;">
                            <li><h6 class="dropdown-header">Xin chào, John!</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-person me-2"></i> Hồ sơ</a></li>
                            <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-gear me-2"></i> Cài đặt</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-box-arrow-left me-2"></i> Đăng xuất</a></li>
                        </ul>
                    </div>
                </div>
            </header>
            
		<!-- Hết Topbar -->
		<!-- Bắt đầu conten -->
			<div id="main">
                <div class="content-wrapper">
                    <div class="page-heading">
                        <h3>Nội dung Trang</h3>
                    </div>
                    <div class="page-content">
                        <section class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Nội dung mẫu</h4>
                                    </div>
                                    <div class="card-body">
                                        Đây là khu vực hiển thị nội dung chính của trang.
                                        <p>Thêm nội dung giả để kiểm tra Footer...</p>
                                        <p>Thêm nội dung giả để kiểm tra Footer...</p>
                                        <p>Thêm nội dung giả để kiểm tra Footer...</p>
                                        <p>Thêm nội dung giả để kiểm tra Footer...</p>
                                        <p>Thêm nội dung giả để kiểm tra Footer...</p>
                                        <p>Thêm nội dung giả để kiểm tra Footer...</p>
                                        <p>Thêm nội dung giả để kiểm tra Footer...</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
		<!-- Kết thúc conten -->
		<!-- Footer đoạn này -->
			<footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2025 &copy; Quản Lý Nhân Sự</p>
                    </div>
                    <div class="float-end">
                        <p>Phát triển bởi <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span> by <a href="#">Your Name</a></p>
                    </div>
                </div>
            </footer>
		<!-- Kết thúc footeer -->
	</div>
        </div>
    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <script src="assets/vendors/apexcharts/apexcharts.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>

    <script src="assets/js/main.js"></script>
</body>

</html>
	