<?php 
include('./layouts/header.php');
include('./view/luong-view-action.php'); 

?>
<style>
    @media print {
        
        /* Đảm bảo toàn bộ cấu trúc trang (trừ Modal) bị ẩn */
        
        /* 1. ẨN CÁC THÀNH PHẦN CHUNG: SIDEBAR, HEADER, FOOTER */
        /* Giả định các ID/Class phổ biến của menu và header */
        #sidebar, 
        .sidebar-wrapper, 
        .header, 
        .navbar, 
        .page-heading,
        .footer,
        .modal-footer,
        .modal-header,
        
        /* Ẩn tất cả nội dung nằm ngoài Modal */
        .page-content > :not(.modal) {
             display: none !important;
        }

        /* 2. HIỂN THỊ CẤU TRÚC MODAL VÀ NỘI DUNG */
        
        /* Đảm bảo Modal luôn hiển thị và không có nền mờ */
        .modal, 
        #luongDetailModal {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            position: absolute !important; /* Dùng absolute để tránh lỗi hiển thị khi cuộn */
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: auto !important;
            overflow: visible !important;
            z-index: 99999 !important; 
            background-color: transparent !important;
        }

        /* 3. ĐẢM BẢO KHUNG MODAL RỘNG VÀ KHÔNG VIỀN */
        #luongDetailModal .modal-dialog {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: none !important;
            transform: none !important; 
        }
        
        #luongDetailModal .modal-content {
            border: none !important;
            box-shadow: none !important;
            background: #fff !important;
            min-height: 100vh; /* Đảm bảo chiều cao tối thiểu cho trang in */
        }
        
        /* 4. ĐẢM BẢO NỘI DUNG CHÍNH HIỂN THỊ VÀ TỐI ƯU */
        #luongDetailModal .modal-body {
            display: block !important;
            overflow: visible !important;
            padding: 30px 40px !important; 
        }
    }
