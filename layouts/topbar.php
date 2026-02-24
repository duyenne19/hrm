
            <header class="mb-3 header-top">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="#" class="burger-btn d-block d-xl-none me-3">
                        <i class="bi bi-justify fs-3"></i>
								</a>
								<h5 class="mb-0 me-auto"></h5>
								
								<div class="dropdown">
									<a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
						   id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">

							<!-- Avatar -->
							<?php 
								$avatarTopbar = isset($_SESSION['user']['hinhanh']) && $_SESSION['user']['hinhanh'] != '' 
									? 'uploads/anh/' . $_SESSION['user']['hinhanh'] . '?v=' . time() 
									: 'assets/images/avatar.png';
								?>
								<img src="<?= htmlspecialchars($avatarTopbar) ?>" 
									 alt="Avatar" width="36" height="36" 
									 class="rounded-circle border border-2 border-primary shadow-sm me-2">

							<!-- Full name -->
							<span class="topbar-name shimmer-text">
								<i class="bi bi-star-fill me-1 text-warning"></i>
								<?= htmlspecialchars($_SESSION['user']['ho'] . ' ' . $_SESSION['user']['ten']); ?>
							</span>
							<i class="bi bi-caret-down-fill ms-1 small text-secondary"></i>
						</a>
						<!-- Dropdown Menu -->
						<ul class="dropdown-menu dropdown-menu-end shadow-lg animate__animated animate__fadeIn" 
							aria-labelledby="dropdownUser">
							<li class="text-center p-4 border-bottom bg-light" style="min-width: 330px;">
								<img src="<?= htmlspecialchars($avatarTopbar) ?>" 
								 alt="Avatar" width="110" height="110" 
								 class="rounded-circle border border-3 border-primary shadow-sm mb-2">
								<span class="topbar-name shimmer-text d-block mb-1"><?= htmlspecialchars($_SESSION['user']['ho'] . ' ' . $_SESSION['user']['ten']); ?></span>
								<small class="text-muted d-block mb-1"><?php echo htmlspecialchars($_SESSION['user']['email']); ?></small>
								<span class="badge bg-info text-dark"><?php echo htmlspecialchars(getTen_Quyen($_SESSION['user']['quyen'])); ?></span>
							</li>
							<li><a class="dropdown-item py-2" href="tai-khoan-ca-nhan.php"><i class="bi bi-person me-2 text-primary"></i>Thông tin cá nhân</a></li>
							<li><hr class="dropdown-divider"></li>
							<li><a class="dropdown-item text-danger py-2" href="action/login-action.php?logout=1">
								<i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
							</a></li>
						</ul>
                    </div>
                </div>
            </header>
            