<?php
	include('./layouts/header.php');
	include(__DIR__ . '/connection/config.php');
	include(__DIR__ . '/models/NhomNV.php');
	$groupCode = "MNH" . time();
	$row_acc = $_SESSION['user'];
	$database = new Database();
	$conn = $database->getConnection();
	$nhomnv = new NhomNV($conn);

	// Danh sách
	$arrShow = $nhomnv->getAll()->fetchAll(PDO::FETCH_ASSOC);
	// Chi tiết khi sửa
	$idEdit = $_GET['idEdit'] ?? null;
	$nhomInfo = $idEdit ? $nhomnv->getById($idEdit) : null;
?>

<div class="page-heading">
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <!-- FORM -->
            <div class="col-md-4 col-12">
                <div class="card shadow-sm">
				 <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="text-primary fw-bold mb-0">
                            <i class="bi bi-person-plus-fill me-2"></i>
                            <?= isset($nhomInfo) ? 'Chỉnh sửa nhóm nhân viên' : 'Thêm nhóm nhân viên' ?>
                        </h5>
                       
                    </div>
                    <div class="card-body">
                        <form id="formNhomNV" class="validate-tooltip" method="post" action="action/nhom-nhan-vien-action.php">
                            <input type="hidden" name="id" value="<?= $nhomInfo['id'] ?? '' ?>">
                            <div class="mb-3">
                                <label>Mã nhóm</label>
                                <input type="text" name="manhom" class="form-control" readonly
                                    value="<?= $nhomInfo['manhom'] ?? $groupCode ?>">
                            </div>
                            <div class="mb-3">
                                <label>Tên nhóm <span class="text-danger">*</span></label>
                                <input type="text" name="tennhom" class="form-control" required
                                    value="<?= $nhomInfo['tennhom'] ?? '' ?>">
                            </div>
                            <div class="mb-3">
                                <label>Mô tả</label>
                                <input type="text" name="mota" class="form-control"
                                    value="<?= $nhomInfo['mota'] ?? '' ?>">
                            </div>
                            <div class="mb-3">
                                <label>Người tạo</label>
                                <input type="text" class="form-control" readonly
                                    value="<?= $nhomInfo['nguoitao_name'] ?? ($row_acc['ho'].' '.$row_acc['ten']) ?>">
                            </div>
                            <div class="mb-3">
                                <label>Ngày tạo</label>
                                <input type="text" class="form-control" readonly
                                    value="<?= $nhomInfo['ngaytao'] ?? date('Y-m-d') ?>">
                            </div>

                            <div class="d-flex justify-content-end mt-4">
							<!-- Nút xem -->
								
                                <?php if (isset($_GET['idEdit']) && !empty($nhomInfo)): ?>
                                    <button type="submit" name="update" class="btn btn-primary me-2">
                                        <i class="bi bi-save"></i> Cập nhật
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="add" class="btn btn-success me-2">
                                        <i class="bi bi-plus-circle"></i> Thêm mới
                                    </button>
                                <?php endif; ?>
                                <a href="nhom-nhan-vien.php" class="btn btn-light">Làm mới</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DANH SÁCH -->
            <div class="col-md-8 col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">📋 Danh sách nhóm nhân viên</h5>
                        <table class="table table-hover text-center" id="tableNhomNV">
                            <thead class="table-light">
                                <tr>
									<th>STT</th>
                                    <th>Mã nhóm</th>
                                    <th>Tên nhóm</th>
                                    <th>Người tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach ($arrShow as $row): ?>
                                <tr>
									<td class="text-start"><?= htmlspecialchars($i) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($row['manhom']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($row['tennhom']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($row['nguoitao_name']) ?></td>
                                    <td>
										<a href="ds-nhom-nhan-vien.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                        <a href="nhom-nhan-vien.php?idEdit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                            data-id="<?= $row['id'] ?>"
                                            data-name="<?= htmlspecialchars($row['tennhom'], ENT_QUOTES) ?>">
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

<!-- Xác nhận xóa -->
<script>
// Kích hoạt DataTable
	
	document.addEventListener('DOMContentLoaded', function () {
    let table = new simpleDatatables.DataTable("#tableNhomNV", {
        
    });
});
document.addEventListener('DOMContentLoaded', function () {
    // ⚠️ Loại bỏ document.querySelectorAll().forEach() cũ.
    // Thay vào đó, gắn sự kiện CLICK vào document.
    document.addEventListener('click', function(e) {
        
        // 1. Dùng .closest() để kiểm tra xem phần tử được click 
        // hoặc phần tử cha gần nhất có class '.btn-delete' hay không.
        const btn = e.target.closest('.btn-delete');

        // 2. Nếu tìm thấy nút .btn-delete, thì xử lý.
        if (btn) {
            e.preventDefault();
            
            // 3. Lấy data từ thuộc tính dataset của nút tìm được
            const id = btn.dataset.id;
            const name = btn.dataset.name;

            // 4. Kiểm tra thư viện Swal (phòng trường hợp chưa load)
            if (typeof Swal === 'undefined') {
                if (confirm(`Bạn có chắc chắn muốn xóa nhóm "${name}" không?`)) {
                    window.location.href = `action/nhom-nhan-vien-action.php?delete=${id}`;
                }
                return;
            }

            // 5. Hiển thị SweetAlert2
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa nhóm "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có xóa",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    // Chuyển hướng xử lý
                    window.location.href = `action/nhom-nhan-vien-action.php?delete=${id}`;
                }
            });
        }
    });
});
</script>


<?php include('./layouts/footer.php'); ?>
