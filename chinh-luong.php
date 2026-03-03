<?php 
// File: chinh-luong.php
    include('./layouts/header.php');

    include(__DIR__ . '/connection/config.php');
    include(__DIR__ . '/models/ChinhLuong.php');
    include(__DIR__ . '/models/NhanVien.php');

    // Khởi tạo đối tượng CSDL và Model
    $database = new Database();
    $conn = $database->getConnection();
    $chinhluong = new ChinhLuong($conn);
    $nhanvien = new NhanVien($conn);

    // ******* LẤY DỮ LIỆU CHUNG CHO VIEW *******
    // Lấy danh sách chỉnh lương (chỉ bản ghi mới nhất cho mỗi NV)
    $stmtShow = $chinhluong->getAllLatest();
    $arrShow = $stmtShow->fetchAll(PDO::FETCH_ASSOC);

    // Lấy danh sách tất cả nhân viên để chọn trong Form
    $stmtNhanVien = $nhanvien->getAllNV_danglam(); 
    $arrNhanVien = $stmtNhanVien->fetchAll(PDO::FETCH_ASSOC);

    // Lấy chi tiết để SỬA
    $chinhluongInfo = null;
    if (isset($_GET['idEdit'])) {
        $idEdit = intval($_GET['idEdit']);
        $chinhluongInfo = $chinhluong->getById($idEdit);
    }

    // Lấy hệ số lương cũ gợi ý (khi chọn nhân viên mới)
    $latestHeSo = null;
    if (isset($_GET['id_nv'])) {
       $id_nv_current = intval($_GET['id_nv']);
        
        // 1. Ưu tiên: Lấy hệ số mới nhất từ bảng chinh_luong
        $latestHeSo = $chinhluong->getLatestHeSoMoi($id_nv_current);

        // 2. Dự phòng: Nếu chưa từng chỉnh lương, lấy hệ số mặc định từ chức vụ
        if (is_null($latestHeSo)) {
            $latestHeSo = $chinhluong->getHeSoMacDinh($id_nv_current);
        }
    }

    $maChinhLuong = "MCL" . time();
    $row_acc = $_SESSION['user'] ?? ['ho' => 'Admin', 'ten' => 'User']; 
?>

