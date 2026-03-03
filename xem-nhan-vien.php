<?php
include('./layouts/header.php');
include(__DIR__ . '/connection/config.php');
include(__DIR__ . '/models/NhanVien.php');

// Khởi tạo kết nối
$database = new Database();
$conn = $database->getConnection();

// Khởi tạo class
$nhanvien = new NhanVien($conn);
$id = $_GET['id'] ?? 0;
$nv = [];

// Xử lý lấy thông tin
if ($id) {
    // Giả sử method getById trả về array thông tin chi tiết
    $nv = $nhanvien->getById($id);
}

if (!$nv) {
     echo '<div class="page-heading">
            <div class="alert alert-danger m-3 d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                <div>Không tìm thấy thông tin nhân viên hoặc tham số không hợp lệ!</div>
            </div>
            <div class="m-3"><a href="ds-nhan-vien.php" class="btn btn-secondary">Quay lại danh sách</a></div>
           </div>';
     include('./layouts/footer.php');
     exit;
}
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Hồ sơ nhân viên</h3>
                <p class="text-subtitle text-muted">Chi tiết thông tin nhân viên <strong><?= htmlspecialchars($nv['hoten']) ?></strong></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="ds-nhan-vien.php">Danh sách nhân viên</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chi tiết hồ sơ</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <!-- Cột trái: Avatar & Thông tin tóm tắt -->
            <div class="col-12 col-lg-4">
                <div class="card shadow border-0 mb-3">
                    <div class="card-body text-center py-5">
                        <div class="avatar avatar-2xl mb-3 mx-auto" style="width: 150px; height: 150px;">
                            <img src="<?= !empty($nv['anhdaidien']) && file_exists('uploads/nhanvien/' . $nv['anhdaidien']) 
                                ? 'uploads/nhanvien/' . htmlspecialchars($nv['anhdaidien']) 
                                : 'assets/images/logo/logo-user.png'; ?>" 
                                alt="Avatar" 
                                class="rounded-circle img-thumbnail shadow-sm w-100 h-100 object-fit-cover">
                        </div>
                        <h4 class="mt-2 text-primary"><?= htmlspecialchars($nv['hoten']) ?></h4>
                        <p class="text-muted mb-1"><?= htmlspecialchars($nv['chuc_vu'] ?? 'Chưa cập nhật chức vụ') ?></p>
                        <span class="badge <?= ($nv['trangthai'] ?? 1) == 1 ? 'bg-success' : 'bg-danger' ?> px-3 py-2 mt-2">
                            <?= ($nv['trangthai'] ?? 1) == 1 ? 'Đang làm việc' : 'Đã nghỉ việc' ?>
                        </span>

                        <div class="d-flex justify-content-center mt-4 gap-2">
                             <a href="them-nhan-vien.php?id=<?= $nv['id'] ?>" class="btn btn-primary shadow-sm">
                                <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa
                            </a>
                            <a href="ds-nhan-vien.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card shadow border-0">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0 fw-bold text-secondary"><i class="bi bi-info-circle me-2"></i>Liên hệ</h5>
                    </div>
                    <div class="card-body pt-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-40px me-3">
                                <span class="badge bg-light-primary text-primary p-2 rounded-circle">
                                    <i class="bi bi-envelope fs-5"></i>
                                </span>
                            </div>
                            <div class="overflow-hidden">
                                <small class="text-muted d-block">Email</small>
                                <span class="fw-bold text-break text-dark"><?= htmlspecialchars($nv['email'] ?? 'Chưa cập nhật') ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="symbol symbol-40px me-3">
                                <span class="badge bg-light-success text-success p-2 rounded-circle">
                                    <i class="bi bi-telephone fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Điện thoại</small>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($nv['sodt'] ?? 'Chưa cập nhật') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Thông tin chi tiết -->
            <div class="col-12 col-lg-8">
                <!-- Thông tin cá nhân -->
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                         <h5 class="card-title mb-0 text-primary fw-bold"><i class="bi bi-person-lines-fill me-2"></i>Thông tin cá nhân</h5>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-3">
                            <div class="col-md-6 mb-2">
                                <label class="text-muted small text-uppercase fw-bold">Mã nhân viên</label>
                                <div class="fw-bold text-dark fs-5"><?= htmlspecialchars($nv['ma_nv']) ?></div>
                            </div>
                             <div class="col-md-6 mb-2">
                                <label class="text-muted small text-uppercase fw-bold">Họ và tên</label>
                                <div class="fw-bold text-dark fs-5"><?= htmlspecialchars($nv['hoten']) ?></div>
                            </div>
                            <!-- Divider -->
                            <div class="col-12"><hr class="my-1 text-muted opacity-25"></div>

                            <div class="col-md-4 mb-2">
                                <label class="text-muted small">Giới tính</label>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($nv['gtinh']) ?></div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="text-muted small">Ngày sinh</label>
                                <div class="fw-bold text-dark"><?= !empty($nv['ngsinh']) ? date('d/m/Y', strtotime($nv['ngsinh'])) : 'N/A' ?></div>
                            </div>
                             <div class="col-md-4 mb-2">
                                <label class="text-muted small">Nơi sinh</label>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($nv['noisinh'] ?? '') ?></div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="text-muted small">Số CCCD/CMND</label>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($nv['so_cccd'] ?? '') ?></div>
                            </div>
                             <div class="col-md-4 mb-2">
                                <label class="text-muted small">Ngày cấp</label>
                                <div class="fw-bold text-dark"><?= !empty($nv['ngaycap_cccd']) ? date('d/m/Y', strtotime($nv['ngaycap_cccd'])) : 'N/A' ?></div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="text-muted small">Nơi cấp</label>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($nv['noicap_cccd'] ?? '') ?></div>
                            </div>
                            
                            <!-- Divider -->
                            <div class="col-12"><hr class="my-1 text-muted opacity-25"></div>

                             <div class="col-md-3 mb-2">
                                <label class="text-muted small">Dân tộc</label>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($nv['dan_toc'] ?? '') ?></div>
                            </div>
                             <div class="col-md-3 mb-2">
                                <label class="text-muted small">Tôn giáo</label>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($nv['ton_giao'] ?? '') ?></div>
                            </div>
                             <div class="col-md-3 mb-2">
                                <label class="text-muted small">Quốc tịch</label>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($nv['quoc_tich'] ?? '') ?></div>
                            </div>
                             <div class="col-md-3 mb-2">
                                <label class="text-muted small">Tình trạng hôn nhân</label>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($nv['hon_nhan'] ?? '') ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin địa chỉ -->
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                         <h5 class="card-title mb-0 text-success fw-bold"><i class="bi bi-geo-alt-fill me-2"></i>Địa chỉ</h5>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-3">
                             <div class="col-12 mb-2">
                                <label class="text-muted small text-uppercase fw-bold"><i class="bi bi-house-door me-1"></i>Hộ khẩu thường trú</label>
                                <div class="fw-bold text-dark border p-2 rounded bg-light"><?= htmlspecialchars($nv['hokhau'] ?? 'Chưa cập nhật') ?></div>
                            </div>
                             <div class="col-12 mb-2">
                                <label class="text-muted small text-uppercase fw-bold"><i class="bi bi-building me-1"></i>Địa chỉ tạm trú</label>
                                <div class="fw-bold text-dark border p-2 rounded bg-light"><?= htmlspecialchars($nv['tamtru'] ?? 'Chưa cập nhật') ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin công việc & Trình độ -->
                <div class="card shadow border-0 mb-4">
                     <div class="card-header bg-white py-3 border-bottom">
                         <h5 class="card-title mb-0 text-info fw-bold"><i class="bi bi-briefcase-fill me-2"></i>Công việc & Trình độ</h5>
                    </div>
                    <div class="card-body pt-4">
                         <div class="row g-3">
                            <div class="col-md-6 mb-2">
                                <label class="text-muted small">Phòng ban</label>
                                <div class="fw-bold text-primary fs-6"><?= htmlspecialchars($nv['phong_ban'] ?? '') ?></div>
                            </div>
                             <div class="col-md-6 mb-2">
                                <label class="text-muted small">Chức vụ</label>
                                <div class="fw-bold text-primary fs-6"><?= htmlspecialchars($nv['chuc_vu'] ?? '') ?></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="text-muted small">Loại nhân viên</label>
                                <div class="badge bg-light-info text-info fs-6"><?= htmlspecialchars($nv['loai_nv'] ?? '') ?></div>
                            </div>
                             <div class="col-md-6 mb-2">
                                <label class="text-muted small">Trình độ học vấn</label>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($nv['trinh_do'] ?? '') ?></div>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="text-muted small">Chuyên môn</label>
                                <div class="fw-bold text-dark fst-italic"><?= htmlspecialchars($nv['chuyen_mon'] ?? 'Chưa cập nhật') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                 <!-- Footer của hồ sơ -->
                 <div class="text-end text-muted small fst-italic mb-4">
                    <span><i class="bi bi-person-fill"></i> Người tạo: <?= htmlspecialchars($nv['nguoitao_name'] ?? 'Không rõ'); ?></span>
                 </div>
            </div>
        </div>
    </section>
</div>

<?php include('./layouts/footer.php'); ?>
