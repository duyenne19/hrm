<?php 
include('./layouts/header.php');
include('./action/bang-cap-action.php');

$bcCode = "MBC" . time();
$row_acc = $_SESSION['user'];
?>

<div class="page-heading">
    <section id="basic-vertical-layouts">
        <div class="row match-height">

            <!-- FORM -->
            <div class="col-md-6 col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="text-primary fw-bold mb-0">
						<i class="bi bi-briefcase-fill me-2"></i>
                            <?= isset($bangcapInfo) ? 'Chỉnh sửa bằng cấp' : 'Thêm bằng cấp mới' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="action/bang-cap-action.php">
                            <input type="hidden" name="id" value="<?= $bangcapInfo['id'] ?? '' ?>">

                            <div class="mb-3">
                                <label>Mã bằng cấp</label>
                                <input type="text" class="form-control" name="ma_bcap"
                                       value="<?= $bangcapInfo['ma_bcap'] ?? $bcCode ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Tên bằng cấp <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ten_bcap"
                                       value="<?= $bangcapInfo['ten_bcap'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Mô tả</label>
                                <textarea name="mota_bcap" class="form-control"><?= $bangcapInfo['mota_bcap'] ?? '' ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label>Người tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $bangcapInfo['nguoitao_name'] ?? ($row_acc['ho'].' '.$row_acc['ten']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Ngày tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $bangcapInfo['ngaytao'] ?? date('Y-m-d') ?>">
                            </div>

                            <div class="d-flex justify-content-end">
                                <?php if (!empty($bangcapInfo)): ?>
                                    <button type="submit" name="update" class="btn btn-primary me-2">Cập nhật</button>
                                <?php else: ?>
                                    <button type="submit" name="add" class="btn btn-success me-2">Thêm mới</button>
                                <?php endif; ?>
                                <a href="bang-cap.php" class="btn btn-light">Làm mới</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DANH SÁCH -->
            <div class="col-md-6 col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">📋 Danh sách bằng cấp</h5>
                        <table class="table table-hover text-center" id="tableBangCap">
                            <thead class="table-light">
                                <tr>
									<th>STT</th>
                                    <th>Mã BC</th>
                                    <th>Tên bằng cấp</th>
                                    <th>Người tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $stt=1; foreach ($arrShow as $bc): ?>
                                <tr>
                                    <td><?= htmlspecialchars($stt) ?></td>
									<td><?= htmlspecialchars($bc['ma_bcap']) ?></td>
									
                                    <td><?= htmlspecialchars($bc['ten_bcap']) ?></td>
                                    <td><?= htmlspecialchars($bc['nguoitao_name']) ?></td>
                                    <td>
                                        <a href="bang-cap.php?idEdit=<?= $bc['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-id="<?= $bc['id'] ?>" data-name="<?= htmlspecialchars($bc['ten_bcap'], ENT_QUOTES) ?>">
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
    </section>
</div>

<!-- ✅ JS xử lý -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    new simpleDatatables.DataTable("#tableBangCap");

    // Confirm delete (mẫu chung)
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa bằng cấp "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có xóa",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/bang-cap-action.php?delete=${id}`;
                }
            });
        });
    });

    
});
</script>

<?php include('./layouts/footer.php'); ?>
