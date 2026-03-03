<?php
include('./layouts/header.php');
include(__DIR__ . '/connection/config.php');
include(__DIR__ . '/models/ChiTietNhom.php');

$database = new Database();
$conn = $database->getConnection();
$model = new ChiTietNhom($conn);
$id_nhom = (int)($_GET['id'] ?? 0);

if($id_nhom == 0){
    echo "<script>window.location.href='nhom-nhan-vien.php';</script>";
    exit;
}

$nhomInfo = $model->getNhomInfo($id_nhom);
$listNV = $model->getMembersByGroup($id_nhom);
$availableNV = $model->getAvailableMembers($id_nhom);
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Chi tiết Nhóm Nhân Viên</h3>
                <p class="text-subtitle text-muted">Quản lý thành viên trong nhóm <strong><?= htmlspecialchars($nhomInfo['tennhom']) ?></strong></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="nhom-nhan-vien.php">Nhóm nhân viên</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Danh sách thành viên</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <!-- Thông tin nhóm -->
            <div class="col-12 col-lg-3">
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <div class="avatar avatar-xl bg-white text-primary mb-2 shadow-sm p-3 rounded-circle" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="bi bi-people-fill fs-1"></i>
                        </div>
                        <h5 class="text-white mt-2 mb-0"><?= htmlspecialchars($nhomInfo['tennhom']) ?></h5>
                        <small class="text-white-50"><?= htmlspecialchars($nhomInfo['manhom']) ?></small>
                    </div>
                    <div class="card-body pt-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-calendar-check me-3 fs-5 text-muted"></i>
                            <div>
                                <small class="text-muted d-block">Ngày tạo</small>
                                <span class="fw-bold"><?= htmlspecialchars($nhomInfo['ngaytao']) ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-person-circle me-3 fs-5 text-muted"></i>
                            <div>
                                <small class="text-muted d-block">Người tạo</small>
                                <span class="fw-bold"><?= htmlspecialchars($nhomInfo['nguoitao_name']) ?></span>
                            </div>
                        </div>
                        <hr class="text-muted opacity-25">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Mô tả</small>
                            <p class="mb-0 text-dark fst-italic bg-light p-2 rounded small">
                                <?= !empty($nhomInfo['mota']) ? htmlspecialchars($nhomInfo['mota']) : 'Không có mô tả' ?>
                            </p>
                        </div>
                        <div class="d-grid gap-2">
                             <a href="nhom-nhan-vien.php?idEdit=<?= $id_nhom ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa thông tin
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách thành viên -->
            <div class="col-12 col-lg-9">
                <div class="card shadow border-0">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center">
                            <h5 class="fw-bold text-secondary mb-0 me-2"><i class="bi bi-person-lines-fill me-2"></i>Danh sách thành viên</h5>
                            <span class="badge bg-primary rounded-pill"><?= count($listNV) ?> thành viên</span>
                        </div>
                        <div>
                             <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                <i class="bi bi-person-plus-fill me-1"></i> Thêm thành viên
                            </button>
                            <a href="nhom-nhan-vien.php" class="btn btn-secondary shadow-sm ms-1">
                                <i class="bi bi-arrow-left me-1"></i> Quay lại
                            </a>
                        </div>
                    </div>
                    <div class="card-body mt-2">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle" id="tableNV">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">STT</th>
                                        <th>Mã NV</th>
                                        <th class="text-center">Ảnh</th>
                                        <th>Họ và tên</th>
                                        <th>Thông tin</th>
                                        <th>Ngày thêm</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $stt = 1; foreach ($listNV as $tv): ?>
                                        <tr>
                                            <td class="text-center fw-bold text-muted"><?= $stt ?></td>
                                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($tv['ma_nv']) ?></span></td>
                                            <td class="text-center">
                                                <div class="avatar avatar-md">
                                                    <img src="<?= !empty($tv['anhdaidien']) && file_exists('uploads/nhanvien/'.$tv['anhdaidien']) ? 'uploads/nhanvien/'.htmlspecialchars($tv['anhdaidien']) : 'assets/images/logo/logo-user.png' ?>" 
                                                         alt="avatar" class="rounded-circle border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-primary"><?= htmlspecialchars($tv['hoten']) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($tv['gtinh']) ?> - <?= !empty($tv['ngsinh']) ? date('d/m/Y', strtotime($tv['ngsinh'])) : '' ?></small>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <i class="bi bi-building me-1 text-muted"></i><?= htmlspecialchars($tv['phongban'] ?? '-') ?><br>
                                                    <i class="bi bi-briefcase me-1 text-muted"></i><?= htmlspecialchars($tv['chucvu'] ?? '-') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="small text-muted"><i class="bi bi-clock me-1"></i><?= !empty($tv['ngaytao']) ? date('d/m/Y', strtotime($tv['ngaytao'])) : '' ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if(isset($tv['trangthai']) && $tv['trangthai']): ?>
                                                    <span class="badge bg-success bg-opacity-75"><i class="bi bi-check-circle me-1"></i>Đang làm</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary bg-opacity-75"><i class="bi bi-dash-circle me-1"></i>Đã nghỉ</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                    data-id-nhom="<?= htmlspecialchars($id_nhom) ?>"
                                                    data-id-nv="<?= htmlspecialchars($tv['id_nv']) ?>"
                                                    data-name="<?= htmlspecialchars($tv['hoten']) ?>"
                                                    title="Xóa khỏi nhóm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php $stt++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- 🔹 Modal thêm nhân viên -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="action/chi-tiet-nhom-action.php">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="addMemberModalLabel">
                        <i class="bi bi-person-plus-fill me-2"></i>Thêm nhân viên vào nhóm
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id_nhom" value="<?= $id_nhom ?>">
                    
                    <div class="alert alert-light-secondary border-secondary border-opacity-25 mb-4">
                        <div class="d-flex align-items-center mb-1">
                            <strong class="me-2 text-primary">Nhóm:</strong> <span><?= htmlspecialchars($nhomInfo['tennhom']) ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <strong class="me-2 text-primary">Mã:</strong> <span><?= htmlspecialchars($nhomInfo['manhom']) ?></span>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="id_nv" class="form-label fw-bold">Chọn nhân viên <span class="text-danger">*</span></label>
                        <select name="id_nv" id="id_nv" class="form-select choice-select" required>
                             <option value="">-- Tìm nhân viên (Tên, Mã, Phòng ban) --</option>
                            <?php foreach ($availableNV as $nv): ?>
                                <option value="<?= $nv['id'] ?>">
                                    <?= htmlspecialchars($nv['ma_nv']) ?> - <?= htmlspecialchars($nv['hoten']) ?> 
                                    (<?= htmlspecialchars($nv['ten_bp'] ?? 'Không PB') ?> - <?= htmlspecialchars($nv['tencv'] ?? 'Không CV') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i>Chỉ hiển thị nhân viên chưa thuộc nhóm này.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" name="addMember" class="btn btn-success px-4">
                        <i class="bi bi-save me-1"></i> Thêm ngay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/vendors/simple-datatables/simple-datatables.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Khởi tạo Datatable
    const tableEl = document.querySelector("#tableNV");
    if (tableEl) {
        new simpleDatatables.DataTable(tableEl, {
            labels: {
                placeholder: "Tìm kiếm...",
                perPage: "mục / trang",
                noRows: "Không tìm thấy dữ liệu",
                info: "Hiển thị {start} đến {end} của {rows} mục",
            }
        });
    }

    // 2. LOGIC XÓA THÀNH VIÊN
    document.addEventListener('click', function(e) {
        const btnDelete = e.target.closest('.btn-delete');
        
        if (btnDelete) {
            e.preventDefault();
            const idNhom = btnDelete.dataset.idNhom;
            const idNv = btnDelete.dataset.idNv;
            const name = btnDelete.dataset.name;

            if (typeof Swal === 'undefined') {
                if (confirm(`Bạn có chắc muốn xóa nhân viên "${name}" khỏi nhóm này không?`)) {
                    window.location.href = `action/chi-tiet-nhom-action.php?delete=1&id_nhom=${idNhom}&id_nv=${idNv}`;
                }
                return;
            }
            
            Swal.fire({
                title: 'Xóa thành viên?',
                html: `Bạn có chắc muốn xóa nhân viên <b>"${name}"</b> khỏi nhóm này không?<br><small class="text-danger">Nhân viên sẽ bị loại khỏi nhóm!</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "<i class='bi bi-trash'></i> Xóa ngay",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/chi-tiet-nhom-action.php?delete=1&id_nhom=${idNhom}&id_nv=${idNv}`;
                }
            });
        }
    });
    
    // 3. Khởi tạo Choices.js
    const choicesEl = document.querySelector('.choice-select');
    if (typeof Choices !== 'undefined' && choicesEl) {
         new Choices(choicesEl, {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
            placeholder: true,
            placeholderValue: 'Chọn nhân viên...',
            noResultsText: 'Không tìm thấy nhân viên nào',
        });
    }
});
</script>

<?php include('./layouts/footer.php'); ?>
