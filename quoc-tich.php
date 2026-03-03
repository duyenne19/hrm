<?php 
	include('./layouts/header.php');
	include(__DIR__ . '/connection/config.php');
	include(__DIR__ . '/models/QuocTich.php');


	$database = new Database();
	$conn = $database->getConnection();
	$quoctich = new QuocTich($conn);

	// 🔹 Lấy danh sách
	$stmt = $quoctich->getAll();
	$arrShow = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// 🔹 Lấy chi tiết khi sửa
	$quoctichInfo = null;
	if (isset($_GET['idEdit'])) {
		$idEdit = intval($_GET['idEdit']);
		$quoctichInfo = $quoctich->getById($idEdit);
	}


	$qtCode = "MQT" . time();
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
							<i class="bi bi-globe me-2"></i>
                            <?= isset($quoctichInfo) ? 'Chỉnh sửa quốc tịch' : 'Thêm quốc tịch mới' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="action/quoc-tich-action.php" class="validate-tooltip">
                            <input type="hidden" name="id" value="<?= $quoctichInfo['id'] ?? '' ?>">

                            <div class="mb-3">
                                <label>Mã quốc tịch</label>
                                <input type="text" class="form-control" name="ma_qt"
                                       value="<?= $quoctichInfo['ma_qt'] ?? $qtCode ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Tên quốc tịch <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ten_qt" 
                                       value="<?= $quoctichInfo['ten_qt'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Người tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $quoctichInfo['nguoitao_name'] ?? ($row_acc['ho'].' '.$row_acc['ten']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Ngày tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $quoctichInfo['ngaytao'] ?? date('Y-m-d') ?>">
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <?php if (!empty($quoctichInfo)): ?>
                                    <button type="submit" name="update" class="btn btn-primary me-2">
                                        <i class="bi bi-save"></i> Cập nhật quốc tịch
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="add" class="btn btn-success me-2">
                                        <i class="bi bi-plus-circle"></i> Thêm mới quốc tịch
                                    </button>
                                <?php endif; ?>
                                <a href="quoc-tich.php" class="btn btn-light">Làm mới</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DANH SÁCH -->
            <div class="col-md-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">📋 Danh sách quốc tịch</h5>
                        <table class="table table-hover text-center" id="tableQuocTich">
                            <thead class="table-light">
                                <tr>
									<th>STT</th>
                                    <th>Mã quốc tịch</th>
                                    <th>Tên quốc tịch</th>
                                    <th>Người tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach ($arrShow as $qt): ?>
                                <tr>
									<td class="text-start"><?= htmlspecialchars($i) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($qt['ma_qt']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($qt['ten_qt']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($qt['nguoitao_name']) ?></td>
                                    <td>
                                        <a href="quoc-tich.php?idEdit=<?= $qt['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-id="<?= $qt['id'] ?>" data-name="<?= htmlspecialchars($qt['ten_qt'], ENT_QUOTES) ?>">
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
    const table = document.querySelector("#tableQuocTich");
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
                if (confirm(`Bạn có chắc chắn muốn xóa quốc tịch "${name}" không?`)) {
                    window.location.href = `action/quoc-tich-action.php?delete=${id}`;
                }
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa quốc tịch "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có xóa",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/quoc-tich-action.php?delete=${id}`;
                }
            });
        }
    });

    // Thêm logic hiển thị thông báo SweetAlert nếu có
    const params = new URLSearchParams(window.location.search);
    const msg = params.get('msg');
    const status = params.get('status');
    if (msg) {
        Swal.fire({
            title: 'Thông báo',
            text: msg,
            icon: status === 'success' ? 'success' : 'error',
            timer: 2000
        });
        window.history.replaceState(null, null, window.location.pathname);
    }
});
</script>

<?php include('./layouts/footer.php'); ?>
