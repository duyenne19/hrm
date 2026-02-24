<?php 
	// include file
	include('./layouts/header.php');
	include('./models/TongQuan.php');
	
	$tongquan = new TongQuan();
    // Lấy số lượng tổng hợp  ************************************************************
	$tong_hop = $tongquan->tongHop();
    // ************************************************************
    
    // 1. Thẻ Info Card & Chỉ số chính
    $current_month = '11/2025';
    $previous_month = '10/2025';

    // --- A. Nhân sự & Tổ chức ---
    $total_employees = $tong_hop["tong_nhan_vien"]; // Tổng nhân viên (đang làm việc)
    $total_departments = $tong_hop["tong_phong_ban"]; // Tổng phòng ban
    $total_positions = $tong_hop["tong_chuc_vu"];   // Tổng số chức vụ

    // --- B. Công tác ---
    $total_on_mission = $tong_hop["dang_cong_tac"]; // Tổng số công đang công tác
    $total_upcoming_mission = $tong_hop["sap_cong_tac"]; // Tổng số sắp đi công tác

    // --- C. Lương ---
    //$avg_salary_previous_month = number_format($tong_hop["luong_tb_thang_truoc"], 0, '.', ','); // Trung bình lương tháng trước (10/2025)

    // --- D. Khen thưởng / Kỷ luật (Sử dụng dữ liệu tháng trước để tính chênh lệch) ---
    $award_this_month = $tong_hop["khen_thuong_thang_truoc"]; 
    $discipline_this_month = $tong_hop["ky_luat_thang_truoc"]; 
    
    


    // 2. Dữ liệu Biểu đồ
	// Phòng ban ************************************************************
		$co_cau_phong_ban = $tongquan->co_cau_phong_ban();

		// 2. Khởi tạo mảng để lưu trữ dữ liệu đã tách
		$dept_labels_data = []; // Tên phòng ban
		$dept_series_data = []; // Số lượng nhân viên

		// 3. Duyệt qua mảng và tách dữ liệu
		if (!empty($co_cau_phong_ban['co_cau_phong_ban'])) {
			foreach ($co_cau_phong_ban['co_cau_phong_ban'] as $item) {
				$count = (int)$item['so_luong'];
				
				// Thêm tên phòng ban và số lượng nhân viên vào mảng
				$dept_labels_data[] = $item['ten_phong_ban'];
				$dept_series_data[] = $count;
			}
		}

		// 4. Chuyển đổi sang JSON
		$dept_labels = json_encode($dept_labels_data);
		$dept_series = json_encode($dept_series_data);
		// ************************************************************
	
	// Giới tính ************************************************************
   $co_cau_gioi_tinh = $tongquan->co_cau_gioi_tinh();

	//Định nghĩa các nhãn cần hiển thị (theo thứ tự mong muốn)
	$gender_labels_data = ['Nam', 'Nữ', 'Khác'];

	//Lấy dữ liệu theo thứ tự nhãn
	$gender_series_data = [];
	$gender_series_data[] = $co_cau_gioi_tinh['Nam'] ?? 0;
	$gender_series_data[] = $co_cau_gioi_tinh['Nữ'] ?? 0;
	$gender_series_data[] = $co_cau_gioi_tinh['Khác'] ?? 0;

	//Chuyển đổi sang JSON
	$gender_labels = json_encode($gender_labels_data);
	$gender_series = json_encode($gender_series_data);
    // ************************************************************
	
	// Hôn nhân ************************************************************
    $co_cau_hon_nhan = $tongquan->co_cau_hon_nhan();
		//Khởi tạo mảng
		$marriage_labels_data = []; 
		$marriage_series_data = [];
		//Duyệt qua mảng và tách dữ liệu (không gộp nhóm)
		if (!empty($co_cau_hon_nhan)) {
			foreach ($co_cau_hon_nhan as $item) {
				// Tên tình trạng hôn nhân
				$marriage_labels_data[] = $item['tinh_trang']; 
				
				// Số lượng nhân viên
				$marriage_series_data[] = (int)$item['so_luong']; 
			}
		}

		//Chuyển đổi sang JSON
		$marriage_labels = json_encode($marriage_labels_data);
		$marriage_series = json_encode($marriage_series_data);
	// ************************************************************
    
    // DỮ LIỆU LƯƠNG 6 THÁNG ************************************************************
	$luong_6_thang = $tongquan->luong_trung_binh_6_thang();
	// 2. Khởi tạo mảng để lưu trữ dữ liệu đã tách
	$avg_salary_6m_data = []; // Dữ liệu lương (Trục Y)
	$salary_6m_months_data = []; // Dữ liệu tháng (Trục X)

	// 3. Duyệt qua mảng và tách dữ liệu
	if (!empty($luong_6_thang)) {
		foreach ($luong_6_thang as $item) {
			// Lấy giá trị lương dạng số (cho biểu đồ)
			$avg_salary_6m_data[] = $item['trung_binh_luong_so'];
			
			// Lấy tên tháng (cho nhãn trục)
			$salary_6m_months_data[] = $item['thang_nam'];
		}
	}

	// 4. Chuyển đổi sang JSON để sử dụng trong JavaScript (Thư viện biểu đồ)
	$avg_salary_6m = json_encode($avg_salary_6m_data);
	$salary_6m_months = json_encode($salary_6m_months_data);
    // ************************************************************
    
    
    // DỮ LIỆU LƯƠNG CƠ BẢN THEO CHỨC VỤ ************************************************************
	// 1. Lấy dữ liệu từ database
		$luong_chuc_vu_data = $tongquan->luong_chuc_vu();

		// 2. Khởi tạo mảng để lưu trữ dữ liệu đã tách
		$base_salary_labels_data = []; // Tên chức vụ (Trục X)
		$base_salary_data_data = [];   // Lương cơ bản (Trục Y)

		// 3. Duyệt qua mảng và tách dữ liệu
		if (!empty($luong_chuc_vu_data)) {
			foreach ($luong_chuc_vu_data as $item) {
				// Lấy Tên chức vụ
				$base_salary_labels_data[] = $item['tencv'];
				
				// Lấy Lương cơ bản
				$base_salary_data_data[] = $item['luong_coban'];
			}
		}

		// 4. Chuyển đổi sang JSON
		$base_salary_labels = json_encode($base_salary_labels_data);
		$base_salary_data = json_encode($base_salary_data_data);
    //$base_salary_labels = json_encode(['Quản lý cấp cao', 'Quản lý cấp trung', 'Trưởng nhóm', 'Nhân viên']);
    //$base_salary_data = json_encode([35000000, 22000000, 16000000, 10000000]); // VND (Giả lập lương cơ bản)
     // ************************************************************
	 
    // DỮ LIỆU TRÌNH ĐỘ HỌC VẤN ************************************************************
   // 1. Lấy dữ liệu từ database
		$trinh_do_data = $tongquan->co_cau_trinh_do();

		//Trích xuất Tổng số nhân viên đang làm việc
		$total_employees = $trinh_do_data['tong_nv_dang_lam'];
		$total_employees_float = (float)$total_employees;

		// Khởi tạo mảng để lưu trữ dữ liệu đã tách
		$level_labels_data = [];    // Tên trình độ (Đại học, Cao đẳng,...)
		$level_counts_data = [];    // Số lượng nhân viên theo trình độ
		$level_percentages_data = []; // Tỷ lệ phần trăm

		// Duyệt qua mảng và tính toán
		if (!empty($trinh_do_data['co_cau_trinh_do'])) {
			foreach ($trinh_do_data['co_cau_trinh_do'] as $item) {
				$count = (int)$item['so_luong'];
				
				// a. Lấy nhãn và số lượng
				$level_labels_data[] = $item['ten_trinh_do'];
				$level_counts_data[] = $count;
				
				// b. Tính tỷ lệ phần trăm (Chỉ tính nếu tổng NV > 0)
				if ($total_employees_float > 0) {
					$percentage = round(($count / $total_employees_float) * 100);
					$level_percentages_data[] = $percentage;
				} else {
					$level_percentages_data[] = 0;
				}
			}
		}

		// Chuyển đổi sang JSON
		$level_labels = json_encode($level_labels_data);
		$level_counts_json = json_encode($level_counts_data);
		$level_percentages_json = json_encode($level_percentages_data);

	// 6. Số lượng nhân viên dạng float (đã có từ bước 2)
	$total_employees_json = json_encode($total_employees);
	// ************************************************************
    // DỮ LIỆU PHÂN BỔ ĐỘ TUỔI ************************************************************
    $co_cau_do_tuoi = $tongquan->co_cau_do_tuoi();

	// 2. Định nghĩa ánh xạ (mapping) giữa khóa CSDL và nhãn tiếng Việt
	$age_mapping = [
		'<25'   => 'Dưới 25 tuổi',
		'25-35' => '25 - 35 tuổi',
		'36-45' => '36 - 45 tuổi',
		'>45'   => 'Trên 45 tuổi',
	];

	// 3. Khởi tạo mảng để lưu trữ dữ liệu đã tách
	$age_labels_data = []; // Nhãn độ tuổi (Trục X)
	$age_series_data = []; // Số lượng nhân viên (Trục Y)

	// 4. Duyệt qua mảng kết quả và tách dữ liệu
	if (!empty($co_cau_do_tuoi)) {
		foreach ($age_mapping as $key => $label) {
			// Lấy số lượng từ dữ liệu CSDL. Dùng $co_cau_do_tuoi[$key]
			$count = $co_cau_do_tuoi[$key] ?? 0; // Lấy số lượng, nếu không có thì mặc định là 0
			
			// Thêm nhãn (label) và số lượng (series data) vào mảng
			$age_labels_data[] = $label;
			$age_series_data[] = $count;
		}
	}

	// 5. Chuyển đổi sang JSON
	$age_labels = json_encode($age_labels_data);
	$age_series = json_encode($age_series_data);
	// ************************************************************
