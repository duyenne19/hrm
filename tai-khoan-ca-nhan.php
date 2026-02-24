<?php
	include('./layouts/header.php');
	include(__DIR__ . '/connection/config.php');
	include(__DIR__ . '/models/TaiKhoan.php');
	
	$database = new Database();
	$conn = $database->getConnection();
	$taiKhoan = new TaiKhoan($conn);

	$userId = $_SESSION['user']['id'] ?? null;
	$user = $userId ? $taiKhoan->getById($userId) : null;
?>

<div class="page-heading">
    <section class="section">
        <div class="row">
            <!-- LEFT: profile display -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <?php 
							$avatar = isset($user['hinhanh']) && $user['hinhanh'] != '' 
								? 'uploads/anh/' . $user['hinhanh'] . '?v=' . time() 
								: 'assets/images/default-avatar.png';
							?>
                        <img src="<?= htmlspecialchars($avatar) ?>" class="img-thumbnail mb-3" style="width:160px;height:160px;object-fit:cover;">

                        <h4 class="mt-2"><?= htmlspecialchars($user['ho'] . ' ' . $user['ten']) ?></h4>
                        <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                        <p><i class="bi bi-telephone"></i> <?= htmlspecialchars($user['sodt'] ?? '---') ?></p>

                        <div class="mt-3">
                            <?php if ((int)$user['trangthai'] === 1): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Đã khoá</span>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <p><span class="badge bg-info"><?= htmlspecialchars(getTen_Quyen($user['quyen'])) ?></span></p>
                        <p><strong>Ngày tạo:</strong> <?= isset($user['ngaytao']) ? date('d-m-Y', strtotime($user['ngaytao'])) : '' ?></p>

                        <div class="mt-3 d-grid">
                            <button class="btn btn-warning" id="btn-change-pass">Đổi mật khẩu</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: edit form -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h5>Chỉnh sửa thông tin</h5></div>
                    <div class="card-body">
                       

                        <form id="formTaiKhoan" method="post" action="action/tai-khoan-action.php" enctype="multipart/form-data" class="form form-horizontal validate-tooltip">
                            <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Họ</label>
                                    <input type="text" name="ho" class="form-control" value="<?= htmlspecialchars($user['ho'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Tên</label>
                                    <input type="text" name="ten" class="form-control" value="<?= htmlspecialchars($user['ten'] ?? '') ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Số điện thoại</label>
									<input type="text" name="sodt" class="form-control"
										value="<?= htmlspecialchars($user['sodt'] ?? '') ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Ảnh đại diện</label>
                                    <input type="file" name="hinhanh" accept="image/*" class="form-control">
                                </div>

                                <div class="col-12 d-flex justify-content-end mt-2">
                                    <button type="submit" name="update-ca-nhan" class="btn btn-primary me-2">Cập nhật</button>
                                    <a href="tai-khoan-ca-nhan.php" class="btn btn-light">Làm mới</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal đổi mật khẩu (SweetAlert2 or Bootstrap modal). We'll use Bootstrap modal here -->
<div class="modal fade" id="modalChangePass" tabindex="-1" aria-labelledby="modalChangePassLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formChangePassword" class="modal-content" method="post" action="action/tai-khoan-action.php">
      <div class="modal-header">
        <h5 class="modal-title" id="modalChangePassLabel">Đổi mật khẩu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
          <div class="mb-3">
              <label>Mật khẩu cũ</label>
              <input type="password" name="mk_cu" class="form-control" required>
          </div>
		   <div class="mb-3">
              <label>Xác nhận mật khẩu cũ</label>
              <input type="password" name="mk_cu_xacnhan" class="form-control" required>
          </div>
          <div class="mb-3">
              <label>Mật khẩu mới</label>
              <input type="password" name="mk_moi" class="form-control" required>
          </div>
          <div class="mb-3">
              <label>Xác nhận mật khẩu mới</label>
              <input type="password" name="mk_moi_xacnhan" class="form-control" required>
          </div>
          <div id="change-pass-message" class="mt-1"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" name="updateChangePassword" class="btn btn-primary">Đổi mật khẩu</button>
      </div>
    </form>
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const btnChangePass = document.getElementById("btn-change-pass");
    const modal = new bootstrap.Modal(document.getElementById("modalChangePass"));
    if (btnChangePass) {
        btnChangePass.addEventListener("click", () => {
            modal.show();
        });
    }
});
</script>
<?php include('./layouts/footer.php'); ?>
