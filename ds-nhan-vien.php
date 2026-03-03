<?php 
    include('./layouts/header.php');
    require_once __DIR__ . '/connection/config.php';
    require_once __DIR__ . '/models/NhanVien.php';
    require_once __DIR__ . '/models/PhongBan.php';

    $database = new Database();
    $conn = $database->getConnection();
    
    // Lấy danh sách phòng ban cho bộ lọc
    $phong_ban_model = new PhongBan($conn);
    $arrPhongBan = $phong_ban_model->getAll()->fetchAll(PDO::FETCH_ASSOC);

    // Lấy dữ liệu nhân viên (có lọc)
    $filter_id_pb = isset($_GET['filter_id_pb']) ? $_GET['filter_id_pb'] : '';
    $nhanvien = new NhanVien($conn);    
    $stmt = $nhanvien->getFilter_NV_PB($filter_id_pb);
    $arrNhanVien = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-heading">
    <!-- HIỂN THỊ THÔNG BÁO LỖI/THÀNH CÔNG -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?= (isset($_GET['status']) && $_GET['status'] == 'success') ? 'success' : 'danger' ?> alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-<?= (isset($_GET['status']) && $_GET['status'] == 'success') ? 'check-circle-fill' : 'exclamation-triangle-fill' ?> me-2"></i><?= htmlspecialchars(urldecode($_GET['msg'])) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Danh sách Nhân viên</h3>
                <p class="text-subtitle text-muted">Quản lý hồ sơ nhân sự, tìm kiếm và thao tác.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Nhân viên</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card shadow border-0">
            <div class="card-header bg-white border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="bi bi-people-fill me-2"></i>Dữ liệu nhân viên
                        </h5>
                    </div>
                    <?php if(!$ke_toan && !$hr): // Chỉ Admin mới thêm được? Tùy logic ?> 
                    <!-- Logic cũ: if(!$ke_toan) -> Admin & HR đều thấy button Thêm -->
                    <?php endif; ?>
                    
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <?php if(!$ke_toan): ?>
                        <a href="them-nhan-vien.php" class="btn btn-primary shadow-sm me-1">
                            <i class="bi bi-person-plus-fill me-1"></i> Thêm mới
                        </a>
                        <?php endif; ?>
                        
                        <div class="btn-group shadow-sm">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download me-1"></i> Xuất dữ liệu
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="action/export_excel_nhanvien.php?filter_id_pb=<?= htmlspecialchars($filter_id_pb) ?>" target="_blank">
                                        <i class="bi bi-file-earmark-excel text-success me-2"></i> Xuất Excel
                                    </a>
                                </li>
                                <li>
                                    <button class="dropdown-item" onclick="window.print()">
                                        <i class="bi bi-printer text-info me-2"></i> In danh sách
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body mt-3">
                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="fw-bold mb-1 text-muted">Lọc theo phòng ban:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-funnel"></i></span>
                            <select id="filter_selectPhongBanNV" class="form-select border-start-0 ps-0">
                                <option value="">-- Tất cả Phòng ban --</option>
                                <?php foreach ($arrPhongBan as $pb): ?>
                                    <option value="<?= $pb['id'] ?>" <?= (strval($pb['id']) === strval($filter_id_pb)) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($pb['ten_bp']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableNhanVien">
                        <thead class="table-light text-nowrap">
                            <tr>
                                <th class="text-center">STT</th>
                                <th>Mã NV</th>
                                <th class="text-center">Avatar</th>
                                <th>Họ tên & Thông tin</th>
                                <th>Phòng ban & Chức vụ</th>
                                <th>Liên hệ</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $stt = 1;
                            foreach ($arrNhanVien as $nv): 
                                $avatarPath = !empty($nv['anhdaidien']) ? 'uploads/nhanvien/' . $nv['anhdaidien'] : 'assets/images/default.png';
                            ?>
                                <tr>
                                    <td class="text-center text-muted"><?= $stt++ ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars($nv['ma_nv']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="avatar avatar-md">
                                            <img src="<?= htmlspecialchars($avatarPath) ?>" alt="avatar" class="rounded-circle shadow-sm" style="object-fit: cover;">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary"><?= htmlspecialchars($nv['hoten']) ?></div>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-gender-<?= ($nv['gtinh'] ?? 1) == 1 ? 'male text-info' : 'female text-danger' ?> me-1"></i>
                                            <?= ($nv['gtinh'] ?? 1) == 1 ? 'Nam' : 'Nữ' ?>
                                            &bull; <?= htmlspecialchars($nv['ngsinh'] ?? '') ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($nv['phong_ban'] ?? 'N/A') ?></div>
                                        <small class="text-muted fst-italic"><?= htmlspecialchars($nv['chuc_vu'] ?? 'N/A') ?></small>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($nv['email'] ?? '') ?>">
                                                <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($nv['email'] ?? '---') ?>
                                            </div>
                                            <div>
                                                <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($nv['sodt'] ?? '---') ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if (($nv['trangthai'] ?? 1) == 1): ?>
                                            <span class="badge bg-success-light text-success"><i class="bi bi-check-circle me-1"></i>Đang làm</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-light text-danger"><i class="bi bi-x-circle me-1"></i>Nghỉ việc</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="xem-nhan-vien.php?id=<?= $nv['id'] ?>" class="btn btn-sm btn-outline-info shadow-sm" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            <?php if(!$ke_toan): ?>
                                            <a href="them-nhan-vien.php?id=<?= $nv['id'] ?>" class="btn btn-sm btn-outline-warning shadow-sm" title="Sửa hồ sơ">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger shadow-sm btn-delete" 
                                                data-id="<?= $nv['id'] ?>" 
                                                data-name="<?= htmlspecialchars($nv['hoten']) ?>"
                                                title="Xóa nhân viên">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Datatable
    const table = document.querySelector("#tableNhanVien");
    if (table) {
        new simpleDatatables.DataTable(table, {
            labels: {
                placeholder: "Tìm kiếm nhân viên...",
                perPage: "mục/trang",
                noRows: "Không tìm thấy nhân viên nào",
                info: "Hiển thị {start} đến {end} của {rows} nhân viên",
            },
            perPage: 10,
        });
    }

    // 2. Filter Trigger
    const filterSelect = document.getElementById('filter_selectPhongBanNV');
    if (filterSelect) {
        // Init ChoiceJS for filter if needed, OR just plain select
        // new Choices(filterSelect, { itemSelectText: '', searchEnabled: false }); 
        // Using standard select for now or Choices if you prefer consistency
        
        filterSelect.addEventListener('change', function() {
            const val = this.value;
            window.location.href = `ds-nhan-vien.php?filter_id_pb=${val}`;
        });
    }

    // 3. Delete Confirmation
    document.addEventListener('click', function(e) {
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) {
            e.preventDefault();
            const id = btnDelete.dataset.id;
            const name = btnDelete.dataset.name;

            const confirmOptions = {
                title: 'Xóa nhân viên?',
                html: `Bạn có chắc chắn muốn xóa hồ sơ nhân viên <b>"${name}"</b> không?<br><small class="text-danger">Lưu ý: Hành động này có thể ảnh hưởng đến dữ liệu lương, công tác!</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: '<i class="bi bi-trash"></i> Xóa hồ sơ',
                cancelButtonText: 'Hủy bỏ',
                reverseButtons: true
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire(confirmOptions).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = `action/nhan-vien-action.php?delete_id=${id}`;
                    }
                });
            } else {
                if (confirm(`Xóa nhân viên "${name}"?`)) {
                    window.location.href = `action/nhan-vien-action.php?delete_id=${id}`;
                }
            }
        }
    });

    // 4. Highlight active row (Optional)
});
</script>

<?php include('./layouts/footer.php'); ?>
