<?php 
include('./layouts/header.php');
include(__DIR__ . '/connection/config.php');
include(__DIR__ . '/models/CongTac.php');

$database = new Database();
$conn = $database->getConnection();
$nhom = new CongTac($conn);

// Lấy danh sách công tác
$stmt = $nhom->getAll();
$arrShow = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<div class="page-heading">
	<section id="basic-vertical-layouts">
		<div class="card shadow border-0">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="fw-bold text-primary mb-0">
						<i class="bi bi-people-fill me-2 text-success"></i>Danh sách  công tác
					</h4>
					<a href="them-cong-tac.php" class="btn btn-success btn-sm">
						<i class="bi bi-plus-circle me-1"></i> Thêm công tác
					</a>
				</div>

				<table class="table table-hover align-middle" id="tableCongTac">
					<thead class="bg-light text-primary fw-semibold">
						<tr class="text-center">
							<th class="text-start">STT</th>
							<th class="text-start">Mã công tác</th>
							<th class="text-start">Nhân viên</th>
							<th class="text-start">Địa điểm</th>
							<th class="text-start">Bắt đầu</th>
							<th class="text-start">Kết thúc</th>
							<th class="text-center">Hành động</th>
						</tr>
					</thead>
					<tbody>
						<?php $i = 1; foreach ($arrShow as $ct): ?>
						<tr>
							<td class="text-start"><?= htmlspecialchars($i) ?></td>
							<td class="text-start fw-semibold text-dark"><?= htmlspecialchars($ct['ma_ctac']) ?></td>
							<td class="text-start"><?= htmlspecialchars($ct['nhanvien_name']) ?></td>
							<td class="text-start"><?= htmlspecialchars($ct['dd_ctac']) ?></td>
							<td class="text-start"><?= htmlspecialchars($ct['bdau_ctac']) ?></td>
							<td class="text-start"><?= htmlspecialchars($ct['kthuc_ctac']) ?></td>
							<td class="text-center">
								<div class="d-flex justify-content-center gap-2">
									<!-- Nút xem -->
									<button type="button" class="btn btn-sm btn-outline-success btn-view"
										data-ma="<?= htmlspecialchars($ct['ma_ctac']) ?>"
										data-nv="<?= htmlspecialchars($ct['nhanvien_name']) ?>"
										data-dd="<?= htmlspecialchars($ct['dd_ctac']) ?>"
										data-bd="<?= htmlspecialchars($ct['bdau_ctac']) ?>"
										data-kt="<?= htmlspecialchars($ct['kthuc_ctac']) ?>"
										data-md="<?= htmlspecialchars($ct['mucdich_ctac']) ?>"
										data-nguoitao="<?= htmlspecialchars($ct['nguoitao_name']) ?>"
										data-ngaytao="<?= htmlspecialchars($ct['ngaytao']) ?>">
										<i class="bi bi-eye"></i>
									</button>

									<!-- Nút sửa -->
									<a href="them-cong-tac.php?idEdit=<?= $ct['id'] ?>" 
										class="btn btn-sm btn-outline-primary">
										<i class="bi bi-pencil"></i>
									</a>

									<!-- Nút xóa -->
									<button type="button" class="btn btn-sm btn-outline-danger btn-delete"
										data-id="<?= $ct['id'] ?>" 
										data-name="<?= htmlspecialchars($ct['ma_ctac'], ENT_QUOTES) ?>">
										<i class="bi bi-trash"></i>
									</button>
								</div>
							</td>
						</tr>
						<?php $i++; endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	// Kích hoạt DataTable
	new simpleDatatables.DataTable("#tableCongTac");

	// Gắn sự kiện CLICK cho toàn bộ document và ủy quyền cho các nút động
	document.addEventListener('click', function(e) {
		
		// 1. Xử lý nút XEM CHI TIẾT (btn-view)
		const btnView = e.target.closest('.btn-view');
		if (btnView) {
			e.preventDefault();
			
			const ma = btnView.dataset.ma;
			const nv = btnView.dataset.nv;
			const dd = btnView.dataset.dd;
			const bd = btnView.dataset.bd;
			const kt = btnView.dataset.kt;
			const md = btnView.dataset.md;
			const nguoitao = btnView.dataset.nguoitao;
			const ngaytao = btnView.dataset.ngaytao;

			// Kiểm tra Swal trước khi sử dụng
			if (typeof Swal === 'undefined') {
				alert(`Chi tiết công tác ${ma}: Nhân viên: ${nv}, Địa điểm: ${dd}`);
				return;
			}
			
			Swal.fire({
				title: `<h5 class="fw-bold text-primary mb-2">Chi tiết công tác: <span class="text-dark">${ma}</span></h5>`,
				html: `
					<div class="text-start mt-3">
						<p><i class="bi bi-person-circle text-primary"></i> <strong>Nhân viên:</strong> ${nv}</p>
						<p><i class="bi bi-geo-alt-fill text-danger"></i> <strong>Địa điểm:</strong> ${dd}</p>
						<p><i class="bi bi-calendar-event text-info"></i> <strong>Thời gian:</strong> ${bd} → ${kt}</p>
						<hr>
						<p class="fw-bold text-secondary"><i class="bi bi-journal-text me-1 text-warning"></i> Mục đích công tác:</p>
						<div class="p-2 bg-light border rounded text-dark" 
							 style="white-space: pre-line; font-size: 0.95rem; line-height: 1.5;">
							 ${md}
						</div>
						<hr>
						<p><i class="bi bi-person-fill text-purple"></i> <strong>Người tạo:</strong> ${nguoitao}</p>
						<p><i class="bi bi-calendar-check text-success"></i> <strong>Ngày tạo:</strong> ${ngaytao}</p>
					</div>
				`,
				width: 600,
				confirmButtonText: 'Đóng',
				confirmButtonColor: '#3085d6',
				showCloseButton: true
			});
		}

		// 2. Xử lý nút XÓA (btn-delete)
		const btnDelete = e.target.closest('.btn-delete');
		if (btnDelete) {
			e.preventDefault();
			
			const id = btnDelete.dataset.id;
			const name = btnDelete.dataset.name;
			
			// Xử lý SweetAlert2
			if (typeof Swal === 'undefined') {
				if (confirm(`Bạn có chắc chắn muốn xóa nhóm công tác "${name}" không?`)) {
					window.location.href = `action/cong-tac-action.php?delete=${id}`;
				}
				return;
			}
			
			Swal.fire({
				title: 'Xác nhận xóa?',
				text: `Bạn có chắc chắn muốn xóa nhóm công tác "${name}" không?`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: "#d33",
				cancelButtonColor: "#3085d6",
				confirmButtonText: "Có, xóa",
				cancelButtonText: "Hủy",
				reverseButtons: true
			}).then(result => {
				if (result.isConfirmed) {
					window.location.href = `action/cong-tac-action.php?delete=${id}`;
				}
			});
		}
	});
});
</script>
<?php include('./layouts/footer.php'); ?>
