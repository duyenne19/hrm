<?php 
	include('./layouts/header.php');
	include(__DIR__ . '/connection/config.php');	
	include(__DIR__ . '/models/NhanVien.php');
	include(__DIR__ . '/models/CongTac.php');

	$ma_ctac = "MCT" . time();
	$row_acc = $_SESSION['user'] ?? [];
	// Danh sách nhân viên cho select box
	$database = new Database();
	$conn = $database->getConnection();
	$nhanvienModel = new NhanVien($conn);
	$ds_nv = $nhanvienModel->getAllNV_danglam()->fetchAll(PDO::FETCH_ASSOC);
	// Lấy chi tiết để sửa
	$nhom = new CongTac($conn);
	$nhomInfo = null;
	$idEdit = $_GET['idEdit'] ?? null;
	if ($idEdit) {
		$nhomInfo = $nhom->getById((int)$idEdit);
	}

?>

<div class="page-heading">
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="text-primary fw-bold mb-0">
                            <i class="bi bi-person-plus-fill me-2"></i>
                            <?= isset($nhomInfo) ? 'Chỉnh sửa công tác' : 'Thêm công tác mới' ?>
                        </h5>
                       
                    </div>

                    <div class="card-body">
                        <form method="post" action="action/cong-tac-action.php" class="validate-tooltip">
                            <input type="hidden" name="id" value="<?= $nhomInfo['id'] ?? '' ?>">

                            <!-- Mã công tác -->
                            <div class="mb-3">
                                <label for="ma_ctac" class="form-label">Mã công tác</label>
                                <input type="text" id="ma_ctac" name="ma_ctac" class="form-control" 
                                       value="<?= $nhomInfo['ma_ctac'] ?? $ma_ctac ?>" readonly>
                            </div>

                            <!-- Nhân viên -->
                            <div class="mb-3">
                                <label for="id_nv" class="form-label">Nhân viên <span class="text-danger">*</span></label>
                                <select name="id_nv" id="id_nv" class="form-select" required>
                                    <option value="">-- Chọn nhân viên --</option>
                                    <?php foreach ($ds_nv as $nv): ?>
                                        <option value="<?= $nv['id'] ?>" <?= ($nhomInfo['id_nv'] ?? '') == $nv['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($nv['ma_nv'].' - '.$nv['hoten'].' - '.$nv['phongban'].' - '.$nv['chucvu']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Địa điểm -->
                            <div class="mb-3">
                                <label for="dd_ctac" class="form-label">Địa điểm công tác <span class="text-danger">*</span></label>
                                <input type="text" id="dd_ctac" name="dd_ctac" class="form-control" 
                                       value="<?= htmlspecialchars($nhomInfo['dd_ctac'] ?? '') ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="bdau_ctac" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                    <input type="date" id="bdau_ctac" name="bdau_ctac" class="form-control" 
                                           value="<?= $nhomInfo['bdau_ctac'] ?? date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="kthuc_ctac" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                    <input type="date" id="kthuc_ctac" name="kthuc_ctac" class="form-control" 
                                           value="<?= $nhomInfo['kthuc_ctac'] ?? date('Y-m-d') ?>" required>
                                </div>
                            </div>

                            <!-- Mục đích -->
                            <div class="mb-3">
                                <label for="mucdich_ctac" class="form-label">Mục đích công tác <span class="text-danger">*</span></label>
                                <textarea id="mucdich_ctac" name="mucdich_ctac" rows="5" class="form-control" required><?= htmlspecialchars($nhomInfo['mucdich_ctac'] ?? '') ?></textarea>
                            </div>

                            <!-- Người tạo -->
                            <div class="mb-3">
                                <label class="form-label">Người tạo</label>
                                <input type="text" class="form-control" readonly 
                                       value="<?= $nhomInfo['nguoitao_name'] ?? (($row_acc['ho'] ?? '') . ' ' . ($row_acc['ten'] ?? '')) ?>">
                            </div>

                            <!-- Ngày tạo -->
                            <div class="mb-3">
                                <label class="form-label">Ngày tạo</label>
                                <input type="text" class="form-control" readonly 
                                       value="<?= $nhomInfo['ngaytao'] ?? date('Y-m-d') ?>">
                            </div>
								
                            <div class="d-flex justify-content-end mt-4" >
                                <?php if (!empty($nhomInfo)): ?>
                                    <button type="submit" name="update" class="btn btn-primary me-2">
                                        <i class="bi bi-save"></i> Cập nhật công tác
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="add" class="btn btn-success me-2">
                                        <i class="bi bi-plus-circle"></i> Thêm mới công tác
                                    </button>
                                <?php endif; ?>
                                <a href="add-nhom-cong-tac.php" class="btn btn-light">Làm mới</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lấy form và các trường input cần thiết
    const form = document.querySelector('.validate-tooltip');
    const startDateInput = document.getElementById('bdau_ctac');
    const endDateInput = document.getElementById('kthuc_ctac');
    
    // Nếu không tìm thấy form, dừng lại để tránh lỗi
    if (!form) return; 

    // Lắng nghe sự kiện submit form
    form.addEventListener('submit', function(event) {
        
        // --- Logic kiểm tra ngày tháng ---
        
        // 1. Lấy giá trị ngày tháng
        const startDateValue = startDateInput.value;
        const endDateValue = endDateInput.value;

        // 2. Chuyển đổi sang đối tượng Date để so sánh
        const startDate = new Date(startDateValue);
        const endDate = new Date(endDateValue);
        
        // Lấy ngày hiện tại (chỉ để đảm bảo người dùng đã nhập ngày hợp lệ)
        if (!startDateValue || !endDateValue) {
            // Nếu dùng thuộc tính required của HTML5, phần này thường không cần thiết
            return;
        }

        // 3. Thực hiện kiểm tra: Ngày kết thúc < Ngày bắt đầu
        if (endDate < startDate) {
            
            // Ngăn chặn form submit
            event.preventDefault();
            event.stopPropagation();

            // Gọi hàm showError từ alert-handler.js để hiển thị thông báo SweetAlert2
            // Hàm showError phải có sẵn trong phạm vi toàn cục (global scope)
            if (typeof showError === 'function') {
                showError("Ngày bắt đầu công tác phải trước hoặc bằng ngày kết thúc công tác.");
            } else {
                alert("Lỗi: Ngày kết thúc phải sau hoặc bằng Ngày bắt đầu.");
            }
            
            // Tùy chọn: Đặt lại tiêu điểm (focus) vào trường Ngày kết thúc
            endDateInput.focus();
        }
        
        // Nếu validation thành công, form sẽ được gửi đi bình thường.
    });
	
	// Tạo search cho selection ***********************************
	new Choices('#id_nv', {
        searchEnabled: true,
        itemSelectText: '',   // tắt chữ "Press to select"
        shouldSort: false     // giữ nguyên thứ tự
    });
	// ***********************************
});
</script>
<?php include('./layouts/footer.php'); ?>