?>
    <style>
        /* ======================================= */
		/* FOOTER – GIỮ NGUYÊN THEO CSS CŨ */
		/* ======================================= */
		.footer-smart {
			margin-top: auto; 
			background: linear-gradient(90deg, #ffffff, #f8f9fa);
			font-size: 0.9rem;
			box-shadow: 0 -1px 8px rgba(0, 0, 0, 0.05);
		}

		/* Chữ số chênh lệch */
		.diff-up { color: #4fbe87; font-size: 0.85em; font-weight: 600; }
		.diff-down { color: #f3616d; font-size: 0.85em; font-weight: 600; }
		.diff-equal { color: #adb5bd; font-size: 0.85em; font-weight: 600; }



		/* ======================================= */
		/* CẤU TRÚC INFO CARDS (CHỈNH MỚI + ĐẸP) */
		/* ======================================= */

		/* Hàng chứa card */
		.info-card-row {
			display: flex;
			flex-wrap: wrap;     /* Cho phép xuống dòng */
			gap: 20px;
		}

		/* Desktop lớn (XL) – Tất cả card nằm 1 hàng, chia đều */
		@media (min-width: 1200px) {
			.info-card-col {
				flex: 1 1 0;      /* Tự chia đều theo số card */
				min-width: 0;
			}
		}

		/* Desktop thường (LG) – 3 card / hàng */
		@media (min-width: 992px) and (max-width: 1199px) {
			.info-card-col {
				flex: 1 1 calc(33.333% - 20px);
			}
		}

		/* Tablet (MD) – 2 card / hàng */
		@media (min-width: 768px) and (max-width: 991px) {
			.info-card-col {
				flex: 1 1 calc(50% - 20px);
			}
		}

		/* Mobile – 1 card / hàng */
		@media (max-width: 767px) {
			.info-card-col {
				flex: 1 1 100%;
			}
		}
		.info-card-row {
			align-items: stretch;
		}

		.info-card-col {
			display: flex;
		}

		.info-card-col .card {
			height: 100%;
			display: flex;
			flex-direction: column;
		}
		/* Cột card */
		.info-card-col {
			flex: 1 1 0;   /* Tự chia đều */
			min-width: 0;  /* Không bị bể bố cục */
		}

		/* CARD – làm đẹp hơn */
		.info-card-col .card {
			border-radius: 16px;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
			transition: transform .2s ease, box-shadow .2s ease;
		}

		.info-card-col .card:hover {
			transform: translateY(-6px);
			box-shadow: 0 8px 22px rgba(0,0,0,0.12);
		}



		/* ======================================= */
		/* RESPONSIVE */
		/* ======================================= */
		@media (max-width: 992px) {
			.info-card-col { flex: 1 1 calc(50% - 20px); } /* Tablet: 2 card/hàng */
		}

		@media (max-width: 576px) {
			.info-card-col { flex: 1 1 100%; } /* Mobile: 1 card/hàng */
		}



		/* ======================================= */
		/* ICON – Giữ class cũ nhưng làm đẹp bằng GRADIENT */
		/* ======================================= */

		.stats-icon {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 55px;
			height: 55px;
			border-radius: 50%;
			font-size: 1.5rem;
			color: #fff !important;
		}

		/* 1. Tổng Nhân viên */
		.stats-icon.light-blue {
			background: linear-gradient(135deg, #76baff, #4e8dff);
		}

		/* 2. Tổng Phòng Ban */
		.stats-icon.lavender {
			background: linear-gradient(135deg, #c79aff, #a874ff);
		}

		/* 3. Tổng Chức Vụ */
		.stats-icon.lime-green {
			background: linear-gradient(135deg, #90e36d, #5cc44c);
		}

		/* 4. Lương TB */
		.stats-icon.light-orange {
			background: linear-gradient(135deg, #ffc96b, #ff9f3f);
		}

		/* 5. Công tác / Sắp đi */
		.stats-icon.deep-blue {
			background: linear-gradient(135deg, #6f7bff, #4a54e1);
		}

		/* 6. Khen thưởng */
		.stats-icon.vibrant-green {
			background: linear-gradient(135deg, #63e39a, #2cbf6d);
		}

		/* 7. Kỷ luật */
		.stats-icon.soft-red {
			background: linear-gradient(135deg, #ff8a8a, #ff5c5c);
		}

    </style>

   <div id="main-content-wrapper">
                
                <div class="page-content">
                    
                    <!-- 1. HÀNG INFO CARDS (7 CHỈ SỐ) -->
                    <!-- SỬ DỤNG CLASS info-card-row ĐÃ SỬA LỖI FLEX-WRAP -->
                    <section class="row mb-4 info-card-row"> 
                        
                        <!-- CARD 1: Tổng Nhân Viên -->
                        <div class="info-card-col">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4"><div class="stats-icon light-blue"><i class="iconly-boldProfile"></i></div></div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Tổng nhân viên</h6>
                                            <h6 class="font-extrabold mb-0"><?= number_format($total_employees) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- CARD 2: Tổng Phòng Ban -->
                        <div class="info-card-col">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4"><div class="stats-icon lavender"><i class="iconly-boldDiscovery"></i></div></div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Tổng phòng ban</h6>
                                            <h6 class="font-extrabold mb-0"><?= number_format($total_departments) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CARD 3: Tổng Chức Vụ -->
                        <div class="info-card-col">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4"><div class="stats-icon lime-green"><i class="bi bi-person-badge-fill"></i></div></div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Tổng Chức vụ</h6>
                                            <h6 class="font-extrabold mb-0"><?= number_format($total_positions) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CARD 4: Lương TB Tháng Trước 
                        <div class="info-card-col">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4"><div class="stats-icon light-orange"><i class="bi bi-currency-dollar"></i></div></div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Lương trung bình (<?= $previous_month ?>)</h6>
                                            <h6 class="font-extrabold mb-0"><?= $avg_salary_previous_month ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        -->
                        <!-- CARD 5: Đang công tác / Sắp đi -->
                        <div class="info-card-col">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4"><div class="stats-icon deep-blue"><i class="bi bi-send-fill"></i></div></div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Công tác/Sắp đi</h6>
                                            <h6 class="font-extrabold mb-0"><?= number_format($total_on_mission) ?> / <?= number_format($total_upcoming_mission) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CARD 6: Khen thưởng Tháng này -->
                        <div class="info-card-col">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4"><div class="stats-icon vibrant-green"><i class="bi bi-award-fill"></i></div></div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Khen thưởng (<?= $previous_month ?>)</h6>
                                            <h6 class="font-extrabold mb-0">
                                                <?= number_format($award_this_month) ?> 
                                                
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- CARD 7: Kỷ luật Tháng này -->
                        <div class="info-card-col">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4"><div class="stats-icon soft-red"><i class="bi bi-x-octagon-fill"></i></div></div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Kỷ luật (<?= $previous_month ?>)</h6>
                                            <h6 class="font-extrabold mb-0">
                                                <?= number_format($discipline_this_month) ?> 
                                                
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- 2. HÀNG BIỂU ĐỒ CHÍNH -->
                    <section class="row mb-4">
                        
                        <!-- BIỂU ĐỒ BAR DỌC (LƯƠNG TB 6 THÁNG) -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">💸 Lương trung bình công ty 6 tháng gần nhất)</h4>
                                </div>
                                <div class="card-body">
                                    <div id="chart-avg-salary-6m"></div>
                                </div>
                            </div>
                        </div>

                        <!-- BIỂU ĐỒ LƯƠNG CƠ BẢN THEO CHỨC VỤ -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">💰 Lương cơ bản theo chức vụ</h4>
                                </div>
                                <div class="card-body">
                                    <div id="chart-base-salary-by-position"></div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 3. HÀNG BIỂU ĐỒ CHI TIẾT -->
                    <section class="row">
                        
                        <!-- BIỂU ĐỒ BAR NGANG (TỶ LỆ TRÌNH ĐỘ HỌC VẤN - HIỂN THỊ CẢ SL VÀ %) -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card">
                                <div class="card-header">  <h4 class="card-title">🎓 Cơ cấu trình độ nhân viên </h4>                                </div>
                                <div class="card-body">
									<div id="chart-education-level"></div>
								</div>
                            </div>
                        </div>
						<div class="col-lg-6 col-md-12">
								<div class="card">
									<div class="card-header"><h4 class="card-title">🏢 Nhân viên phân bổ trong phòng ban </h4></div>
									<div class="card-body">
										<div id="chart-department-distribution"></div>									
									</div>
								</div>
							
						</div>
                        <!-- PHÂN BỔ CÁC BIỂU ĐỒ NHỎ: ĐỘ TUỔI, GIỚI TÍNH, HÔN NHÂN -->
                        <div class="col-lg-12 col-md-12">
                            <div class="row">
                                <!-- Phân bổ Phòng Ban (Chiếm 50% hàng ngang trên màn hình vừa/lớn) -->
                                
                                
                                <!-- Phân bổ Độ Tuổi (Chiếm 50% hàng ngang trên màn hình vừa/lớn) -->
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header"><h4 class="card-title">🎂 Tỉ lệ tuổi của nhân viên </h4></div>
                                        <div class="card-body">
                                            <div id="chart-age-distribution"></div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header"><h4 class="card-title">🚻 Tỉ lệ giới tính của nhân viên </h4></div>
                                        <div class="card-body">
                                            <div id="chart-gender-distribution"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header"><h4 class="card-title">💍 Tình trạng hôn nhân </h4></div>
                                        <div class="card-body">
                                            <div id="chart-marriage-status"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
				
		   </div>
		   
	
 <script src="assets/js/format_currency.js"></script>               
<?php 
	include('./layouts/footer.php');
?>
<script>
        // Dữ liệu PHP được truyền vào JavaScript
        const DEPT_SERIES = <?php echo $dept_series; ?>;
        const DEPT_LABELS = <?php echo $dept_labels; ?>;
        const GENDER_SERIES = <?php echo $gender_series; ?>;
        const GENDER_LABELS = <?php echo $gender_labels; ?>;
        const MARRIAGE_SERIES = <?php echo $marriage_series; ?>;
        const MARRIAGE_LABELS = <?php echo $marriage_labels; ?>;
        const AVG_SALARY_6M = <?php echo $avg_salary_6m; ?>;
        const SALARY_6M_MONTHS = <?php echo $salary_6m_months; ?>;
        const BASE_SALARY_DATA = <?php echo $base_salary_data; ?>;
        const BASE_SALARY_LABELS = <?php echo $base_salary_labels; ?>;
        const LEVEL_PERCENTAGES = <?php echo $level_percentages_json; ?>;
        const LEVEL_COUNTS = <?php echo $level_counts_json; ?>;
        const LEVEL_LABELS = <?php echo $level_labels; ?>; 
        const AGE_SERIES = <?php echo $age_series; ?>;
        const AGE_LABELS = <?php echo $age_labels; ?>;

        // MẢNG CHỨA TẤT CẢ INSTANCE CỦA APEXCHARTS (FIX LỖI 1)
        const apexChartsInstances = [];

        // Hàm định dạng tiền tệ Việt Nam
        function formatCurrency(val) {
             return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', minimumFractionDigits: 0 }).format(val);
        }

        // Hàm định dạng số nguyên
        function formatNumber(val) {
            return new Intl.NumberFormat('vi-VN').format(val);
        }

        // Hàm định dạng phần trăm
        function formatPercentage(val) {
            return val.toFixed(0) + ' %';
        }

        // --- CHỨC NĂNG CHỐNG RUNG (DEBOUNCE) CHO SỰ KIỆN RESIZE ---
        function debounce(func, delay) {
            let timeoutId;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(context, args);
                }, delay);
            };
        }
        
        // ************************************************************
        // HÀM KHỞI TẠO VÀ VẼ TẤT CẢ BIỂU ĐỒ (FIX LỖI 1)
        // ************************************************************
        function initializeAllCharts() {
            
            // Hủy các biểu đồ cũ (nếu đã tồn tại)
            apexChartsInstances.forEach(chart => {
                try {
                    chart.destroy();
                } catch (e) {
                    // Bỏ qua lỗi nếu biểu đồ chưa được khởi tạo
                }
            });
            apexChartsInstances.length = 0; // Xóa mảng tham chiếu

            // --- BIỂU ĐỒ CHÍNH ---

            // 1. BIỂU ĐỒ BAR DỌC (LƯƠNG TB 6 THÁNG)
            var optionsAvgSalary6M = {
                series: [{
                    name: "Lương Trung Bình",
                    data: AVG_SALARY_6M
                }],
                chart: {
                    height: 350,
                    type: 'bar', // Cột đứng
                    zoom: {enabled: false},
                    toolbar: {show: false}
                },
                plotOptions: {
                    bar: {
                        horizontal: false, // Dạng cột đứng
                        columnWidth: '55%',
                        // ⭐ FIX LỖI 2: GÓC VUÔNG
                        borderRadius: 0 
                    },
                },
                dataLabels: {enabled: false},
                xaxis: {
                    categories: SALARY_6M_MONTHS,
                    title: {text: 'Tháng'}
                },
                yaxis: {
                    title: {text: 'VND'},
                    labels: {formatter: function (val) {return formatCurrency(val);}}
                },
                tooltip: {
                    y: {formatter: function (val) {return formatCurrency(val);}}
                },
                colors: ['#435ebe'] 
            };
            const chartAvgSalary6M = new ApexCharts(document.querySelector("#chart-avg-salary-6m"), optionsAvgSalary6M);
            chartAvgSalary6M.render();
            apexChartsInstances.push(chartAvgSalary6M); // LƯU VÀO MẢNG
            
            // 2. BIỂU ĐỒ BAR NGANG (LƯƠNG CƠ BẢN THEO CHỨC VỤ)
            var optionsBaseSalary = {
                series: [{name: 'Lương Cơ Bản (TB)', data: BASE_SALARY_DATA}],
                chart: {type: 'bar', height: 350, toolbar: {show: false}},
                plotOptions: {
                    bar: {
                        horizontal: true, 
                        dataLabels: {position: 'top'},
                        // ⭐ FIX LỖI 2: GÓC VUÔNG
                        borderRadius: 0 
                    }
                },
                dataLabels: {
                    enabled: true, 
                    formatter: formatCurrency,
                    offsetX: 40, 
                    style: {colors: ['#002152']} 
                },
                xaxis: {categories: BASE_SALARY_LABELS, labels: {formatter: formatCurrency}},
                tooltip: {y: {formatter: formatCurrency}},
                colors: ['#ff9800'] 
            };
            const chartBaseSalary = new ApexCharts(document.querySelector("#chart-base-salary-by-position"), optionsBaseSalary);
            chartBaseSalary.render();
            apexChartsInstances.push(chartBaseSalary); // LƯU VÀO MẢNG
            
            // --- BIỂU ĐỒ CHI TIẾT ---

            // 3. BIỂU ĐỒ BAR NGANG (TRÌNH ĐỘ HỌC VẤN - HIỂN THỊ SL VÀ %)
            var optionsEducation = {
                series: [{name: 'Tỉ lệ (%)', data: LEVEL_PERCENTAGES}],
                chart: {type: 'bar', height: 350, toolbar: {show: false}},
                plotOptions: {
                    bar: {
                        horizontal: true, 
                        dataLabels: {position: 'top'},
                        // ⭐ FIX LỖI 2: GÓC VUÔNG
                        borderRadius: 0 
                    }
                },
                dataLabels: {
                    enabled: true, 
                    formatter: function (val, opts) {
                        const index = opts.dataPointIndex;
                        const count = LEVEL_COUNTS[index];
                        return formatNumber(count) + ' NV (' + formatPercentage(val) + ')';
                    },
                    offsetX: 40, 
                    style: {colors: ['#002152']} 
                },
                xaxis: {
                    categories: LEVEL_LABELS, 
                    labels: {formatter: formatPercentage},
                    max: 100 
                },
                tooltip: {
                    custom: function({series, seriesIndex, dataPointIndex, w}) {
                        const label = LEVEL_LABELS[dataPointIndex];
                        const count = LEVEL_COUNTS[dataPointIndex];
                        const percent = series[seriesIndex][dataPointIndex];
                        
                        return '<div class="arrow_box p-2 bg-white shadow-sm border rounded">' +
                          '<div><b>' + label + '</b></div>' +
                          '<div>Số lượng: ' + formatNumber(count) + ' nhân viên</div>' +
                          '<div>Tỷ lệ: ' + formatPercentage(percent) + '</div>' +
                          '</div>';
                    }
                },
                colors: ['#4fbe87'] 
            };
            const chartEducation = new ApexCharts(document.querySelector("#chart-education-level"), optionsEducation);
            chartEducation.render();
            apexChartsInstances.push(chartEducation); // LƯU VÀO MẢNG


            // 4. BIỂU ĐỒ DONUT (PHÂN BỔ PHÒNG BAN) 
           // 4. BIỂU ĐỒ BAR NGANG (PHÂN BỔ PHÒNG BAN) - Horizontal Bar Chart
				var optionsDept = {
					series: [{
						name: 'Số lượng Nhân viên',
						data: DEPT_SERIES // Sử dụng mảng số lượng nhân viên
					}],
					chart: {
						type: 'bar', // Đổi loại chart từ 'donut' sang 'bar'
						height: 350, 
						toolbar: {
							show: false
						}
					},
					plotOptions: {
						bar: {
							horizontal: true, // ✅ QUAN TRỌNG: Thiết lập biểu đồ cột ngang
							borderRadius: 4,
							distributed: true // Giữ màu sắc khác nhau cho mỗi cột
						}
					},
					dataLabels: {
						enabled: true,
						formatter: function (val) {
							return val; // Hiển thị số lượng nhân viên trên cột
						},
						style: {
							fontSize: '13px',
							fontWeight: 'bold',
							colors: ['#000']
						},
						offsetX: 10
					},
					xaxis: {
						categories: DEPT_LABELS, // Tên phòng ban cho trục Y (khi horizontal: true)
						title: {
							text: 'Số lượng Nhân viên' // Tiêu đề trục X
						}
					},
					colors: ['#435ebe', '#002152', '#4fbe87', '#eaca4a', '#f3616d', '#56b6f7'], // Giữ nguyên mảng màu cũ
					grid: {
						show: false
					},
					responsive: [{
						breakpoint: 480,
						options: {
							chart: {
								width: '100%'
							},
							legend: {
								position: 'bottom' // Chuyển Legend xuống dưới (hoặc có thể bỏ nếu không cần)
							}
						}
					}]
				};

				const chartDepartment = new ApexCharts(document.querySelector("#chart-department-distribution"), optionsDept);
				chartDepartment.render();
				apexChartsInstances.push(chartDepartment); // LƯU VÀO MẢNG

            // 5. BIỂU ĐỒ DONUT (CƠ CẤU ĐỘ TUỔI)
            var optionsAge = {
                series: AGE_SERIES,
                chart: {type: 'donut', height: 200, toolbar: {show: false}},
                labels: AGE_LABELS,
                colors: ['#ffc107', '#20c997', '#fd7e14', '#dc3545'], // Vàng, Xanh lá, Cam, Đỏ
                responsive: [{breakpoint: 480, options: {chart: {width: '100%'}, legend: {position: 'bottom'}}}]
            };
            const chartAge = new ApexCharts(document.querySelector("#chart-age-distribution"), optionsAge);
            chartAge.render();
            apexChartsInstances.push(chartAge); // LƯU VÀO MẢNG
            
            // 6. BIỂU ĐỒ DONUT (PHÂN BỔ GIỚI TÍNH)
            var optionsGender = {
                series: GENDER_SERIES,
                chart: {type: 'donut', height: 200, toolbar: {show: false}},
                labels: GENDER_LABELS,
                legend: {show: true, position: 'bottom'},
                responsive: [{breakpoint: 480, options: {chart: {width: '100%'}, legend: {position: 'bottom'}}}]
            };
            const chartGender = new ApexCharts(document.querySelector("#chart-gender-distribution"), optionsGender);
            chartGender.render();
            apexChartsInstances.push(chartGender); // LƯU VÀO MẢNG

            // 7. BIỂU ĐỒ DONUT (HÔN NHÂN)
            var optionsMarriage = {
                series: MARRIAGE_SERIES,
                chart: {type: 'donut', height: 200, toolbar: {show: false}},
                labels: MARRIAGE_LABELS,
                legend: {show: true, position: 'bottom'},
                responsive: [{breakpoint: 480, options: {chart: {width: '100%'}, legend: {position: 'bottom'}}}]
            };
            const chartMarriage = new ApexCharts(document.querySelector("#chart-marriage-status"), optionsMarriage);
            chartMarriage.render();
            apexChartsInstances.push(chartMarriage); // LƯU VÀO MẢNG
        }

        // --- BỘ XỬ LÝ SỰ KIỆN RESIZE MÀN HÌNH (FIX LỖI 1) ---
        const handleResize = debounce(() => {
            initializeAllCharts(); // Gọi hàm vẽ lại
        }, 250); // Chờ 250ms sau khi dừng resize mới vẽ lại

        // KHỞI TẠO LẦN ĐẦU KHI TẢI TRANG
        document.addEventListener('DOMContentLoaded', function() {
            initializeAllCharts();
        });
        
        // LẮNG NGHE SỰ KIỆN RESIZE
        window.addEventListener('resize', handleResize);
        // ----------------------------------------------------------------------------------

    </script>