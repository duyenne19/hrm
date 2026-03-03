<?php 
	include('./layouts/header.php');

	include(__DIR__ . '/connection/config.php');
	include(__DIR__ . '/models/TrinhDo.php');



	$database = new Database();
	$conn = $database->getConnection();
	$trinhdo = new TrinhDo($conn);

	// Lấy danh sách
	$stmt = $trinhdo->getAll();
	$arrShow = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Lấy chi tiết khi sửa
	$trinhdoInfo = null;
	if (isset($_GET['idEdit'])) {
		$idEdit = intval($_GET['idEdit']);
		$trinhdoInfo = $trinhdo->getById($idEdit);
	}
	$tdCode = "MTD" . time();
	$row_acc = $_SESSION['user'];
?>

<div class="page-heading">
    <section id="basic-vertical-layouts">
        <div class="row match-height">

            <!-- Form thêm / sửa -->
            <div class="col-md-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-header">
                        <h5 class="text-primary fw-bold mb-0">
                            <i class="bi bi-award me-2"></i>
                            <?= isset($idEdit) ? 'Chỉnh sửa trình độ' : 'Thêm trình độ mới' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="formTrinhdo" class="validate-tooltip" method="post" action="action/trinh-do-action.php">
                            <input type="hidden" name="id" value="<?= $trinhdoInfo['id'] ?? '' ?>">

                            <div class="mb-3">
                                <label>Mã trình độ</label>
                                <input type="text" name="ma_td" class="form-control" value="<?= $trinhdoInfo['ma_td'] ?? $tdCode ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Tên trình độ <span class="text-danger">*</span></label>
                                <input type="text" name="ten_td" class="form-control" required value="<?= $trinhdoInfo['ten_td'] ?? '' ?>">
                            </div>

                          <div class="mb-3">
                                <label>Mô tả</label>
                                <input type="text" name="mota_td" class="form-control" value="<?= $trinhdoInfo['mota_td'] ?? '' ?>">
                            </div>

                            <div class="mb-3">
                                <label>Người tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $trinhdoInfo['nguoitao_name'] ?? ($row_acc['ho'] . ' ' . $row_acc['ten']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Ngày tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $trinhdoInfo['ngaytao'] ?? date('Y-m-d') ?>">
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <?php if (isset($_GET['idEdit']) && !empty($trinhdoInfo)): ?>
                                    <button type="submit" name="update" class="btn btn-primary me-2">
                                        <i class="bi bi-save"></i> Cập nhật trình độ
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="add" class="btn btn-success me-2">
                                        <i class="bi bi-plus-circle"></i> Thêm mới trình độ
                                    </button>
                                <?php endif; ?>
                                <a href="trinh-do.php" class="btn btn-light">Làm mới</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Danh sách -->
            <div class="col-md-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">📋 Danh sách trình độ</h5>
                        <table class="table table-hover text-center" id="tableTrinhdo">
                            <thead class="table-light">
                                <tr>
									<th>STT</th>
                                    <th>Mã trình độ</th>
                                    <th>Tên trình độ</th>
                                    <th>Người tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach ($arrShow as $td): ?>
                                <tr>
									<td class="text-start"><?= htmlspecialchars($i) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($td['ma_td']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($td['ten_td']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($td['nguoitao_name']) ?></td>
                                    <td class="text-start">
                                        <a href="trinh-do.php?idEdit=<?= $td['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-id="<?= $td['id'] ?>" data-name="<?= htmlspecialchars($td['ten_td'], ENT_QUOTES) ?>">
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
    const table = document.querySelector("#tableTrinhdo");
	if (table) new simpleDatatables.DataTable(table);

    // 2. LOGIC XÓA (SỬ DỤNG EVENT DELEGATION)
    // Lắng nghe sự kiện click trên toàn bộ tài liệu (document)
	document.addEventListener('click', function(e) {
        // Sử dụng .closest() để tìm nút có class .btn-delete mà sự kiện click diễn ra
        const btnDelete = e.target.closest('.btn-delete');
        
        if (btnDelete) {
            e.preventDefault();
            
            const id = btnDelete.dataset.id;
			const name = btnDelete.dataset.name;
			
            // Kiểm tra Swal trước khi sử dụng
            if (typeof Swal === 'undefined') {
                if (confirm(`Bạn có chắc chắn muốn xóa trình độ "${name}" không?`)) {
                    window.location.href = `action/trinh-do-action.php?delete=${id}`;
                }
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa trình độ "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có xóa",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/trinh-do-action.php?delete=${id}`;
                }
            });
        }
	});
});
</script>
<?php include('./layouts/footer.php'); ?>
