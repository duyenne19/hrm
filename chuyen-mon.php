<?php 
include('./layouts/header.php');
include('./action/chuyen-mon-action.php');

$cmCode = "MCM" . time();
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
						<i class="bi bi-person-plus-fill me-2"></i>

                            <?= isset($chuyenmonInfo) ? 'Chỉnh sửa chuyên môn' : 'Thêm chuyên môn mới' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="action/chuyen-mon-action.php">
                            <input type="hidden" name="id" value="<?= $chuyenmonInfo['id'] ?? '' ?>">

                            <div class="mb-3">
                                <label>Mã chuyên môn</label>
                                <input type="text" class="form-control" name="ma_cm"
                                       value="<?= $chuyenmonInfo['ma_cm'] ?? $cmCode ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Tên chuyên môn <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ten_cm"
                                       value="<?= $chuyenmonInfo['ten_cm'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Mô tả</label>
                                
								<input type="text" class="form-control" name="mota"
                                       value="<?= $chuyenmonInfo['mota'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Người tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $chuyenmonInfo['nguoitao_name'] ?? ($row_acc['ho'].' '.$row_acc['ten']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Ngày tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $chuyenmonInfo['ngaytao'] ?? date('Y-m-d') ?>">
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <?php if (!empty($chuyenmonInfo)): ?>
                                    <button type="submit" name="update" class="btn btn-primary me-2"><i class="bi bi-save"></i> Cập nhật chuyên môn</button>
                                <?php else: ?>
                                    <button type="submit" name="add" class="btn btn-success me-2"><i class="bi bi-plus-circle"></i> Thêm mới chuyên môn</button>
                                <?php endif; ?>
                                <a href="chuyen-mon.php" class="btn btn-light">Làm mới</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DANH SÁCH -->
            <div class="col-md-6 col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">📋 Danh sách chuyên môn</h5>
                        <table class="table table-hover text-center" id="tableChuyenMon">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
									<th>Mã chuyên môn</th>
                                    <th>Tên chuyên môn</th>
                                    <th>Người tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $stt = 1; foreach ($arrShow as $cm): ?>
                                <tr>
                                    <td class="text-start"><?= htmlspecialchars($stt) ?></td>
									<td class="text-start"><?= htmlspecialchars($cm['ma_cm']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($cm['ten_cm']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($cm['nguoitao_name']) ?></td>
                                    <td>
                                        <a href="chuyen-mon.php?idEdit=<?= $cm['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-id="<?= $cm['id'] ?>" data-name="<?= htmlspecialchars($cm['ten_cm'], ENT_QUOTES) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Khởi tạo Datatable
    const table = document.querySelector("#tableChuyenMon");
	if (table) new simpleDatatables.DataTable(table);

    // 2. LOGIC XÓA (SỬ DỤNG EVENT DELEGATION)
    // Lắng nghe sự kiện click trên toàn bộ tài liệu
	document.addEventListener('click', function(e) {
        // Sử dụng .closest() để tìm nút có class .btn-delete
        const btnDelete = e.target.closest('.btn-delete');
        
        if (btnDelete) {
            e.preventDefault();
            
            const id = btnDelete.dataset.id;
			const name = btnDelete.dataset.name;
			
            // Kiểm tra Swal trước khi sử dụng
            if (typeof Swal === 'undefined') {
                if (confirm(`Bạn có chắc chắn muốn xóa chuyên môn "${name}" không?`)) {
                    window.location.href = `action/chuyen-mon-action.php?delete=${id}`;
                }
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa chuyên môn "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có xóa",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/chuyen-mon-action.php?delete=${id}`;
                }
            });
        }
    });


});
</script>

<?php include('./layouts/footer.php'); ?>
