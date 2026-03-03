<?php
    include('./layouts/header.php');
    require_once __DIR__ . '/connection/config.php';
    require_once __DIR__ . '/models/NhanVien.php';
    
    // Include Models cho Select Option
    require_once __DIR__ . '/models/QuocTich.php';
    require_once __DIR__ . '/models/TonGiao.php';
    require_once __DIR__ . '/models/DanToc.php';
    require_once __DIR__ . '/models/LoaiNhanVien.php';
    require_once __DIR__ . '/models/TrinhDo.php';
    require_once __DIR__ . '/models/ChuyenMon.php';
    require_once __DIR__ . '/models/PhongBan.php';
    require_once __DIR__ . '/models/ChucVu.php';
    require_once __DIR__ . '/models/HonNhan.php';

    $database = new Database();
    $conn = $database->getConnection();

    // Lấy dữ liệu cho các Select Box
    $ds_quoc_tich  = (new QuocTich($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
    $ds_ton_giao   = (new TonGiao($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
    $ds_dan_toc    = (new DanToc($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
    $ds_loai_nv    = (new LoaiNhanVien($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
    $ds_trinh_do   = (new TrinhDo($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
    $ds_chuyen_mon = (new ChuyenMon($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
    $ds_phong_ban  = (new PhongBan($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
    $ds_chuc_vu    = (new ChucVu($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);
    $ds_hon_nhan   = (new HonNhan($conn))->getAll()->fetchAll(PDO::FETCH_ASSOC);

    $nhanvien = new NhanVien($conn);
    $nhanvienInfo = [];
    $isEdit = false;

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $nhanvienInfo = $nhanvien->getById($id);
        if ($nhanvienInfo) {
            $isEdit = true;
        }
    }
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $isEdit ? 'Cập nhật hồ sơ nhân viên' : 'Tiếp nhận nhân viên mới' ?></h3>
                <p class="text-subtitle text-muted">Vui lòng điền đầy đủ thông tin vào biểu mẫu dưới đây.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="ds-nhan-vien.php">Nhân viên</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $isEdit ? 'Cập nhật' : 'Thêm mới' ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <form id="formNhanVien" class="validate-tooltip needs-validation" novalidate method="post" enctype="multipart/form-data" 
              action="action/nhan-vien-action.php">
            
            <input type="hidden" name="id" value="<?= $isEdit ? $nhanvienInfo['id'] : '' ?>">
            <input type="hidden" name="isEdit" value="<?= $isEdit ? 1 : 0 ?>">

            <div class="row match-height">
                <!-- Cột trái: Ảnh và Thông tin cơ bản -->
                <div class="col-md-4 col-12">
                    <div class="card shadow border-0 mb-4">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-4 mw-bold text-primary">Ảnh đại diện</h5>
                            <div class="avatar-upload mb-3 position-relative d-inline-block">
                                <?php 
                                $avatarUrl = !empty($nhanvienInfo['anhdaidien']) 
                                    ? "uploads/nhanvien/" . htmlspecialchars($nhanvienInfo['anhdaidien']) 
                                    : "assets/images/default.png";
                                ?>
                                <img src="<?= $avatarUrl ?>" id="preview_image" 
                                     class="rounded-circle shadow border bg-white" 
                                     style="width: 180px; height: 180px; object-fit: cover; cursor: pointer;"
                                     onclick="document.getElementById('anhdaidien').click();">
                                
                                <label for="anhdaidien" class="position-absolute bottom-0 end-0 bg-primary text-white p-2 rounded-circle shadow-sm" style="cursor: pointer;">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                            </div>
                            <input type="file" id="anhdaidien" name="anhdaidien" class="d-none" accept="image/*" 
                                   onchange="document.getElementById('preview_image').src = window.URL.createObjectURL(this.files[0])">
                            
                            <p class="text-muted small">Nhấp vào ảnh để thay đổi (jpg, png)</p>
                            
                            <?php if ($isEdit): ?>
                            <div class="mt-3">
                                <label class="fw-bold d-block">Mã Nhân Viên</label>
                                <span class="badge bg-light-primary text-primary fs-5 border border-primary">
                                    <?= htmlspecialchars($nhanvienInfo['ma_nv']) ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card shadow border-0">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="fw-bold text-primary mb-0"><i class="bi bi-info-circle me-2"></i>Trạng thái làm việc</h6>
                        </div>
                        <div class="card-body mt-3">
                             <div class="mb-3">
                                <label class="fw-bold mb-1">Loại nhân viên <span class="text-danger">*</span></label>
                                <select name="loai_nv" class="form-select" required>
                                    <option value="">-- Chọn loại --</option>
                                    <?php foreach ($ds_loai_nv as $item): ?>
                                        <option value="<?= $item['id'] ?>" <?= ($nhanvienInfo['id_loainv'] ?? '') == $item['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($item['ten_lnv']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold mb-1">Trạng thái</label>
                                <select name="trang_thai" class="form-select">
                                    <option value="1" <?= ($nhanvienInfo['trangthai'] ?? 1) == 1 ? 'selected' : '' ?>>Đang làm việc</option>
                                    <option value="0" <?= ($nhanvienInfo['trangthai'] ?? 1) == 0 ? 'selected' : '' ?>>Đã nghỉ việc</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Form chi tiết -->
                <div class="col-md-8 col-12">
                    <div class="card shadow border-0">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between">
                            <h5 class="card-title fw-bold text-primary mb-0"><i class="bi bi-person-vcard me-2"></i>Thông tin chi tiết</h5>
                        </div>
                        <div class="card-body mt-3">
                            <!-- Nav Tabs -->
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">
                                        <i class="bi bi-person me-1"></i> Cá nhân
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="job-tab" data-bs-toggle="tab" data-bs-target="#job" type="button" role="tab" aria-controls="job" aria-selected="false">
                                        <i class="bi bi-briefcase me-1"></i> Công việc
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                                        <i class="bi bi-geo-alt me-1"></i> Liên hệ & Khác
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content pt-4" id="myTabContent">
                                <!-- TAB 1: THÔNG TIN CÁ NHÂN -->
                                <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Họ và tên <span class="text-danger">*</span></label>
                                            <input type="text" name="ten_nv" class="form-control" required
                                                value="<?= htmlspecialchars($nhanvienInfo['hoten'] ?? '') ?>" placeholder="Nhập họ tên...">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Giới tính <span class="text-danger">*</span></label>
                                            <select name="gioi_tinh" class="form-select" required>
                                                <option value="1" <?= ($nhanvienInfo['gtinh'] ?? 1) == 1 ? 'selected' : '' ?>>Nam</option>
                                                <option value="0" <?= ($nhanvienInfo['gtinh'] ?? 1) == 0 ? 'selected' : '' ?>>Nữ</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Ngày sinh <span class="text-danger">*</span></label>
                                            <input type="date" name="ngaysinh" class="form-control" required
                                                value="<?= $nhanvienInfo['ngsinh'] ?? '' ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Nơi sinh</label>
                                            <input type="text" name="noisinh" class="form-control"
                                                value="<?= htmlspecialchars($nhanvienInfo['noisinh'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Số CCCD/CMND <span class="text-danger">*</span></label>
                                            <input type="text" name="cmnd" class="form-control" required
                                                value="<?= htmlspecialchars($nhanvienInfo['so_cccd'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Ngày cấp</label>
                                            <input type="date" name="ngaycap" class="form-control"
                                                value="<?= $nhanvienInfo['ngaycap_cccd'] ?? '' ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Nơi cấp</label>
                                            <input type="text" name="noicap" class="form-control"
                                                value="<?= htmlspecialchars($nhanvienInfo['noicap_cccd'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Tình trạng hôn nhân</label>
                                            <select name="hon_nhan" class="form-select">
                                                <option value="">-- Chọn --</option>
                                                <?php foreach ($ds_hon_nhan as $item): ?>
                                                    <option value="<?= $item['id'] ?>" <?= ($nhanvienInfo['id_honnhan'] ?? '') == $item['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($item['ten_hn']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 2: CÔNG VIỆC -->
                                <div class="tab-pane fade" id="job" role="tabpanel" aria-labelledby="job-tab">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Phòng ban <span class="text-danger">*</span></label>
                                            <select name="phong_ban" class="form-select" required>
                                                <option value="">-- Chọn phòng ban --</option>
                                                <?php foreach ($ds_phong_ban as $item): ?>
                                                    <option value="<?= $item['id'] ?>" <?= ($nhanvienInfo['id_phongban'] ?? '') == $item['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($item['ten_bp']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Chức vụ <span class="text-danger">*</span></label>
                                            <select name="chuc_vu" class="form-select" required>
                                                <option value="">-- Chọn chức vụ --</option>
                                                <?php foreach ($ds_chuc_vu as $item): ?>
                                                    <option value="<?= $item['id'] ?>" <?= ($nhanvienInfo['id_chucvu'] ?? '') == $item['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($item['tencv']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Trình độ</label>
                                            <select name="trinh_do" class="form-select">
                                                <option value="">-- Chọn trình độ --</option>
                                                <?php foreach ($ds_trinh_do as $item): ?>
                                                    <option value="<?= $item['id'] ?>" <?= ($nhanvienInfo['id_trinhdo'] ?? '') == $item['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($item['ten_td']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Chuyên môn</label>
                                            <select name="chuyen_mon" class="form-select">
                                                <option value="">-- Chọn chuyên môn --</option>
                                                <?php foreach ($ds_chuyen_mon as $item): ?>
                                                    <option value="<?= $item['id'] ?>" <?= ($nhanvienInfo['id_chuyenmon'] ?? '') == $item['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($item['ten_cm']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 3: LIÊN HỆ & KHÁC -->
                                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" required
                                                value="<?= htmlspecialchars($nhanvienInfo['email'] ?? '') ?>" placeholder="example@domain.com">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold mb-1">Số điện thoại <span class="text-danger">*</span></label>
                                            <input type="text" name="sodt" class="form-control" required
                                                value="<?= htmlspecialchars($nhanvienInfo['sodt'] ?? '') ?>" placeholder="09xxxxxxxx">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="fw-bold mb-1">Hộ khẩu thường trú</label>
                                            <input type="text" name="hokhau" class="form-control"
                                                value="<?= htmlspecialchars($nhanvienInfo['hokhau'] ?? '') ?>">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="fw-bold mb-1">Chỗ ở hiện nay</label>
                                            <input type="text" name="tamtru" class="form-control"
                                                value="<?= htmlspecialchars($nhanvienInfo['tamtru'] ?? '') ?>">
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label class="fw-bold mb-1">Quốc tịch</label>
                                            <select name="quoc_tich" class="form-select choices-select">
                                                <option value="">-- Chọn --</option>
                                                <?php foreach ($ds_quoc_tich as $item): ?>
                                                    <option value="<?= $item['id'] ?>" <?= ($nhanvienInfo['id_quoctich'] ?? '') == $item['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($item['ten_qt']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="fw-bold mb-1">Dân tộc</label>
                                            <select name="dan_toc" class="form-select choices-select">
                                                <option value="">-- Chọn --</option>
                                                <?php foreach ($ds_dan_toc as $item): ?>
                                                    <option value="<?= $item['id'] ?>" <?= ($nhanvienInfo['id_dantoc'] ?? '') == $item['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($item['ten_dt']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="fw-bold mb-1">Tôn giáo</label>
                                            <select name="ton_giao" class="form-select choices-select">
                                                <option value="">-- Chọn --</option>
                                                <?php foreach ($ds_ton_giao as $item): ?>
                                                    <option value="<?= $item['id'] ?>" <?= ($nhanvienInfo['id_tongiao'] ?? '') == $item['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($item['ten_tg']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="mt-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="ds-nhan-vien.php" class="btn btn-light-secondary shadow-sm">
                                    <i class="bi bi-arrow-left me-1"></i> Trở về danh sách
                                </a>
                                <button type="submit" name="<?= $isEdit ? 'update' : 'add' ?>" class="btn btn-primary shadow-sm px-4">
                                    <i class="bi bi-save me-1"></i> Lưu thông tin
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Init Choices.js cho các select box (trừ các select đơn giản)
    const selects = document.querySelectorAll('.form-select');
    selects.forEach(select => {
        // Chỉ apply Choices cho select không phải 'choices-select' nếu cần, 
        // hoặc apply hết nếu UI cho phép. Ở đây ta apply hết cho đẹp.
        new Choices(select, {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
             placeholder: true,
            placeholderValue: 'Chọn...',
            noResultsText: 'Không tìm thấy kết quả',
        });
    });

    // 2. Client-side validation helper
    const form = document.getElementById('formNhanVien');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

<?php include('./layouts/footer.php'); ?>
