<?php 
	include('./layouts/header.php');
	
	include(__DIR__ . '/connection/config.php');
	include(__DIR__ . '/models/DanToc.php');



	$database = new Database();
	$conn = $database->getConnection();
	$dantoc = new DanToc($conn);

	// 🔹 Lấy danh sách
	$stmt = $dantoc->getAll();
	$arrShow = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// 🔹 Lấy chi tiết
	$dantocInfo = null;
	if (isset($_GET['idEdit'])) {
		$idEdit = intval($_GET['idEdit']);
		$dantocInfo = $dantoc->getById($idEdit);
	}
	$dtCode = "MDT" . time();
	$row_acc = $_SESSION['user'];
?>
<div class="page-heading">
    <section id="basic-vertical-layouts">
        <div class="row match-height">

            <!-- FORM -->
            <div class="col-md-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-header">
                        <h5 class="text-primary fw-bold mb-0">
						<i class="bi bi-people me-2"></i>
                            <?= isset($dantocInfo) ? 'Chỉnh sửa dân tộc' : 'Thêm dân tộc mới' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="action/dan-toc-action.php">
                            <input type="hidden" name="id" value="<?= $dantocInfo['id'] ?? '' ?>">

                            <div class="mb-3">
                                <label>Mã dân tộc</label>
                                <input type="text" class="form-control" name="ma_dt"
                                       value="<?= $dantocInfo['ma_dt'] ?? $dtCode ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Tên dân tộc <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ten_dt"
                                       value="<?= $dantocInfo['ten_dt'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Người tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $dantocInfo['nguoitao_name'] ?? ($row_acc['ho'].' '.$row_acc['ten']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Ngày tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $dantocInfo['ngaytao'] ?? date('Y-m-d') ?>">
                            </div>

                            <div class="d-flex justify-content-end">
                                <?php if (!empty($dantocInfo)): ?>
                                    <button type="submit" name="update" class="btn btn-primary me-2">
                                        <i class="bi bi-save"></i> Cập nhật dân tộc
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="add" class="btn btn-success me-2">
                                        <i class="bi bi-plus-circle"></i> Thêm mới dân tộc
                                    </button>
                                <?php endif; ?>
                                <a href="dan-toc.php" class="btn btn-light">Làm mới</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DANH SÁCH -->
            <div class="col-md-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">📋 Danh sách dân tộc</h5>
                        <table class="table table-hover text-center" id="tableDanToc">
                            <thead class="table-light">
                                <tr>
                                    <th>STT</th>
									<th>Mã DT</th>
                                    <th>Tên dân tộc</th>
                                    <th>Người tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $stt =1; foreach ($arrShow as $dt): ?>
                                <tr>
									<td class="text-start"><?= htmlspecialchars($stt) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($dt['ma_dt']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($dt['ten_dt']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($dt['nguoitao_name']) ?></td>
                                    <td>
                                        <a href="dan-toc.php?idEdit=<?= $dt['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-id="<?= $dt['id'] ?>" data-name="<?= htmlspecialchars($dt['ten_dt'], ENT_QUOTES) ?>">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Khởi tạo Datatable
    const table = document.querySelector("#tableDanToc");
	if (table) new simpleDatatables.DataTable(table);

    // 2. LOGIC XÓA (SỬ DỤNG EVENT DELEGATION)
    // Lắng nghe sự kiện click trên toàn bộ tài liệu (document)
	document.addEventListener('click', function(e) {
        // Sử dụng .closest() để tìm nút có class .btn-delete
        const btnDelete = e.target.closest('.btn-delete');
        
        if (btnDelete) {
            e.preventDefault();
            
            const id = btnDelete.dataset.id;
			const name = btnDelete.dataset.name;
			
            // Kiểm tra Swal trước khi sử dụng
            if (typeof Swal === 'undefined') {
                if (confirm(`Bạn có chắc muốn xóa dân tộc "${name}" không?`)) {
                    window.location.href = `action/dan-toc-action.php?delete=${id}`;
                }
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc muốn xóa dân tộc "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có, xóa",
                cancelButtonText: "Không",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/dan-toc-action.php?delete=${id}`;
                }
            });
        }
    });
});
</script>

<?php include('./layouts/footer.php'); ?>
