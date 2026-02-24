<?php
	include('./layouts/header.php');
	include(__DIR__ . '/connection/config.php');
	include(__DIR__ . '/models/TaiKhoan.php');

	$database = new Database();
	$conn = $database->getConnection();
	$taiKhoan = new TaiKhoan($conn);

	$idEdit = $_GET['id'] ?? null;
	$isEdit = !empty($idEdit);
	$taiKhoanInfo = [];

	if ($isEdit) {
		$taiKhoanInfo = $taiKhoan->getById($idEdit);
	}

?>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="text-primary fw-bold mb-0">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    <?= $isEdit ? 'Chỉnh sửa tài khoản' : 'Thêm tài khoản mới' ?>
                </h5>
            </div>

            <div class="card-body">
                <?php if (isset($_GET['status']) && $_GET['status'] === 'fail'): ?>
                    <div class="alert alert-danger">Thao tác không thành công. Vui lòng kiểm tra lại!</div>
                <?php endif; ?>

                <form id="formTaiKhoan" class="form form-horizontal validate-tooltip"
                      action="action/tai-khoan-action.php" method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="id" value="<?= $isEdit ? (int)$taiKhoanInfo['id'] : '' ?>">
                    <input type="hidden" name="isEdit" value="<?= $isEdit ? 1 : 0 ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Họ</label>
                            <input type="text" name="ho" class="form-control"
                                   value="<?= htmlspecialchars($taiKhoanInfo['ho'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Tên</label>
                            <input type="text" name="ten" class="form-control"
                                   value="<?= htmlspecialchars($taiKhoanInfo['ten'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($taiKhoanInfo['email'] ?? '') ?>" <?= $isEdit ? 'readonly' : 'required' ?>>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Số điện thoại</label>
                            <input type="text" name="sodt" class="form-control"
                                   value="<?= htmlspecialchars($taiKhoanInfo['sodt'] ?? '') ?>" required>
                        </div>

                        <?php if (!$isEdit): ?>
                        <div class="col-md-6 mb-3">
							<label>Mật khẩu</label>
							<input type="password" name="mk" id="mk" class="form-control" required minlength="6"
								   data-msg="Mật khẩu phải có ít nhất 6 ký tự">
						</div>
						<?php endif; ?>

                        <div class="col-md-6 mb-3">
                            <label>Ảnh đại diện</label>
                            <input type="file" name="hinhanh" class="form-control" accept="image/*">
                        </div>

                        <?php if (!$isEdit): ?>
                        
						<div class="col-md-6 mb-3">
							<label>Nhập lại mật khẩu</label>
							<input type="password" name="re_mk" id="re_mk" class="form-control" required 
								   data-match="#mk" data-msg="Mật khẩu nhập lại không khớp">
						</div>
						<?php endif; ?>

                        <div class="col-md-6 mb-3">
                            <label>Quyền</label>
                            <select name="quyen" class="form-select" required>
                                <option value="">-- Chọn quyền --</option>
                                <option value="1" <?= ($taiKhoanInfo['quyen'] ?? '') == 1 ? 'selected' : '' ?>><?= htmlspecialchars(getTen_Quyen(1)) ?></option>
                                <option value="2" <?= ($taiKhoanInfo['quyen'] ?? '') == 2 ? 'selected' : '' ?>><?= htmlspecialchars(getTen_Quyen(2)) ?></option>
								<option value="3" <?= ($taiKhoanInfo['quyen'] ?? '') == 3 ? 'selected' : '' ?>><?= htmlspecialchars(getTen_Quyen(3)) ?></option>
                            </select>
                        </div>
						<?php if ($isEdit && !empty($taiKhoanInfo['hinhanh'])): ?>
							<div class="col-md-6 mb-3 text-center">
								<label>Ảnh hiện tại</label><br>
								<img src="uploads/anh/<?= htmlspecialchars($taiKhoanInfo['hinhanh']) ?>?v=<?= time(); ?>"
									 alt="avatar" class="rounded border shadow-sm mt-2"
									 style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
							</div>
							<?php endif; ?>
                        <div class="col-md-6 mb-3">
                            <label>Ngày tạo</label>
                            <input type="text" class="form-control" readonly
                                   value="<?= $isEdit && !empty($taiKhoanInfo['ngaytao'])
                                       ? date('d-m-Y', strtotime($taiKhoanInfo['ngaytao']))
                                       : date('d-m-Y') ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Trạng thái</label>
                            <select name="trangthai" class="form-select" required>
                                <option value="1" <?= ($taiKhoanInfo['trangthai'] ?? '') == 1 ? 'selected' : '' ?>>Hoạt động</option>
                                <option value="0" <?= ($taiKhoanInfo['trangthai'] ?? '') == 0 ? 'selected' : '' ?>>Đã khoá</option>
                            </select>
                        </div>

                        <div class="col-12 d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-save"></i> <?= $isEdit ? 'Cập nhật' : 'Thêm mới' ?>
                            </button>
                            <a href="ds-tai-khoan.php" class="btn btn-secondary">Danh sách tài khoản</a>
                        </div>
                    </div>
                </form>
            </div>
        
		</div>
    </section>
</div>


<?php include('./layouts/footer.php'); ?>