</style>
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Quản lý Lương Nhân Viên</h3>
                <p class="text-subtitle text-muted">Tính lương, xem lịch sử và xuất báo cáo lương.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lương</li>
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
                            <i class="<?= isset($idEdit) ? 'bi bi-pencil-square' : 'bi bi-calculator' ?> me-2"></i>
                            <?= isset($idEdit) ? 'Chỉnh sửa phiếu lương' : 'Tính lương tháng mới' ?>
                        </h5>
                    </div>
                    <div class="card-body mt-3">
                        <form id="formLuong" class="validate-tooltip" method="post" action="action/luong-action.php">
                            <input type="hidden" name="id" value="<?= $luongInfo['id'] ?? '' ?>">
                            
                            <input type="hidden" name="luong_co_ban_input" id="hidden_luongCoBan" value="<?= $luongCoBan ?>">
                            <input type="hidden" name="he_so_luong_input" id="hidden_heSoLuong" value="<?= $heSoLuong ?>">
                            <input type="hidden" name="he_so_phu_cap_input" id="hidden_heSoPhuCap" value="<?= $heSoPhuCap ?>">

                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="fw-bold mb-1">Mã lương</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-upc-scan"></i></span>
                                        <input type="text" name="ma_luong" class="form-control bg-light" 
                                            value="<?= $luongInfo['ma_luong'] ?? $maLuong ?>" readonly>
                                    </div>
                                </div>
								
                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Phòng Ban <span class="text-danger">*</span></label>
                                    <div class="choices-container">
										<select name="id_pb" id="selectPhongBan" class="form-select select2" required 
											<?= isset($idEdit) ? 'disabled' : '' ?>>
											<option value="">-- Chọn Phòng ban --</option>
											<?php 
											// Vòng lặp qua danh sách phòng ban ($arrPhongBan phải có sẵn)
											
											foreach ($arrPhongBan as $pb):
												$isSelected = ($selected_id_pb == $pb['id']) ? 'selected' : '';
											?>
												<option value="<?= $pb['id'] ?>" <?= $isSelected ?>>
													<?= htmlspecialchars($pb['ten_bp']) ?>
												</option>
											<?php endforeach; ?>
										</select>
										<?php if (isset($idEdit)): ?>
											<input type="hidden" name="id_pb" value="<?= $selected_id_pb ?>">
										<?php endif; ?>
                                    </div>
                                </div>
								
                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Nhân viên <span class="text-danger">*</span></label>
                                    <div class="choices-container">
                                        <select name="id_nv" id="selectNhanVien" class="form-select select2" required 
                                            <?= isset($idEdit) ? 'readonly disabled' : '' ?>>
                                            <option value="">-- Chọn Nhân viên --</option>
                                            <?php 
                                            foreach ($arrNhanVien as $nv):
                                                $isSelected = ($selected_id_nv == $nv['id']) ? 'selected' : '';
                                            ?>
                                                <option value="<?= $nv['id'] ?>" <?= $isSelected ?>>
                                                    <?= htmlspecialchars($nv['hoten']) ?> [ <?= htmlspecialchars($nv['chucvu'] ?? 'N/A') ?> ]
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php if (isset($idEdit) || isset($_GET['id_nv'])): ?>
                                        <input type="hidden" name="id_nv" value="<?= $selected_id_nv ?>">
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold mb-1">Lương cơ bản (VNĐ) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold text-success">$</span>
                                        <input type="text" id="luongCoBanDisplay" class="form-control bg-light fw-bold text-end" required readonly
                                            value="<?= number_format($luongCoBan) ?>">
                                    </div>
                                    <div class="d-flex justify-content-between mt-1 px-1 small">
                                        <span class="text-muted"><i class="bi bi-x"></i> HS Lương: <strong><?= $heSoLuong ?></strong></span>
                                        <span class="text-muted"><i class="bi bi-plus"></i> HS Phụ cấp: <strong><?= $heSoPhuCap ?></strong></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
								<div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Tháng lương</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-month"></i></span>
                                        <input type="text" name="ky_luong_display" id="thangTinhLuong" 
                                            class="form-control date-picker-month" required 
                                            <?= isset($idEdit) ? 'disabled' : 'readonly' ?>   
                                            style="background-color: white !important;"
                                            value="<?= $thangTinhLuongValue ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Ngày công <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                                        <input type="number" step="0.5" name="ngay_cong" class="form-control" required
                                            placeholder="VD: 26"
                                            value="<?= $luongInfo['ngay_cong'] ?? 26 ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="fw-bold mb-1">Tạm ứng (VNĐ)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-cash-coin"></i></span>
                                        <input type="text" name="tam_ung_display" id="tamUngDisplay" class="form-control number-format" 
                                            placeholder="Nhập số tiền..."
                                            value="<?= number_format($luongInfo['tam_ung'] ?? 0) ?>">
                                        <input type="hidden" name="tam_ung" id="tamUngValue" value="<?= $luongInfo['tam_ung'] ?? 0 ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-3 d-flex align-items-end mb-3">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end w-100">
                                        <?php if (isset($idEdit)): ?>
                                            <button type="submit" name="update" class="btn btn-primary shadow-sm flex-grow-1">
                                                <i class="bi bi-save me-1"></i> Cập nhật
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="add" class="btn btn-success shadow-sm flex-grow-1">
                                                <i class="bi bi-plus-lg me-1"></i> Thêm mới
                                            </button>
                                        <?php endif; ?>
                                        <a href="luong.php" class="btn btn-light-secondary shadow-sm" title="Làm mới">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </a>
                                    </div>
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
                                <i class="bi bi-table me-2"></i>Danh sách lương (<?= $display_month ?>)
                            </h5>
                            
                            <div class="d-flex gap-2">
                                <button id="btnPrintDanhSach" class="btn btn-outline-primary shadow-sm btn-sm">
                                    <i class="bi bi-printer me-1"></i> In Danh sách
                                </button>
                                <button id="btnExportExcel" class="btn btn-success shadow-sm btn-sm">
                                    <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
                                </button>
                            </div>
                        </div>

                        <!-- Filter Section -->
                        <div class="bg-light p-3 rounded mb-4 border">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="fw-bold mb-1 small">Lọc theo Phòng Ban</label>
                                    <div class="choices-container">
                                        <select name="filter_id_pb" id="filter_selectPhongBan" class="form-select select2">
                                            <option value="0">-- Tất cả Phòng ban --</option>
                                            <?php 
                                                foreach ($filter_arrPhongBan as $pb):
                                                    $isSelected = ($pb['id'] == $filter_id_pb) ? 'selected' : '';
                                            ?>
                                                    <option value="<?= $pb['id'] ?>" <?= $isSelected ?>>
                                                        <?= htmlspecialchars($pb['ten_bp']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="fw-bold mb-1 small">Từ tháng</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-range"></i></span>
                                        <input type="text" id="filterFromMonth" class="form-control date-picker-month" 
                                            value="<?= date('m/Y', strtotime($from_date)) ?>" readonly style="background-color: white !important;">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="fw-bold mb-1 small">Đến tháng</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                        <input type="text" id="filterToMonth" class="form-control date-picker-month" 
                                            value="<?= date('m/Y', strtotime($to_date)) ?>" readonly style="background-color: white !important;">
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" id="btnFilterLuong" class="btn btn-info w-100 shadow-sm text-white">
                                        <i class="bi bi-filter me-1"></i> Lọc dữ liệu
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-striped" id="tableLuong">
                                <thead class="table-light text-nowrap">
                                    <tr>
                                        <th class="text-center">STT</th>
                                        <th>Mã lương</th>                                        
                                        <th>Họ tên</th>
                                        <th>Lương cơ bản</th>
                                        <th class="text-center">Công</th>
                                        <th class="text-end">Tạm ứng</th>
                                        <th class="text-end">Phụ cấp</th>
                                        <th class="text-end">Khoản trừ</th>
                                        <th class="text-end">Thực lãnh</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $stt = 1; foreach ($arrLuong as $l): ?>
                                    <tr>
                                        <td class="text-center"><?= $stt++ ?></td>
                                        <td>
                                             <span class="badge bg-light-secondary text-secondary">
                                                <i class="bi bi-upc me-1"></i><?= htmlspecialchars($l['ma_luong']) ?>
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold"><?= htmlspecialchars($l['hoten']) ?></span>
                                                <small class="text-muted fst-italic"><?= htmlspecialchars($l['chucvu']) ?></small>
                                            </div>
                                        </td>
                                        <td class="text-end"><?= number_format($l['luong_coban']) ?></td>
                                        <td class="text-center fw-bold bg-light text-primary"><?= htmlspecialchars($l['ngay_cong']) ?></td>
                                        <td class="text-end text-warning"><?= number_format($l['tam_ung']) ?></td>
                                        <td class="text-end text-success"><?= number_format($l['phu_cap']) ?></td>
                                        <td class="text-end text-danger"><?= number_format($l['khoan_tru']) ?></td>
                                        <td class="text-end fw-bold text-primary fs-6"><?= number_format($l['thuc_lanh']) ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-info btn-view-luong shadow-sm"
                                                        data-id="<?= $l['id'] ?>" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <a href="luong.php?idEdit=<?= $l['id'] ?><?php echo $redirect_query_string; ?>" 
                                                   class="btn btn-sm btn-outline-warning shadow-sm" title="Chỉnh sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-luong shadow-sm"
                                                        title="Xóa"
                                                        data-id="<?= $l['id'] ?><?php echo $redirect_query_string; ?>" 
                                                        data-name="<?= htmlspecialchars($l['hoten'], ENT_QUOTES) ?> - Tháng <?= htmlspecialchars($l['thang_nam'] ?? '') ?>">
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

<div class="modal fade" id="luongDetailModal" tabindex="-1" aria-labelledby="luongDetailModalLabel" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"> 
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="luongDetailModalLabel"><i class="bi bi-file-earmark-text me-2"></i> Chi tiết Bảng Lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
			
                <div id="loadingContent" class="text-center text-muted"></div>
                <div class="row mb-4 border-bottom pb-2">
                    <div class="col-12"><h6 class="text-primary fw-bold">Thông tin Nhân viên</h6></div>
					<div class="col-md-3 text-center">  
						<img id="detailAnhNV" 
							 src="assets/images/default-avatar.png" 
							 class="img-fluid"  
							 style="width: 75px; height: 100px; object-fit: cover;" 
							 alt="Ảnh Nhân viên">
					</div>
                    <div class="col-md-5">
						<p class="mb-1"><strong>Mã nhân viên:</strong> <span id="detailMaNV"></span></p>
                        <p class="mb-1"><strong>Họ tên:</strong> <span id="detailHoTen"></span></p>
                        
						<p class="mb-1"><strong>Giới tính:</strong> <span id="detailGioiTinh"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Chức vụ:</strong> <span id="detailChucVu"></span></p>
						<p class="mb-1"><strong>Phòng ban:</strong> <span id="detailPhongBan"></span></p>
                        <p class="mb-1"><strong>Số điện thoại:</strong> <span id="detailSdt"></span></p>
						<p class="mb-1"><strong>Email:</strong> <span id="detailEmail"></span></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12"><h6 class="text-primary fw-bold">Chi tiết Lương Tháng (<span id="detailMonth"></span>)</h6></div>
                    <div class="col-md-4">
                        <p class="mb-1">Lương cơ bản: <strong class="text-success" id="detailLuongCB"></strong></p>
                        <p class="mb-1">Hệ số Lương: <strong id="detailHSLuong"></strong></p>
						<p class="mb-1">Phụ cấp : <strong id="detailPhuCap"></strong></p>
						<p class="mb-1">Hệ số phụ cấp: <strong id="detailHSPhuCap"></strong></p>
                        <p class="mb-1">Ngày công: <strong id="detailNgayCong"></strong></p>
                       
                    </div>
                    <div class="col-md-4">
                        
                        <p class="mb-1">BHXH (8%): <strong id="detailBHXH"></strong></p>
                        <p class="mb-1">BHYT (1.5%): <strong id="detailBHYT"></strong></p>
                        <p class="mb-1">BHTN (1%): <strong id="detailBHTN"></strong></p>
						<p class="mb-1">Thuế thu nhập cá nhân: <strong id="detailThueTNCN"></strong></p>
                        <p class="mb-1">Tạm ứng: <strong  id="detailTamUng"></strong></p>
                    </div>
                    <div class="col-md-4">
                        
                        <p class="mb-1 fs-5">Thực lãnh: <strong class="text-danger" id="detailThucLanh"></strong></p>
                        <hr>
                        <p class="mb-1"><small>Người tạo: <span id="detailNguoiTao"></span></small></p>
                        <p class="mb-1"><small>Ngày tạo: <span id="detailNgayTao"></span></small></p>
						<hr>
                        <p class="mb-1"><small>Người sửa: <span id="detailNguoiSua"></span></small></p>
                        <p class="mb-1"><small>Ngày sửa: <span id="detailNgaySua"></span></small></p>
						
                    </div>
                </div>
            </div>
            <div class="modal-footer">
				<button type="button" id="btnPrintLuong" class="btn btn-primary me-auto">
					<i class="bi bi-printer"></i> In Bảng Lương
				</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<script src="assets/vendors/jquery/jquery.min.js"></script> 

<script src="assets/js/bootstrap-datepicker.min.js"></script>

<script src="assets/js/bootstrap-datepicker.vi.min.js"></script>
<?php include('./layouts/footer.php'); ?>

<script>

document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------
    // 0. KHỞI TẠO CHOICES.JS
    // ----------------------------------------------------
    const selectElements = [
        document.getElementById('selectPhongBan'),
        document.getElementById('selectNhanVien'),
        document.getElementById('filter_selectPhongBan')
    ];

    selectElements.forEach(el => {
        if (el && typeof Choices !== 'undefined') {
            new Choices(el, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
                placeholder: true,
                noResultsText: 'Không tìm thấy kết quả',
            });
        }
    });

    // ----------------------------------------------------
    // 1. TIỆN ÍCH CHUNG (Định dạng số và hiệu ứng Loading)
    // ----------------------------------------------------
    
    function formatNumber(value) {
        if (value === null || value === undefined || isNaN(parseFloat(value))) return '0';
        return parseFloat(value).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function cleanNumber(value) {
        if (typeof value !== 'string') return value;
        // Chú ý: Cần loại bỏ cả ký tự khoảng trắng đặc biệt (U+00A0) nếu có
        return value.replace(/[^0-9]/g, '');
    }

    document.querySelectorAll('.number-format').forEach(input => {
        if (input.id === 'tamUngDisplay') {
            input.addEventListener('input', function() {
                let value = this.value;
                let cleanValue = cleanNumber(value);
                document.getElementById('tamUngValue').value = cleanValue;
                this.value = formatNumber(cleanValue);
            });
        }
    });

    const style = document.createElement('style');
    style.innerHTML = '@keyframes spin { 0% { transform: transform: rotate(0deg); } 100% { transform: rotate(360deg); } } .spin { animation: spin 1s linear infinite; }';
    document.head.appendChild(style);

    // ----------------------------------------------------
    // 2. LOGIC FORM (Chọn Phòng Ban và Nhân viên và Filter)
    // ----------------------------------------------------
	const selectPhongBan = document.getElementById('selectPhongBan');
    const selectNhanVien = document.getElementById('selectNhanVien');
	const currentUrlPath = window.location.pathname;
	// Logic cho việc chọn Phòng ban
	if (selectPhongBan && !document.querySelector('button[name="update"]')) {
		selectPhongBan.addEventListener('change', function() {
			const id_pb = this.value;
			// ⭐️ Lấy các tham số hiện có, loại trừ id_nv và id_pb (vì id_pb sẽ được set lại)
			const params = giu_lai_tham_so_URL(['id_nv', 'id_pb']);
			
			let url = currentUrlPath + '?';
			
			// Gắn lại các tham số cũ (như ky_luong_chon, idEdit)
			for (const key in params) {
				url += `${key}=${params[key]}&`;
			}

			if (id_pb) {
				// Thêm id_pb mới
				url += `id_pb=${id_pb}`;
			} else {
				 // Xóa dấu & thừa nếu không có id_pb mới
				 url = url.endsWith('&') ? url.slice(0, -1) : url;
			}

			window.location.href = url;
		});
	}
    if (selectNhanVien && !document.querySelector('button[name="update"]')) {
        selectNhanVien.addEventListener('change', function() {
            const id_nv = this.value;
			// ⭐️ Lấy các tham số hiện có, loại trừ id_nv
			const params = giu_lai_tham_so_URL(['id_nv']);

			let url = currentUrlPath + '?';
			
			// Gắn lại các tham số cũ (như ky_luong_chon, id_pb, idEdit)
			for (const key in params) {
				url += `${key}=${params[key]}&`;
			}
			
			if (id_nv) {
				// Thêm id_nv mới
				url += `id_nv=${id_nv}`;
			} else {
				 // Xóa dấu & thừa nếu không có id_nv mới
				 url = url.endsWith('&') ? url.slice(0, -1) : url;
			}
			
			window.location.href = url;
        });
    }

    document.getElementById('btnFilterLuong').addEventListener('click', function() {
        const fromMonthStr = document.getElementById('filterFromMonth').value; 
        const toMonthStr = document.getElementById('filterToMonth').value; 
        
        // ⭐️ BỔ SUNG: Lấy giá trị Phòng ban từ select lọc 
        const id_pb_filter = document.getElementById('filter_selectPhongBan') ? 
                             document.getElementById('filter_selectPhongBan').value : '';
        
        // Hàm tiện ích: Chuyển chuỗi MM/YYYY thành đối tượng Date (Dùng ngày 1 để so sánh)
        function parseMonthYear(dateString) {
            // Tách chuỗi MM/YYYY
            const parts = dateString.split('/');
            // parts[0] = MM, parts[1] = YYYY. Trả về new Date(YYYY, MM - 1, 1)
            return new Date(parts[1], parts[0] - 1, 1);  
        }
        
        const fromDate = parseMonthYear(fromMonthStr);
        const toDate = parseMonthYear(toMonthStr);
        
        // Kiểm tra điều kiện: Tháng Bắt đầu phải <= Tháng Kết thúc
        if (fromDate > toDate) {
            // Dùng Swal.fire để thông báo lỗi
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Lỗi bộ lọc',
                    text: 'Tháng bắt đầu cần phải nhỏ hơn hoặc bằng tháng kết thúc lọc. Hãy kiểm tra lại!',
                    icon: 'error',
                    confirmButtonText: 'Đã hiểu'
                });
            } else {
                // Trường hợp không có Swal, dùng alert mặc định
                alert('Lỗi: Tháng Bắt Đầu phải nhỏ hơn hoặc bằng Tháng Kết Thúc.');
            }
        } else {
            // Hợp lệ, tiến hành lọc bằng cách chuyển hướng
            
            let url = 'luong.php?';
            const params = [];

            // 1. Thêm tham số Tháng Lọc
            params.push(`from_month=${fromMonthStr}`);
            params.push(`to_month=${toMonthStr}`);

            // 2. ⭐️ THÊM THAM SỐ LỌC PHÒNG BAN
            // Chỉ thêm nếu có giá trị VÀ khác giá trị mặc định "0"
            if (id_pb_filter && id_pb_filter !== '0') {
                params.push(`filter_id_pb=${id_pb_filter}`);
            }
            
            url += params.join('&');
            
            window.location.href = url;
        }
    });
    
    if (typeof simpleDatatables !== 'undefined' && document.getElementById('tableLuong')) {
        new simpleDatatables.DataTable("#tableLuong");
    }

   
    // ----------------------------------------------------