<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Quản lý Điều Chỉnh Lương</h3>
                <p class="text-subtitle text-muted">Thực hiện nâng lương hoặc điều chỉnh hệ số lương cho nhân viên.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                         <li class="breadcrumb-item active" aria-current="page">Chỉnh lương</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-12">
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="text-primary fw-bold mb-0">
                            <i class="<?= isset($idEdit) ? 'bi bi-pencil-square' : 'bi bi-plus-circle' ?> me-2"></i>
                            <?= isset($idEdit) ? 'Chỉnh sửa quyết định lương' : 'Tạo quyết định điều chỉnh lương' ?>
                        </h5>
                    </div>
                    <div class="card-body mt-3">
                        <form id="formChinhLuong" class="validate-tooltip" method="post" action="action/chinh-luong-action.php">
                            <input type="hidden" name="id" value="<?= $chinhluongInfo['id'] ?? '' ?>">
                            
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Mã chỉnh lương</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" name="ma_chinhluong" class="form-control bg-light" 
                                            value="<?= $chinhluongInfo['ma_chinhluong'] ?? $maChinhLuong ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="col-md-5 mb-3">
                                    <label class="fw-bold mb-1">Nhân viên <span class="text-danger">*</span></label>
                                    <div class="choices-container">
                                        <select name="id_nv" id="selectNhanVien" class="form-select select2" required 
                                            <?= isset($idEdit) ? 'readonly disabled' : '' ?>>
                                            <option value="">-- Chọn Nhân viên --</option>
                                            <?php 
                                            $selected_id_nv = $chinhluongInfo['id_nv'] ?? ($_GET['id_nv'] ?? null);
                                            foreach ($arrNhanVien as $nv): 
                                                $isSelected = ($selected_id_nv == $nv['id']) ? 'selected' : '';
                                            ?>
                                                <option value="<?= $nv['id'] ?>" <?= $isSelected ?>>
                                                    <?= htmlspecialchars($nv['hoten']) ?> | <?= htmlspecialchars($nv['chucvu'] ?? 'N/A') ?> | <?= htmlspecialchars($nv['phongban'] ?? 'N/A') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php if (isset($idEdit) || isset($_GET['id_nv'])): ?>
                                        <input type="hidden" name="id_nv" value="<?= $selected_id_nv ?>">
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="fw-bold mb-1">Số quyết định</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                                        <input type="text" name="so_quyet_dinh" class="form-control" 
                                            placeholder="Số QĐ..."
                                            value="<?= $chinhluongInfo['so_quyet_dinh'] ?? '' ?>">
                                    </div>
                                </div>

                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <a href="chinh-luong.php" class="btn btn-light-secondary shadow-sm w-100">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Làm mới
                                    </a>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Hệ số cũ <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-calculator"></i></span>
                                        <input type="number" step="0.01" name="he_so_cu" class="form-control bg-light text-muted fw-bold" required readonly 
                                            value="<?= $chinhluongInfo['he_so_cu'] ?? ($latestHeSo ?? '') ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Hệ số mới <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text text-primary"><i class="bi bi-graph-up-arrow"></i></span>
                                        <input type="number" step="0.01" name="he_so_moi" class="form-control fw-bold text-primary" required 
                                            placeholder="Nhập hệ số..."
                                            value="<?= $chinhluongInfo['he_so_moi'] ?? '' ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Ngày hiệu lực <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                        <input type="date" name="ngay_hieu_luc" class="form-control" required 
                                            value="<?= $chinhluongInfo['ngay_hieu_luc'] ?? date('Y-m-d') ?>">
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Ngày ký quyết định <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                        <input type="date" name="ngay_ky_ket" class="form-control" required 
                                            value="<?= $chinhluongInfo['ngay_ky_ket'] ?? date('Y-m-d') ?>">
                                    </div>
                                </div>
                                
                                <div class="col-12 d-flex justify-content-end mt-3">
                                    <?php if (isset($idEdit)): ?>
                                        <button type="submit" name="update" class="btn btn-primary shadow-sm px-4">
                                            <i class="bi bi-save me-1"></i> Cập nhật quyết định
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="add" class="btn btn-success shadow-sm px-4">
                                            <i class="bi bi-plus-lg me-1"></i> Thêm mới quyết định
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                             <h5 class="fw-bold text-primary mb-0">
                                <i class="bi bi-list-columns-reverse me-2"></i>Danh sách điều chỉnh lương gần nhất
                            </h5>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="tableChinhLuong">
                                <thead class="table-light text-nowrap">
                                    <tr>
                                        <th class="text-center">STT</th>
                                        <th>Mã quyết định</th>
                                        <th>Nhân viên</th>
                                        <th class="text-center">Hệ số cũ</th>
                                        <th class="text-center">Hệ số mới</th>                                    
                                        <th>Ngày hiệu lực</th>
                                        <th>Số QĐ</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $stt = 1;
                                    foreach ($arrShow as $cl): 
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $stt++ ?></td>
                                        <td>
                                            <span class="badge bg-light-primary text-primary">
                                                <i class="bi bi-upc me-1"></i><?= htmlspecialchars($cl['ma_chinhluong']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold"><?= htmlspecialchars($cl['ten_nhanvien']) ?></span>
                                                <small class="text-muted fst-italic">
                                                    <?= htmlspecialchars($cl['chucvu']) ?> - <?= htmlspecialchars($cl['phongban']) ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted"><?= htmlspecialchars($cl['he_so_cu']) ?> </td>
                                        <td class="text-center fw-bold text-success fs-6">
                                            <?= htmlspecialchars($cl['he_so_moi']) ?>
                                            <i class="bi bi-arrow-up-short"></i>
                                        </td>
                                        <td><i class="bi bi-calendar-event me-1 text-info"></i><?= date('d/m/Y', strtotime($cl['ngay_hieu_luc'])) ?></td>
                                        <td><?= htmlspecialchars($cl['so_quyet_dinh'] ?? 'N/A') ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-info btn-view-history shadow-sm"
                                                        data-idnv="<?= $cl['id_nhanvien'] ?>" 
                                                        title="Xem lịch sử"
                                                        data-name="<?= htmlspecialchars($cl['ten_nhanvien'], ENT_QUOTES) ?>">
                                                    <i class="bi bi-clock-history"></i>
                                                </button>
                                                <a href="chinh-luong.php?idEdit=<?= $cl['id'] ?>" class="btn btn-sm btn-outline-warning shadow-sm" title="Sửa">
                                                    <i class="bi bi-pencil-square"></i> 
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete shadow-sm"
                                                        title="Xóa"
                                                        data-id="<?= $cl['id'] ?>" data-name="<?= htmlspecialchars($cl['ten_nhanvien'], ENT_QUOTES) ?>">
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
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Lịch Sử -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true" style="z-index: 9999;">
  <div class="modal-dialog modal-lg modal-dialog-scrollable"> 
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="historyModalLabel"><i class="bi bi-clock-history me-2"></i> Lịch sử Điều chỉnh Lương</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body bg-light">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <h6 id="nvNameHistory" class="fw-bold text-primary mb-1">Nhân viên: </h6>
                <small id="nvDetailsHistory" class="text-muted"></small>
            </div>
        </div>
        
        <div id="historyContent" class="bg-white p-3 rounded shadow-sm">
            <p class="text-center text-muted">Đang tải dữ liệu...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------
    // 1. CẤU HÌNH VÀ TIỆN ÍCH
    // ----------------------------------------------------
    
    // Khởi tạo Datatable
    if (typeof simpleDatatables !== 'undefined' && document.getElementById('tableChinhLuong')) {
        new simpleDatatables.DataTable("#tableChinhLuong", {
            labels: {
                placeholder: "Tìm kiếm...",
                perPage: "mục/trang",
                noRows: "Không có dữ liệu",
                info: "Hiển thị {start} đến {end} của {rows} mục",
            }
        });
    }

    // Choices.js cho Select Nhân viên
    const choicesEl = document.getElementById('selectNhanVien');
    if (choicesEl) {
        new Choices(choicesEl, {
            searchEnabled: true,
            itemSelectText: '',   
            shouldSort: false,
            placeholder: true,
            placeholderValue: 'Tìm kiếm nhân viên...',
            noResultsText: 'Không tìm thấy kết quả',
        });
    }

    // Thêm CSS loading
    const style = document.createElement('style');
    style.innerHTML = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } } .spin { animation: spin 1s linear infinite; }';
    document.head.appendChild(style);

    // ----------------------------------------------------
    // 2. LOGIC TỰ ĐỘNG LẤY HỆ SỐ CŨ
    // ----------------------------------------------------
    const selectNhanVien = document.getElementById('selectNhanVien');
    if (selectNhanVien && !document.querySelector('button[name="update"]')) {
        selectNhanVien.addEventListener('change', function() {
            const id_nv = this.value;
            const currentUrlPath = window.location.pathname;
            
            if (id_nv) {
                // Tải lại trang với tham số id_nv để PHP lấy hệ số cũ
                window.location.href = `${currentUrlPath}?id_nv=${id_nv}`;
            } else {
                window.location.href = currentUrlPath;
            }
        });
    }

    // ----------------------------------------------------
    // 3. LOGIC SỰ KIỆN ĐỘNG (XÓA và XEM LỊCH SỬ)
    // ----------------------------------------------------
    document.addEventListener('click', function(e) {
        // --- A. XÓA ---
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) {
            e.preventDefault();
            const id = btnDelete.dataset.id;
            const name = btnDelete.dataset.name;

            const confirmOptions = {
                title: 'Xác nhận xóa?',
                html: `Bạn có chắc chắn muốn xóa Quyết định chỉnh lương của nhân viên <b>${name}</b> không?`,
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
                        window.location.href = `action/chinh-luong-action.php?delete=${id}`;
                    }
                });
            } else {
                if (confirm(`Xóa quyết định của ${name}?`)) {
                    window.location.href = `action/chinh-luong-action.php?delete=${id}`;
                }
            }
        }
        
        // --- B. XEM LỊCH SỬ ---
        const btnViewHistory = e.target.closest('.btn-view-history');
        if (btnViewHistory) {
            e.preventDefault();
            const id_nv = btnViewHistory.dataset.idnv;
            const nv_name = btnViewHistory.dataset.name;
            const modalBody = document.getElementById('historyContent');
            
            // Set thông tin cơ bản
            document.getElementById('nvNameHistory').textContent = 'Nhân viên: ' + nv_name;
            document.getElementById('nvDetailsHistory').innerHTML = '<span class="text-info"><i class="bi bi-arrow-clockwise spin me-1"></i>Đang tải thông tin...</span>'; 
            modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Đang tải lịch sử...</p></div>';
            
            // Show modal
            const historyModalElement = document.getElementById('historyModal');
            const historyModal = new bootstrap.Modal(historyModalElement);
            historyModal.show();

            // Fetch Data
            fetch(`action/fetch-chinh-luong-history.php?id_nv=${id_nv}`)
                .then(response => response.json())
                .then(result => {
                    // Update Info
                    if (result.nhanvien_info) {
                        const info = result.nhanvien_info;
                        document.getElementById('nvNameHistory').innerHTML = `<i class="bi bi-person-check me-2"></i>${info.hoten} <span class="badge bg-secondary ms-2">${info.ma_nv}</span>`;
                        document.getElementById('nvDetailsHistory').innerHTML = 
                            `<span class="me-3"><i class="bi bi-diagram-3 me-1"></i>${info.phongban || 'N/A'}</span> 
                             <span><i class="bi bi-briefcase me-1"></i>${info.chucvu || 'N/A'}</span>`;
                    }

                    // Update History Table
                    if (result.success && result.data.length > 0) {
                        let html = '<div class="table-responsive"><table class="table table-hover table-bordered mb-0 align-middle">';
                        html += '<thead class="table-light text-primary"><tr><th>Mã CL</th><th>Hệ số cũ</th><th>Hệ số mới</th><th>Ngày hiệu lực</th><th>Người tạo</th></tr></thead><tbody>';
                        
                        result.data.forEach(item => {
                            const ngayHieuLuc = new Date(item.ngay_hieu_luc).toLocaleDateString('vi-VN');
                            const arrowIcon = parseFloat(item.he_so_moi) > parseFloat(item.he_so_cu) 
                                ? '<i class="bi bi-arrow-up text-success"></i>' 
                                : '<i class="bi bi-arrow-down text-danger"></i>';

                            html += `<tr>
                                <td><span class="badge bg-light text-dark border">${item.ma_chinhluong}</span></td>
                                <td class="text-muted text-center">${item.he_so_cu}</td>
                                <td class="fw-bold text-center text-primary">${item.he_so_moi} ${arrowIcon}</td>
                                <td>${ngayHieuLuc}</td>
                                <td class="small">${item.nguoitao_name}</td>
                            </tr>`;
                        });
                        
                        html += '</tbody></table></div>';
                        modalBody.innerHTML = html;
                    } else if (result.success) {
                        modalBody.innerHTML = '<div class="text-center py-3"><img src="assets/images/empty-state.png" style="width:60px; opacity:0.5"><p class="text-muted mt-2">Chưa có lịch sử điều chỉnh nào.</p></div>';
                    } else {
                          modalBody.innerHTML = `<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>${result.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error(error);
                    modalBody.innerHTML = `<div class="alert alert-danger">Lỗi kết nối: ${error.message}</div>`;
                });
        }
    });
});
</script>

<?php include('./layouts/footer.php'); ?>
