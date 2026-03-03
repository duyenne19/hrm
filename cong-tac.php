<?php 
include('./layouts/header.php');
include(__DIR__ . '/connection/config.php');
include(__DIR__ . '/models/CongTac.php');

$database = new Database();
$conn = $database->getConnection();
$congTacModel = new CongTac($conn);

// Lấy danh sách công tác
$stmt = $congTacModel->getAll();
$ds_congtac = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Danh Sách Công Tác</h3>
                <p class="text-subtitle text-muted">Quản lý lịch trình công tác của nhân viên.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Công tác</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card shadow border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-primary mb-0">
                    <i class="bi bi-list-check me-2"></i> Danh sách lịch công tác
                </h5>
                <a href="them-cong-tac.php" class="btn btn-primary shadow-sm btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Thêm mới
                </a>
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle" id="tableCongTac">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">STT</th>
                            <th>Mã công tác</th>
                            <th>Nhân viên</th>
                            <th>Địa điểm</th>
                            <th>Thời gian</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($ds_congtac as $ct): ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>
                            <td>
                                <span class="badge bg-light-primary text-primary">
                                    <i class="bi bi-upc-scan me-1"></i><?= htmlspecialchars($ct['ma_ctac']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-warning me-2">
                                        <span class="avatar-content"><?= strtoupper(substr($ct['nhanvien_name'] ?? 'N', 0, 1)) ?></span>
                                    </div>
                                    <span class="fw-bold"><?= htmlspecialchars($ct['nhanvien_name']) ?></span>
                                </div>
                            </td>
                            <td>
                                <i class="bi bi-geo-alt text-danger me-1"></i>
                                <?= htmlspecialchars($ct['dd_ctac']) ?>
                            </td>
                            <td>
                                <div class="d-flex flex-column small">
                                    <span><i class="bi bi-calendar-event text-success me-1"></i> Bắt đầu: <strong><?= !empty($ct['bdau_ctac']) ? date('d/m/Y', strtotime($ct['bdau_ctac'])) : '---' ?></strong></span>
                                    <span><i class="bi bi-calendar-check text-danger me-1"></i> Kết thúc: <strong><?= !empty($ct['kthuc_ctac']) && $ct['kthuc_ctac'] != '0000-00-00' ? date('d/m/Y', strtotime($ct['kthuc_ctac'])) : '<span class="text-success small fst-italic">Đang công tác</span>' ?></strong></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-info btn-view shadow-sm"
                                        title="Xem chi tiết"
                                        data-ma="<?= htmlspecialchars($ct['ma_ctac']) ?>"
                                        data-nv="<?= htmlspecialchars($ct['nhanvien_name']) ?>"
                                        data-dd="<?= htmlspecialchars($ct['dd_ctac']) ?>"
                                        data-bd="<?= date('d/m/Y', strtotime($ct['bdau_ctac'])) ?>"
                                        data-kt="<?= date('d/m/Y', strtotime($ct['kthuc_ctac'])) ?>"
                                        data-md="<?= htmlspecialchars($ct['mucdich_ctac']) ?>"
                                        data-nguoitao="<?= htmlspecialchars($ct['nguoitao_name']) ?>"
                                        data-ngaytao="<?= date('H:i d/m/Y', strtotime($ct['ngaytao'])) ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="them-cong-tac.php?idEdit=<?= $ct['id'] ?>" class="btn btn-sm btn-outline-warning shadow-sm" title="Chỉnh sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete shadow-sm"
                                        title="Xóa"
                                        data-id="<?= $ct['id'] ?>" 
                                        data-name="<?= htmlspecialchars($ct['ma_ctac']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Init DataTable
    const tableElement = document.getElementById('tableCongTac');
    if (tableElement) {
        new simpleDatatables.DataTable(tableElement, {
            searchable: true,
            fixedHeight: false,
            perPage: 10,
            labels: {
                placeholder: "Tìm kiếm...",
                perPage: "mục mỗi trang",
                noRows: "Không có dữ liệu",
                info: "Hiển thị {start} đến {end} của {rows} mục",
            }
        });
    }

    // 2. Event Delegation for Buttons
    document.addEventListener('click', function(e) {
        // --- VIEW DETAILS ---
        const btnView = e.target.closest('.btn-view');
        if (btnView) {
            const data = btnView.dataset;
            Swal.fire({
                title: `<h5 class="fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>Chi tiết công tác</h5>`,
                html: `
                    <div class="text-start p-2">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-bold text-nowrap" style="width: 130px;"><i class="bi bi-upc-scan text-primary me-2"></i>Mã CT:</td>
                                <td><span class="badge bg-light-primary text-primary">${data.ma}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-nowrap"><i class="bi bi-person text-success me-2"></i>Nhân viên:</td>
                                <td class="fw-bold">${data.nv}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-nowrap"><i class="bi bi-geo-alt text-danger me-2"></i>Địa điểm:</td>
                                <td>${data.dd}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-nowrap"><i class="bi bi-calendar-range text-info me-2"></i>Thời gian:</td>
                                <td>${data.bd} <i class="bi bi-arrow-right mx-1 text-muted"></i> ${data.kt}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-nowrap" colspan="2"><i class="bi bi-card-text text-secondary me-2"></i>Mục đích:</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="alert alert-light border mb-0 p-2 text-dark bg-white">
                                        ${data.md}
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="d-flex justify-content-between mt-3 pt-2 border-top small text-muted">
                            <span><i class="bi bi-person-check me-1"></i>Tạo bởi: ${data.nguoitao}</span>
                            <span><i class="bi bi-clock me-1"></i>${data.ngaytao}</span>
                        </div>
                    </div>
                `,
                width: 600,
                showConfirmButton: true,
                confirmButtonText: '<i class="bi bi-check2 me-1"></i> Đóng',
                confirmButtonColor: '#435ebe',
                showCloseButton: true
            });
        }

        // --- DELETE CONFIRM ---
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) {
            const id = btnDelete.dataset.id;
            const name = btnDelete.dataset.name;
            
            Swal.fire({
                title: 'Xác nhận xóa?',
                html: `Bạn có chắc chắn muốn xóa công tác mã <b>${name}</b> không?<br>Hành động này không thể hoàn tác!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash"></i> Xóa ngay',
                cancelButtonText: 'Hủy bỏ',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `action/cong-tac-action.php?delete=${id}`;
                }
            });
        }
    });
});
</script>
<?php include('./layouts/footer.php'); ?>
