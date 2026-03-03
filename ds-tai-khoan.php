<?php
    include('./layouts/header.php');
    require_once __DIR__ . '/connection/config.php';
    require_once __DIR__ . '/models/TaiKhoan.php';

    $database = new Database();
    $conn = $database->getConnection();

    $taiKhoanModel = new TaiKhoan($conn);
    $accounts = $taiKhoanModel->getAll();

    function avatar_url($filename) {
        $default = 'assets/images/logo/logo-sm.png'; // Avatar mặc định
        $path = 'uploads/anh/' . $filename;
        if (!empty($filename) && file_exists(__DIR__ . '/' . $path)) {
            return $path . '?v=' . time();
        }
        return $default;
    }
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Quản lý Tài khoản</h3>
                <p class="text-subtitle text-muted">Danh sách tài khoản hệ thống và phân quyền.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                         <li class="breadcrumb-item active" aria-current="page">Tài khoản</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card shadow border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                 <h5 class="fw-bold text-primary mb-0">
                    <i class="bi bi-people-fill me-2"></i>Danh sách tài khoản
                </h5>
                <a href="them-tai-khoan.php" class="btn btn-primary shadow-sm">
                    <i class="bi bi-person-plus-fill me-1"></i> Thêm mới
                </a>
            </div>

            <div class="card-body mt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableTaiKhoan">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">STT</th>
                                <th class="text-center">Avatar</th>
                                <th>Họ & Tên</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th class="text-center">Vai trò</th>
                                <th class="text-center">Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($accounts)): ?>
                                <!-- Không có dữ liệu -->
                            <?php else: ?>
                                <?php $i = 1; foreach ($accounts as $acc): ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td class="text-center">
                                            <div class="avatar avatar-md">
                                                <img src="<?= htmlspecialchars(avatar_url($acc['hinhanh'] ?? '')) ?>" alt="avatar" class="rounded-circle shadow-sm" style="object-fit: cover;">
                                            </div>
                                        </td>
                                        <td class="fw-bold text-primary">
                                            <?= htmlspecialchars(($acc['ho'] ?? '') . ' ' . ($acc['ten'] ?? '')) ?>
                                        </td>
                                        <td><a href="mailto:<?= htmlspecialchars($acc['email'] ?? '') ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($acc['email'] ?? '') ?></a></td>
                                        <td><?= htmlspecialchars($acc['sodt'] ?? '') ?></td>
                                        <td class="text-center">
                                            <?php 
                                            // Mapping màu sắc cho quyền (nếu muốn)
                                            $roleColor = 'bg-secondary';
                                            $roleName = getTen_Quyen($acc['quyen']);
                                            if ($roleName == 'Admin') $roleColor = 'bg-danger';
                                            elseif ($roleName == 'HR') $roleColor = 'bg-warning text-dark';
                                            elseif ($roleName == 'Kế toán') $roleColor = 'bg-info text-dark';
                                            ?>
                                            <span class="badge <?= $roleColor ?>"><?= htmlspecialchars($roleName) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if (($acc['trangthai'] ?? 0) == 1): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Hoạt động</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><i class="bi bi-lock-fill me-1"></i>Đã khóa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($acc['ngaytao'] ?? ''))) ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="<?php
                                                    if (isset($_SESSION['user']['id']) && $_SESSION['user']['id'] == $acc['id']) {
                                                        echo 'tai-khoan-ca-nhan.php';
                                                    } else {
                                                        echo 'them-tai-khoan.php?id=' . (int)$acc['id'];
                                                    }
                                                ?>" class="btn btn-sm btn-outline-primary shadow-sm" title="Chỉnh sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <?php if (!isset($_SESSION['user']['id']) || $_SESSION['user']['id'] != $acc['id']): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger shadow-sm btn-delete" 
                                                        data-id="<?= (int)$acc['id'] ?>"
                                                        data-name="<?= htmlspecialchars(($acc['ho'] ?? '') . ' ' . ($acc['ten'] ?? '')) ?>"
                                                        title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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

    // Xử lý xóa
    document.addEventListener('click', function(e) {
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) {
            e.preventDefault();
            const id = btnDelete.dataset.id;
            const name = btnDelete.dataset.name;

            const confirmOptions = {
                title: 'Xác nhận xóa?',
                html: `Bạn có chắc chắn muốn xóa tài khoản <b>"${name}"</b> không?<br><small class="text-danger">Hành động này không thể hoàn tác!</small>`,
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
                         window.location.href = `action/tai-khoan-action.php?delete=${id}`;
                    }
                });
            } else {
                 if (confirm(`Xóa tài khoản "${name}"?`)) {
                    window.location.href = `action/tai-khoan-action.php?delete=${id}`;
                }
            }
        }
    });
});
</script>

<?php include('./layouts/footer.php'); ?>
