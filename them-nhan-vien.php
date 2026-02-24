<?php
include('./layouts/header.php');
include(__DIR__ . '/view/them-nhan-vien-view-action.php');

?>

<div class="page-heading">
    

    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
							
                    <div class="card-content">
                        <div class="card-body">
						<div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="text-primary fw-bold mb-0">
                            <i class="bi bi-people-fill me-2"></i>
                            <?= $isEdit ? 'Cập nhật nhân viên' : 'Thêm nhân viên mới'; ?>
                        </h5>
                      
                    </div>		
                            <form id="formNhanvien" class="form form-vertical validate-tooltip" 
                                  method="post" enctype="multipart/form-data" 
                                  action="action/nhan-vien-action.php<?= $isEdit ? '?id=' . $nhanvienInfo['id'] : ''; ?>">
                                <div class="form-body">
                                    <div class="row">

                                        <!-- hidden id -->
                                        <?php if ($isEdit): ?>
                                            <input type="hidden" name="id" value="<?= $nhanvienInfo['id']; ?>">
                                        <?php endif; ?>

                                        <!-- left column -->
                                        <div class="col-md-6">
											<?php if ($isEdit): ?>
												<div class="form-group mb-3">
													<label for="ma_nv">Mã nhân viên:</label>
													<input type="text" id="ma_nv" name="ma_nv" class="form-control" 
														value="<?= $isEdit ? htmlspecialchars($nhanvienInfo['ma_nv']) : htmlspecialchars($ma_nv_default); ?>" readonly>
												</div>
											<?php endif; ?>
                                            <div class="form-group mb-3">
                                                <label for="ten_nv">Tên nhân viên <span class="text-danger">*</span></label>
                                                <input type="text" id="ten_nv" name="ten_nv" class="form-control" required
                                                    value="<?= $nhanvienInfo['hoten'] ?? ''; ?>" >
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="sodt">Số điện thoại <span class="text-danger">*</span></label>
                                                <input type="text" id="sodt" name="sodt" class="form-control"
                                                    value="<?= $nhanvienInfo['sodt'] ?? ''; ?>" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="email">Email <span class="text-danger">*</span></label>
                                                <input type="email" id="email" name="email" class="form-control"
                                                    value="<?= $nhanvienInfo['email'] ?? ''; ?>" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="gioi_tinh">Giới tính <span class="text-danger">*</span></label>
                                                <select id="gioi_tinh" name="gioi_tinh" class="form-select" required>
                                                    <option value="">--- Chọn giới tính ---</option>
                                                    <option value="Nam" <?= ($nhanvienInfo['gtinh'] ?? '') == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                                                    <option value="Nữ" <?= ($nhanvienInfo['gtinh'] ?? '') == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
													<option value="Khác" <?= ($nhanvienInfo['gtinh'] ?? '') == 'Khác' ? 'selected' : ''; ?>>Khác</option>
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="ngaysinh">Ngày sinh <span class="text-danger">*</span></label>
                                                <input type="date" id="ngaysinh" name="ngaysinh" class="form-control"
                                                    value="<?= $nhanvienInfo['ngsinh'] ?? date('Y-m-d'); ?>">
                                            </div>
                                            

                                            <div class="form-group mb-3">
                                                <label for="cmnd">Số CCCD <span class="text-danger">*</span></label>
                                                <input type="text" id="cmnd" name="cmnd" class="form-control"
                                                    value="<?= $nhanvienInfo['so_cccd'] ?? ''; ?>" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="ngaycap">Ngày cấp <span class="text-danger">*</span></label>
                                                <input type="date" id="ngaycap" name="ngaycap" class="form-control" required
                                                    value="<?= $nhanvienInfo['ngaycap_cccd'] ?? date('Y-m-d'); ?>">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="noicap">Nơi cấp</label>
                                                <input type="text" id="noicap" name="noicap" class="form-control"
                                                    value="<?= $nhanvienInfo['noicap_cccd'] ?? ''; ?>">
                                            </div>
											<div class="form-group mb-3">
                                                <label for="hokhau">Hộ khẩu <span class="text-danger">*</span></label>
                                                
												<input type="text" id="hokhau" name="hokhau" class="form-control" required
                                                    value="<?= $nhanvienInfo['hokhau'] ?? ''; ?>" >
                                            </div>
											<div class="form-group mb-3">
                                                <label for="noisinh">Nơi sinh <span class="text-danger">*</span></label>
                                                
												<input type="text"  id="noisinh" name="noisinh" class="form-control" required
                                                    value="<?= $nhanvienInfo['noisinh'] ?? ''; ?>">
                                            </div>	
                                            <div class="form-group mb-3">
                                                <label for="tamtru">Tạm trú </label>
                                                
												<input type="text" id="tamtru" name="tamtru" class="form-control"
                                                    value="<?= $nhanvienInfo['tamtru'] ?? ''; ?>" >
                                            </div>
                                            
											

			
                                        </div>

                                        <!-- right column -->
                                        <div class="col-md-6">
										<div class="form-group mb-3">
                                                <label for="anhdaidien">Ảnh 3x4 (Nếu có)</label>
                                                <input type="file" id="anhdaidien" name="anhdaidien" class="form-control">
                                                <?php if (!empty($nhanvienInfo['anhdaidien'])): ?>
                                                    <img src="uploads/nhanvien/<?= htmlspecialchars($nhanvienInfo['anhdaidien']); ?>" 
                                                         width="90" class="mt-2 rounded shadow-sm">
                                                <?php endif; ?>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="loai_nv">Loại nhân viên <span class="text-danger">*</span></label>
                                                <select  id="loai_nv" name="loai_nv" class="form-select" required>
                                                    <option value="">--- Chọn loại nhân viên ---</option>
                                                    <?php foreach ($ds_loai_nv as $r): ?>
                                                        <option value="<?= $r['id']; ?>" <?= ($nhanvienInfo['id_loainv'] ?? '') == $r['id'] ? 'selected' : ''; ?>>
                                                            <?= $r['ten_lnv']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="trinh_do">Trình độ <span class="text-danger">*</span></label>
                                                <select id="trinh_do" name="trinh_do" class="form-select" required>
                                                    <option value="">--- Chọn trình độ ---</option>
                                                    <?php foreach ($ds_trinh_do as $r): ?>
                                                        <option value="<?= $r['id']; ?>" <?= ($nhanvienInfo['id_trinhdo'] ?? '') == $r['id'] ? 'selected' : ''; ?>>
                                                            <?= $r['ten_td']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="chuyen_mon">Chuyên môn <span class="text-danger">*</span></label>
                                                <select id="chuyen_mon" name="chuyen_mon" class="form-select" required>
                                                    <option value="">--- Chọn chuyên môn ---</option>
                                                    <?php foreach ($ds_chuyen_mon as $r): ?>
                                                        <option value="<?= $r['id']; ?>" <?= ($nhanvienInfo['id_chuyenmon'] ?? '') == $r['id'] ? 'selected' : ''; ?>>
                                                            <?= $r['ten_cm']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            

                                            <div class="form-group mb-3">
                                                <label for="phong_ban">Phòng ban <span class="text-danger">*</span></label>
                                                <select id="phong_ban" name="phong_ban" class="form-select" required>
                                                    <option value="">--- Chọn phòng ban ---</option>
                                                    <?php foreach ($ds_phong_ban as $r): ?>
                                                        <option value="<?= $r['id']; ?>" <?= ($nhanvienInfo['id_phongban'] ?? '') == $r['id'] ? 'selected' : ''; ?>>
                                                            <?= $r['ten_bp']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="chuc_vu">Chức vụ <span class="text-danger">*</span></label>
                                                <select id="chuc_vu" name="chuc_vu" class="form-select" required>
                                                    <option value="">--- Chọn chức vụ ---</option>
                                                    <?php foreach ($ds_chuc_vu as $r): ?>
                                                        <option value="<?= $r['id']; ?>" <?= ($nhanvienInfo['id_chucvu'] ?? '') == $r['id'] ? 'selected' : ''; ?>>
                                                            <?= $r['tencv']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
											<div class="form-group mb-3">
                                                <label for="quoc_tich">Quốc tịch <span class="text-danger">*</span></label>
                                                <select id="quoc_tich" name="quoc_tich" class="form-select" required>
                                                    <option value="">--- Chọn quốc tịch ---</option>
                                                    <?php foreach ($ds_quoc_tich as $r): ?>
                                                        <option value="<?= $r['id']; ?>" <?= ($nhanvienInfo['id_quoctich'] ?? '') == $r['id'] ? 'selected' : ''; ?>>
                                                            <?= $r['ten_qt']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="ton_giao">Tôn giáo <span class="text-danger">*</span></label>
                                                <select id="ton_giao" name="ton_giao" class="form-select" required>
                                                    <option value="">--- Chọn tôn giáo ---</option>
                                                    <?php foreach ($ds_ton_giao as $r): ?>
                                                        <option value="<?= $r['id']; ?>" <?= ($nhanvienInfo['id_tongiao'] ?? '') == $r['id'] ? 'selected' : ''; ?>>
                                                            <?= $r['ten_tg']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="dan_toc">Dân tộc <span class="text-danger">*</span></label>
                                                <select id="dan_toc" name="dan_toc" class="form-select" required>
                                                    <option value="">--- Chọn dân tộc ---</option>
                                                    <?php foreach ($ds_dan_toc as $r): ?>
                                                        <option value="<?= $r['id']; ?>" <?= ($nhanvienInfo['id_dantoc'] ?? '') == $r['id'] ? 'selected' : ''; ?>>
                                                            <?= $r['ten_dt']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
											<div class="form-group mb-3">
                                                <label for="hon_nhan">Tình trạng hôn nhân <span class="text-danger">*</span></label>
                                                <select id="hon_nhan" name="hon_nhan" required class="form-select">
                                                    <option value="">--- Chọn tình trạng hôn nhân ---</option>
                                                    <?php foreach ($ds_hon_nhan as $r): ?>
                                                        <option value="<?= $r['id']; ?>" <?= ($nhanvienInfo['id_honnhan'] ?? '') == $r['id'] ? 'selected' : ''; ?>>
                                                            <?= $r['ten_hn']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
											<div class="form-group mb-3">
                                                <label for="trang_thai">Trạng thái</label>
                                                <select id="trang_thai" name="trang_thai" class="form-select"  <?= $isEdit ? '' : 'disabled'; ?>>
                                                    <option value="1" <?= ($nhanvienInfo['trangthai'] ?? 1) == 1 ? 'selected' : ''; ?>>Đang làm việc</option>
                                                    <option value="0" <?= ($nhanvienInfo['trangthai'] ?? 1) == 0 ? 'selected' : ''; ?>>Đã nghỉ việc</option>
                                                </select>
                                            </div>	
                                            

                                            <div class="form-group mb-3">
                                                <label>Người tạo</label>
                                                <input type="text" class="form-control" readonly 
                                                    value="<?= $isEdit ? ($nhanvienInfo['nguoitao_name'] ?? '') : (($row_acc['ho'] ?? '') . ' ' . ($row_acc['ten'] ?? '')); ?>" >
                                            </div>

                                            <div class="form-group mb-3">
                                                <label>Ngày tạo</label>
                                                <input type="text" class="form-control" readonly 
                                                    value="<?= $isEdit ? date('d-m-Y', strtotime($nhanvienInfo['ngaytao'] ?? date('Y-m-d'))) : date('d-m-Y'); ?>">
                                            </div>
											
                                            <div class="col-12 d-flex  mt-3">
											<?php if ($isEdit): ?>
												<button type="submit" name="update" class="btn btn-warning me-1 mb-1">
													💾 Cập nhật nhân viên
												</button>
											<?php else: ?>
												<button type="submit" name="add" class="btn btn-success me-1 mb-1">
													+ Thêm mới nhân viên
												</button>
											<?php endif; ?>

											<a href="them-nhan-vien.php" class="btn btn-light-secondary me-1 mb-1">Làm mới</a>
											</div>


                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?php include('./layouts/footer.php'); ?>
