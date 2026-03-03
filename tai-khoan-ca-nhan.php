<?php
    include('./layouts/header.php');
    require_once __DIR__ . '/connection/config.php';
    require_once __DIR__ . '/models/TaiKhoan.php';
    
    $database = new Database();
    $conn = $database->getConnection();
    $taiKhoan = new TaiKhoan($conn);

    $userId = $_SESSION['user']['id'] ?? null;
    // Nếu không có session, đẩy về login
    if (!$userId) {
        echo "<script>window.location.href='login.php';</script>";
        exit;
    }

    $user = $taiKhoan->getById((int)$userId);
    if (!$user) {
        echo "<script>alert('Không tìm thấy thông tin tài khoản!'); window.location.href='logout.php';</script>";
        exit;
    }

    // Avatar
    $avatar = !empty($user['hinhanh']) 
        ? 'uploads/anh/' . $user['hinhanh'] . '?v=' . time() 
        : 'assets/images/logo/logo-sm.png';
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Thông tin cá nhân</h3>
                <p class="text-subtitle text-muted">Quản lý thông tin tài khoản và mật khẩu.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Hồ sơ cá nhân</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <!-- LEFT: Profile Card -->
            <div class="col-md-4">
                <div class="card shadow border-0 h-100">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="position-relative mb-3">
                            <img src="<?= htmlspecialchars($avatar) ?>" 
                                class="rounded-circle shadow border bg-white" 
                                style="width: 150px; height: 150px; object-fit: cover;">
                            <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-white rounded-circle">
                                <span class="visually-hidden">Online</span>
                            </span>
                        </div>

                        <h4 class="fw-bold mb-1"><?= htmlspecialchars(($user['ho'] ?? '') . ' ' . ($user['ten'] ?? '')) ?></h4>
                        <p class="text-muted mb-3"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($user['email'] ?? '') ?></p>
                        
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                             <?php 
                                $roleName = getTen_Quyen($user['quyen']);
                                $roleColor = 'bg-secondary';
                                if ($roleName == 'Admin') $roleColor = 'bg-danger';
                                elseif ($roleName == 'HR') $roleColor = 'bg-warning text-dark';
                                elseif ($roleName == 'Kế toán') $roleColor = 'bg-info text-dark';
                            ?>
                            <span class="badge <?= $roleColor ?> shadow-sm px-3 py-2 rounded-pill">
                                <?= htmlspecialchars($roleName) ?>
                            </span>

                            <?php if (($user['trangthai'] ?? 0) == 1): ?>
                                <span class="badge bg-light-success text-success shadow-sm px-3 py-2 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i>Hoạt động
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light-danger text-danger shadow-sm px-3 py-2 rounded-pill">
                                    <i class="bi bi-lock me-1"></i>Đã khóa
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid w-100 px-4">
                            <button type="button" class="btn btn-outline-primary mb-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalChangePass">
                                <i class="bi bi-key me-2"></i>Đổi mật khẩu
                            </button>
                        </div>
                         <div class="mt-auto pt-3 text-muted small">
                            Ngày tham gia: <?= isset($user['ngaytao']) ? date('d/m/Y', strtotime($user['ngaytao'])) : 'N/A' ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Edit Form -->
            <div class="col-md-8">
                <div class="card shadow border-0 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa thông tin
                        </h5>
                    </div>
                    <div class="card-body mt-3">
                        <form id="formProfile" method="post" action="action/tai-khoan-action.php" enctype="multipart/form-data" class="validate-tooltip needs-validation" novalidate>
                            <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                            <input type="hidden" name="update-ca-nhan" value="1">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="fw-bold mb-1">Họ</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="ho" class="form-control" value="<?= htmlspecialchars($user['ho'] ?? '') ?>" required>
                                        <div class="invalid-tooltip">Vui lòng nhập họ.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold mb-1">Tên</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="ten" class="form-control" value="<?= htmlspecialchars($user['ten'] ?? '') ?>" required>
                                        <div class="invalid-tooltip">Vui lòng nhập tên.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="fw-bold mb-1">Email (Không thể thay đổi)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope-fill"></i></span>
                                        <input type="email" class="form-control bg-light text-muted" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="fw-bold mb-1">Số điện thoại</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" name="sodt" class="form-control" value="<?= htmlspecialchars($user['sodt'] ?? '') ?>" required pattern="[0-9]{10,11}">
                                        <div class="invalid-tooltip">Số điện thoại không hợp lệ.</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="fw-bold mb-1">Ảnh đại diện mới (Nếu muốn thay đổi)</label>
                                    <input type="file" name="hinhanh" accept="image/*" class="form-control">
                                    <div class="form-text">Chấp nhận file: jpg, jpeg, png, webp.</div>
                                </div>

                                <div class="col-12 mt-4 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary shadow-sm px-4">
                                        <i class="bi bi-save me-1"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Đổi mật khẩu -->
<div class="modal fade" id="modalChangePass" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold"><i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formChangePassword" class="validate-tooltip needs-validation" novalidate method="post" action="action/tai-khoan-action.php">
        <div class="modal-body">
            <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
            <input type="hidden" name="updateChangePassword" value="1">
            
            <div class="mb-3">
                <label class="fw-bold mb-1">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" name="mk_cu" class="form-control" required>
                    <div class="invalid-tooltip">Vui lòng nhập mật khẩu hiện tại.</div>
                </div>
            </div>
             <div class="mb-3">
                <label class="fw-bold mb-1">Xác nhận MK hiện tại <span class="text-danger">*</span></label>
                 <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                    <input type="password" name="mk_cu_xacnhan" class="form-control" required>
                     <div class="invalid-tooltip">Vui lòng xác nhận mật khẩu hiện tại.</div>
                </div>
            </div>
            <div class="mb-3">
                <label class="fw-bold mb-1">Mật khẩu mới <span class="text-danger">*</span></label>
                 <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="mk_moi" id="mk_moi" class="form-control" required minlength="6">
                     <div class="invalid-tooltip">Mật khẩu mới phải có ít nhất 6 ký tự.</div>
                </div>
            </div>
            <div class="mb-3">
                <label class="fw-bold mb-1">Nhập lại MK mới <span class="text-danger">*</span></label>
                 <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="mk_moi_xacnhan" id="mk_moi_xacnhan" class="form-control" required>
                     <div class="invalid-tooltip">Mật khẩu xác nhận không khớp.</div>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
          <button type="submit" class="btn btn-warning fw-bold shadow-sm">
              <i class="bi bi-check-circle me-1"></i> Xác nhận đổi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Custom validate cho form đổi pass
    const formPass = document.getElementById('formChangePassword');
    const mk_moi = document.getElementById('mk_moi');
    const mk_moi_xacnhan = document.getElementById('mk_moi_xacnhan');

    if (formPass) {
        formPass.addEventListener('submit', function(event) {
            if (mk_moi.value !== mk_moi_xacnhan.value) {
                mk_moi_xacnhan.setCustomValidity("Mật khẩu xác nhận không khớp.");
                event.preventDefault();
                event.stopPropagation();
            } else {
                mk_moi_xacnhan.setCustomValidity("");
            }

            if (!formPass.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            formPass.classList.add('was-validated');
        });

        mk_moi_xacnhan.addEventListener('input', function() {
             if (mk_moi.value === mk_moi_xacnhan.value) {
                mk_moi_xacnhan.setCustomValidity("");
             }
        });
    }
});
</script>

<?php include('./layouts/footer.php'); ?>
