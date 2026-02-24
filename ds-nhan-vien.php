<?php 
include('./layouts/header.php');
include('./view/ds-nhan-vien-view-action.php');
?>

<div class="page-heading">
   

    <section class="section">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="fw-bold text-primary mb-0">
						<i class="bi bi-people-fill me-2 text-success"></i>Danh sách nhân viên
					</h4>					
				</div>
				
				<div class="d-flex justify-content-between align-items-center mb-3 mt-4">
					<div class="d-flex align-items-center flex-wrap">
						<div class="d-flex align-items-center">
							<label for="filter_selectPhongBanNV" class="me-2 mb-0" style="white-space: nowrap;">Phòng ban</label>
							<select name="filter_id_pb" id="filter_selectPhongBanNV" class="form-select select2" style="width: 350px;">
								<option value="">-- Tất cả Phòng ban --</option>
								<?php 
									foreach ($arrPhongBan as $pb):
										$isSelected = (strval($pb['id']) === strval($filter_id_pb)) ? 'selected' : '';
								?>
										<option value="<?= $pb['id'] ?>" <?= $isSelected ?>>
											<?= htmlspecialchars($pb['ten_bp']) ?>
										</option>
									<?php endforeach; 								
								?>
							</select>
						</div>
					</div>
					
					<div class="d-flex align-items-center flex-wrap justify-content-end">
						<?php if(!$ke_toan){ ?>
						<a href="them-nhan-vien.php" class="btn btn-sm btn-primary mb-2 me-4">
							<i class="bi bi-plus-circle me-1"></i> Thêm mới nhân viên
						</a>
						<?php }?>
						
						<button id="btnPrintDanhSach" class="btn btn-sm btn-info me-4 mb-2">
							<i class="bi bi-printer me-1"></i> In danh sách
						</button>
						
						
							 <a href="action/export_excel_nhanvien.php?filter_id_pb=<?php echo htmlspecialchars($filter_id_pb)?>" 
							   id="btnExportExcelNV" 
							   class="btn btn-sm btn-success me-4 mb-2" 
							   target="_blank">
								<i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
							</a>
						
						
						
					</div>
				</div>
				
                    <table class="table table-striped" id="tableNhanVien">
                        <thead>
                            <tr>
								<th>STT</th>
                                <th>Mã nhân viên</th>
                                <th>Ảnh</th>
                                <th>Tên nhân viên</th>
                                <th>Giới tính</th>
                                <th>Ngày sinh</th>
                                <th>Nơi sinh</th>
                                <th>Số cccd</th>
                                <th>Tình trạng</th>
                                <th>Hành động</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php
							$stt = 1;
							foreach ($arrNhanVien as $nv): ?>
                                <tr>
                                    <td><?= htmlspecialchars($stt); ?></td>
									<td><?= htmlspecialchars($nv['ma_nv']); ?></td>
                                    <!-- Ảnh -->
                                    <td>
                                        <?php if (!empty($nv['anhdaidien'])): ?>
                                            <img src="uploads/nhanvien/<?= htmlspecialchars($nv['anhdaidien']); ?>" 
                                                 alt="Ảnh" width="60" height="75" style="object-fit: cover;">
                                        <?php else: ?>
                                            <span class="text-muted">Không có</span>
                                        <?php endif; ?>
                                    </td>

                                    <td data-content="<?php echo htmlspecialchars(trim($nv['ten'])); ?>" ><?= htmlspecialchars($nv['hoten']); ?></td>
                                    <td><?= htmlspecialchars($nv['gtinh']); ?></td>
                                    <td><?= htmlspecialchars($nv['ngsinh']); ?></td>
                                    <td><?= htmlspecialchars($nv['noisinh']); ?></td>
                                    <td><?= htmlspecialchars($nv['so_cccd']); ?></td>

                                    <td>
                                        <?php if ($nv['trangthai'] == 1): ?>
                                            <span class="badge bg-primary">Đang làm việc</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Đã nghỉ việc</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Nút hành động -->
                                    <td>
									<a href="xem-nhan-vien.php?id=<?= $nv['id']; ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                    <?php if(!$ke_toan){ ?><a href="them-nhan-vien.php?id=<?= $nv['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="<?php echo $nv['id']; ?>" data-name="<?php echo htmlspecialchars($nv['hoten']); ?>"><i class="bi bi-trash"></i></button></td>
									<?php }?>
                                </tr>
                            <?php $stt++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Simple DataTable -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    let table = new simpleDatatables.DataTable("#tableNhanVien", {
        columns: [
		
           { select: [2, 6, 7, 8, 9], sortable: false },
		   
        ]
    });
});
</script>
<!-- SweetAlert2 Xóa -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Thông báo xóa
    document.addEventListener('click', function(e) {
        
        // Dùng .closest() để kiểm tra xem element được click hoặc một element cha
        // của nó có class là .btn-delete hay không.
        const btn = e.target.closest('.btn-delete');

        if (btn) {
            e.preventDefault();
            
            // Lấy data từ attribute
            const id = btn.dataset.id;
            const name = btn.dataset.name;

            // Kiểm tra thư viện Swal đã load chưa
            if (typeof Swal === 'undefined') {
                if (confirm(`Bạn có muốn xóa nhân viên "${name}" không?`)) {
                    window.location.href = `action/nhan-vien-action.php?delete=${id}`;
                }
                return;
            }

            // Hiển thị SweetAlert2
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có muốn xóa nhân viên "${name}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Có, xóa",
                cancelButtonText: "Không",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Chuyển hướng xử lý
                    window.location.href = `action/nhan-vien-action.php?delete=${id}`;
                }
            });
        }
    });
	// 2. Thực hiện sự kiện chọn phòng ban 
		const selectElement = document.getElementById('filter_selectPhongBanNV');
        
        selectElement.addEventListener('change', function() {
            const selectedValue = this.value;
            
            // Lấy URL hiện tại (ví dụ: /danh-sach-nhan-vien.php)
            let currentUrl = new URL(window.location.href);
            
            // Xóa tham số phân trang (page) nếu có, để tránh lỗi phân trang sau khi lọc
            currentUrl.searchParams.delete('page');

            if (selectedValue) {
                // Nếu chọn một phòng ban cụ thể, thêm/cập nhật tham số filter_id_pb
                currentUrl.searchParams.set('filter_id_pb', selectedValue);
            } else {
                // Nếu chọn "-- Tất cả Phòng ban --" (value=""), loại bỏ tham số
                currentUrl.searchParams.delete('filter_id_pb');
            }
            
            // Chuyển hướng đến URL mới
            window.location.href = currentUrl.toString();
        });
	
	// 3. Load in  ***********
	const btnPrint = document.getElementById("btnPrintDanhSach");
    if (btnPrint) {
        btnPrint.addEventListener("click", function () {
            const url = "action/print_nhanvien.php?filter_id_pb=<?= htmlspecialchars($filter_id_pb) ?>";

            // Tạo iframe ẩn
            const iframe = document.createElement("iframe");
            iframe.style.display = "none";
            iframe.src = url;
            document.body.appendChild(iframe);

            // Khi iframe load xong → gọi print
            iframe.onload = function () {
                iframe.contentWindow.focus(); // đảm bảo iframe focus
                iframe.contentWindow.print();

                // Dọn iframe sau 1s
                setTimeout(function () {
                    document.body.removeChild(iframe);
                }, 1000);
            };
        });
    }
	//***************
});
</script>


<?php include('./layouts/footer.php'); ?>