// 3. LOGIC XEM CHI TIẾT (Modal View)
		$('body').on('click', '.btn-view-luong', function () {
		const id_luong = $(this).data('id'); // ⭐ SỬ DỤNG JQUERY
		const loadingContent = document.getElementById('loadingContent');
		const modalBody = document.querySelector('#luongDetailModal .modal-body');
		
		// Ẩn nội dung chi tiết, chỉ hiển thị loading
		modalBody.querySelectorAll('.row').forEach(el => el.style.display = 'none');
		loadingContent.innerHTML = '<p class="text-center text-muted"><i class="bi bi-arrow-clockwise spin me-2"></i>Đang tải dữ liệu...</p>';
		loadingContent.style.display = 'block';
		
		const detailModalElement = document.getElementById('luongDetailModal');
		// Khởi tạo và hiển thị modal
		const detailModal = new bootstrap.Modal(detailModalElement);
		detailModal.show();

		fetch(`action/fetch-luong-details.php?id=${id_luong}`)
			.then(response => {
				if (!response.ok) {
					throw new Error(`Lỗi HTTP: ${response.status} (${response.statusText})`);
				}
				return response.json();
			})
			.then(result => {
				loadingContent.style.display = 'none'; 
				
				if (result.success) {
					modalBody.querySelectorAll('.row').forEach(el => el.style.display = 'flex');

					const data = result.data;
					const nv = result.nhanvien_info;
					
					
					const defaultImg = 'assets/images/default-avatar.png';
					document.getElementById('detailAnhNV').src = data.anh_url || defaultImg;
					
					// Cập nhật thông tin NV
					document.getElementById('detailMaNV').textContent = nv.ma_nv;
					document.getElementById('detailHoTen').textContent = nv.hoten;
					document.getElementById('detailPhongBan').textContent = nv.phongban;
					document.getElementById('detailChucVu').textContent = nv.chucvu;
					document.getElementById('detailSdt').textContent = nv.sodt;
					document.getElementById('detailEmail').textContent = nv.email;
					document.getElementById('detailGioiTinh').textContent = nv.gioitinh;

					document.getElementById('detailMonth').textContent = data.ky_luong_display;

					// Cập nhật chi tiết Lương
					document.getElementById('detailLuongCB').textContent = formatNumber(data.luong_co_ban_goc);
					document.getElementById('detailHSLuong').textContent = data.he_so_luong_goc; 
					document.getElementById('detailHSPhuCap').textContent = data.he_so_phu_cap_goc; 
					
					document.getElementById('detailNgayCong').textContent = data.ngay_cong;
					
					document.getElementById('detailPhuCap').textContent = formatNumber(data.phu_cap);
					document.getElementById('detailBHXH').textContent = formatNumber(data.bhxh);
					document.getElementById('detailBHYT').textContent = formatNumber(data.bhyt);
					document.getElementById('detailBHTN').textContent = formatNumber(data.bhtn);
					document.getElementById('detailThueTNCN').textContent = formatNumber(data.thue_tncn);
					document.getElementById('detailTamUng').textContent = formatNumber(data.tam_ung);
					
					document.getElementById('detailThucLanh').textContent = formatNumber(data.thuc_lanh) + ' VNĐ';

					document.getElementById('detailNguoiTao').textContent = data.nguoitao_name;
					document.getElementById('detailNgayTao').textContent = data.ngaytao_display;
					document.getElementById('detailNguoiSua').textContent = data.nguoisua_name;
					document.getElementById('detailNgaySua').textContent = data.ngaysua_display;
					
				} else {
					loadingContent.innerHTML = `<p class="alert alert-danger text-center">Lỗi: ${result.message || 'Không tìm thấy chi tiết lương.'}</p>`;
					loadingContent.style.display = 'block';
					modalBody.querySelectorAll('.row').forEach(el => el.style.display = 'none');
				}
			})
			.catch(error => {
				loadingContent.innerHTML = `<p class="alert alert-danger text-center">Đã xảy ra lỗi khi tải dữ liệu: ${error.message}. Vui lòng kiểm tra console log.</p>`;
				loadingContent.style.display = 'block';
				modalBody.querySelectorAll('.row').forEach(el => el.style.display = 'none');
				console.error("Lỗi Fetch/JSON:", error);
			});
	});

	// ----------------------------------------------------
	// 4. LOGIC XÓA (Đã dùng Event Delegation)
	// ----------------------------------------------------
	$('body').on('click', '.btn-delete-luong', function (e) {
		e.preventDefault();
		const id_luong = $(this).data('id'); // ⭐ SỬ DỤNG JQUERY
		const hoten = $(this).data('name'); // ⭐ SỬ DỤNG JQUERY

		if (typeof Swal !== 'undefined') {
			Swal.fire({
				title: 'Xác nhận xóa?',
				text: `Bạn có chắc chắn muốn xóa bảng lương của nhân viên "${hoten}" không? Thao tác này không thể hoàn tác!`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: "#d33",
				cancelButtonColor: "#3085d6",
				confirmButtonText: "Có xóa",
				cancelButtonText: "Hủy",
				reverseButtons: true
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = `action/luong-action.php?delete=${id_luong}`;
				}
			});
		} else {
			if (confirm(`Bạn có chắc chắn muốn xóa bảng lương của nhân viên ${hoten} không?`)) {
				window.location.href = `action/luong-action.php?delete=${id_luong}`;
			}
		}
	});
	// 5. LOGIC CHỌN THÁNG (Sử dụng Bootstrap Datepicker)
    // ----------------------------------------------------
    if (typeof $ !== 'undefined' && typeof $.fn.datepicker !== 'undefined') {
    
		// Tính toán ngày giới hạn: Ngày cuối cùng của tháng trước
		var lastDayOfPreviousMonth = new Date();
		lastDayOfPreviousMonth.setDate(0); 

		$('.date-picker-month').datepicker({
			// Cấu hình quan trọng nhất để chỉ chọn Tháng/Năm
			format: 'mm/yyyy',       // Định dạng mm/yyyy khớp với PHP date('m/Y')
			
			// ⭐ SỬA TẠI ĐÂY: Thay 'years' thành 'months' ⭐
			startView: 'months',     // Bắt đầu từ chế độ xem tháng (Hiện 12 tháng)
			
			minViewMode: 'months',   // Chỉ cho phép chọn mức tối thiểu là tháng (loại bỏ chọn ngày)
			
			// Các tùy chọn phụ trợ
			autoclose: true,         // Tự động đóng sau khi chọn
			language: 'vi',          // Sử dụng bản Tiếng Việt
			
			// Giới hạn ngày chọn: Chỉ chọn được đến tháng trước
			endDate: lastDayOfPreviousMonth,
			
		}); 
	}
	// 6 ⭐ BỔ SUNG LOGIC CHO THÁNG TÍNH LƯƠNG ⭐
	const thangTinhLuongInput = document.getElementById('thangTinhLuong');

	if (thangTinhLuongInput) {
		// Sử dụng JQuery vì bạn dùng Datepicker, Datepicker thường kích hoạt sự kiện change/hide
		$('#thangTinhLuong').on('change', function() {
			const ky_luong_chon = this.value; // Dạng MM/YYYY
        
			// ⭐️ Lấy các tham số hiện có, loại trừ ky_luong_chon
			const params = giu_lai_tham_so_URL(['ky_luong_chon']);
			
			let url = currentUrlPath + '?';
			
			// Gắn lại các tham số cũ (như id_pb, id_nv, idEdit)
			for (const key in params) {
				url += `${key}=${params[key]}&`;
			}
			
			// THAM SỐ QUAN TRỌNG: Gửi tháng tính lương đã chọn
			url += `ky_luong_chon=${ky_luong_chon}`;
			
			window.location.href = url;
		});
	}
	
	// Thêm hàm tiện ích mới
	function giu_lai_tham_so_URL(paramsToExclude = []) {
		const searchParams = new URLSearchParams(window.location.search);
		const existingParams = {};

		// Lặp qua tất cả các tham số hiện có
		for (const [key, value] of searchParams.entries()) {
			// Chỉ giữ lại các tham số KHÔNG nằm trong danh sách loại trừ
			if (!paramsToExclude.includes(key)) {
				existingParams[key] = value;
			}
		}
		return existingParams;
	}
	// 7. LOGIC IN BẢNG LƯƠNG
	document.getElementById('btnPrintLuong').addEventListener('click', function() {
		const detailModalElement = document.getElementById('luongDetailModal');
		const backdrop = document.querySelector('.modal-backdrop.fade.show'); 
		const isModalOpen = detailModalElement.classList.contains('show');
		detailModalElement.classList.add('d-block'); 
		detailModalElement.style.opacity = '1'; 
		if (backdrop) {
			backdrop.style.display = 'none';
		}
		window.onafterprint = function() {
			
			detailModalElement.classList.remove('d-block');
			detailModalElement.style.opacity = ''; 
			if (backdrop) {
				backdrop.style.display = 'block';
			}
			window.onafterprint = null;
		};
		window.print();
	});
	
	// 8. Xuất file exl bảng lương ***************************************************************
	
    const btnExportExcel = document.getElementById('btnExportExcel');

		if (btnExportExcel) {
			btnExportExcel.addEventListener('click', function() {
				
				const url = new URL(window.location.href);
				const searchParams = url.searchParams;
				const filterData = {};
				
				filterData.from_month = searchParams.get('from_month') || 
										document.getElementById('filterFromMonth').value; 
				
				filterData.to_month = searchParams.get('to_month') || 
									  document.getElementById('filterToMonth').value;
									  
				
				const idPbElement = document.getElementById('filter_selectPhongBan');
            
				filterData.filter_id_pb = searchParams.get('filter_id_pb') || 
                                     (idPbElement ? idPbElement.value : '');
				  if (filterData.filter_id_pb === '0') {
					filterData.filter_id_pb = '';
				}
				const params = new URLSearchParams(filterData).toString();
				//alert('Tham số xuất Excel: ');
				window.location.href = 'action/export_excel_luong.php?' + params;  
			});
		}
	// ***************************************************************
	// 9. Inbang luong ***************************************************************
	// 9. In bảng lương bằng iframe ẩn
	const btnPrint = document.getElementById('btnPrintDanhSach'); 

	if (btnPrint) {
		btnPrint.addEventListener('click', function() {
			const url = new URL(window.location.href);
			const searchParams = url.searchParams;
			const filterData = {};

			// 1. Thu thập tham số lọc
			filterData.from_month = searchParams.get('from_month') || document.getElementById('filterFromMonth').value;
			filterData.to_month = searchParams.get('to_month') || document.getElementById('filterToMonth').value;

			const idPbElement = document.getElementById('filter_selectPhongBan');
			filterData.filter_id_pb = searchParams.get('filter_id_pb') || (idPbElement ? idPbElement.value : '');
			if (filterData.filter_id_pb === '0') filterData.filter_id_pb = '';

			// 2. Chuyển thành chuỗi tham số URL
			const params = new URLSearchParams(filterData).toString();
			const printUrl = 'action/print_luong.php?' + params;

			// 3. Tạo iframe ẩn và in
			const iframe = document.createElement('iframe');
			iframe.style.display = 'none';
			iframe.src = printUrl;
			document.body.appendChild(iframe);

			iframe.onload = function() {
				iframe.contentWindow.focus();
				iframe.contentWindow.print();

				// Dọn iframe sau 1s
				setTimeout(function() {
					document.body.removeChild(iframe);
				}, 1000);
			};
		});
	}

	
});
</script>