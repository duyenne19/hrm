<?php 
	include('./layouts/header.php');
	include(__DIR__ . '/connection/config.php');
	include(__DIR__ . '/models/TonGiao.php');

	$database = new Database();
	$conn = $database->getConnection();
	$tongiao = new TonGiao($conn);

	// 🔹 Lấy danh sách
	$stmt = $tongiao->getAll();
	$arrShow = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// 🔹 Lấy chi tiết khi sửa
	$tongiaoInfo = null;
	if (isset($_GET['idEdit'])) {
		$idEdit = intval($_GET['idEdit']);
		$tongiaoInfo = $tongiao->getById($idEdit);
	}

	$tgCode = "MTG" . time();
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
                            <?= isset($tongiaoInfo) ? 'Chỉnh sửa tôn giáo' : 'Thêm tôn giáo mới' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="action/ton-giao-action.php" class="validate-tooltip">
                            <input type="hidden" name="id" value="<?= $tongiaoInfo['id'] ?? '' ?>">

                            <div class="mb-3">
                                <label>Mã tôn giáo</label>
                                <input type="text" class="form-control" name="ma_tg"
                                       value="<?= $tongiaoInfo['ma_tg'] ?? $tgCode ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Tên tôn giáo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ten_tg"
                                       value="<?= $tongiaoInfo['ten_tg'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Người tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $tongiaoInfo['nguoitao_name'] ?? ($row_acc['ho'].' '.$row_acc['ten']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Ngày tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $tongiaoInfo['ngaytao'] ?? date('Y-m-d') ?>">
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <?php if (!empty($tongiaoInfo)): ?>
                                    <button type="submit" name="update" class="btn btn-primary me-2">
                                        <i class="bi bi-save"></i> Cập nhật tôn giáo
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="add" class="btn btn-success me-2">
                                        <i class="bi bi-plus-circle"></i> Thêm mới tôn giáo
                                    </button>
                                <?php endif; ?>
                                <a href="ton-giao.php" class="btn btn-light">Làm mới</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DANH SÁCH -->
            <div class="col-md-6 col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">📋 Danh sách tôn giáo</h5>
                        <table class="table table-hover text-center" id="tableTonGiao">
                            <thead class="table-light">
                                <tr>
									<th>STT</th>
                                    <th>Mã tôn giáo</th>
                                    <th>Tên tôn giáo</th>
                                    <th>Người tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach ($arrShow as $tg): ?>
                                <tr>
									<td class="text-start"><?= htmlspecialchars($i) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($tg['ma_tg']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($tg['ten_tg']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($tg['nguoitao_name']) ?></td>
                                    <td>
                                        <a href="ton-giao.php?idEdit=<?= $tg['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-id="<?= $tg['id'] ?>" data-name="<?= htmlspecialchars($tg['ten_tg'], ENT_QUOTES) ?>">
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
    const table = document.querySelector("#tableTonGiao");
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
                if (confirm(`Bạn có chắc chắn muốn xóa tôn giáo "${name}" không?`)) {
                    window.location.href = `action/ton-giao-action.php?delete=${id}`;
                }
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa tôn giáo "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có xóa",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/ton-giao-action.php?delete=${id}`;
                }
            });
        }
    });

    
});
</script>
<?php include('./layouts/footer.php'); ?>
