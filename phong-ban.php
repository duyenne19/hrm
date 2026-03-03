<?php
	include('./layouts/header.php');
	include(__DIR__ . '/connection/config.php');
	include(__DIR__ . '/models/PhongBan.php');

	$database = new Database();
	$conn = $database->getConnection();
	$phongban = new PhongBan($conn);

	// Danh sách
	$stmt = $phongban->getAll();
	$arrShow = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Lấy chi tiết để sửa
	$idEdit = null;
	$phongbanInfo = null;
	if (isset($_GET['idEdit'])) {
		$idEdit = intval($_GET['idEdit']);
		$phongbanInfo = $phongban->getById($idEdit);
	}

	$roomCode = "MBP" . time();
	$row_acc = $_SESSION['user'];

?>
<div class="page-heading">   

    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <!-- Form thêm/sửa -->
            <div class="col-md-6 col-12">
                <div class="card shadow border-0">
				<div class="card-header">
					<h5 class="text-primary fw-bold mb-0">
						<i class="bi bi-building me-2"></i>
						<?= $idEdit ? 'Chỉnh sửa phòng ban' : 'Thêm phòng ban mới' ?>
					</h5>
				</div>
                    <div class="card-body">
                        <form id="formPhongban" class="validate-tooltip" method="post" action="action/phong-ban-action.php">
                            <input type="hidden" name="id" value="<?= $phongbanInfo['id'] ?? '' ?>">

                            <div class="mb-3">
                                <label>Mã phòng ban</label>
                                <input type="text" name="ma_bp" class="form-control" value="<?= $phongbanInfo['ma_bp'] ?? $roomCode ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Tên phòng ban <span class="text-danger">*</span></label>
                                <input type="text" name="ten_bp" class="form-control" required value="<?= $phongbanInfo['ten_bp'] ?? '' ?>">
                            </div>

                            <div class="mb-3">
                                <label>Mô tả</label>
                                <input type="text" name="mota" class="form-control" value="<?= $phongbanInfo['mota'] ?? '' ?>">
                            </div>

                            <div class="mb-3">
                                <label>Người tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $phongbanInfo['nguoitao_name'] ?? ($row_acc['ho'] . ' ' . $row_acc['ten']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Ngày tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $phongbanInfo['ngaytao'] ?? date('Y-m-d') ?>">
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <?php if (isset($_GET['idEdit']) && !empty($phongbanInfo)): ?>
                                    <button type="submit" name="update" class="btn btn-primary me-2">
                                        <i class="bi bi-save"></i> Cập nhật phòng ban
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="add" class="btn btn-success me-2">
                                        <i class="bi bi-plus-circle"></i> Thêm mới phòng ban
                                    </button>
                                <?php endif; ?>
                                <a href="phong-ban.php" class="btn btn-light">Làm mới</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Danh sách -->
            <div class="col-md-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">📋 Danh sách phòng ban</h5>
                        <table class="table table-hover text-center" id="tablePhongban">
                            <thead class="table-light">
                                <tr>
									<th>STT</th>
                                    <th>Mã Phòng</th>
                                    <th>Tên Phòng</th>
                                    <th>Người tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach ($arrShow as $pb): ?>
                                <tr>
									<td class="text-start"><?= htmlspecialchars($i) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($pb['ma_bp']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($pb['ten_bp']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($pb['nguoitao_name']) ?></td>
                                    <td>
                                        <a href="phong-ban.php?idEdit=<?= $pb['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-id="<?= $pb['id'] ?>" data-name="<?= htmlspecialchars($pb['ten_bp'], ENT_QUOTES) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php $i++; endforeach; ?>
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
    const table = document.querySelector("#tablePhongban");
    if (table) new simpleDatatables.DataTable(table);

    // 2. LOGIC XÓA (SỬ DỤNG EVENT DELEGATION)
    // Lắng nghe sự kiện click trên toàn bộ tài liệu
    document.addEventListener('click', function(e) {
        // Sử dụng .closest() để tìm nút có class .btn-delete
        const btnDelete = e.target.closest('.btn-delete');
        
        if (btnDelete) {
            e.preventDefault(); // Ngăn hành động mặc định của thẻ <a> (nếu có, dù ở đây là <button>)
            
            const id = btnDelete.dataset.id;
            const name = btnDelete.dataset.name;
            
            // Kiểm tra Swal trước khi sử dụng
            if (typeof Swal === 'undefined') {
                if (confirm(`Bạn có chắc chắn muốn xóa phòng ban "${name}" không?`)) {
                    window.location.href = `action/phong-ban-action.php?delete=${id}`;
                }
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa phòng ban "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có xóa",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/phong-ban-action.php?delete=${id}`;
                }
            });
        }
    });
});
</script>

<?php include('./layouts/footer.php'); ?>
