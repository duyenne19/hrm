<?php 
include('./layouts/header.php');
include(__DIR__ . '/connection/config.php');
include(__DIR__ . '/models/LoaiNhanVien.php');
$database = new Database();
$conn = $database->getConnection();
$loaiNV = new LoaiNhanVien($conn);

// Danh sách
$stmt = $loaiNV->getAll();
$arrShow = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy chi tiết
$lnvInfo = null;
if (isset($_GET['idEdit'])) {
    $idEdit = intval($_GET['idEdit']);
    $lnvInfo = $loaiNV->getById($idEdit);
}


$lnvCode = "MLNV" . time();
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
							<i class="bi bi-tags me-2"></i>
                            <?= isset($lnvInfo) ? 'Chỉnh sửa loại nhân viên' : 'Thêm mới loại nhân viên' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="action/loai-nhanvien-action.php">
                            <input type="hidden" name="id" value="<?= $lnvInfo['id'] ?? '' ?>">

                            <div class="mb-3">
                                <label>Mã loại nhân viên</label>
                                <input type="text" class="form-control" name="ma_lnv"
                                       value="<?= $lnvInfo['ma_lnv'] ?? $lnvCode ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label>Tên loại nhân viên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ten_lnv"
                                       value="<?= $lnvInfo['ten_lnv'] ?? '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Mô tả</label>
                                <input type="text" class="form-control" name="mota"
                                       value="<?= $lnvInfo['mota'] ?? '' ?>">
                            </div>

                            <div class="mb-3">
                                <label>Người tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $lnvInfo['nguoitao_name'] ?? ($row_acc['ho'].' '.$row_acc['ten']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Ngày tạo</label>
                                <input type="text" class="form-control" readonly
                                       value="<?= $lnvInfo['ngaytao'] ?? date('Y-m-d') ?>">
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <?php if (!empty($lnvInfo)): ?>
                                    <button type="submit" name="update" class="btn btn-primary me-2"><i class="bi bi-save"></i> Cập nhật loại nhân viên</button>
                                <?php else: ?>
                                    <button type="submit" name="add" class="btn btn-success me-2"><i class="bi bi-plus-circle"></i> Thêm mới loại nhân viên</button>
                                <?php endif; ?>
                                <a href="loai-nhanvien.php" class="btn btn-light">Làm mới</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- DANH SÁCH -->
            <div class="col-md-6 col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">📋 Danh sách loại nhân viên</h5>
                        <table class="table table-hover text-center" id="tableLoaiNV">
                            <thead class="table-light">
                                <tr>
									<th>STT</th>
                                    <th>Mã</th>
                                    <th>Tên loại</th>
                                    <th>Người tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach ($arrShow as $lnv): ?>
                                <tr>
									<td class="text-start"><?= htmlspecialchars($i) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($lnv['ma_lnv']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($lnv['ten_lnv']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($lnv['nguoitao_name']) ?></td>
                                    <td>
                                        <a href="loai-nhanvien.php?idEdit=<?= $lnv['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                                data-id="<?= $lnv['id'] ?>" data-name="<?= htmlspecialchars($lnv['ten_lnv'], ENT_QUOTES) ?>">
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
    const table = document.querySelector("#tableLoaiNV");
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
                if (confirm(`Bạn có chắc chắn muốn xóa loại nhân viên "${name}" không?`)) {
                    window.location.href = `action/loai-nhanvien-action.php?delete=${id}`;
                }
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa loại nhân viên "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có, xóa!",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/loai-nhanvien-action.php?delete=${id}`;
                }
            });
        }
	});
});
</script>

<?php include('./layouts/footer.php'); ?>
