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
	$ds_nv = $nvModel->getAllNV_danglam()->fetchAll(PDO::FETCH_ASSOC);

	// Lấy danh sách bản ghi để hiển thị (lọc theo ck nếu muốn)
	$stmt = $model->getAll($ck_khenthuong);
	$arrShow = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

	// Lấy 1 bản ghi khi edit
	$idEdit = $_GET['idEdit'] ?? null;
	$ktklInfo = null;
	if ($idEdit) {
		$ktklInfo = $model->getById((int)$idEdit);
	}

	$isKhenThuong = ($ck_khenthuong == 1);
	$title = $isKhenThuong ? "Tạo khen thưởng" : "Tiến hành kỷ luật";
	$title_sua = $isKhenThuong ? "khen thưởng" : "kỷ luật";
	$prefix = $isKhenThuong ? "KT" : "KL";
	$ma_ktkl = $prefix . time();
	$row_acc = $_SESSION['user'] ?? [];
?>

<div class="page-heading">
<section id="basic-vertical-layouts">
    
    <!-- FORM NHẬP / SỬA -->
	
    <div class="card shadow-sm border-0 mb-4" <?php if($ke_toan){ ?> style="display: none;"  <?php }?>>
      
        <div class="card-body">
		
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h4 class="fw-bold text-primary mb-0 mt-2">
				<i class="bi bi-person-plus-fill me-2"></i>
                <?= isset($idEdit) ? "Cập nhật $title_sua" : "$title" ?>
			</h4>					
		</div>
		
            <form method="post" action="action/khen-thuong-ky-luat-action.php" class="validate-tooltip mt-2" id="ktklForm">
                <input type="hidden" name="id" value="<?= $ktklInfo['id'] ?? '' ?>">
                <input type="hidden" name="ck_khenthuong" value="<?= $ck_khenthuong ?>">

                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Mã <?= strtolower($title_sua) ?></label>
                        <input type="text" name="ma_ktkl" class="form-control" 
                               value="<?= $ktklInfo['ma_ktkl'] ?? $ma_ktkl ?>" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tên <?= $isKhenThuong ? "khen thưởng" : "kỷ luật" ?> <span class="text-danger">*</span></label>
                        <input type="text" name="ten_ktkl" class="form-control" required 
                               value="<?= htmlspecialchars($ktklInfo['ten_ktkl'] ?? '') ?>">
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Nhân viên <span class="text-danger">*</span></label>
                        <select name="id_nv" id="id_nv" class="form-select" required>
                            <option value="">-- Chọn nhân viên --</option>
                            <?php foreach ($ds_nv as $nv): ?>
                                
                                    <option value="<?= $nv['id'] ?>" <?= ($ktklInfo['id_nv'] ?? '') == $nv['id'] ? 'selected' : '' ?>>
                                        (<?= htmlspecialchars($nv['ma_nv']) ?>) <?= htmlspecialchars($nv['hoten']) ?> |  <?= htmlspecialchars($nv['phongban']) ?> | <?= htmlspecialchars($nv['chucvu']) ?>
                                    </option>
                                
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Số tiền <?= $isKhenThuong ? "khen thưởng" : "kỷ luật" ?></label>
                        <input type="text" id="so_tien_display" class="form-control" placeholder="0" 
                               value="<?= number_format(floatval($ktklInfo['so_tien'] ?? 0)) ?>">
                        <input type="hidden" id="so_tien" name="so_tien" 
                               value="<?= $ktklInfo['so_tien'] ?? 0 ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Hình thức</label>
                        <input type="text" name="hinh_thuc" class="form-control" 
                               value="<?= htmlspecialchars($ktklInfo['hinh_thuc'] ?? '') ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label"><?= $isKhenThuong ? "Ngày quyết định khen thưởng" : "Ngày quyết định kỷ luật" ?></label>
                        <input type="date" name="ngayqd" class="form-control" 
                               value="<?= $ktklInfo['ngayqd'] ?? date('Y-m-d') ?>">
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Nội dung  <?=$title_sua ?></label>
                        <textarea name="noidung" rows="2" class="form-control"><?= htmlspecialchars($ktklInfo['noidung'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <?php if (!empty($ktklInfo)): ?>
                        <button type="submit" name="update" class="btn btn-primary me-2">
                            <i class="bi bi-save"></i> Cập nhật <?=$title_sua ?>
                        </button>
                    <?php else: ?>
                        <button type="submit" name="add" class="btn btn-success me-2">
                            <i class="bi bi-plus-circle"></i> <?=$title ?>
                        </button>
                    <?php endif; ?>
                    <a href="khen-thuong-ky-luat.php?ck_khenthuong=<?= $ck_khenthuong ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Làm mới
                    </a>
                </div>
            </form>
        </div>
    </div>
	
    <!-- DANH SÁCH -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="fw-bold text-primary mb-3">📋 Danh sách <?= $isKhenThuong ? "khen thưởng" : "kỷ luật" ?></h5>

            <table class="table table-hover align-middle" id="tableKTKL">
                <thead class="table-light">
                    <tr>
						<th>STT</th>
                        <th>Mã</th>
                        <th>Tên  <?=$title_sua ?></th>
                        <th>Nhân viên</th>
                        <th>Số tiền</th>
                        <th>Hình thức</th>
                        <th>Nội dung</th>
						<th>Người tạo</th>
						<th>Ngày quyết định</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
					$stt = 1;
					foreach ($arrShow as $row): ?>
                    <tr>
						<td class="text-start"><?= htmlspecialchars($stt) ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['ma_ktkl']) ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['ten_ktkl']) ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['nhanvien_name']) ?></td>
                        <td class="text-start"><?= number_format(floatval($row['so_tien'] ?? 0)) ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['hinh_thuc']) ?></td>
                        <td class="text-start"><?= htmlspecialchars($row['noidung']) ?></td>
						<td class="text-start"><?= htmlspecialchars($row['nguoitao']) ?></td>
						<td class="text-start"><?= date('d/m/Y', strtotime($row['ngayqd'])) ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-success btn-view" 
                                    data-bs-toggle="modal" data-bs-target="#viewModal"
                                    data-ma="<?= htmlspecialchars($row['ma_ktkl']) ?>"
                                    data-ten="<?= htmlspecialchars($row['ten_ktkl']) ?>"
                                    data-nv="<?= htmlspecialchars($row['nhanvien_name']) ?>"
                                    data-so="<?= number_format(floatval($row['so_tien'] ?? 0)) ?>"
                                    data-ht="<?= htmlspecialchars($row['hinh_thuc']) ?>"
                                    data-nd="<?= htmlspecialchars($row['noidung']) ?>"
                                    data-ngay="<?= htmlspecialchars($row['ngayqd']) ?>"
                                    data-anh="<?= htmlspecialchars($row['anhdaidien'] ?? 'default.png') ?>"
                                    data-sdt="<?= htmlspecialchars($row['sodt'] ?? '') ?>"
                                    data-email="<?= htmlspecialchars($row['email'] ?? '') ?>"
                                    data-cv="<?= htmlspecialchars($row['chuc_vu'] ?? '') ?>"
                                    data-pb="<?= htmlspecialchars($row['phong_ban'] ?? '') ?>"
                                    data-gt="<?= htmlspecialchars($row['gtinh'] ?? '') ?>">
                                    <i class="bi bi-eye"></i>
                                </button>
								<?php if(!$ke_toan){ ?>
                                <a href="khen-thuong-ky-luat.php?ck_khenthuong=<?= $ck_khenthuong ?>&idEdit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                                    data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['ten_ktkl']) ?>" data-ck="<?= $ck_khenthuong ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
								<?php }?>
                            </div>
                        </td>
                    </tr>
                    <?php 
					$stt++;
					endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
