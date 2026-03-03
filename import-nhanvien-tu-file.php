<?php
include('./layouts/header.php');

// Kiểm tra quyền truy cập (nếu cần)
// if (!checkPermission('import')) { ... }
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Nhập liệu Nhân viên</h3>
                <p class="text-subtitle text-muted">Thêm mới hàng loạt nhân viên từ file Excel.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                         <li class="breadcrumb-item"><a href="ds-nhan-vien.php">Danh sách nhân viên</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Import Excel</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row match-height">
            <!-- 1. Hướng dẫn & File mẫu -->
            <div class="col-md-5 col-12">
                <div class="card shadow border-0 h-100">
                    <div class="card-header bg-white border-bottom">
                         <h5 class="fw-bold text-primary mb-0"><i class="bi bi-info-circle me-2"></i>Hướng dẫn nhập liệu</h5>
                    </div>
                    <div class="card-body mt-3">
                        <div class="alert alert-light-primary color-primary border-primary">
                            <i class="bi bi-star-fill text-warning me-1"></i>
                            Vui lòng sử dụng file mẫu chuẩn để tránh lỗi dữ liệu.
                        </div>
                        
                        <p>Quy trình thực hiện:</p>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item bg-transparent"><i class="bi bi-1-circle-fill text-primary me-2"></i>Tải file mẫu về máy tính.</li>
                            <li class="list-group-item bg-transparent"><i class="bi bi-2-circle-fill text-primary me-2"></i>Nhập thông tin nhân viên vào file Excel.</li>
                            <li class="list-group-item bg-transparent"><i class="bi bi-3-circle-fill text-primary me-2"></i>Không thay đổi tiêu đề cột trong file mẫu.</li>
                            <li class="list-group-item bg-transparent"><i class="bi bi-4-circle-fill text-primary me-2"></i>Chọn file đã nhập và nhấn "Tải lên".</li>
                        </ul>

                        <div class="d-grid">
                            <a href="action/download_mau_nhanvien.php" class="btn btn-outline-primary shadow-sm" target="_blank">
                                <i class="bi bi-download me-2"></i>Tải file mẫu (.xlsx)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Form Upload -->
            <div class="col-md-7 col-12">
                <div class="card shadow border-0 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="fw-bold text-success mb-0"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Tải lên file dữ liệu</h5>
                    </div>
                    <div class="card-body mt-3 d-flex flex-column justify-content-center">
                        
                        <form action="action/import-nhanvien-action.php" method="POST" enctype="multipart/form-data" id="formImport">
                            
                            <div class="mb-4 text-center">
                                <label for="fileExcel" class="form-label fw-bold mb-3 d-block">Chọn file Excel (.xlsx, .xls) để nhập liệu</label>
                                
                                <div class="upload-area p-5 border border-2 border-dashed rounded-3 bg-light position-relative" id="uploadArea">
                                    <input type="file" name="file" id="fileExcel" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" 
                                        accept=".xlsx, .xls" required onchange="updateFileName(this)">
                                    
                                    <div class="text-center" id="uploadPlaceholder">
                                        <i class="bi bi-cloud-arrow-up fs-1 text-secondary"></i>
                                        <p class="mt-2 text-muted">Kéo thả file vào đây hoặc click để chọn</p>
                                    </div>
                                    
                                    <div class="text-center d-none" id="fileInfo">
                                        <i class="bi bi-file-earmark-excel fs-1 text-success"></i>
                                        <p class="mt-2 fw-bold text-dark" id="fileNameDisplay"></p>
                                        <span class="badge bg-light-success text-success">Đã sẵn sàng tải lên</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="ds-nhan-vien.php" class="btn btn-secondary shadow-sm">
                                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                                </a>
                                <button type="submit" name="import" class="btn btn-success shadow-sm">
                                    <i class="bi bi-upload me-1"></i> Tiến hành Import
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .upload-area:hover {
        background-color: #e9ecef !important;
        border-color: #435ebe !important;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>

<script>
    function updateFileName(input) {
        const placeholder = document.getElementById('uploadPlaceholder');
        const fileInfo = document.getElementById('fileInfo');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const uploadArea = document.getElementById('uploadArea');

        if (input.files && input.files[0]) {
            placeholder.classList.add('d-none');
            fileInfo.classList.remove('d-none');
            fileNameDisplay.textContent = input.files[0].name;
            uploadArea.classList.remove('bg-light');
            uploadArea.classList.add('bg-white');
            uploadArea.style.borderColor = '#198754'; // Success color
        } else {
            placeholder.classList.remove('d-none');
            fileInfo.classList.add('d-none');
            fileNameDisplay.textContent = '';
            uploadArea.classList.add('bg-light');
            uploadArea.classList.remove('bg-white');
            uploadArea.style.borderColor = '#dee2e6';
        }
    }
</script>

<?php include('./layouts/footer.php'); ?>
