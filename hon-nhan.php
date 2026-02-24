<?php 
	include('./layouts/header.php');
	include(__DIR__ . '/connection/config.php');
	include(__DIR__ . '/models/HonNhan.php');



	$database = new Database();
	$conn = $database->getConnection();
	$honnhan = new HonNhan($conn);

	// 🔹 Danh sách
	$stmt = $honnhan->getAll();
	$arrShow = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// 🔹 Lấy chi tiết
	$honnhanInfo = null;
	if (isset($_GET['idEdit'])) {
		$idEdit = intval($_GET['idEdit']);
		$honnhanInfo = $honnhan->getById($idEdit);
	}

	$hnCode = "MHN" . time();
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
                            <?= isset($honnhanInfo) ? 'Chỉnh sửa tình trạng hôn nhân' : 'Thêm mới tình trạng hôn nhân' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="action/hon-nhan-action.php">
                            <input type="hidden" name="id" value="<?= $honnhanInfo['id'] ?? '' ?>">

                            <div class="mb-3">
                                <label>Mã tình trạng</label>
                                <input type="text" class="form-control" name="ma_hn"
                                       value="<?= $honnhanInfo['ma_hn'] ?? $hnCode ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Tên tình trạng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ten_hn"
                                       value="<?= $honnhanInfo['ten_hn'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Người tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $honnhanInfo['nguoitao_name'] ?? ($row_acc['ho'].' '.$row_acc['ten']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Ngày tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $honnhanInfo['ngaytao'] ?? date('Y-m-d') ?>">
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <?php if (!empty($honnhanInfo)): ?>
                                    <button type="submit" name="update" class="btn btn-primary me-2"><i class="bi bi-save"></i> Cập nhật hôn nhân</button>
                                <?php else: ?>
                                    <button type="submit" name="add" class="btn btn-success me-2"><i class="bi bi-plus-circle"></i> Thêm mới hôn nhân</button>
                                <?php endif; ?>
                                <a href="hon-nhan.php" class="btn btn-light">Làm mới</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DANH SÁCH -->
            <div class="col-md-6 col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">📋 Danh sách tình trạng hôn nhân</h5>
                        <table class="table table-hover text-center" id="tableHonNhan">
                            <thead class="table-light">
                                <tr>
									<th>STT</th>
                                    <th>Mã</th>
                                    <th>Tên tình trạng</th>
                                    <th>Người tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i =1; foreach ($arrShow as $hn): ?>
                                <tr>
                                    <td class="text-start"><?= htmlspecialchars($i) ?></td>
									<td class="text-start"><?= htmlspecialchars($hn['ma_hn']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($hn['ten_hn']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($hn['nguoitao_name']) ?></td>
                                    <td class="text-start">
                                        <a href="hon-nhan.php?idEdit=<?= $hn['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-id="<?= $hn['id'] ?>" data-name="<?= htmlspecialchars($hn['ten_hn'], ENT_QUOTES) ?>">
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
    const table = document.querySelector("#tableHonNhan");
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
                if (confirm(`Bạn có chắc chắn muốn xóa tình trạng hôn nhân "${name}" không?`)) {
                    window.location.href = `action/hon-nhan-action.php?delete=${id}`;
                }
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa tình trạng hôn nhân "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có xóa",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/hon-nhan-action.php?delete=${id}`;
                }
            });
        }
    });
});
</script>

<?php include('./layouts/footer.php'); ?>