</div>

<!-- MODAL XEM CHI TIẾT -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold text-primary">
            <i class="bi bi-info-circle"></i> Chi tiết <?= strtolower($title) ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Thông tin nhân viên -->
        <div class="border-bottom pb-3 mb-3">
          <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-person-badge me-2"></i>Thông tin nhân viên</h6>
          <div class="d-flex align-items-center">
            <img id="v_anh" src="assets/images/default-avatar.php" class="rounded border me-3" style="width: 80px; height: 100px; object-fit: cover;">
            <div>
              <p class="mb-1"><strong>Tên:</strong> <span id="v_nv"></span></p>
              <p class="mb-1"><strong>Giới tính:</strong> <span id="v_gt"></span></p>
              <p class="mb-1"><strong>Chức vụ:</strong> <span id="v_cv"></span></p>
              <p class="mb-1"><strong>Phòng ban:</strong> <span id="v_pb"></span></p>
              <p class="mb-1"><strong>SĐT:</strong> <span id="v_sdt"></span></p>
              <p class="mb-1"><strong>Email:</strong> <span id="v_email"></span></p>
            </div>
          </div>
        </div>

        <!-- Thông tin khen thưởng / kỷ luật -->
        <div>
          <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-award me-2"></i>Thông tin <?= strtolower($title) ?></h6>
          <table class="table table-sm table-borderless">
            <tr><th>Mã:</th><td id="v_ma"></td></tr>
            <tr><th>Tên:</th><td id="v_ten"></td></tr>
            <tr><th>Số tiền:</th><td id="v_so"></td></tr>
            <tr><th>Hình thức:</th><td id="v_ht"></td></tr>
            <tr><th><?= $isKhenThuong ? "Ngày khen thưởng" : "Ngày kỷ luật" ?>:</th><td id="v_ngay"></td></tr>
            <tr><th>Nội dung:</th><td id="v_nd"></td></tr>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
	const table = document.querySelector("#tableKTKL");
    if (table) new simpleDatatables.DataTable(table);
    
    // Số tiền: tự format dấu phẩy khi nhập
    const display = document.getElementById('so_tien_display');
    const hidden = document.getElementById('so_tien');
    
    if (display) {
        display.addEventListener('input', function () {
            let val = this.value.replace(/,/g, '');
            if (!isNaN(val) && val !== '') {
                this.value = Number(val).toLocaleString('en-US');
                hidden.value = val;
            } else {
                hidden.value = '';
            }
        });
    }

    // ----------------------------------------------------
    // LOGIC SỰ KIỆN ĐỘNG (XEM và XÓA) - SỬ DỤNG EVENT DELEGATION
    // ----------------------------------------------------
    document.addEventListener('click', function(e) {

        // --- A. XỬ LÝ NÚT XEM CHI TIẾT (.btn-view) ---
        // Do bạn đã sử dụng data-bs-toggle="modal", logic chính chỉ cần điền dữ liệu
        const btnView = e.target.closest('.btn-view');
        if (btnView) {
            // e.preventDefault() là không cần thiết vì đã có data-bs-toggle
            
            // Lấy dữ liệu và điền vào Modal
            document.getElementById('v_ma').textContent = btnView.dataset.ma;
            document.getElementById('v_ten').textContent = btnView.dataset.ten;
            document.getElementById('v_nv').textContent = btnView.dataset.nv;
            document.getElementById('v_so').textContent = btnView.dataset.so;
            document.getElementById('v_ht').textContent = btnView.dataset.ht;
            document.getElementById('v_nd').textContent = btnView.dataset.nd;
            document.getElementById('v_ngay').textContent = btnView.dataset.ngay;
            document.getElementById('v_sdt').textContent = btnView.dataset.sdt;
            document.getElementById('v_email').textContent = btnView.dataset.email;
            document.getElementById('v_cv').textContent = btnView.dataset.cv;
            document.getElementById('v_pb').textContent = btnView.dataset.pb;
            document.getElementById('v_gt').textContent = btnView.dataset.gt;
            document.getElementById('v_anh').src = `uploads/nhanvien/${btnView.dataset.anh}`;

            // Nút xem đã hoạt động ổn do sử dụng thuộc tính data-bs-toggle, 
            // nhưng logic điền dữ liệu cần chuyển sang Event Delegation.
            // Nếu bạn muốn mở Modal bằng JS thay vì HTML:
            // const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
            // viewModal.show();
        }

        // --- B. XỬ LÝ NÚT XÓA (.btn-delete) ---
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) {
            e.preventDefault();
            
            const id = btnDelete.dataset.id;
            const name = btnDelete.dataset.name;
            const ck = btnDelete.dataset.ck; // ck_khenthuong

            // Kiểm tra Swal trước khi sử dụng
            if (typeof Swal === 'undefined') {
                if (confirm(`Bạn có chắc muốn xóa mục "${name}" không?`)) {
                    window.location.href = `action/khen-thuong-ky-luat-action.php?delete=${id}&ck_khenthuong=${ck}`;
                }
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc muốn xóa mục "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có, xóa",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/khen-thuong-ky-luat-action.php?delete=${id}&ck_khenthuong=${ck}`;
                }
            });
        }
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
