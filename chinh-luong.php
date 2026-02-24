<?php 
// File: chinh-luong.php
	include('./layouts/header.php');

	include(__DIR__ . '/connection/config.php');
	include(__DIR__ . '/models/ChinhLuong.php');
	include(__DIR__ . '/models/NhanVien.php'); // Cần để lấy danh sách nhân viên cho dropdown

	// Khởi tạo đối tượng CSDL và Model
	$database = new Database();
	$conn = $database->getConnection();
	$chinhluong = new ChinhLuong($conn);
	$nhanvien = new NhanVien($conn);

	// ******* LẤY DỮ LIỆU CHUNG CHO VIEW (3. giao diện) *******
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
	<section id="basic-vertical-layouts">
		
		<div class="row match-height">
			<div class="col-12">
				<div class="card shadow-sm mb-4">
					<div class="card-header">
						<h5 class="text-primary fw-bold mb-0">
							<i class="bi bi-people-fill me-2"></i>
							<?= isset($idEdit) ? 'Chỉnh sửa chỉnh lương' : 'Thêm mới chỉnh lương' ?>
						</h5>
					</div>
					<div class="card-body">
						<form id="formChinhLuong" class="validate-tooltip" method="post" action="action/chinh-luong-action.php">
							<input type="hidden" name="id" value="<?= $chinhluongInfo['id'] ?? '' ?>">
							
							<div class="row">
								<div class="col-md-2 mb-3">
									<label>Mã chỉnh lương</label>
									<input type="text" name="ma_chinhluong" class="form-control" 
										value="<?= $chinhluongInfo['ma_chinhluong'] ?? $maChinhLuong ?>" readonly>
								</div>
								
								<div class="col-md-5 mb-3">
									<label>Nhân viên <span class="text-danger">*</span></label>
									<select name="id_nv" id="selectNhanVien" class="form-select select2" required 
										<?= isset($idEdit) ? 'readonly disabled' : '' ?>>
										<option value="">-- Chọn Nhân viên --</option>
										<?php 
										// ⭐️ Khởi tạo biến ID nhân viên được chọn, ưu tiên từ idEdit, sau đó là từ GET
										$selected_id_nv = $chinhluongInfo['id_nv'] ?? ($_GET['id_nv'] ?? null);
										
										foreach ($arrNhanVien as $nv): 
											// ⭐️ Tạo điều kiện selected mới
											$isSelected = ($selected_id_nv == $nv['id']) ? 'selected' : '';
										?>
											<option value="<?= $nv['id'] ?>" <?= $isSelected ?>>
												<?= htmlspecialchars($nv['hoten']) ?> | <?= htmlspecialchars($nv['chucvu'] ?? 'N/A') ?> | <?= htmlspecialchars($nv['phongban'] ?? 'N/A') ?>
											</option>
										<?php endforeach; ?>
									</select>
									<?php if (isset($idEdit) || isset($_GET['id_nv'])): ?>
										<input type="hidden" name="id_nv" value="<?= $selected_id_nv ?>">
									<?php endif; ?>
								</div>
								
								<div class="col-md-2 mb-3">
									<label>Hệ số cũ <span class="text-danger">*</span></label>
									<input type="number" step="0.01" name="he_so_cu" class="form-control" required readonly 
										value="<?= $chinhluongInfo['he_so_cu'] ?? ($latestHeSo ?? '') ?>">
								</div>
								
								<div class="col-md-2 mb-3">
									<label>Hệ số mới <span class="text-danger">*</span></label>
									<input type="number" step="0.01" name="he_so_moi" class="form-control" required 
										value="<?= $chinhluongInfo['he_so_moi'] ?? '' ?>">
								</div>
								
								<div class="col-md-2 mb-3">
									<label>Số quyết định</label>
									<input type="text" name="so_quyet_dinh" class="form-control" 
										value="<?= $chinhluongInfo['so_quyet_dinh'] ?? '' ?>">
								</div>
								
								<div class="col-md-2 mb-3">
									<label>Ngày ký kết <span class="text-danger">*</span></label>
									<input type="date" name="ngay_ky_ket" class="form-control" required 
										value="<?= $chinhluongInfo['ngay_ky_ket'] ?? date('Y-m-d') ?>">
								</div>
								
								<div class="col-md-2 mb-3">
									<label>Ngày hiệu lực <span class="text-danger">*</span></label>
									<input type="date" name="ngay_hieu_luc" class="form-control" required 
										value="<?= $chinhluongInfo['ngay_hieu_luc'] ?? date('Y-m-d') ?>">
								</div>
								
								<div class="col-md-6 d-flex align-items-end mb-3">
									<?php if (isset($idEdit)): ?>
										<button type="submit" name="update" class="btn btn-primary me-2">
											<i class="bi bi-save"></i> Cập nhật quyết định
										</button>
									<?php else: ?>
										<button type="submit" name="add" class="btn btn-success me-2">
											<i class="bi bi-plus-circle"></i> Thêm mới quyết định
										</button>
									<?php endif; ?>
									<a href="chinh-luong.php" class="btn btn-light">Làm mới</a>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="col-12">
				<div class="card shadow-sm">
					<div class="card-body">
						<h5 class="fw-bold text-primary mb-3">📋 Danh sách chỉnh lương</h5>
						<table class="table table-hover" id="tableChinhLuong">
							<thead class="table-light">
								<tr>
									<th>STT</th>
									<th>Mã chỉnh lương</th>
									<th>Nhân viên</th>
									<th>Phòng ban</th>
									<th>Chức vụ</th>
									<th>Hệ số cũ</th>
									<th>Hệ số mới</th>									
									<th>Ngày hiệu lực</th>
									<th>Số quyết định</th>
									<th>Hành động</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									$stt =1;
								foreach ($arrShow as $cl): 
								?>
								<tr>
									<td><?= htmlspecialchars($stt) ?></td>
									<td><?= htmlspecialchars($cl['ma_chinhluong']) ?></td>
									<td><?= htmlspecialchars($cl['ten_nhanvien']) ?></td>
									<td><?= htmlspecialchars($cl['phongban']) ?></td>
									<td><?= htmlspecialchars($cl['chucvu']) ?></td>
									<td><?= htmlspecialchars($cl['he_so_cu']) ?> </td>
									<td><?= htmlspecialchars($cl['he_so_moi']) ?></td>
									<td><?= date('d/m/Y', strtotime($cl['ngay_hieu_luc'])) ?></td>
									<td><?= htmlspecialchars($cl['so_quyet_dinh']) ?></td>
									<td>
										<button type="button" class="btn btn-sm btn-outline-info me-1 btn-view-history"
												data-idnv="<?= $cl['id_nhanvien'] ?>" data-name="<?= htmlspecialchars($cl['ten_nhanvien'], ENT_QUOTES) ?>">
											<i class="bi bi-eye"></i>
										</button>
										<a href="chinh-luong.php?idEdit=<?= $cl['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
											<i class="bi bi-pencil"></i> 
										</a>
										<button type="button" class="btn btn-sm btn-outline-danger btn-delete"
												data-id="<?= $cl['id'] ?>" data-name="<?= htmlspecialchars($cl['ten_nhanvien'], ENT_QUOTES) ?>">
											<i class="bi bi-trash"></i> 
										</button>
									</td>
								</tr>
								<?php $stt++;
									endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true" style="z-index: 9999;">
  <div class="modal-dialog modal-xl modal-dialog-scrollable"> 
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="historyModalLabel"><i class="bi bi-clock-history me-2"></i> Lịch sử Điều chỉnh Lương</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3 p-2 border-start border-5 border-primary bg-light">
    <h6 id="nvNameHistory" class="fw-bold text-primary mb-1">Nhân viên: </h6>
    <small id="nvDetailsHistory" class="text-muted"></small>
