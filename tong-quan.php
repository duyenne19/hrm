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
        /* GENERAL DASHBOARD STYLES */
        /* ======================================= */
        body {
            background-color: #f2f7ff; /* Light blue-grey background for depth */
            font-family: 'Nunito', sans-serif;
        }
        
        .page-content {
            padding: 2rem;
        }

        /* Tittle & Subtitle */
        .page-heading h3 {
            font-weight: 800;
            color: #2c3e50;
            letter-spacing: -0.5px;
        }
        
        .text-muted {
            color: #8c9097 !important;
        }

        /* GLOBAL CARD STYLES */
        .card {
            border: none;
            border-radius: 20px; /* Consistent rounded corners */
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03); /* Soft shadow */
            background: #fff;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            margin-bottom: 24px;
            overflow: hidden; /* Clips content to rounded corners */
        }
        
        .card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            transform: translateY(-4px);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.03);
            padding: 1.5rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header.text-center {
            justify-content: center;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #4b5563;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.7px;
        }

        .card-body {
            padding: 1.75rem;
        }

		/* ======================================= */
		/* CẤU TRÚC INFO CARDS (CHỈNH MỚI + ĐẸP) */
		/* ======================================= */

		/* Hàng chứa card */
		.info-card-row {
			display: grid;
            /* Tự động chia thành 6 cột đều nhau (vì có 6 card) */
			grid-template-columns: repeat(6, 1fr); 
			gap: 20px;
            margin-bottom: 30px;
            overflow-x: auto; /* Cho phép cuộn ngang nếu màn hình quá nhỏ */
            padding-bottom: 10px; /* Khoảng đệm cho thanh cuộn */
		}
        
        /* Responsive: Dưới 1200px (Laptop nhỏ) thì chuyển về dạng cuộn hoặc 3 hàng */
        @media (max-width: 1400px) {
            .info-card-row {
                grid-template-columns: repeat(3, 1fr); /* 2 dòng */
            }
        }
        
        @media (max-width: 991px) {
            .info-card-row {
                grid-template-columns: repeat(2, 1fr); /* 3 dòng */
            }
        }
        
        @media (max-width: 576px) {
             .info-card-row {
                grid-template-columns: repeat(1, 1fr); /* 1 dòng dọc */
            }
        }


		/* CARD CON */
		.info-card-col .card {
			height: 100%;
            margin-bottom: 0; 
            border-radius: 12px; /* Bo tròn nhỏ lại xíu */
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.02); 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04); 
            background: #ffffff;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
		}

        .info-card-col .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        }
        
        .info-card-col .card-body {
            display: flex;
            align-items: center;
            padding: 1rem 1rem; 
        }

        .info-card-content {
            margin-left: 1rem; /* Tăng khoảng cách icon và text lên để thoáng hơn */
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: right; /* Đẩy chữ sang phải để tách biệt với Icon bên trái */
        }

        .info-card-title {
            color: #8c9097; 
            font-size: 0.75rem; 
            font-weight: 600;
            text-transform: uppercase; 
            letter-spacing: 0.3px;
            margin-bottom: 0.15rem;
        }

        .info-card-value {
            color: #2c3e50;
            font-size: 1.1rem; 
            font-weight: 700;
            line-height: 1;
        }

		/* ======================================= */
		/* ICON – GRADIENT & SHAPES */
		/* ======================================= */

		.stats-icon {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 42px; 
			height: 42px;
			border-radius: 10px; 
			font-size: 1.1rem; 
			color: #fff !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            flex-shrink: 0;
            /* Không cần chỉnh margin-right, dùng margin-left của content */
		}

		/* 1. Tổng Nhân viên */
		.stats-icon.light-blue {
			background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
		}

		/* 2. Tổng Phòng Ban */
		.stats-icon.lavender {
			background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
            box-shadow: 0 4px 15px rgba(161, 140, 209, 0.4);
		}

		/* 3. Tổng Chức Vụ */
		.stats-icon.lime-green {
			background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            box-shadow: 0 4px 15px rgba(67, 233, 123, 0.4);
		}

		/* 4. Lương TB */
		.stats-icon.light-orange {
			background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            box-shadow: 0 4px 15px rgba(250, 112, 154, 0.4);
		}

		/* 5. Công tác / Sắp đi */
		.stats-icon.deep-blue {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
		}

		/* 6. Khen thưởng */
		.stats-icon.vibrant-green {
			background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%);
            box-shadow: 0 4px 15px rgba(11, 163, 96, 0.4);
		}

		/* 7. Kỷ luật */
		.stats-icon.soft-red {
			background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%); /* Slightly different red */
             background: linear-gradient(135deg, #ff5858 0%, #f09819 100%);
             box-shadow: 0 4px 15px rgba(255, 88, 88, 0.4);
		}

    </style>

   <div id="main-content-wrapper">
                
                <div class="page-content">
                    <div class="page-heading mb-4">
                        <h3>Tổng quan Nhân sự</h3>
                        <p class="text-subtitle text-muted">Báo cáo tổng hợp tình hình nhân sự công ty.</p>
                    </div>

                    <!-- 1. HÀNG INFO CARDS (7 CHỈ SỐ) -->
                    <!-- SỬ DỤNG CLASS info-card-row ĐÃ SỬA LỖI FLEX-WRAP -->
                    <section class="info-card-row"> 
                        
                        <!-- CARD 1: Tổng Nhân Viên -->
                        <div class="info-card-col">
                            <div class="card shadow border-0">
                                <div class="card-body">
                                    <div class="stats-icon light-blue">
                                        <i class="iconly-boldProfile"></i>
                                    </div>
                                    <div class="info-card-content">
                                        <h6 class="info-card-title">Tổng nhân viên</h6>
                                        <h6 class="info-card-value"><?= number_format($total_employees) ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- CARD 2: Tổng Phòng Ban -->
                        <div class="info-card-col">
                            <div class="card shadow border-0">
                                <div class="card-body">
                                    <div class="stats-icon lavender">
                                        <i class="iconly-boldDiscovery"></i>
                                    </div>
                                    <div class="info-card-content">
                                        <h6 class="info-card-title">Tổng phòng ban</h6>
                                        <h6 class="info-card-value"><?= number_format($total_departments) ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CARD 3: Tổng Chức Vụ -->
                        <div class="info-card-col">
                            <div class="card shadow border-0">
                                <div class="card-body">
                                    <div class="stats-icon lime-green">
                                        <i class="bi bi-person-badge-fill"></i>
                                    </div>
                                    <div class="info-card-content">
                                        <h6 class="info-card-title">Tổng Chức vụ</h6>
                                        <h6 class="info-card-value"><?= number_format($total_positions) ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CARD 5: Đang công tác / Sắp đi -->
                        <div class="info-card-col">
                            <div class="card shadow border-0">
                                <div class="card-body">
                                    <div class="stats-icon deep-blue">
                                        <i class="bi bi-send-fill"></i>
                                    </div>
                                    <div class="info-card-content">
                                        <h6 class="info-card-title">Công tác / Sắp đi</h6>
                                        <h6 class="info-card-value"><?= number_format($total_on_mission) ?> / <?= number_format($total_upcoming_mission) ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CARD 6: Khen thưởng Tháng này -->
                        <div class="info-card-col">
                            <div class="card shadow border-0">
                                <div class="card-body">
                                    <div class="stats-icon vibrant-green">
                                        <i class="bi bi-award-fill"></i>
                                    </div>
                                    <div class="info-card-content">
                                        <h6 class="info-card-title">Khen thưởng (<?= $previous_month ?>)</h6>
                                        <h6 class="info-card-value"><?= number_format($award_this_month) ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- CARD 7: Kỷ luật Tháng này -->
                        <div class="info-card-col">
                            <div class="card shadow border-0">
                                <div class="card-body">
                                    <div class="stats-icon soft-red">
                                        <i class="bi bi-x-octagon-fill"></i>
                                    </div>
                                    <div class="info-card-content">
                                        <h6 class="info-card-title">Kỷ luật (<?= $previous_month ?>)</h6>
                                        <h6 class="info-card-value"><?= number_format($discipline_this_month) ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- 2. HÀNG BIỂU ĐỒ CHÍNH -->
                    <section class="row mb-4">
                        
                        <!-- BIỂU ĐỒ BAR DỌC (LƯƠNG TB 6 THÁNG) -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card h-100 shadow border-0">
                                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                    <h5 class="card-title text-primary fw-bold mb-0">Lương trung bình 6 tháng</h5>
                                    <!-- Optional: Add a dropdown or action button here -->
                                </div>
                                <div class="card-body">
                                    <div id="chart-avg-salary-6m"></div>
                                </div>
                            </div>
                        </div>

                        <!-- BIỂU ĐỒ LƯƠNG CƠ BẢN THEO CHỨC VỤ -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card h-100 shadow border-0">
                                <div class="card-header bg-white border-bottom">
                                    <h5 class="card-title text-primary fw-bold mb-0">Lương cơ bản theo chức vụ</h5>
                                </div>
                                <div class="card-body">
                                    <div id="chart-base-salary-by-position"></div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 3. HÀNG BIỂU ĐỒ CHI TIẾT -->
                    <section class="row mb-4">
                        
                        <!-- BIỂU ĐỒ BAR NGANG (TỶ LỆ TRÌNH ĐỘ HỌC VẤN) -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card h-100 shadow border-0">
                                <div class="card-header bg-white border-bottom">  
                                    <h5 class="card-title text-primary fw-bold mb-0">Cơ cấu trình độ nhân viên</h5>                                
                                </div>
                                <div class="card-body">
									<div id="chart-education-level"></div>
								</div>
                            </div>
                        </div>
                        
                        <!-- BIỂU ĐỒ PHÂN BỔ PHÒNG BAN -->
						<div class="col-lg-6 col-md-12">
								<div class="card h-100 shadow border-0">
									<div class="card-header bg-white border-bottom">
                                        <h5 class="card-title text-primary fw-bold mb-0">Nhân viên theo phòng ban</h5>
                                    </div>
									<div class="card-body">
										<div id="chart-department-distribution"></div>									
									</div>
								</div>
						</div>
                    </section>

                    <!-- 4. HÀNG BIỂU ĐỒ TRÒN (ĐỘ TUỔI, GIỚI TÍNH, HÔN NHÂN) -->
                    <section class="row">
                        <div class="col-12">
                            <div class="row">
                                <!-- Độ Tuổi -->
                                <div class="col-md-4">
                                    <div class="card h-100 shadow border-0">
                                        <div class="card-header bg-white border-bottom text-center">
                                            <h5 class="card-title text-primary fw-bold mb-0">Độ tuổi</h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="chart-age-distribution"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Giới tính -->
                                <div class="col-md-4">
                                    <div class="card h-100 shadow border-0">
                                        <div class="card-header bg-white border-bottom text-center">
                                            <h5 class="card-title text-primary fw-bold mb-0">Giới tính</h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="chart-gender-distribution"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Hôn nhân -->
                                <div class="col-md-4">
                                    <div class="card h-100 shadow border-0">
                                        <div class="card-header bg-white border-bottom text-center">
                                            <h5 class="card-title text-primary fw-bold mb-0">Hôn nhân</h5>
                                        </div>
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

            // Common Options
            const fontFamily = "'Nunito', 'Segoe UI', Arial, sans-serif";
            const commonChartOptions = {
                fontFamily: fontFamily,
                toolbar: { show: false },
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            };

            // --- BIỂU ĐỒ CHÍNH ---

            // 1. BIỂU ĐỒ BAR DỌC (LƯƠNG TB 6 THÁNG)
            var optionsAvgSalary6M = {
                ...commonChartOptions,
                series: [{
                    name: "Lương Trung Bình",
                    data: AVG_SALARY_6M
                }],
                chart: {
                    height: 350,
                    type: 'bar', // Cột đứng
                    zoom: {enabled: false},
                    toolbar: {show: false},
                    fontFamily: fontFamily
                },
                plotOptions: {
                    bar: {
                        horizontal: false, // Dạng cột đứng
                        columnWidth: '50%',
                        borderRadius: 6, // Bo góc làm dịu mắt
                        className: 'chart-bar-custom'
                    },
                },
                dataLabels: {enabled: false},
                xaxis: {
                    categories: SALARY_6M_MONTHS,
                    title: {text: 'Tháng'},
                    axisBorder: {show: false},
                    axisTicks: {show: false}
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            if (val >= 1000000) return (val / 1000000).toFixed(1) + "M";
                            return val;
                        }
                    }
                },
                tooltip: {
                    y: {formatter: function (val) {return formatCurrency(val);}}
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.9,
                        colorStops: [
                            { offset: 0, color: "#4facfe", opacity: 1 },
                            { offset: 100, color: "#00f2fe", opacity: 1 }
                        ]
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                }
            };
            const chartAvgSalary6M = new ApexCharts(document.querySelector("#chart-avg-salary-6m"), optionsAvgSalary6M);
            chartAvgSalary6M.render();
            apexChartsInstances.push(chartAvgSalary6M); 
            
            // 2. BIỂU ĐỒ BAR NGANG (LƯƠNG CƠ BẢN THEO CHỨC VỤ)
            var optionsBaseSalary = {
                ...commonChartOptions,
                series: [{name: 'Lương Cơ Bản (TB)', data: BASE_SALARY_DATA}],
                chart: {type: 'bar', height: 350, toolbar: {show: false}, fontFamily: fontFamily},
                plotOptions: {
                    bar: {
                        horizontal: true, 
                        barHeight: '60%',
                        borderRadius: 4,
                        distributed: true // Màu sắc khác nhau
                    }
                },
                dataLabels: {
                    enabled: true, 
                    formatter: function(val) {
                        if (val >= 1000000) return (val / 1000000).toFixed(1) + "M";
                        return val;
                    },
                    textAnchor: 'start',
                    style: {colors: ['#fff']}, 
                    offsetX: 0,
                },
                xaxis: {
                    categories: BASE_SALARY_LABELS, 
                    labels: {formatter: function(val) { return (val / 1000000).toFixed(0) + "M"; }}
                },
                tooltip: {y: {formatter: formatCurrency}},
                colors: ['#4facfe', '#00f2fe', '#a18cd1', '#fbc2eb', '#43e97b'], // Gradient-like palette
                legend: { show: false }
            };
            const chartBaseSalary = new ApexCharts(document.querySelector("#chart-base-salary-by-position"), optionsBaseSalary);
            chartBaseSalary.render();
            apexChartsInstances.push(chartBaseSalary); 
            
            // --- BIỂU ĐỒ CHI TIẾT ---

            // 3. BIỂU ĐỒ BAR NGANG (TRÌNH ĐỘ HỌC VẤN)
            var optionsEducation = {
                ...commonChartOptions,
                series: [{name: 'Tỉ lệ (%)', data: LEVEL_PERCENTAGES}],
                chart: {type: 'bar', height: 350, toolbar: {show: false}, fontFamily: fontFamily},
                plotOptions: {
                    bar: {
                        horizontal: true, 
                        barHeight: '50%',
                        borderRadius: 4,
                    }
                },
                dataLabels: {
                    enabled: true, 
                    formatter: function (val, opts) {
                        const index = opts.dataPointIndex;
                        const count = LEVEL_COUNTS[index];
                        return count + ' NV'; // Chỉ hiện số lượng cho gọn
                    },
                    style: {colors: ['#fff']},
                    offsetX: 0 
                },
                xaxis: {
                    categories: LEVEL_LABELS, 
                    max: 100,
                    labels: { formatter: function(val) { return val + "%" } }
                },
                tooltip: {
                    custom: function({series, seriesIndex, dataPointIndex, w}) {
                        const label = LEVEL_LABELS[dataPointIndex];
                        const count = LEVEL_COUNTS[dataPointIndex];
                        const percent = series[seriesIndex][dataPointIndex];
                        return '<div class="p-2 border" style="background: #fff; color: #333">' +
                          '<b>' + label + '</b><br/>' +
                          'Số lượng: ' + formatNumber(count) + '<br/>' +
                          'Tỷ lệ: ' + percent + '%' +
                          '</div>';
                    }
                },
                colors: ['#43e97b'] 
            };
            const chartEducation = new ApexCharts(document.querySelector("#chart-education-level"), optionsEducation);
            chartEducation.render();
            apexChartsInstances.push(chartEducation);


            // 4. BIỂU ĐỒ BAR NGANG (PHÂN BỔ PHÒNG BAN)
            var optionsDept = {
                ...commonChartOptions,
                series: [{
                    name: 'Nhân viên',
                    data: DEPT_SERIES 
                }],
                chart: {
                    type: 'bar', 
                    height: 350, 
                    toolbar: {show: false},
                    fontFamily: fontFamily
                },
                plotOptions: {
                    bar: {
                        horizontal: true, 
                        borderRadius: 4,
                        distributed: true 
                    }
                },
                dataLabels: {
                    enabled: true,
                    textAnchor: 'start',
                    style: {colors: ['#fff']},
                    formatter: function (val) { return val; },
                    offsetX: 0
                },
                xaxis: {
                    categories: DEPT_LABELS, 
                },
                colors: ['#667eea', '#764ba2', '#a18cd1', '#fbc2eb', '#4facfe', '#00f2fe', '#43e97b', '#38f9d7'], 
                legend: { show: false },
                grid: { show: false }
            };

            const chartDepartment = new ApexCharts(document.querySelector("#chart-department-distribution"), optionsDept);
            chartDepartment.render();
            apexChartsInstances.push(chartDepartment); 

            // 5. BIỂU ĐỒ DONUT (CƠ CẤU ĐỘ TUỔI)
            var optionsAge = {
                ...commonChartOptions,
                series: AGE_SERIES,
                chart: {type: 'donut', height: 280, toolbar: {show: false}, fontFamily: fontFamily},
                labels: AGE_LABELS,
                colors: ['#4facfe', '#00f2fe', '#a18cd1', '#ff9a9e'],
                legend: { position: 'bottom' },
                dataLabels: { enabled: false },
                plotOptions: { 
                    pie: { 
                        donut: { 
                            size: '65%', 
                            labels: { 
                                show: true, 
                                total: { 
                                    show: true, 
                                    label: 'Tổng', 
                                    color: '#333',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => {
                                            return a + b
                                        }, 0)
                                    }
                                } 
                            } 
                        } 
                    } 
                }
            };
            const chartAge = new ApexCharts(document.querySelector("#chart-age-distribution"), optionsAge);
            chartAge.render();
            apexChartsInstances.push(chartAge); 
            
            // 6. BIỂU ĐỒ DONUT (PHÂN BỔ GIỚI TÍNH)
            var optionsGender = {
                ...commonChartOptions,
                series: GENDER_SERIES,
                chart: {type: 'donut', height: 280, toolbar: {show: false}, fontFamily: fontFamily},
                labels: GENDER_LABELS,
                colors: ['#667eea', '#fbc2eb', '#43e97b'], 
                legend: { position: 'bottom' },
                dataLabels: { enabled: false },
                plotOptions: { 
                    pie: { 
                        donut: { 
                            size: '65%', 
                            labels: { 
                                show: true, 
                                total: { 
                                    show: true, 
                                    label: 'Tổng', 
                                    color: '#333',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => {
                                            return a + b
                                        }, 0)
                                    }
                                } 
                            } 
                        } 
                    } 
                }
            };
            const chartGender = new ApexCharts(document.querySelector("#chart-gender-distribution"), optionsGender);
            chartGender.render();
            apexChartsInstances.push(chartGender); 

            // 7. BIỂU ĐỒ DONUT (HÔN NHÂN)
            var optionsMarriage = {
                ...commonChartOptions,
                series: MARRIAGE_SERIES,
                chart: {type: 'donut', height: 280, toolbar: {show: false}, fontFamily: fontFamily},
                labels: MARRIAGE_LABELS,
                colors: ['#fa709a', '#fee140', '#00f2fe'], 
                legend: { position: 'bottom' },
                dataLabels: { enabled: false },
                plotOptions: { 
                    pie: { 
                        donut: { 
                            size: '65%', 
                            labels: { 
                                show: true, 
                                total: { 
                                    show: true, 
                                    label: 'Tổng', 
                                    color: '#333',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => {
                                            return a + b
                                        }, 0)
                                    }
                                } 
                            } 
                        } 
                    } 
                }
            };
            const chartMarriage = new ApexCharts(document.querySelector("#chart-marriage-status"), optionsMarriage);
            chartMarriage.render();
            apexChartsInstances.push(chartMarriage); 
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