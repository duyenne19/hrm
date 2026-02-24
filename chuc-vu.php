<?php 
	include('./layouts/header.php');
	include(__DIR__ . '/connection/config.php');
	include(__DIR__ . '/models/ChucVu.php');

	$database = new Database();
	$conn = $database->getConnection();
	$chucvu = new ChucVu($conn);

	// Danh sách
	$stmt = $chucvu->getAll();
	$arrShow = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Lấy chi tiết để sửa
	$idEdit = null;
	$chucvuInfo = null;
	if (isset($_GET['idEdit'])) {
		$idEdit = intval($_GET['idEdit']);
		$chucvuInfo = $chucvu->getById($idEdit);
	}


$roomCode = "MCV" . time();
$row_acc = $_SESSION['user'];
?>

<div class="page-heading">
	<section id="basic-vertical-layouts">
		<div class="row match-height">
			<!-- Form thêm / sửa -->
			<div class="col-md-6 col-12">
				<div class="card shadow-sm">
					<div class="card-header">
						<h5 class="text-primary fw-bold mb-0">
							<i class="bi bi-person-plus-fill me-2"></i>
							<?= isset($idEdit) ? 'Chỉnh sửa chức vụ' : 'Thêm chức vụ mới' ?>
						</h5>
					</div>
					<div class="card-body">
						<form id="formChucvu" class="validate-tooltip" method="post" action="action/chuc-vu-action.php">
							<input type="hidden" name="id" value="<?= $chucvuInfo['id'] ?? '' ?>">

							<div class="mb-3">
								<label>Mã chức vụ</label>
								<input type="text" name="macv" class="form-control" value="<?= $chucvuInfo['macv'] ?? $roomCode ?>" readonly>
							</div>

							<div class="mb-3">
								<label>Tên chức vụ <span class="text-danger">*</span></label>
								<input type="text" name="tencv" class="form-control" required value="<?= $chucvuInfo['tencv'] ?? '' ?>">
							</div>
							<div class="mb-3">
								<label>Lương cơ bản <span class="text-danger">*</span></label>
								<input type="text" name="luong_coban" class="form-control" id="nhap_tien" oninput="formatCurrency(this)" required value="<?= $chucvuInfo['luong_coban'] ?? '' ?>">
							</div>
							<div class="mb-3">
								<label>Hệ số lương <span class="text-danger">*</span></label>
								<input type="text" name="he_so_luong" class="form-control" id="nhap_he_so" oninput="formatDecimal(this)" required value="<?= $chucvuInfo['he_so_luong'] ?? '' ?>">
							</div>
							<div class="mb-3">
								<label>Hệ số phụ cấp <span class="text-danger">*</span></label>
								<input type="text" name="he_so_phu_cap" class="form-control" id="nhap_he_so" oninput="formatDecimal(this)" required value="<?= $chucvuInfo['he_so_phu_cap'] ?? '' ?>">
							</div>
							<div class="mb-3">
								<label>Mô tả</label>
								<input type="text" name="mota" class="form-control" value="<?= $chucvuInfo['mota'] ?? '' ?>">
							</div>

							<div class="mb-3">
								<label>Người tạo</label>
								<input type="text" class="form-control" readonly
									value="<?= $chucvuInfo['nguoitao_name'] ?? ($row_acc['ho'].' '.$row_acc['ten']) ?>">
							</div>

							<div class="mb-3">
								<label>Ngày tạo</label>
								<input type="text" class="form-control" readonly
									value="<?= $chucvuInfo['ngaytao'] ?? date('Y-m-d') ?>">
							</div>

							<div class="d-flex justify-content-end mt-4">
								<?php if (isset($_GET['idEdit']) && !empty($chucvuInfo)): ?>
									<button type="submit" name="update" class="btn btn-primary me-2">
										<i class="bi bi-save"></i> Cập nhật chức vụ
									</button>
								<?php else: ?>
									<button type="submit" name="add" class="btn btn-success me-2">
										<i class="bi bi-plus-circle"></i> Thêm mới chức vụ
									</button>
								<?php endif; ?>
								<a href="chuc-vu.php" class="btn btn-light">Làm mới</a>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- Danh sách -->
			<div class="col-md-6 col-12">
				<div class="card shadow-sm">
					<div class="card-body">
						<h5 class="fw-bold text-primary mb-3">📋 Danh sách chức vụ</h5>
						<table class="table table-hover text-center" id="tableChucvu">
							<thead class="table-light">
								<tr>
									<th>STT</th>
									<th>Mã CV</th>
									<th>Tên chức vụ</th>
									<th>Người tạo</th>
									<th>Hành động</th>
								</tr>
							</thead>
							<tbody>
								<?php $stt = 1; foreach ($arrShow as $cv): ?>
								<tr>
									<td class="text-start"><?= htmlspecialchars($stt) ?></td>
									<td class="text-start"><?= htmlspecialchars($cv['macv']) ?></td>
									<td class="text-start"><?= htmlspecialchars($cv['tencv']) ?></td>
									<td class="text-start"><?= htmlspecialchars($cv['nguoitao_name']) ?></td>
									<td>
										<a href="chuc-vu.php?idEdit=<?= $cv['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
											<i class="bi bi-pencil"></i>
										</a>
										<button type="button" class="btn btn-sm btn-outline-danger btn-delete"
												data-id="<?= $cv['id'] ?>" data-name="<?= htmlspecialchars($cv['tencv'], ENT_QUOTES) ?>">
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
document.addEventListener('DOMContentLoaded', function () {
    // 1. Khởi tạo Datatable
	const table = document.querySelector("#tableChucvu");
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
                if (confirm(`Bạn có chắc chắn muốn xóa chức vụ "${name}" không?`)) {
                    window.location.href = `action/chuc-vu-action.php?delete=${id}`;
                }
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa chức vụ "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
				confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có xóa",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/chuc-vu-action.php?delete=${id}`;
                }
            });
        }
	});
});
</script>
<script src="assets/js/format_currency.js"></script>
<?php include('./layouts/footer.php'); ?>