</div>
        <div id="historyContent">
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
        new simpleDatatables.DataTable("#tableChinhLuong");
    }

    // Thêm CSS cho hiệu ứng xoay (Dùng cho icon loading)
    const style = document.createElement('style');
    style.innerHTML = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } } .spin { animation: spin 1s linear infinite; }';
    document.head.appendChild(style);


    // ----------------------------------------------------
    // 2. LOGIC ĐIỀU CHỈNH HỆ SỐ CŨ (Reload trang)
    // ----------------------------------------------------
    const selectNhanVien = document.getElementById('selectNhanVien');
    
    // Kiểm tra xem có đang ở chế độ thêm mới (không có nút update)
    if (selectNhanVien && !document.querySelector('button[name="update"]')) {
        selectNhanVien.addEventListener('change', function() {
            const id_nv = this.value;
            // Lấy path hiện tại của trang (ví dụ: /chinh-luong.php)
            const currentUrlPath = window.location.pathname;
            
            if (id_nv) {
                // Tải lại trang với tham số id_nv để PHP tính toán Hệ số cũ
                window.location.href = `${currentUrlPath}?id_nv=${id_nv}`;
            } else {
                // Nếu chọn option "Chọn Nhân viên", tải lại trang gốc (xóa tham số)
                window.location.href = currentUrlPath;
            }
        });
    }

    // ----------------------------------------------------
    // 3. LOGIC SỰ KIỆN ĐỘNG (XÓA và XEM LỊCH SỬ)
    // SỬ DỤNG EVENT DELEGATION
    // ----------------------------------------------------
    document.addEventListener('click', function(e) {

        // --- A. XỬ LÝ NÚT XÓA (.btn-delete) ---
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) {
            e.preventDefault();
            
            const id = btnDelete.dataset.id;
            const name = btnDelete.dataset.name;

            // Xử lý SweetAlert
            if (typeof Swal === 'undefined') {
                if (confirm(`Bạn có chắc chắn muốn xóa Quyết định chỉnh lương của nhân viên "${name}" không?`)) {
                    window.location.href = `action/chinh-luong-action.php?delete=${id}`;
                }
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa Quyết định chỉnh lương của nhân viên "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có xóa",
                cancelButtonText: "Hủy",
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    window.location.href = `action/chinh-luong-action.php?delete=${id}`;
                }
            });
        }
        
        // --- B. XỬ LÝ NÚT XEM LỊCH SỬ (.btn-view-history) ---
        const btnViewHistory = e.target.closest('.btn-view-history');
        if (btnViewHistory) {
            e.preventDefault();
            
            const id_nv = btnViewHistory.dataset.idnv;
            const nv_name = btnViewHistory.dataset.name;
            const modalBody = document.getElementById('historyContent');
            
            // 1. Hiển thị trạng thái tải và Modal
            document.getElementById('nvNameHistory').textContent = 'Nhân viên: ' + nv_name;
            document.getElementById('nvDetailsHistory').innerHTML = '<span class="text-info">Đang tải thông tin...</span>'; // Đặt trạng thái tải
            modalBody.innerHTML = '<p class="text-center text-muted"><i class="bi bi-arrow-clockwise spin me-2"></i>Đang tải dữ liệu lịch sử...</p>';
            
            // Khởi tạo và hiển thị Modal
            const historyModalElement = document.getElementById('historyModal');
            const historyModal = new bootstrap.Modal(historyModalElement);
            historyModal.show();

            // 2. Gọi AJAX để lấy lịch sử
            fetch(`action/fetch-chinh-luong-history.php?id_nv=${id_nv}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Lỗi HTTP: ${response.status} (${response.statusText})`);
                    }
                    return response.json();
                })
                .then(result => {
                    // CẬP NHẬT THÔNG TIN NHÂN VIÊN (Phòng ban/Chức vụ)
                    if (result.nhanvien_info) {
                        const info = result.nhanvien_info;
                        document.getElementById('nvNameHistory').textContent = 
                            `Nhân viên: ${info.hoten} (${info.ma_nv})`;
                        document.getElementById('nvDetailsHistory').innerHTML = 
                            `<b>Phòng ban:</b> ${info.phongban || 'N/A'} |  <b>Chức vụ:</b> ${info.chucvu || 'N/A'}`;
                    } else {
                        document.getElementById('nvDetailsHistory').textContent = 'Không tìm thấy thông tin chi tiết nhân viên.';
                    }

                    // HIỂN THỊ LỊCH SỬ CHỈNH LƯƠNG
                    if (result.success && result.data.length > 0) {
                        let html = '<table class="table table-bordered table-striped table-sm">';
                        html += '<thead class="table-dark"><tr><th>Mã CL</th><th>Hệ số cũ</th><th>Hệ số mới</th><th>Ngày hiệu lực</th><th>Số QĐ</th><th>Người tạo</th><th>Ngày tạo</th></tr></thead><tbody>';
                        
                        result.data.forEach(item => {
                            // Định dạng ngày tháng
                            const ngayHieuLuc = new Date(item.ngay_hieu_luc).toLocaleDateString('vi-VN', { year: 'numeric', month: '2-digit', day: '2-digit' });
                            const ngayTao = new Date(item.ngaytao).toLocaleDateString('vi-VN', { year: 'numeric', month: '2-digit', day: '2-digit' });
                            
                            html += `<tr>
                                <td>${item.ma_chinhluong}</td>
                                <td>${item.he_so_cu}</td>
                                <td>${item.he_so_moi}</td>
                                <td>${ngayHieuLuc}</td>
                                <td>${item.so_quyet_dinh ?? ''}</td>
                                <td>${item.nguoitao_name}</td>
                                <td>${ngayTao}</td>
                            </tr>`;
                        });
                        
                        html += '</tbody></table>';
                        modalBody.innerHTML = html;
                    } else if (result.success && result.data.length === 0) {
                        modalBody.innerHTML = '<p class="alert alert-info text-center">Không tìm thấy lịch sử điều chỉnh lương cho nhân viên này.</p>';
                    } else {
                          modalBody.innerHTML = `<p class="alert alert-danger text-center">Lỗi Logic Server: ${result.message || 'Không rõ.'}</p>`;
                    }
                })
                .catch(error => {
                    console.error('Lỗi tải lịch sử:', error);
                    modalBody.innerHTML = `<p class="alert alert-danger text-center">Đã xảy ra lỗi khi tải dữ liệu: ${error.message}.</p>`;
                });
        }
    });

	// Tạo search cho selection ***********************************
	new Choices('#selectNhanVien', {
        searchEnabled: true,
        itemSelectText: '',   // tắt chữ "Press to select"
        shouldSort: false     // giữ nguyên thứ tự
    });
	// ***********************************
});
</script>

<?php include('./layouts/footer.php'); ?>