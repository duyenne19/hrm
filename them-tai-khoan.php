<?php
    include('./layouts/header.php');
    require_once __DIR__ . '/connection/config.php';
    require_once __DIR__ . '/models/TaiKhoan.php';

    $database = new Database();
    $conn = $database->getConnection();
    $taiKhoan = new TaiKhoan($conn);

    $idEdit = $_GET['id'] ?? null;
    $isEdit = !empty($idEdit);
    $taiKhoanInfo = [];

    if ($isEdit) {
        $taiKhoanInfo = $taiKhoan->getById((int)$idEdit);
        if (!$taiKhoanInfo) {
            echo "<script>alert('Tài khoản không tồn tại!'); window.location.href='ds-tai-khoan.php';</script>";
            exit;
        }
    }

    // Tiêu đề trang
    $pageTitle = $isEdit ? 'Cập nhật tài khoản' : 'Thêm mới tài khoản';
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Quản lý Tài khoản</h3>
                <p class="text-subtitle text-muted">Thêm hoặc chỉnh sửa thông tin tài khoản người dùng.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="ds-tai-khoan.php">Tài khoản</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $isEdit ? 'Cập nhật' : 'Thêm mới' ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card shadow border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="fw-bold text-primary mb-0">
                    <i class="<?= $isEdit ? 'bi bi-pencil-square' : 'bi bi-person-plus-fill' ?> me-2"></i>
                    <?= $pageTitle ?>
                </h5>
            </div>

            <div class="card-body mt-4">
                
                <form id="formTaiKhoan" class="validate-tooltip needs-validation" novalidate
                      action="action/tai-khoan-action.php" method="POST" enctype="multipart/form-data">

                    <!-- Các input ẩn -->
                    <input type="hidden" name="id" value="<?= $isEdit ? (int)$taiKhoanInfo['id'] : '' ?>">
                    <input type="hidden" name="isEdit" value="<?= $isEdit ? 1 : 0 ?>">

                    <div class="row">
                        <!-- Cột trái: Thông tin cơ bản -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold mb-1">Họ <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="ho" class="form-control" placeholder="Nhập họ..."
                                            value="<?= htmlspecialchars($taiKhoanInfo['ho'] ?? '') ?>" required>
                                        <div class="invalid-tooltip">Vui lòng nhập họ.</div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold mb-1">Tên <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="ten" class="form-control" placeholder="Nhập tên..."
                                            value="<?= htmlspecialchars($taiKhoanInfo['ten'] ?? '') ?>" required>
                                        <div class="invalid-tooltip">Vui lòng nhập tên.</div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold mb-1">Email (Tên đăng nhập) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" class="form-control" placeholder="example@domain.com"
                                            value="<?= htmlspecialchars($taiKhoanInfo['email'] ?? '') ?>" 
                                            <?= $isEdit ? 'readonly style="background-color: #e9ecef;"' : 'required' ?>>
                                        <div class="invalid-tooltip">Vui lòng nhập email hợp lệ.</div>
                                    </div>
                                    <?php if($isEdit): ?><small class="text-muted fst-italic">Email không thể thay đổi.</small><?php endif; ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold mb-1">Số điện thoại <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" name="sodt" class="form-control" placeholder="Số điện thoại..."
                                            value="<?= htmlspecialchars($taiKhoanInfo['sodt'] ?? '') ?>" required pattern="[0-9]{10,11}">
                                        <div class="invalid-tooltip">Vui lòng nhập số điện thoại hợp lệ.</div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold mb-1">Vai trò hệ thống <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <label class="input-group-text" for="quyen"><i class="bi bi-shield-lock"></i></label>
                                        <select name="quyen" id="quyen" class="form-select" required>
                                            <option value="">-- Chọn quyền --</option>
                                            <option value="1" <?= ($taiKhoanInfo['quyen'] ?? '') == 1 ? 'selected' : '' ?>>Admin (Quản trị viên)</option>
                                            <option value="2" <?= ($taiKhoanInfo['quyen'] ?? '') == 2 ? 'selected' : '' ?>>HR (Nhân sự)</option>
                                            <option value="3" <?= ($taiKhoanInfo['quyen'] ?? '') == 3 ? 'selected' : '' ?>>Kế toán</option>
                                            <option value="0" <?= ($taiKhoanInfo['quyen'] ?? '') === 0 ? 'selected' : '' ?>>Khách (User)</option>
                                        </select>
                                        <div class="invalid-tooltip">Vui lòng chọn vai trò.</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold mb-1">Trạng thái</label>
                                    <div class="input-group">
                                        <label class="input-group-text" for="trangthai"><i class="bi bi-toggle-on"></i></label>
                                        <select name="trangthai" id="trangthai" class="form-select">
                                            <option value="1" <?= ($taiKhoanInfo['trangthai'] ?? 1) == 1 ? 'selected' : '' ?>>Hoạt động</option>
                                            <option value="0" <?= ($taiKhoanInfo['trangthai'] ?? 1) == 0 ? 'selected' : '' ?>>Khóa</option>
                                        </select>
                                    </div>
                                </div>

                                <?php if (!$isEdit): ?>
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold mb-1">Mật khẩu <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                                            <input type="password" name="mk" id="mk" class="form-control" required minlength="6"
                                                placeholder="Tối thiểu 6 ký tự">
                                            <div class="invalid-tooltip">Vui lòng nhập mật khẩu (tối thiểu 6 ký tự).</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold mb-1">Nhập lại mật khẩu <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                            <input type="password" name="re_mk" id="re_mk" class="form-control" required 
                                                data-match="#mk">
                                            <div class="invalid-tooltip">Mật khẩu nhập lại không khớp.</div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Cột phải: Avatar -->
                        <div class="col-md-4">
                            <div class="card bg-light border-0 shadow-sm h-100">
                                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                                    <label class="fw-bold mb-3">Ảnh đại diện</label>
                                    
                                    <div class="avatar-wrapper mb-3 position-relative">
                                        <img id="avatarPreview" 
                                             src="<?= !empty($taiKhoanInfo['hinhanh']) ? 'uploads/anh/' . $taiKhoanInfo['hinhanh'] : 'assets/images/logo/logo-sm.png' ?>" 
                                             class="rounded-circle shadow border bg-white" 
                                             style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;"
                                             onclick="document.getElementById('hinhanhInput').click();">
                                        
                                        <div class="position-absolute bottom-0 end-0 bg-primary text-white p-2 rounded-circle shadow-sm" 
                                             style="cursor: pointer; transform: translate(10%, 10%);"
                                             onclick="document.getElementById('hinhanhInput').click();">
                                            <i class="bi bi-camera-fill"></i>
                                        </div>
                                    </div>
                                    
                                    <input type="file" name="hinhanh" id="hinhanhInput" class="form-control d-none" accept="image/*">
                                    <small class="text-muted d-block mt-2">Nhấp vào ảnh để thay đổi</small>
                                    <small class="text-muted fst-italic">(Dạng file: jpg, png, webp)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Form -->
                        <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end">
                            <a href="ds-tai-khoan.php" class="btn btn-light-secondary shadow-sm me-2">
                                <i class="bi bi-arrow-left me-1"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-<?= $isEdit ? 'primary' : 'success' ?> shadow-sm px-4">
                                <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Lưu thay đổi' : 'Tạo tài khoản' ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Xem trước ảnh khi chọn file
    const fileInput = document.getElementById('hinhanhInput');
    const previewImg = document.getElementById('avatarPreview');

    if (fileInput && previewImg) {
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // 2. Custom Validation Logic cho "Nhập lại mật khẩu" (nếu form không dùng jquery-validate)
    const form = document.getElementById('formTaiKhoan');
    const mk = document.getElementById('mk');
    const re_mk = document.getElementById('re_mk');

    if (form && mk && re_mk) {
        form.addEventListener('submit', function(event) {
            if (mk.value !== re_mk.value) {
                re_mk.setCustomValidity("Mật khẩu nhập lại không khớp.");
                event.preventDefault();
                event.stopPropagation();
            } else {
                re_mk.setCustomValidity("");
            }
            
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });

        // Xóa lỗi khi nhập lại
        re_mk.addEventListener('input', function() {
             if (mk.value === re_mk.value) {
                re_mk.setCustomValidity("");
             }
        });
    }
});
</script>

<?php include('./layouts/footer.php'); ?>
