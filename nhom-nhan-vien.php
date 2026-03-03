<?php
	include('./layouts/header.php');
	include(__DIR__ . '/connection/config.php');
	include(__DIR__ . '/models/NhomNV.php');
	$groupCode = "MNH" . time();
	$row_acc = $_SESSION['user'];
	$database = new Database();
	$conn = $database->getConnection();
	$nhomnv = new NhomNV($conn);

	// Danh sách
	$arrShow = $nhomnv->getAll()->fetchAll(PDO::FETCH_ASSOC);
	
	// Chi tiết khi sửa
	$idEdit = $_GET['idEdit'] ?? null;
	$nhomInfo = $idEdit ? $nhomnv->getById($idEdit) : null;
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Quản lý Nhóm Nhân Viên</h3>
                <p class="text-subtitle text-muted">Danh sách và thông tin các nhóm nhân viên trong hệ thống.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Nhóm nhân viên</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <!-- FORM -->
            <div class="col-md-4 col-12">
                <div class="card shadow border-0">
				    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="background: #435ebe;">
                        <h5 class="text-white fw-bold mb-0">
                            <i class="<?= isset($nhomInfo) ? 'bi bi-pencil-square' : 'bi bi-plus-circle' ?> me-2"></i>
                            <?= isset($nhomInfo) ? 'Chỉnh sửa nhóm' : 'Thêm nhóm mới' ?>
                        </h5>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form id="formNhomNV" class="validate-tooltip" method="post" action="action/nhom-nhan-vien-action.php">
                                <input type="hidden" name="id" value="<?= $nhomInfo['id'] ?? '' ?>">
                                
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group has-icon-left">
                                                <label class="form-label fw-bold">Mã nhóm</label>
                                                <div class="position-relative">
                                                    <input type="text" name="manhom" class="form-control bg-light" readonly
                                                        value="<?= $nhomInfo['manhom'] ?? $groupCode ?>" placeholder="Mã nhóm tự động">
                                                    <div class="form-control-icon">
                                                        <i class="bi bi-upc-scan"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <div class="form-group has-icon-left">
                                                <label class="form-label fw-bold">Tên nhóm <span class="text-danger">*</span></label>
                                                <div class="position-relative">
                                                    <input type="text" name="tennhom" class="form-control" required 
                                                        value="<?= $nhomInfo['tennhom'] ?? '' ?>" placeholder="Nhập tên nhóm...">
                                                    <div class="form-control-icon">
                                                        <i class="bi bi-people"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group has-icon-left">
                                                <label class="form-label fw-bold">Mô tả</label>
                                                <div class="position-relative">
                                                    <textarea name="mota" class="form-control" rows="3" placeholder="Nhập mô tả ngắn gọn..."><?= $nhomInfo['mota'] ?? '' ?></textarea>
                                                    <div class="form-control-icon">
                                                        <i class="bi bi-card-text"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group has-icon-left">
                                                <label class="form-label fw-bold">Người tạo</label>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control bg-light" readonly style="font-size: 0.9rem;"
                                                        value="<?= $nhomInfo['nguoitao_name'] ?? ($row_acc['ho'].' '.$row_acc['ten']) ?>">
                                                    <div class="form-control-icon">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 col-12">
                                            <div class="form-group has-icon-left">
                                                <label class="form-label fw-bold">Ngày tạo</label>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control bg-light" readonly style="font-size: 0.9rem;"
                                                        value="<?= $nhomInfo['ngaytao'] ?? date('Y-m-d') ?>">
                                                    <div class="form-control-icon">
                                                        <i class="bi bi-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-end mt-4">
                                            <a href="nhom-nhan-vien.php" class="btn btn-light-secondary me-2 mb-1 shadow-sm">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Làm mới
                                            </a>
                                            <?php if (isset($_GET['idEdit']) && !empty($nhomInfo)): ?>
                                                <button type="submit" name="update" class="btn btn-primary mb-1 shadow-sm">
                                                    <i class="bi bi-save me-1"></i> Cập nhật
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" name="add" class="btn btn-success mb-1 shadow-sm">
                                                    <i class="bi bi-plus-lg me-1"></i> Thêm mới
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DANH SÁCH -->
            <div class="col-md-8 col-12">
                <div class="card shadow border-0">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-secondary mb-0"><i class="bi bi-list-ul me-2"></i>Danh sách nhóm nhân viên</h5>
                        <span class="badge bg-primary rounded-pill"><?= count($arrShow) ?> nhóm</span>
                    </div>
                    <div class="card-body mt-2">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle" id="tableNhomNV">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="5%">STT</th>
                                        <th>Mã nhóm</th>
                                        <th>Tên nhóm</th>
                                        <th>Mô tả</th>
                                        <th>Người tạo</th>
                                        <th class="text-center" width="15%">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i=1; foreach ($arrShow as $row): ?>
                                    <tr>
                                        <td class="text-center fw-bold text-muted"><?= htmlspecialchars($i) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['manhom']) ?></span></td>
                                        <td class="fw-bold text-primary"><?= htmlspecialchars($row['tennhom']) ?></td>
                                        <td><small class="text-muted"><?= htmlspecialchars($row['mota'] ?? '') ?></small></td>
                                        <td><small><i class="bi bi-person-circle me-1 text-secondary"></i><?= htmlspecialchars($row['nguoitao_name']) ?></small></td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <a href="ds-nhom-nhan-vien.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info text-white me-1" title="Xem thành viên">
                                                    <i class="bi bi-people-fill"></i>
                                                </a>
                                                <a href="nhom-nhan-vien.php?idEdit=<?= $row['id'] ?>" class="btn btn-sm btn-primary me-1" title="Sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-name="<?= htmlspecialchars($row['tennhom'], ENT_QUOTES) ?>" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $i++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Xác nhận xóa -->
<script>
	document.addEventListener('DOMContentLoaded', function () {
        // Init table
        let table1 = document.querySelector('#tableNhomNV');
        if (table1) {
            let dataTable = new simpleDatatables.DataTable(table1, {
                labels: {
                    placeholder: "Tìm kiếm...",
                    perPage: "mục / trang",
                    noRows: "Không tìm thấy dữ liệu",
                    info: "Hiển thị {start} đến {end} của {rows} mục",
                }
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        // SweetAlert2 delete confirmation
        document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-delete');
            if (btn) {
                e.preventDefault();
                const id = btn.dataset.id;
                const name = btn.dataset.name;

                if (typeof Swal === 'undefined') {
                    if (confirm(`Bạn có chắc chắn muốn xóa nhóm "${name}" không?`)) {
                        window.location.href = `action/nhom-nhan-vien-action.php?delete=${id}`;
                    }
                    return;
                }

                Swal.fire({
                    title: 'Xác nhận xóa?',
                    html: `Bạn có chắc chắn muốn xóa nhóm <b>"${name}"</b> không?<br><small class="text-danger">Hành động này không thể hoàn tác!</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "<i class='bi bi-trash'></i> Xóa ngay",
                    cancelButtonText: "Hủy bỏ",
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = `action/nhom-nhan-vien-action.php?delete=${id}`;
                    }
                });
            }
        });
    });
</script>

<?php include('./layouts/footer.php'); ?>
