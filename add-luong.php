<?php 
include('./layouts/header.php');
include('./action/luong-action.php');

$maluong = "ML" . time();
$row_acc = $_SESSION['user'];
?>

<div class="page-heading">
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="bi bi-cash-coin me-2"></i>
                            <?= isset($idEdit) ? 'Chỉnh sửa bảng lương' : 'Thêm mới bảng lương' ?>
                        </h5>
                    </div>

                    <div class="card-body">
                        <form method="post" action="action/luong-action.php" class="validate-tooltip">
                            <input type="hidden" name="id" value="<?= $luongInfo['id'] ?? '' ?>">

                            <div class="row">
                                <!-- Cột trái -->
                                <div class="col-md-6 border-end">
                                    <div class="mb-3">
                                        <label for="maluong" class="form-label">Mã lương</label>
                                        <input type="text" id="maluong" name="maluong" class="form-control"
                                            value="<?= $luongInfo['maluong'] ?? $maluong ?>" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label for="id_nv" class="form-label">Nhân viên <span class="text-danger">*</span></label>
                                        <select id="id_nv" name="id_nv" class="form-select" required>
                                            <option value="">-- Chọn nhân viên --</option>
                                            <?php foreach ($ds_nv as $nv): ?>
                                                <option value="<?= $nv['id'] ?>" <?= ($luongInfo['id_nv'] ?? '') == $nv['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($nv['hoten']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                   

                                    <div class="mb-3">
                                        <label for="ngaycong" class="form-label">Ngày công</label>
                                        <input type="number" id="ngaycong" name="ngaycong" class="form-control"
                                            value="<?= htmlspecialchars($luongInfo['ngaycong'] ?? '') ?>" placeholder="VD: 26">
                                    </div>

                                    <div class="mb-3">
                                        <label for="phucap" class="form-label">Phụ cấp</label>
                                        <input type="text" id="phucap" name="phucap" class="form-control"
                                            value="<?= htmlspecialchars($luongInfo['phucap'] ?? '') ?>" placeholder="VD: 500000">
                                    </div>
                                </div>

                                <!-- Cột phải -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="khoannop" class="form-label">Khoản nộp</label>
                                        <input type="text" id="khoannop" name="khoannop" class="form-control"
                                            value="<?= htmlspecialchars($luongInfo['khoannop'] ?? '') ?>" placeholder="VD: 300000">
                                    </div>

                                    <div class="mb-3">
                                        <label for="tamung" class="form-label">Tạm ứng</label>
                                        <input type="text" id="tamung" name="tamung" class="form-control"
                                            value="<?= htmlspecialchars($luongInfo['tamung'] ?? '') ?>" placeholder="VD: 1000000">
                                    </div>


                                    <div class="mb-3">
                                        <label for="ngaycham" class="form-label">Ngày chấm</label>
                                        <input type="date" id="ngaycham" name="ngaycham" class="form-control"
                                            value="<?= htmlspecialchars($luongInfo['ngaycham'] ?? date('Y-m-d')) ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Người tạo</label>
                                        <input type="text" class="form-control" readonly
                                            value="<?= $luongInfo['nguoitao_name'] ?? (($row_acc['ho'] ?? '') . ' ' . ($row_acc['ten'] ?? '')) ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Ngày tạo</label>
                                        <input type="text" class="form-control" readonly
                                            value="<?= $luongInfo['ngaytao'] ?? date('Y-m-d') ?>">
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-end">
                                <?php if (!empty($luongInfo)): ?>
                                    <button type="submit" name="update" class="btn btn-primary me-2">
                                        <i class="bi bi-save"></i> Cập nhật
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="add" class="btn btn-success me-2">
                                        <i class="bi bi-plus-circle"></i> Thêm mới
                                    </button>
                                <?php endif; ?>
                                <a href="add-luong.php" class="btn btn-light">Làm mới</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('./layouts/footer.php'); ?>
