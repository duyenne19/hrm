<?php


include('./connection/config.php');
include('./models/ChiTietNhom.php');
$database = new Database();
$conn = $database->getConnection();
$model = new ChiTietNhom($conn);
$id_nhom = (int)($_GET['id'] ?? 0);
if($id_nhom == 0){
		header("Location: nhom-vn.php");
		exit;
}
$nhomInfo = $model->getNhomInfo($id_nhom);
$listNV = $model->getMembersByGroup($id_nhom);
$availableNV = $model->getAvailableMembers($id_nhom);
include('./layouts/header.php');
?>

<div class="page-heading">
	<section id="basic-vertical-layouts">
		<div class="card shadow border-0">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="fw-bold text-primary mb-0">
						<i class="bi bi-people-fill me-2 text-success"></i>Nhóm: <?= htmlspecialchars($nhomInfo['tennhom']) ?>
					</h4>
					<button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
						<i class="bi bi-person-plus"></i> Thêm nhân viên vào nhóm
					</button>
				</div>
				<div class="d-flex justify-content-between align-items-center mt-2 mb-3">
					<?= htmlspecialchars($nhomInfo['mota']) ?>
				</div>
				<table class="table table-hover align-middle text-center" id="tableNV">
					<thead class="bg-light text-primary fw-semibold">
						<tr>
							<th>STT</th>
							<th>Mã nhân viên</th>
							<th>Ảnh</th>
							<th>Họ tên</th>
							<th>Giới tính</th>
							<th>Ngày sinh</th>
							<th>Chức vụ</th>
							<th>Phòng ban</th>
							<th>Ngày thêm</th>
							<th>Trạng thái</th>
							<th>Hành động</th>
						</tr>
					</thead>
					<tbody>
						<?php $stt =1 ; foreach ($listNV as $tv): ?>
							<tr>
								<td><?= htmlspecialchars($stt) ?></td>
								<td><?= htmlspecialchars($tv['ma_nv']) ?></td>
								<td>
									<img src="uploads/nhanvien/<?= htmlspecialchars($tv['anhdaidien'] ?: 'no-image.png') ?>" 
										 width="40" height="50" class="rounded border">
								</td>
								<td class="text-start"><?= htmlspecialchars($tv['hoten']) ?></td>
								<td class="text-start"><?= htmlspecialchars($tv['gtinh'])?></td>
								<td class="text-start"><?= htmlspecialchars($tv['ngsinh']) ?></td>
								<td class="text-start"><?= htmlspecialchars($tv['chucvu'] ?? '-') ?></td>
								<td class="text-start"><?= htmlspecialchars($tv['phongban'] ?? '-') ?></td>
								<td><?= htmlspecialchars($tv['ngaytao']) ?></td>
								<td>
									<span class="badge bg-<?= $tv['trangthai'] ? 'success' : 'secondary' ?>">
										<?= $tv['trangthai'] ? 'Đang làm' : 'Nghỉ' ?>
									</span>
								</td>
								<td>
									<button type="button" class="btn btn-sm btn-outline-danger btn-delete"
										data-id-nhom="<?= htmlspecialchars($id_nhom) ?>"
										data-id-nv="<?= htmlspecialchars($tv['id_nv']) ?>"
										data-name="<?= htmlspecialchars($tv['hoten']) ?>">
										<i class="bi bi-trash"></i>
									</button>
								</td>
							</tr>
						<?php $stt++; endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>
</div>

<!-- 🔹 Modal thêm nhân viên -->
<div class="modal fade" id="addMemberModal"  tabindex="-1" style="z-index: 9999;">
	<div class="modal-dialog modal-md" style="max-width: 625px;" >
		<div class="modal-content">
			<form method="POST" action="action/chi-tiet-nhom-action.php">
				<div class="modal-header text-white">
					<h5 class="modal-title">Thêm nhân viên vào nhóm</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="id_nhom" value="<?= $id_nhom ?>">

					<div class="mb-2"><strong>Mã nhóm:</strong> <?= htmlspecialchars($nhomInfo['manhom']) ?></div>
					<div class="mb-2"><strong>Tên nhóm:</strong> <?= htmlspecialchars($nhomInfo['tennhom']) ?></div>
					<div class="mb-2"><strong>Người tạo:</strong> <?= htmlspecialchars($nhomInfo['nguoitao_name']) ?></div>
					<div class="mb-3"><strong>Ngày tạo:</strong> <?= htmlspecialchars($nhomInfo['ngaytao']) ?></div>

					<label for="id_nv" class="form-label">Chọn nhân viên</label>
					<select name="id_nv" id="id_nv" class="form-select" required>
						<option value="">-- Chọn nhân viên --</option>
						<?php foreach ($availableNV as $nv): ?>
							<option value="<?= $nv['id'] ?>"><?= htmlspecialchars($nv['ma_nv'] . ' - ' . $nv['hoten']. ' - '. $nv['ten_bp']. ' - '. $nv['tencv']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="modal-footer">
					<button type="submit" name="addMember" class="btn btn-success">
						<i class="bi bi-save"></i> Thêm
					</button>
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
	// 1. Khởi tạo Datatable
	const table = document.querySelector("#tableNV");
    if (table) new simpleDatatables.DataTable(table);

	// 2. LOGIC XÓA THÀNH VIÊN (SỬ DỤNG EVENT DELEGATION)
    // Lắng nghe sự kiện click trên toàn bộ tài liệu (document)
	document.addEventListener('click', function(e) {
        // Sử dụng .closest() để tìm nút có class .btn-delete
		const btnDelete = e.target.closest('.btn-delete');
        
		if (btnDelete) {
            e.preventDefault();
			const idNhom = btnDelete.dataset.idNhom;
			const idNv = btnDelete.dataset.idNv;
			const name = btnDelete.dataset.name;

            // Kiểm tra Swal trước khi sử dụng
            if (typeof Swal === 'undefined') {
                if (confirm(`Bạn có chắc muốn xóa nhân viên "${name}" khỏi nhóm này không?`)) {
                    window.location.href = `action/chi-tiet-nhom-action.php?delete=1&id_nhom=${idNhom}&id_nv=${idNv}`;
                }
                return;
            }
            
			Swal.fire({
				title: 'Xác nhận xóa?',
				text: `Bạn có chắc muốn xóa nhân viên "${name}" khỏi nhóm này không?`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: "#d33",
				cancelButtonColor: "#3085d6",
				confirmButtonText: "Có, xóa!",
				cancelButtonText: "Hủy",
				reverseButtons: true
			}).then(result => {
				if (result.isConfirmed) {
					window.location.href = `action/chi-tiet-nhom-action.php?delete=1&id_nhom=${idNhom}&id_nv=${idNv}`;
				}
			});
		}
	});
	
	new Choices('#id_nv', {
        searchEnabled: true,
        itemSelectText: '',   // tắt chữ "Press to select"
        shouldSort: false     // giữ nguyên thứ tự
    });
});
</script>

<?php include('./layouts/footer.php'); ?>
