<?php
include(__DIR__ . '/action/tra-cuu-nhan-vien-action.php');	
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhân sự</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">

    <link rel="stylesheet" href="assets/vendors/iconly/bold.css">
	
    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon">
	<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
	<link rel="stylesheet" href="assets/css/bootstrap-datepicker.min.css">
	 <link rel="stylesheet" href="assets/css/header.css">
	

</head>

<body>
	
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="./assets/images/logo/logohrm.png" style="height: 70px;" alt="Logo Công ty ABC">
                    <span class="fw-bold text-primary ms-2">Công Ty ABC</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">Trang Chủ</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="gioiThieuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Giới Thiệu
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="gioiThieuDropdown">
                                <li><a class="dropdown-item" href="#vechungtoi">Về Chúng Tôi</a></li>
                                <li><a class="dropdown-item" href="#su-menh">Tầm Nhìn & Sứ Mệnh</a></li>
                                <li><a class="dropdown-item" href="#doi-ngu">Đội Ngũ</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#dichvu">Sản Phẩm/Dịch Vụ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#lienhe">Liên Hệ</a>
                        </li>
						
                        <li class="nav-item ms-lg-3">
                            <a href="tra-cuu-nhan-vien.php" class="btn btn-primary shadow-lg">Tra cứu nhân viên</a>
                        </li>
						<li class="nav-item ms-lg-3">
                            <a href="login.php" class="btn btn-primary shadow-lg">Đăng nhập</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div id="app" style="padding-top: 70px;">
		
			<div id="main">
				<div class="content-wrapper">
					<!------------ Bắt đầu nội dung ở đây --------------------!>
					<div class="page-heading">
						<section id="basic-vertical-layouts">
							<div class="row match-height">
								<div class="col-12">
									<div class="card shadow border-0">
										<div class="card-header text-center pt-4 pb-0 bg-white">
											<h3 class="text-primary fw-bold mb-0">HỆ THỐNG TRA CỨU THÔNG TIN NHÂN SỰ</h3>
										</div>
										<div class="card-body">
											<!-- Form tra cứ của nhân viên ------------------ -->
											<form method="POST" action="action/tra-cuu-nhan-vien-action.php">
												<div class="p-3 rounded-3" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
													<div class="row justify-content-center"> 
														
														<div class="col-lg-3 col-md-6 col-12 mb-3">
															<div class="d-flex align-items-center">
																<label for="maNhanVien" class="form-label fw-bold mb-0 me-3 text-end" style="min-width: 120px;">Mã nhân viên <span class="text-danger">*</span></label>
																<input type="text" name="maNhanVien" id="maNhanVien" class="form-control" required >
															</div>
														</div>
														
														<div class="col-lg-3 col-md-6 col-12 mb-3">
															<div class="d-flex align-items-center">
																<label for="soCccd" class="form-label fw-bold mb-0 me-3 text-end" style="min-width: 120px;">Số CCCD <span class="text-danger">*</span></label>
																<input type="text" name="soCccd" id="soCccd" class="form-control" required>
															</div>
														</div>
														
														<div class="col-lg-2 col-md-12 col-12 mb-3 text-center">
															<button name="btn_Tracuu" type="submit" class="btn btn-primary w-100 w-lg-75"> 
																<i class="bi bi-search me-1"></i> Tra Cứu
															</button>
														</div>
													</div>
												</div>
											</form>
											<!-- ---------------------- ------------------ -->
											<hr>
											<?php if ($thongTinNV_hienThi): ?>
											<!-- Thông tin của nhân viên ------------------ -->
											
											<div class="row mb-4">
												
												<div class="col-lg-3 col-md-4 col-12 mb-3 mb-lg-0 text-center">
													<div class="p-3 border rounded-3 d-inline-block shadow-sm" style="background-color: #f0f3ff;">
														
														<div class="mb-2 mx-auto" style="width: 100px; height: 100px; overflow: hidden; border-radius: 50%; border: 2px solid #6070ff;">
														<?php 
															$avatar_path = './uploads/nhanvien/'.$thongTinNV_hienThi['anhdaidien'] ?? null;
															$default_avatar = './assets/images/default-avatar.png'; // Đường dẫn đến ảnh đại diện mặc định
															
															// Kiểm tra xem có đường dẫn ảnh và file ảnh có tồn tại không
															if ($avatar_path && file_exists($avatar_path)) {
																$image_src = $avatar_path;
															} else {
																$image_src = $default_avatar; // Sử dụng ảnh mặc định
															}
															//$image_src = $avatar_path;
															//echo $avatar_path;
														?>
														<img src="<?php echo $image_src; ?>" alt="<?php echo $thongTinNV_hienThi['hoten'] ?? 'Avatar'; ?>" 
															 style="width: 100%; height: 100%; object-fit: cover;">
													</div>
														
														<h5 class="fw-bold mb-0 text-primary">
															<?php echo $thongTinNV_hienThi['hoten'] ?? 'Không rõ'; ?>
														</h5>
														<label class="text-muted small fw-bold">
															Mã NV: <?php echo $thongTinNV_hienThi['ma_nv'] ?? '---'; ?>
														</label>
													</div>
												</div>
												
												<div class="col-lg-9 col-md-8 col-12">
													<div class="row g-3">
														
														<div class="col-lg-4 col-md-6 col-12">
															<div class="d-flex align-items-start"><i class="bi bi-person-badge me-2 fs-5 text-primary"></i>
																<div>
																	<label class="fw-bold mb-0">Chức vụ</label>
																	<div class="text-dark"><?php echo $thongTinNV_hienThi['chuc_vu'] ?? 'Chưa xác định'; ?></div>
																</div>
															</div>
														</div>
														
														<div class="col-lg-4 col-md-6 col-12">
															<div class="d-flex align-items-start"><i class="bi bi-briefcase me-2 fs-5 text-primary"></i>
																<div>
																	<label class="fw-bold mb-0">Phòng ban</label>
																	<div class="text-dark"><?php echo $thongTinNV_hienThi['phong_ban'] ?? 'Chưa xác định'; ?></div>
																</div>
															</div>
														</div>

														<div class="col-lg-4 col-md-6 col-12">
															<div class="d-flex align-items-start"><i class="bi bi-calendar-date me-2 fs-5 text-primary"></i>
																<div>
																	<label class="fw-bold mb-0">Ngày sinh</label>
																	<div class="text-dark">
																		<?php 
																			$ngay_sinh = $thongTinNV_hienThi['ngsinh'] ?? null;
																			echo $ngay_sinh ? date('d/m/Y', strtotime($ngay_sinh)) : '---'; 
																		?>
																	</div>
																</div>
															</div>
														</div>
														
														
														
														<div class="col-lg-4 col-md-6 col-12">
															<div class="d-flex align-items-start"><i class="bi bi-telephone me-2 fs-5 text-primary"></i>
																<div>
																	<label class="fw-bold mb-0">Số điện thoại</label>
																	<div class="text-dark"><?php echo $thongTinNV_hienThi['sodt'] ?? '---'; ?></div>
																</div>
															</div>
														</div>

														<div class="col-lg-4 col-md-6 col-12">
															<div class="d-flex align-items-start"><i class="bi bi-envelope me-2 fs-5 text-primary"></i>
																<div>
																	<label class="fw-bold mb-0">Email</label>
																	<div class="text-dark"><?php echo $thongTinNV_hienThi['email'] ?? '---'; ?></div>
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-md-6 col-12">
															<div class="d-flex align-items-start"><i class="bi bi-pin-map me-2 fs-5 text-primary"></i>
																<div>
																	<label class="fw-bold mb-0">Nơi sinh</label>
																	<div class="text-dark"><?php echo $thongTinNV_hienThi['noisinh'] ?? '---'; ?></div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											
											
											<!-- ---------------------- ------------------ -->
											<hr>
											<!-- Thông tin về Nhóm, Lương, Kỷ luật ---------------------- ------------------ -->
											
											<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
												<!-- Khối 1 các button của trang cứu  ------------------------------ -->
												<div class="nav" id="myTab" role="tablist">
												<button class="btn btn-primary rounded-pill me-3 fw-bold px-4 active" 
														id="nhom-tab" data-bs-toggle="tab" data-bs-target="#tab-nhom" 
														type="button" role="tab" aria-controls="tab-nhom" aria-selected="true">
													<i class="bi bi-people-fill me-1"></i> Nhóm
												</button>
												<button class="btn btn-outline-primary rounded-pill me-3 fw-bold px-4" 
														id="luong-tab" data-bs-toggle="tab" data-bs-target="#tab-luong" 
														type="button" role="tab" aria-controls="tab-luong" aria-selected="false">
													<i class="bi bi-currency-dollar me-1"></i> Lương
												</button>
												<button class="btn btn-outline-primary rounded-pill fw-bold px-4" 
														id="khenthuong-tab" data-bs-toggle="tab" data-bs-target="#tab-khenthuong" 
														type="button" role="tab" aria-controls="tab-khenthuong" aria-selected="false">
													<i class="bi bi-award me-1"></i> Khen thưởng - Kỷ luật
												</button>
											</div>
												
												<div class="d-flex align-items-center mt-3 mt-md-0 ms-auto">
													<label for="locThangNam" class="form-label me-2 mb-0 fw-bold">Lọc theo Tháng/Năm:</label>
													
													<input type="text"  name="ky_luong_display" id="locThangNam" style="width: 150px;background-color: white !important;" class="form-control date-picker-month" required value="<?php echo $kyLuongMacDinh; ?>" readonly />   
												</div>
											</div>
											 <!--  ------------------------------ -->
											<div class="tab-content" id="myTabContent">
												<!-- Khối 2 Bảng dánh sách nhóm  --------------------------->
												<div class="tab-pane fade show active mt-4" id="tab-nhom" role="tabpanel" aria-labelledby="nhom-tab" tabindex="0">
													<?php if (empty($danhSachNhomVaThanhVien)): ?>
														<div class="alert alert-info text-center">
															Nhân viên này hiện không thuộc nhóm làm việc nào.
														</div>
													<?php else: ?>
														
														<?php foreach ($danhSachNhomVaThanhVien as $nhomData): ?>
															
															<?php $info = $nhomData['thong_tin_nhom']; ?>
															<?php $thanhVienList = $nhomData['thanh_vien']; ?>
															
															<div class="mb-4 p-3 border rounded-3 shadow-sm bg-white">
																
																<h5 class="fw-bold mb-3 mt-2 text-primary border-bottom pb-2">
																	<i class="bi bi-people-fill me-2"></i> 
																	<b>Tên nhóm:</b> 
																	<span class="text-dark fw-bold"><?php echo $info['tennhom']; ?></span>
																	<small class="text-muted fw-normal">(Mã: <?php echo $info['manhom']; ?>)</small>
																</h5>
																<div>
																	<?php echo $info['mota']; ?>
																</div>
																<h6 class="fw-bold mb-3 mt-4 text-secondary">Danh sách thành viên (<?php echo count($thanhVienList); ?>)</h6>
																
																<div class="table-responsive">
																	<table class="table table-striped table-bordered text-start align-middle">
																		<thead class="table-info">
																			<tr>
																				<th scope="col" class="text-center" style="width: 50px;">STT</th>
																				<th scope="col" class="text-center" style="width: 70px;">Ảnh</th>
																				<th scope="col">Họ tên</th>
																				
																				<th scope="col">Chức vụ</th>
																				<th scope="col">Phòng ban</th>
																				<th scope="col" style="width: 80px;">Giới tính</th>
																			</tr>
																		</thead>
																		<tbody>
																			<?php $stt = 1; ?>
																			<?php foreach ($thanhVienList as $tv): ?>
																				<tr>
																					<td class="text-center"><?php echo $stt++; ?></td>
																					<td class="text-center">
																						<?php 
																							$tv_avatar = './uploads/nhanvien/'.$tv['anhdaidien'] ?? null;
																							$tv_image_src = ($tv_avatar && file_exists($tv_avatar)) ? $tv_avatar : './assets/images/default-avatar.png';
																						?>
																						<img src="<?php echo $tv_image_src; ?>" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;" alt="TV Avatar">
																					</td>
																					<td>
																						<?php echo $tv['hoten']; ?> 
																						<?php if ($tv['id_nv'] == $thongTinNV_hienThi['id']): ?>
																							<span class="badge bg-primary"> Tôi </span>
																						<?php endif; ?>
																					</td>
																					
																					<td><?php echo $tv['chucvu']; ?></td>
																					<td><?php echo $tv['phongban']; ?></td>
																					<td><?php echo $tv['gtinh']; ?></td>
																				</tr>
																			<?php endforeach; ?>
																		</tbody>
																	</table>
																</div>
															</div>
														<?php endforeach; ?>
														
													<?php endif; ?>
												</div>
												<!-- Khối 3 Chi tiết lương  --------------------------->
												
												<div class="tab-pane fade mt-4" id="tab-luong" role="tabpanel" aria-labelledby="luong-tab" tabindex="0">
													<?php 

														if ($luongChiTiet): 
															function format_currency($amount) {
																return number_format($amount ?? 0, 0, ',', '.') . ' VNĐ';
															}
															function format_decimal($value, $decimals = 2) {
																return number_format($value ?? 0, $decimals);
															}
														?>
															<div class="p-3 border rounded-3 bg-light">
														
													<div class="row mb-4">
														<div class="col-12"><h6 class="text-primary fw-bold">Chi tiết Lương Tháng (<span id="detailMonth"><?php echo $kyLuongMacDinh; ?></span>)</h6></div>
														
														<div class="col-md-4">
															<p class="mb-1">Lương cơ bản: <strong class="text-success" id="detailLuongCB"><?php echo format_currency($luongChiTiet['luong_co_ban_goc']); ?></strong></p>
															<p class="mb-1">Hệ số Lương: <strong id="detailHSLuong"><?php echo format_decimal($luongChiTiet['he_so_luong_goc']); ?></strong></p>
															<p class="mb-1">Phụ cấp: <strong id="detailPhuCap"><?php echo format_currency($luongChiTiet['phu_cap']); ?></strong></p>
															<p class="mb-1">Hệ số phụ cấp: <strong id="detailHSPhuCap"><?php echo format_decimal($luongChiTiet['he_so_phu_cap_goc']); ?></strong></p>
															<p class="mb-1">Ngày công: <strong id="detailNgayCong"><?php echo format_decimal($luongChiTiet['ngay_cong'], 1); ?> ngày</strong></p>
														</div>
														
														<div class="col-md-4">
															<p class="mb-1">BHXH (8%): <strong class="text-danger" id="detailBHXH"><?php echo format_currency($luongChiTiet['bhxh']); ?></strong></p>
															<p class="mb-1">BHYT (1.5%): <strong class="text-danger" id="detailBHYT"><?php echo format_currency($luongChiTiet['bhyt']); ?></strong></p>
															<p class="mb-1">BHTN (1%): <strong class="text-danger" id="detailBHTN"><?php echo format_currency($luongChiTiet['bhtn']); ?></strong></p>
															<p class="mb-1">Thuế thu nhập cá nhân: <strong class="text-danger" id="detailThueTNCN"><?php echo format_currency($luongChiTiet['thue_tncn']); ?></strong></p>
															<p class="mb-1">Tạm ứng: <strong class="text-danger" id="detailTamUng"><?php echo format_currency($luongChiTiet['tam_ung']); ?></strong></p>
														</div>
														
														<div class="col-md-4">
															<p class="mb-1 fs-5">Thực lãnh: <strong class="text-success fs-4" id="detailThucLanh"><?php echo format_currency($luongChiTiet['thuc_lanh']); ?></strong></p>
															<hr>
															
															<p class="mb-1"><small>Người tạo: <span id="detailNguoiTao" class="text-muted"><?php echo $luongChiTiet['nguoitao_name'] ?? 'N/A'; ?></span></small></p>
															<p class="mb-1"><small>Ngày tạo: <span id="detailNgayTao" class="text-muted"><?php echo date('d/m/Y', strtotime($luongChiTiet['ngaytao'])); ?></span></small></p>
															
															<hr>
															
															<p class="mb-1"><small>Người sửa: <span id="detailNguoiSua" class="text-muted"><?php echo $luongChiTiet['nguoisua_name'] ?? 'N/A'; ?></span></small></p>
															<p class="mb-1"><small>Ngày sửa: <span id="detailNgaySua" class="text-muted"><?php echo date('d/m/Y', strtotime($luongChiTiet['ngaysua'])); ?></span></small></p>

															
														</div>
													</div>
												</div>

														<?php else: ?>
														<div class="alert alert-warning text-center">
															Không tìm thấy bảng lương cho nhân viên này trong tháng: <?php echo $kyLuongMacDinh; ?>.
														</div>
													<?php endif; ?>				
												</div>
												<!-- --------------------------->
												 <!-- Khối 4 Khen thưởng - kỉ luật  --------------------------->
												<div class="tab-pane fade mt-4" id="tab-khenthuong" role="tabpanel" aria-labelledby="khenthuong-tab" tabindex="0">
													<!-- Khối 4.1 Khen thưởng --------------------------->	
													<h5 class="fw-bold mb-3 mt-2 text-success border-bottom pb-2">
														<i class="bi bi-award me-2"></i> Khen thưởng tháng: <?php echo $kyLuongMacDinh; ?> 
													</h5>

													<?php if (!empty($khenThuongList)): ?>
														<div class="table-responsive mb-5">
															<table class="table table-hover table-bordered text-center align-middle">
																<thead class="table-success">
																	<tr>
																		<th scope="col">STT</th>
																		<th scope="col">Mã khen thưởng</th>
																		<th scope="col" >Tên khen thưởng</th>
																		<th scope="col" >Số tiền</th>
																		<th scope="col" >Hình thức</th>
																		<th scope="col">Nội dung</th>
																		<th scope="col">Ngày quyết định</th>
																	</tr>
																</thead>
																<tbody>
																	<?php $stt_kt = 1; ?>
																	<?php foreach ($khenThuongList as $kt): ?>
																		<tr>
																			<td><?php echo $stt_kt++; ?></td>
																			<td><?php echo $kt['ma_ktkl']; ?></td>
																			<td class="text-success fw-bold"><?php echo $kt['ten_ktkl']; ?></td>
																			<td><?php echo number_format($kt['so_tien'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></td>
																			<td><?php echo $kt['hinh_thuc']; ?></td>
																			<td class="text-start"><?php echo $kt['noidung']; ?></td>
																			<td><?php echo date('d/m/Y', strtotime($kt['ngayqd'])); ?></td>
																		</tr>
																	<?php endforeach; ?>
																</tbody>
															</table>
														</div>
													<?php else: ?>
														<div class="alert alert-light text-center">Không có Khen thưởng trong tháng này.</div>
													<?php endif; ?>
													<!-- ---------------------- ------------------ -->
													<!-- Khối 4.2 Kỷ luật --------------------------->
													<h5 class="fw-bold mb-3 mt-2 text-danger border-bottom pb-2">
														<i class="bi bi-x-octagon-fill me-2"></i> Kỷ luật tháng: <?php echo $kyLuongMacDinh; ?>
													</h5>

													<?php if (!empty($kyLuatList)): ?>
														<div class="table-responsive">
															<table class="table table-hover table-bordered text-center align-middle">
																<thead class="table-danger">
																	<tr>
																		<th scope="col" >STT</th>
																		<th scope="col" >Mã kỷ luật</th>
																		<th scope="col">Tên kỷ luật</th>
																		<th scope="col" >Số tiên</th>
																		<th scope="col" >Hình thức</th>
																		<th scope="col">Nội dung</th>
																		<th scope="col" >Ngày quyết định</th>
																	</tr>
																</thead>
																<tbody>
																	<?php $stt_kl = 1; ?>
																	<?php foreach ($kyLuatList as $kl): ?>
																		<tr>
																			<td><?php echo $stt_kl++; ?></td>
																			<td><?php echo $kl['ma_ktkl']; ?></td>
																			<td class="text-danger fw-bold"><?php echo $kl['ten_ktkl']; ?></td>
																			<td><?php echo number_format($kl['ck_khenthuong'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></td>
																			<td><?php echo $kl['hinh_thuc']; ?></td>
																			<td class="text-start"><?php echo $kl['noidung']; ?></td>
																			<td><?php echo date('d/m/Y', strtotime($kl['ngayqd'])); ?></td>
																		</tr>
																	<?php endforeach; ?>
																</tbody>
															</table>
														</div>
													<?php else: ?>
														<div class="alert alert-light text-center">Không có Kỷ luật trong tháng này.</div>
													<?php endif; ?>
													<!-- ---------------------- ------------------ -->								
												</div>
												
											</div>
											<!-- ---------------------- ------------------ -->
											<?php endif; ?>
										</div>
									</div>
								</div>
								
							</div>
						</section>
					</div>
					
					<!------------ Kết thúc nội dung ở đây --------------------!>

				</div>
			</div>
		
    </div>
	
    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
     <script src="assets/js/main.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>
   
	<script src="assets/vendors/simple-datatables/simple-datatables.js"></script>
	<script src="assets/js/sweetalert2.js"></script>
	<script src="assets/js/alert-handler.js"></script>
	<script src="assets/js/validator-tooltip.js"></script>
	
	<script src="assets/vendors/jquery/jquery.min.js"></script> 

	<script src="assets/js/bootstrap-datepicker.min.js"></script>

	<script src="assets/js/bootstrap-datepicker.vi.min.js"></script>
</body>
</html>
<script>
					document.addEventListener('DOMContentLoaded', function() {
						const tabButtons = document.querySelectorAll('#myTab button');
						
						tabButtons.forEach(button => {
							button.addEventListener('click', function() {
								// Loại bỏ active/primary class khỏi tất cả
								tabButtons.forEach(btn => {
									btn.classList.remove('btn-primary');
									btn.classList.add('btn-outline-primary');
								});

								// Thiết lập nút được click là active
								this.classList.remove('btn-outline-primary');
								this.classList.add('btn-primary');
							});
						});
					
						// 6 ⭐ BỔ SUNG LOGIC CHO THÁNG TÍNH LƯƠNG ⭐
						const locThangNamInput = document.getElementById('locThangNam');

						if (locThangNamInput) {
							// ==========================================================
							// ⭐ BỔ SUNG: KHỞI TẠO DATEPICKER (Đã thêm dòng này)
							// ==========================================================
							$('#locThangNam').datepicker({
								format: "mm/yyyy",          // Định dạng Tháng/Năm
								startView: "months",        // Mở bộ chọn ở chế độ xem tháng
								minViewMode: "months",      // Chỉ cho phép chọn tới tháng
								autoclose: true,            // Tự động đóng sau khi chọn
								language: "vi"              // Sử dụng ngôn ngữ tiếng Việt (cần file .vi.min.js)
							})
							// Đổi sự kiện từ 'change' (kém tin cậy) sang 'changeDate' (chính thức)
							.on('changeDate', function(e) { 
								const ky_luong_chon = $(this).val(); // Dạng MM/YYYY

								// Lấy URL hiện tại
								let url = new URL(window.location.href);
								url.searchParams.set('ky_luong_chon', ky_luong_chon);

								// Chuyển hướng
								window.location.href = url.toString();
							});
						}
					});
					</script>
	