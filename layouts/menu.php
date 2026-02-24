
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header d-flex justify-content-center">
    <div class="logo">
        <a href="tong-quan.php">
            <img src="./assets/images/logo/logohrm.png" alt="Logo">
        </a>
    </div>
</div>

        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>
			
                <!-- 1. Tổng quan -->
                <li class="sidebar-item">
                    <a href="tong-quan.php" class='sidebar-link'>
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
					
						<?php if(!$ke_toan){ ?>
						<!-- 2.1. Thêm nhân viên -->
						<li class="sidebar-item no-submenu">
							<a href="them-nhan-vien.php" class='sidebar-link'>
								<i class="bi bi-person-plus"></i>
								<span>Thêm nhân viên</span>
							</a>
						</li>
						<?php }?>
						<!-- 2.2. Danh sách nhân viên -->
						<li class="sidebar-item no-submenu">
							<a href="ds-nhan-vien.php" class='sidebar-link'>
								<i class="bi bi-list-ul"></i>
								<span>Danh sách nhân viên</span>
							</a>
						</li>
                        <!-- 2.3. Nhóm nhân viên -->
						<?php if(!$ke_toan){ ?>
						<li class="sidebar-item no-submenu">
							<a href="nhom-nhan-vien.php" class='sidebar-link'>
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
									<a href="them-cong-tac.php" ><i class="bi bi-plus-circle"></i> Tạo công tác</a>
								</li>
								<li class="submenu-item">
									<a href="cong-tac.php"><i class="bi bi-list-check"></i> Danh sách công tác</a>
								</li>
							</ul>
						</li>
				<?php }?>
                    </ul>
                </li>
				
				
                        
                <!-- 3. Quản lý lương -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-cash-stack"></i>
                        <span><b>Quản lý lương</b></span>
                    </a>
                    <ul class="submenu <?= $is_luong_active ? 'active' : ''; ?>">
					
						<?php if(!$hr){ ?>
							<li class="submenu-item"><a href="luong.php"><i class="bi bi-calculator-fill"></i> Tính lương</a></li>
						<?php }?>
						
                        <?php if(!$ke_toan){ ?>
							<li class="submenu-item"><a href="chinh-luong.php"><i class="bi bi-receipt"></i> Quản lý chỉnh lương</a></li>
						<?php }?>
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
				<?php 
				if($admin){ ?>
				<!-- 5 Thiết lập nhân sự -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-gear-fill"></i>
                        <span><b>Thiết lập nhân sự</b></span>
                    </a>					
							<ul class="submenu <?= $is_thiet_lap_ns_active ? 'active' : ''; ?>">
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
						
                <!-- 6. Tài khoản -->
				
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-person-circle"></i>
                        <span><b>Tài khoản</b></span>
                    </a>
                    <ul class="submenu <?= $is_taikhoan_active ? 'active' : ''; ?>">
                        <li class="submenu-item"><a href="them-tai-khoan.php"><i class="bi bi-person-plus-fill"></i> Tạo tài khoản</a></li>
                        <li class="submenu-item"><a href="ds-tai-khoan.php"><i class="bi bi-person-lines-fill"></i> Danh sách tài khoản</a></li>
                    </ul>
                </li>
				<?php }?>
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