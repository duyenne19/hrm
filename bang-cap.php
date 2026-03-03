<?php 
include('./layouts/header.php');
include('./action/bang-cap-action.php');

$bcCode = "MBC" . time();
$row_acc = $_SESSION['user'];
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Quản lý Bằng Cấp</h3>
                <p class="text-subtitle text-muted">Danh mục các loại bằng cấp và trình độ.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Bằng cấp</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section id="basic-vertical-layouts">
        <div class="row match-height">

            <!-- FORM -->
            <div class="col-md-5 col-12">
                <div class="card shadow border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="text-primary fw-bold mb-0">
                            <i class="<?= isset($bangcapInfo) ? 'bi bi-pencil-square' : 'bi bi-plus-circle' ?> me-2"></i>
                            <?= isset($bangcapInfo) ? 'Chỉnh sửa bằng cấp' : 'Thêm bằng cấp mới' ?>
                        </h5>
                    </div>
                    <div class="card-body mt-3">
                        <form method="post" action="action/bang-cap-action.php" class="validate-tooltip">
                            <input type="hidden" name="id" value="<?= $bangcapInfo['id'] ?? '' ?>">

                            <div class="mb-3">
                                <label class="fw-bold mb-1">Mã bằng cấp</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-upc-scan"></i></span>
                                    <input type="text" class="form-control bg-light" name="ma_bcap"
                                           value="<?= $bangcapInfo['ma_bcap'] ?? $bcCode ?>" readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold mb-1">Tên bằng cấp <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" class="form-control" name="ten_bcap" placeholder="Nhập tên bằng cấp..."
                                           value="<?= $bangcapInfo['ten_bcap'] ?? '' ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold mb-1">Mô tả</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-file-text"></i></span>
                                    <textarea name="mota_bcap" class="form-control" rows="3" placeholder="Mô tả chi tiết..."><?= $bangcapInfo['mota_bcap'] ?? '' ?></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold mb-1">Người tạo</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control bg-light" readonly
                                               value="<?= $bangcapInfo['nguoitao_name'] ?? ($row_acc['ho'].' '.$row_acc['ten']) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold mb-1">Ngày tạo</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-clock"></i></span>
                                        <input type="text" class="form-control bg-light" readonly
                                               value="<?= isset($bangcapInfo['ngaytao']) ? date('d/m/Y', strtotime($bangcapInfo['ngaytao'])) : date('d/m/Y') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-2">
                                <a href="bang-cap.php" class="btn btn-light-secondary me-2 shadow-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Làm mới
                                </a>
                                <?php if (!empty($bangcapInfo)): ?>
                                    <button type="submit" name="update" value="update" class="btn btn-primary shadow-sm">
                                        <i class="bi bi-save me-1"></i> Cập nhật
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="add" value="add" class="btn btn-success shadow-sm">
                                        <i class="bi bi-plus-lg me-1"></i> Thêm mới
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DANH SÁCH -->
            <div class="col-md-7 col-12">
                <div class="card shadow border-0">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-list-ul me-2"></i>Danh sách bằng cấp</h5>
                    </div>
                    <div class="card-body mt-3">
                        <table class="table table-hover align-middle text-center text-nowrap" id="tableBangCap">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Mã BC</th>
                                    <th>Tên bằng cấp</th>
                                    <th>Người tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $stt=1; foreach ($arrShow as $bc): ?>
                                <tr>
                                    <td><?= htmlspecialchars($stt) ?></td>
                                    <td>
                                        <span class="badge bg-light-primary text-primary">
                                            <?= htmlspecialchars($bc['ma_bcap']) ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold text-start"><?= htmlspecialchars($bc['ten_bcap']) ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars($bc['nguoitao_name']) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="bang-cap.php?idEdit=<?= $bc['id'] ?>" class="btn btn-sm btn-outline-primary shadow-sm" title="Chỉnh sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete shadow-sm"
                                                    data-id="<?= $bc['id'] ?>" data-name="<?= htmlspecialchars($bc['ten_bcap'], ENT_QUOTES) ?>" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php $stt++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ✅ JS xử lý -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Init Table
    const tableEl = document.getElementById('tableBangCap');
    if (tableEl) {
        new simpleDatatables.DataTable(tableEl, {
            searchable: true,
            fixedHeight: false, 
            labels: {
                placeholder: "Tìm kiếm...",
                perPage: "mục / trang",
                noRows: "Không có dữ liệu",
                info: "Hiển thị {start} đến {end} của {rows} mục",
            }
        });
    }

    // 2. Confirm delete (Event Delegation)
    document.addEventListener('click', function(e) {
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) {
            const id = btnDelete.dataset.id;
            const name = btnDelete.dataset.name;
            
            Swal.fire({
                title: 'Xác nhận xóa?',
                html: `Bạn có chắc chắn muốn xóa bằng cấp <b>"${name}"</b> không?<br>Hành động này không thể hoàn tác!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "<i class='bi bi-trash'></i> Xóa ngay",
                cancelButtonText: "Hủy bỏ",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/bang-cap-action.php?delete=${id}`;
                }
            });
        }
    });
});
</script>

<?php include('./layouts/footer.php'); ?>
