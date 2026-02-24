<?php
	include('./layouts/header.php');
	require_once __DIR__ . '/connection/config.php';    // đường dẫn tới file config của bạn
	require_once __DIR__ . '/models/TaiKhoan.php';      // đường dẫn tới model


	// kết nối db (Database class assumed có trong config.php)
	$database = new Database();
	$conn = $database->getConnection();

	$taiKhoanModel = new TaiKhoan($conn);
	$accounts = $taiKhoanModel->getAll();

	function avatar_url($filename) {
		// nếu filename rỗng -> default
		$default = 'assets/images/avatar.png';
		$path = 'uploads/anh/' . $filename;
		if (!empty($filename) && file_exists(__DIR__ . '/' . $path)) {
			// cache busting bằng thời gian sửa file
			$v = filemtime(__DIR__ . '/' . $path);
			return $path . '?v=' . $v;
		}
		return $default;
	}
?>

<div class="page-content">
    <section class="section">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-people-fill me-2"></i>Danh sách tài khoản
                </h5>
                <a href="them-tai-khoan.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Thêm tài khoản
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center" id="tableTaiKhoan">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Ảnh</th>
                                <th>Họ & Tên</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Quyền</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($accounts)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Chưa có tài khoản nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php $i = 1; foreach ($accounts as $acc): ?>
                                    <tr>
                                         <td class="text-start"><?= htmlspecialchars($i) ?></td>
                                        <td>
                                            <img src="<?= htmlspecialchars(avatar_url($acc['hinhanh'] ?? '')) ?>?v=<?= time(); ?>" 
                                                 alt="avatar" class="rounded-circle shadow-sm"
                                                 style="width:45px;height:45px;object-fit:cover;">
                                        </td>
                                        <td class="fw-semibold text-dark text-start">
                                            <?= htmlspecialchars(($acc['ho'] ?? '') . ' ' . ($acc['ten'] ?? '')) ?>
                                        </td>
                                        <td class="text-start"><?= htmlspecialchars($acc['email'] ?? '') ?></td>
                                        <td class="text-start"><?= htmlspecialchars($acc['sodt'] ?? '') ?></td>
                                        <td >
                                           
                                                <span class="badge bg-info"><?= htmlspecialchars(getTen_Quyen($acc['quyen'])) ?></span>
                                           
                                        </td>
                                        <td>
                                            <?php if (($acc['trangthai'] ?? 0) == 1): ?>
                                                <span class="badge bg-success">Hoạt động</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Đã khóa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars(date('d-m-Y', strtotime($acc['ngaytao'] ?? ''))) ?></td>
                                        <td>
                                            <a href="<?php
												if (isset($_SESSION['user']['id']) && $_SESSION['user']['id'] == $acc['id']) {
													echo 'tai-khoan-ca-nhan.php';
												} else {
													echo 'them-tai-khoan.php?id=' . (int)$acc['id'];
												}
											?>" 
                                               class="btn btn-sm btn-outline-primary me-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= (int)$acc['id'] ?>">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const table = document.querySelector("#tableTaiKhoan");
    if (table) new simpleDatatables.DataTable(table);

    // ----------------------------------------------------
    // LOGIC XÓA (SỬ DỤNG EVENT DELEGATION)
    // ----------------------------------------------------
    document.addEventListener('click', function(e) {
        // Tìm nút có class .btn-delete mà sự kiện click diễn ra
        const btnDelete = e.target.closest('.btn-delete');
        
        if (btnDelete) {
            e.preventDefault();
            const id = btnDelete.getAttribute('data-id');

            // Kiểm tra Swal trước khi sử dụng
            if (typeof Swal === 'undefined') {
                if (confirm("Bạn có chắc chắn muốn xóa tài khoản này không?")) {
                    window.location.href = "action/tai-khoan-action.php?delete_id=" + id;
                }
                return;
            }

            Swal.fire({
                title: "Xác nhận xóa?",
                text: "Bạn có chắc chắn muốn xóa tài khoản này không?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có xóa",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Chú ý: URL action là add-tai-khoan-action.php
                    window.location.href = "action/tai-khoan-action.php?delete_id=" + id;
                }
            });
        }
    });
});
</script>

<?php include('./layouts/footer.php'); ?>
