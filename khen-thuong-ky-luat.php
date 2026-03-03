<?php 
    include('./layouts/header.php');

    include(__DIR__ . '/connection/config.php');
    include(__DIR__ . '/models/KhenThuongKyLuat.php');
    include(__DIR__ . '/models/NhanVien.php');

    $database = new Database();
    $conn = $database->getConnection();

    $model = new KhenThuongKyLuat($conn);
    $nvModel = new NhanVien($conn);

    // ck_khenthuong từ GET (1 = khen thưởng, 0 = kỷ luật). Mặc định là 1 (khen thưởng).
    $ck_khenthuong = isset($_GET['ck_khenthuong']) ? (int)$_GET['ck_khenthuong'] : (isset($_POST['ck_khenthuong']) ? (int)$_POST['ck_khenthuong'] : 1);

    // Danh sách nhân viên (dùng cho select). Chỉ lấy nhân viên trạng thái = 1 (đang làm)
    $stmtNV = $nvModel->getAllNV_danglam();
    $ds_nv = $stmtNV ? $stmtNV->fetchAll(PDO::FETCH_ASSOC) : [];

    // Lấy danh sách bản ghi để hiển thị
    $stmt = $model->getAll($ck_khenthuong);
    $arrShow = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

    // Lấy 1 bản ghi khi edit
    $idEdit = $_GET['idEdit'] ?? null;
    $ktklInfo = null;
    if ($idEdit) {
        $ktklInfo = $model->getById((int)$idEdit);
        // Nếu đang edit, cập nhật lại ck_khenthuong theo bản ghi đang edit để tránh nhầm tab
        if ($ktklInfo) {
            $ck_khenthuong = $ktklInfo['ck_khenthuong'];
        }
    }

    $isKhenThuong = ($ck_khenthuong == 1);
    $title = $isKhenThuong ? "Khen thưởng" : "Kỷ luật";
    $prefix = $isKhenThuong ? "KT" : "KL";
    $ma_ktkl = $prefix . time(); // Mã mặc định khi thêm mới
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Quản lý Khen thưởng & Kỷ luật</h3>
                <p class="text-subtitle text-muted">Ghi nhận thành tích hoặc xử lý vi phạm của nhân viên.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section id="basic-vertical-layouts">
        <!-- Tabs chuyển đổi -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-pills shadow-sm bg-white p-2 rounded">
                    <li class="nav-item">
                        <a class="nav-link <?= $isKhenThuong ? 'active' : '' ?>" href="khen-thuong-ky-luat.php?ck_khenthuong=1">
                            <i class="bi bi-trophy-fill me-2"></i>Khen thưởng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= !$isKhenThuong ? 'active bg-danger text-white' : 'text-danger' ?>" href="khen-thuong-ky-luat.php?ck_khenthuong=0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Kỷ luật
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row match-height">
            <!-- Form Card -->
            <div class="col-12 <?php if($ke_toan) echo 'd-none'; ?>">
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white border-bottom">
                         <h5 class="fw-bold text-primary mb-0">
                            <i class="<?= isset($idEdit) ? 'bi bi-pencil-square' : 'bi bi-plus-circle' ?> me-2"></i>
                            <?= isset($idEdit) ? "Cập nhật quyết định $title" : "Tạo quyết định $title bản mới" ?>
                        </h5>
                    </div>
                    <div class="card-body mt-3">
                        <form method="post" action="action/khen-thuong-ky-luat-action.php" class="validate-tooltip" id="ktklForm">
                            <input type="hidden" name="id" value="<?= $ktklInfo['id'] ?? '' ?>">
                            <input type="hidden" name="ck_khenthuong" value="<?= $ck_khenthuong ?>">

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Mã quyết định</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" name="ma_ktkl" class="form-control bg-light" 
                                            value="<?= $ktklInfo['ma_ktkl'] ?? $ma_ktkl ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="col-md-5 mb-3">
                                    <label class="fw-bold mb-1">Tiêu đề quyết định <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-type-h1"></i></span>
                                        <input type="text" name="ten_ktkl" class="form-control" required 
                                            placeholder="VD: Khen thưởng nhân viên xuất sắc..."
                                            value="<?= htmlspecialchars($ktklInfo['ten_ktkl'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold mb-1">Người nhận <span class="text-danger">*</span></label>
                                    <select name="id_nv" id="selectNhanVien" class="form-select" required>
                                        <option value="">-- Chọn nhân viên --</option>
                                        <?php 
                                        $selected_id_nv = $ktklInfo['id_nv'] ?? '';
                                        foreach ($ds_nv as $nv): 
                                            $isSelected = ($selected_id_nv == $nv['id']) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $nv['id'] ?>" <?= $isSelected ?>>
                                                (<?= htmlspecialchars($nv['ma_nv']) ?>) <?= htmlspecialchars($nv['hoten']) ?> | <?= htmlspecialchars($nv['phongban'] ?? 'N/A') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Số tiền (VNĐ)</label>
                                    <div class="input-group">
                                        <span class="input-group-text text-success fw-bold"><i class="bi bi-cash"></i></span>
                                        <input type="text" id="so_tien_display" class="form-control text-end fw-bold text-success" 
                                            placeholder="0" 
                                            value="<?= number_format(floatval($ktklInfo['so_tien'] ?? 0)) ?>">
                                        <input type="hidden" id="so_tien" name="so_tien" 
                                            value="<?= $ktklInfo['so_tien'] ?? 0 ?>">
                                        <span class="input-group-text"></span>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Hình thức</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input type="text" name="hinh_thuc" class="form-control" 
                                            placeholder="VD: Tiền, Bằng khen..."
                                            value="<?= htmlspecialchars($ktklInfo['hinh_thuc'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Ngày quyết định</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                        <input type="date" name="ngayqd" class="form-control" 
                                            value="<?= $ktklInfo['ngayqd'] ?? date('Y-m-d') ?>">
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="fw-bold mb-1">Nội dung chi tiết</label>
                                    <textarea name="noidung" rows="3" class="form-control" placeholder="Mô tả chi tiết lý do..."><?= htmlspecialchars($ktklInfo['noidung'] ?? '') ?></textarea>
                                </div>
                                
                                <div class="col-12 d-flex justify-content-end">
                                    <a href="khen-thuong-ky-luat.php?ck_khenthuong=<?= $ck_khenthuong ?>" class="btn btn-light-secondary shadow-sm me-2">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Hủy / Làm mới
                                    </a>
                                    <?php if (!empty($ktklInfo)): ?>
                                        <button type="submit" name="update" class="btn btn-primary shadow-sm px-4">
                                            <i class="bi bi-save me-1"></i> Lưu thay đổi
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="add" class="btn btn-success shadow-sm px-4">
                                            <i class="bi bi-plus-lg me-1"></i> Tạo quyết định
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- List Card -->
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                             <h5 class="fw-bold text-primary mb-0">
                                <i class="bi bi-list-check me-2"></i>Danh sách <?= $title ?>
                            </h5>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="tableKTKL">
                                <thead class="table-light text-nowrap">
                                    <tr>
                                        <th class="text-center">STT</th>
                                        <th>Mã QĐ</th>
                                        <th>Tiêu đề</th>
                                        <th>Nhân viên</th>
                                        <th class="text-end">Số tiền</th>
                                        <th>Hình thức</th>
                                        <th>Ngày QĐ</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $stt = 1;
                                    foreach ($arrShow as $row): 
                                        $money = floatval($row['so_tien'] ?? 0);
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $stt++ ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['ma_ktkl']) ?></span></td>
                                        
                                        <td style="max-width: 250px;">
                                            <div class="fw-bold text-truncate" title="<?= htmlspecialchars($row['ten_ktkl']) ?>">
                                                <?= htmlspecialchars($row['ten_ktkl']) ?>
                                            </div>
                                            <small class="text-muted text-truncate d-block" style="max-width: 200px;">
                                                <?= htmlspecialchars($row['noidung'] ?? '') ?>
                                            </small>
                                        </td>
                                        
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <img src="uploads/nhanvien/<?= !empty($row['anhdaidien']) ? $row['anhdaidien'] : 'default.png' ?>" alt="avatar" class="rounded-circle">
                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($row['nhanvien_name']) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($row['phong_ban'] ?? '') ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="text-end fw-bold <?= $money > 0 ? ($isKhenThuong ? 'text-success' : 'text-danger') : 'text-muted' ?>">
                                            <?= number_format($money) ?> 
                                        </td>
                                        
                                        <td><?= htmlspecialchars($row['hinh_thuc']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['ngayqd'])) ?></td>
                                        
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-info shadow-sm btn-view" 
                                                    title="Xem chi tiết"
                                                    data-bs-toggle="modal" data-bs-target="#viewModal"
                                                    data-ma="<?= htmlspecialchars($row['ma_ktkl']) ?>"
                                                    data-ten="<?= htmlspecialchars($row['ten_ktkl']) ?>"
                                                    data-nv="<?= htmlspecialchars($row['nhanvien_name']) ?>"
                                                    data-so="<?= number_format($money) ?>"
                                                    data-ht="<?= htmlspecialchars($row['hinh_thuc']) ?>"
                                                    data-nd="<?= htmlspecialchars($row['noidung']) ?>"
                                                    data-ngay="<?= date('d/m/Y', strtotime($row['ngayqd'])) ?>"
                                                    data-anh="<?= htmlspecialchars($row['anhdaidien'] ?? 'default.png') ?>"
                                                    data-sdt="<?= htmlspecialchars($row['sodt'] ?? 'N/A') ?>"
                                                    data-email="<?= htmlspecialchars($row['email'] ?? 'N/A') ?>"
                                                    data-cv="<?= htmlspecialchars($row['chuc_vu'] ?? 'N/A') ?>"
                                                    data-pb="<?= htmlspecialchars($row['phong_ban'] ?? 'N/A') ?>"
                                                    data-gt="<?= htmlspecialchars($row['gtinh'] == 1 ? 'Nam' : 'Nữ') ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                
                                                <?php if(!$ke_toan): ?>
                                                <a href="khen-thuong-ky-luat.php?ck_khenthuong=<?= $ck_khenthuong ?>&idEdit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning shadow-sm" title="Sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger shadow-sm btn-delete" 
                                                    title="Xóa"
                                                    data-id="<?= $row['id'] ?>" 
                                                    data-name="<?= htmlspecialchars($row['ten_ktkl']) ?>" 
                                                    data-ck="<?= $ck_khenthuong ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- MODAL XEM CHI TIẾT -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold">
            <i class="bi bi-info-circle me-2"></i>Chi tiết Quyết định
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body bg-light">
        <!-- Thông tin nhân viên -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="bi bi-person-badge me-2"></i>Thông tin nhân viên</h6>
                <div class="d-flex align-items-start">
                    <img id="v_anh" src="assets/images/default.png" class="rounded border me-4 shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <div class="row">
                            <div class="col-md-6 mb-2"><strong>Họ tên:</strong> <span id="v_nv" class="text-primary fw-bold"></span></div>
                            <div class="col-md-6 mb-2"><strong>Giới tính:</strong> <span id="v_gt"></span></div>
                            <div class="col-md-6 mb-2"><strong>Chức vụ:</strong> <span id="v_cv"></span></div>
                            <div class="col-md-6 mb-2"><strong>Phòng ban:</strong> <span id="v_pb"></span></div>
                            <div class="col-md-6 mb-2"><strong>SĐT:</strong> <span id="v_sdt"></span></div>
                            <div class="col-md-6 mb-2"><strong>Email:</strong> <span id="v_email"></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin quyết định -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="bi bi-file-earmark-text me-2"></i>Nội dung Quyết định</h6>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th class="bg-light w-25">Mã QĐ</th>
                            <td id="v_ma" class="fw-bold text-dark"></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Tiêu đề</th>
                            <td id="v_ten" class="fw-bold text-primary"></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Số tiền</th>
                            <td id="v_so" class="fw-bold text-danger"></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Hình thức</th>
                            <td id="v_ht"></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Ngày QĐ</th>
                            <td id="v_ngay"></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Nội dung</th>
                            <td id="v_nd" style="white-space: pre-line;"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Datatable
    const table = document.querySelector("#tableKTKL");
    if (table) {
        new simpleDatatables.DataTable(table, {
             labels: {
                placeholder: "Tìm kiếm...",
                perPage: "mục/trang",
                noRows: "Không có dữ liệu",
                info: "Hiển thị {start} đến {end} của {rows} mục",
            }
        });
    }
    
    // 2. Choices.js
    const selectNhanVien = document.getElementById('selectNhanVien');
    if (selectNhanVien) {
        new Choices(selectNhanVien, {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
            placeholder: true,
            placeholderValue: 'Tìm kiếm nhân viên...',
            noResultsText: 'Không tìm thấy kết quả',
        });
    }

    // 3. Auto Format Money
    const display = document.getElementById('so_tien_display');
    const hidden = document.getElementById('so_tien');
    
    if (display) {
        // Format on load
        if (display.value) {
            let val = display.value.replace(/,/g, '');
            if (!isNaN(val) && val !== '') {
                 display.value = Number(val).toLocaleString('en-US');
            }
        }

        display.addEventListener('input', function () {
            let val = this.value.replace(/,/g, '');
            // Chỉ giữ lại số
            val = val.replace(/[^0-9.]/g, ''); 
            
            if (!isNaN(val) && val !== '') {
                this.value = Number(val).toLocaleString('en-US'); // Format hiển thị
                hidden.value = val; // Lưu giá trị thực
            } else {
                hidden.value = '';
            }
        });
    }

    // 4. Modal View - Event Delegation
    const modalReview = document.getElementById('viewModal');
    if (modalReview) {
        modalReview.addEventListener('show.bs.modal', function (event) {
            // Button trigger modal
            const button = event.relatedTarget;
            
            // Extract info from data-* attributes
            const ma = button.getAttribute('data-ma');
            const ten = button.getAttribute('data-ten');
            const nv = button.getAttribute('data-nv');
            const so = button.getAttribute('data-so');
            const ht = button.getAttribute('data-ht');
            const nd = button.getAttribute('data-nd');
            const ngay = button.getAttribute('data-ngay');
            const sdt = button.getAttribute('data-sdt');
            const email = button.getAttribute('data-email');
            const cv = button.getAttribute('data-cv');
            const pb = button.getAttribute('data-pb');
            const gt = button.getAttribute('data-gt');
            const anh = button.getAttribute('data-anh');

            // Update the modal's content.
            const modal = this;
            modal.querySelector('#v_ma').textContent = ma;
            modal.querySelector('#v_ten').textContent = ten;
            modal.querySelector('#v_nv').textContent = nv;
            modal.querySelector('#v_so').textContent = so + ' ';
            modal.querySelector('#v_ht').textContent = ht;
            modal.querySelector('#v_nd').textContent = nd;
            modal.querySelector('#v_ngay').textContent = ngay;
            
            modal.querySelector('#v_sdt').textContent = sdt;
            modal.querySelector('#v_email').textContent = email;
            modal.querySelector('#v_cv').textContent = cv;
            modal.querySelector('#v_pb').textContent = pb;
            modal.querySelector('#v_gt').textContent = gt;
            
            modal.querySelector('#v_anh').src = `uploads/nhanvien/${anh}`;
        });
    }

    // 5. Delete Confirmation
    document.addEventListener('click', function(e) {
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) {
            e.preventDefault();
            const id = btnDelete.dataset.id;
            const name = btnDelete.dataset.name;
            const ck = btnDelete.dataset.ck; 

            const confirmOptions = {
                title: 'Xác nhận xóa?',
                html: `Bạn có chắc chắn muốn xóa bản ghi <b>"${name}"</b> không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: '<i class="bi bi-trash"></i> Xóa ngay',
                cancelButtonText: 'Hủy bỏ',
                reverseButtons: true
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire(confirmOptions).then(result => {
                    if (result.isConfirmed) {
                         window.location.href = `action/khen-thuong-ky-luat-action.php?delete=${id}&ck_khenthuong=${ck}`;
                    }
                });
            } else {
                 if (confirm(`Xóa bản ghi "${name}"?`)) {
                    window.location.href = `action/khen-thuong-ky-luat-action.php?delete=${id}&ck_khenthuong=${ck}`;
                }
            }
        }
    });

});
</script>

<?php include('./layouts/footer.php'); ?>
