<?php
include('./layouts/header.php');
include(__DIR__ . '/connection/config.php');
include(__DIR__ . '/models/NhanVien.php');
// Khởi tạo kết nối
	$database = new Database();
	$conn = $database->getConnection();

	// Khởi tạo class
	$nhanvien = new NhanVien($conn);
	$id = $_GET['id'] ?? 0;
	$nv = [];

	if ($id) {
		$nv = $nhanvien->getById($id);
		if (!$nv) {
			die('<div class="alert alert-danger m-3">Không tìm thấy nhân viên!</div>');
		}
	}
?>

<div class="page-heading">
   
    <!-- Thân hiển thị -->
    <section id="profile-detail">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mt-3">
            <div class="card-body bg-light-subtle">
			<div class="row align-items-center">
            <div class="col-12 col-md-8">
                <h3>Thông tin chi tiết nhân viên</h3>
                <p class="text-muted">Mã nhân viên: <strong><?= htmlspecialchars($nv['ma_nv']); ?></strong></p>
            </div>
            
        </div>
                <div class="row">
                    <!-- Ảnh -->
                    <div class="col-md-3 text-center border-end">
                        <img src="<?= !empty($nv['anhdaidien']) ? 'uploads/nhanvien/' . htmlspecialchars($nv['anhdaidien']) : 'assets/images/default-avatar.png'; ?>" 
                             alt="Ảnh nhân viên" class="rounded-3 border shadow-sm mb-3" width="180" height="240">
                        <h5 class="fw-bold"><?= htmlspecialchars($nv['hoten']); ?></h5>
                        <p class="text-muted"><i class="bi bi-envelope"></i> <?= htmlspecialchars($nv['email']); ?></p>
                        <p class="text-muted"><i class="bi bi-telephone"></i> <?= htmlspecialchars($nv['sodt']); ?></p>
                        <span class="badge <?= $nv['trangthai'] ? 'bg-success' : 'bg-danger'; ?> px-3 py-2 fs-6">
                            <?= $nv['trangthai'] ? 'Đang làm việc' : 'Đã nghỉ việc'; ?>
                        </span>
                    </div>

                    <!-- Thông tin -->
                    <div class="col-md-9 ps-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p><strong>Giới tính:</strong> <?= htmlspecialchars($nv['gtinh']); ?></p>
                                <p><strong>Ngày sinh:</strong> <?= date('d/m/Y', strtotime($nv['ngsinh'])); ?></p>
                                <p><strong>Nơi sinh:</strong> <?= htmlspecialchars($nv['noisinh']); ?></p>
                                
                                <p><strong>Tình trạng hôn nhân:</strong> <?= htmlspecialchars($nv['hon_nhan']); ?></p>
                                <p><strong>Số CCCD:</strong> <?= htmlspecialchars($nv['so_cccd']); ?></p>
                                <p><strong>Nơi cấp:</strong> <?= htmlspecialchars($nv['noicap_cccd']); ?></p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <p><strong>Hộ khẩu:</strong> <?= htmlspecialchars($nv['hokhau']); ?></p>
                                <p><strong>Tạm trú:</strong> <?= htmlspecialchars($nv['tamtru']); ?></p>
                                <p><strong>Quốc tịch:</strong> <?= htmlspecialchars($nv['quoc_tich']); ?></p>
                                <p><strong>Dân tộc:</strong> <?= htmlspecialchars($nv['dan_toc']); ?></p>
                                <p><strong>Tôn giáo:</strong> <?= htmlspecialchars($nv['ton_giao']); ?></p>
                                <hr>
                                <p><strong>Phòng ban:</strong> <?= htmlspecialchars($nv['phong_ban']); ?></p>
                                <p><strong>Chức vụ:</strong> <?= htmlspecialchars($nv['chuc_vu']); ?></p>
                                <p><strong>Loại nhân viên:</strong> <?= htmlspecialchars($nv['loai_nv']); ?></p>
                                <p><strong>Trình độ:</strong> <?= htmlspecialchars($nv['trinh_do']); ?></p>
                                <p><strong>Chuyên môn:</strong> <?= htmlspecialchars($nv['chuyen_mon']); ?></p>                                
                            </div>
                        </div>
						<hr>
						<div class="text-end text-muted small fst-italic">
							Ngày tạo: <?= date('d/m/Y', strtotime($nv['ngaytao'])); ?> — Người tạo: <?= htmlspecialchars($nv['nguoitao_name'] ?? 'Không rõ'); ?>
						</div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('./layouts/footer.php'); ?>
