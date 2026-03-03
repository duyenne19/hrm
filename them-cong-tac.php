<?php 
include('./layouts/header.php');
include(__DIR__ . '/connection/config.php');
include(__DIR__ . '/models/NhanVien.php');
include(__DIR__ . '/models/CongTac.php');

$ma_ctac = "MCT" . time();
$row_acc = $_SESSION['user'] ?? [];

// Check login
if (!isset($_SESSION['user'])) {
header("Location: login.php");
exit;
}

$database = new Database();
$conn = $database->getConnection();

    $nhanvienModel = new NhanVien($conn);
$ds_nv = $nhanvienModel->getAllNV_danglam()->fetchAll(PDO::FETCH_ASSOC);

    // Lấy chi tiết để sửa
$congTacModel = new CongTac($conn);
$ctInfo = null;
$idEdit = $_GET['idEdit'] ?? null;
$isEdit = false;

if ($idEdit) {
$ctInfo = $congTacModel->getById((int)$idEdit);
if ($ctInfo) {
$isEdit = true;
}
}
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Quản lý Công Tác</h3>
                <p class="text-subtitle text-muted">Thêm mới hoặc chỉnh sửa thông tin công tác của nhân viên.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="cong-tac.php">Danh sách công tác</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= $isEdit ? 'Chỉnh sửa' : 'Thêm mới' ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section id="basic-vertical-layouts">
        <div class="row match-height justify-content-center">
            <div class="col-md-8 col-12">
                <div class="card shadow border-0">
<div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="<?= $isEdit ? 'bi bi-pencil-square' : 'bi bi-plus-circle' ?> me-2"></i>
                            <?= $isEdit ? 'Chỉnh sửa thông tin công tác' : 'Thêm công tác mới' ?>
                        </h5>
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <form method="post" action="action/cong-tac-action.php" class="validate-tooltip" id="formCongTac">
                                <?php if($isEdit): ?>
<input type="hidden" name="id" value="<?= $ctInfo['id'] ?>">
<?php endif; ?>

                                <div class="form-body">
                                    <div class="row">
                                        <!-- Mã công tác (Chỉ cho phép sửa khi thêm mới) -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-3">
                                                <label for="ma_ctac" class="form-label fw-bold">Mã công tác <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text <?= $isEdit ? 'bg-light' : '' ?>"><i class="bi bi-upc-scan"></i></span>
                                                    <input type="text" id="ma_ctac" name="ma_ctac" 
                                                        class="form-control <?= $isEdit ? 'bg-light' : '' ?>" 
                                                        value="<?= $isEdit ? htmlspecialchars($ctInfo['ma_ctac']) : $ma_ctac ?>" 
                                                        required
                                                        <?= $isEdit ? 'readonly' : '' ?>>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Nhân viên (Select) -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-3">
                                                <label for="id_nv" class="form-label fw-bold">Nhân viên <span class="text-danger">*</span></label>
                                                <div class="choices-container">
<select name="id_nv" id="id_nv" class="form-select" required>
<option value="">-- Chọn nhân viên --</option>
<?php foreach ($ds_nv as $nv): ?>
<option value="<?= $nv['id'] ?>" <?= ($isEdit && $ctInfo['id_nv'] == $nv['id']) ? 'selected' : '' ?>>
<?= htmlspecialchars($nv['ma_nv'] . ' - ' . $nv['hoten']) ?>
</option>
<?php endforeach; ?>
</select>
</div>
                                            </div>
                                        </div>

<!-- Địa điểm -->
                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label for="dd_ctac" class="form-label fw-bold">Địa điểm công tác <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                                    <input type="text" id="dd_ctac" name="dd_ctac" class="form-control" 
                                                        placeholder="Nhập địa điểm công tác..."
                                                        value="<?= $isEdit ? htmlspecialchars($ctInfo['dd_ctac']) : '' ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Thời gian công tác -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-3">
                                                <label for="bdau_ctac" class="form-label fw-bold">Ngày bắt đầu <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                                    <input type="date" id="bdau_ctac" name="bdau_ctac" class="form-control" 
                                                        value="<?= $isEdit ? $ctInfo['bdau_ctac'] : date('Y-m-d') ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group mb-3">
                                                <label for="kthuc_ctac" class="form-label fw-bold">Ngày kết thúc <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                                    <input type="date" id="kthuc_ctac" name="kthuc_ctac" class="form-control" 
                                                        value="<?= $isEdit ? $ctInfo['kthuc_ctac'] : date('Y-m-d') ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Mục đích -->
                                        <div class="col-12">
                                            <div class="form-group mb-3">
                                                <label for="mucdich_ctac" class="form-label fw-bold">Mục đích công tác <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                                    <textarea id="mucdich_ctac" name="mucdich_ctac" rows="4" class="form-control" 
                                                        placeholder="Nhập mục đích công tác..." required><?= $isEdit ? htmlspecialchars($ctInfo['mucdich_ctac']) : '' ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Buttons -->
                                    <div class="col-12 d-flex justify-content-end mt-4">
                                        <a href="cong-tac.php" class="btn btn-secondary me-2 shadow-sm">
                                            <i class="bi bi-arrow-left me-1"></i> Quay lại
                                        </a>
                                        <?php if (!$isEdit): ?>
                                        <a href="them-cong-tac.php" class="btn btn-light-secondary me-2 shadow-sm">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Làm mới
                                        </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($isEdit): ?>
                                            <button type="submit" name="update" value="update" class="btn btn-primary shadow-sm">
                                                <i class="bi bi-save me-1"></i> Cập nhật
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="add" value="add" class="btn btn-success shadow-sm">
                                                <i class="bi bi-plus-lg me-1"></i> Thêm mới
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('./layouts/footer.php'); ?>

<!-- JavaScript Logic -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Validation Logic
        const form = document.querySelector('.validate-tooltip');
        const startDateInput = document.getElementById('bdau_ctac');
        const endDateInput = document.getElementById('kthuc_ctac');
        
        if (form) {
            form.addEventListener('submit', function(event) {
                const startDateValue = startDateInput.value;
                const endDateValue = endDateInput.value;

                if (!startDateValue || !endDateValue) return;

                const startDate = new Date(startDateValue);
                const endDate = new Date(endDateValue);
                
                if (endDate < startDate) {
                    event.preventDefault();
                    event.stopPropagation();

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi ngày tháng',
                            text: 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu',
                            confirmButtonText: 'Đã hiểu'
                        });
                    } else if (typeof showError === 'function') {
                        showError("Ngày bắt đầu công tác phải trước hoặc bằng ngày kết thúc công tác.");
                    } else {
                        alert("Lỗi: Ngày kết thúc phải sau hoặc bằng Ngày bắt đầu.");
                    }
                    
                    endDateInput.focus();
                }
            });
        }
        
        // 2. Init Choices.js for Employee Selection
        const choicesEl = document.querySelector('#id_nv');
        if (typeof Choices !== 'undefined' && choicesEl) {
             new Choices(choicesEl, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
                placeholder: true,
                placeholderValue: 'Chọn nhân viên...',
                noResultsText: 'Không tìm thấy nhân viên',
            });
        }
    });
</script>
