<?php 
	// include file
	include('./layouts/header.php');
	include('./models/TrangChu.php');
	// BỎ QUA KIỂM TRA LOGIN ĐỂ CHẠY TEST GIAO DIỆN
	$tongquan = new BangCap($conn);
    // ************************************************************
	
    // ************************************************************
    
    // 1. Thẻ Info Card & Chỉ số chính
    $current_month = '11/2025';
    $previous_month = '10/2025';

    // --- A. Nhân sự & Tổ chức ---
    $total_employees = 1540; // Tổng nhân viên (đang làm việc)
    $total_departments = 15; // Tổng phòng ban
    $total_positions = 28;   // Tổng số chức vụ

    // --- B. Công tác ---
    $total_on_mission = 85; // Tổng số công đang công tác
    $total_upcoming_mission = 40; // Tổng số sắp đi công tác

    // --- C. Lương ---
    $avg_salary_previous_month = '15,200,000 đ'; // Trung bình lương tháng trước (10/2025)

    // --- D. Khen thưởng / Kỷ luật (Sử dụng dữ liệu tháng trước để tính chênh lệch) ---
    $award_this_month = 12; 
    $discipline_this_month = 2; 
    $award_previous_month = 15; 
    $discipline_previous_month = 3; 

    // Tính chênh lệch để hiển thị mũi tên (Up/Down)
    $award_diff = $award_this_month - $award_previous_month;
    $discipline_diff = $discipline_this_month - $discipline_previous_month;

    // Tính chênh lệch kỷ luật: giảm là tốt (mũi tên lên màu đỏ là xấu, mũi tên xuống màu xanh là tốt)
    $discipline_indicator = $discipline_diff < 0 ? 'diff-up' : ($discipline_diff > 0 ? 'diff-down' : 'diff-equal');
    $discipline_arrow = $discipline_diff < 0 ? '↓' : ($discipline_diff > 0 ? '↑' : '≈');


    // 2. Dữ liệu Biểu đồ
    $dept_labels = json_encode(['Phòng IT', 'Hành chính', 'Marketing', 'Kinh doanh', 'Kế toán', 'Sản xuất']);
    $dept_series = json_encode([350, 200, 410, 320, 160, 100]);
    
    $gender_labels = json_encode(['Nam', 'Nữ', 'Khác']);
    $gender_series = json_encode([780, 750, 10]);
    
    $marriage_labels = json_encode(['Độc thân', 'Đã kết hôn', 'Ly hôn/Khác']);
    $marriage_series = json_encode([900, 600, 40]);
    
    // DỮ LIỆU LƯƠNG 6 THÁNG
    $avg_salary_6m = json_encode([14500000, 15000000, 15200000, 15500000, 15300000, 16000000]); // Lương TB 6 tháng
    $salary_6m_months = json_encode(['T6/25', 'T7/25', 'T8/25', 'T9/25', 'T10/25', 'T11/25']);
    
    // DỮ LIỆU LƯƠNG CƠ BẢN THEO CHỨC VỤ
    $base_salary_labels = json_encode(['Quản lý cấp cao', 'Quản lý cấp trung', 'Trưởng nhóm', 'Nhân viên']);
    $base_salary_data = json_encode([35000000, 22000000, 16000000, 10000000]); // VND (Giả lập lương cơ bản)
    
    // DỮ LIỆU TRÌNH ĐỘ HỌC VẤN (SỐ LƯỢNG VÀ TỶ LỆ)
    $total_employees_float = (float)$total_employees;
    $level_counts = [924, 385, 154, 77]; 
    $level_labels = json_encode(['Đại học', 'Cao đẳng', 'Thạc sĩ', 'Trung học']);
    $level_percentages = array_map(function($count) use ($total_employees_float) {
        return round(($count / $total_employees_float) * 100);
    }, $level_counts);
    $level_percentages_json = json_encode($level_percentages); 
    $level_counts_json = json_encode($level_counts);

    // DỮ LIỆU PHÂN BỔ ĐỘ TUỔI (MỚI)
    $age_labels = json_encode(['Dưới 25 tuổi', '25 - 35 tuổi', '36 - 45 tuổi', 'Trên 45 tuổi']);
    $age_series = json_encode([200, 750, 490, 100]); // Tổng: 1540
?>
    <style>
        .footer-smart {
            margin-top: auto; 
            background: linear-gradient(90deg, #ffffff, #f8f9fa);
            font-size: 0.9rem;
            box-shadow: 0 -1px 8px rgba(0, 0, 0, 0.05);
        }
        
        /* Style cho chữ số chênh lệch */
        .diff-up { color: #4fbe87; font-size: 0.85em; font-weight: 600; }
        .diff-down { color: #f3616d; font-size: 0.85em; font-weight: 600; }
        .diff-equal { color: #adb5bd; font-size: 0.85em; font-weight: 600; }
        
        /* ********************************************************* */
        /* KHẮC PHỤC LỖI RESPONSIVE CHO HÀNG INFO CARDS (TỪ FILE GỐC) */
        /* ********************************************************* */
        .info-card-col {
            flex: 0 0 auto;
            width: 100%; /* Mặc định 100% trên màn hình siêu nhỏ */
            padding: 0.5rem; /* Thêm padding để thẻ không dính vào nhau */
        }
        
        /* Tablet/Small Desktop (Phù hợp với Bootstrap sm) */
        @media (min-width: 576px) {
            .info-card-col { 
                width: 50%; /* 2 thẻ/dòng */
            }
        }
        
        /* Medium Desktop (Phù hợp với Bootstrap md) */
        @media (min-width: 768px) {
            .info-card-col { 
                width: 33.3333%; /* 3 thẻ/dòng */
            }
        }

        /* Large Desktop (Phù hợp với Bootstrap lg) */
        @media (min-width: 992px) {
            .info-card-col { 
                width: 25%; /* 4 thẻ/dòng (Thay vì 7, để tránh quá nhỏ trên màn hình 1024px) */
            }
        }
        
        /* Extra Large Desktop (Từ 1400px trở lên) */
        @media (min-width: 1400px) {
            .info-card-col {
                width: 14.2857%; /* 7 thẻ/dòng */
            }
        }
        
        /* Đảm bảo hàng Info Card TỰ ĐỘNG XUỐNG DÒNG */
        .info-card-row {
            display: flex; /* Đảm bảo flex được kích hoạt */
            flex-wrap: wrap !important; /* Rất quan trọng: cho phép xuống dòng */
            overflow-x: hidden !important; /* Không cần thanh cuộn ngang nữa */
            padding-bottom: 0 !important;
        }


        /* ****************************************** */
        /* MÀU SẮC CHO INFO CARDS (TƯƠI MỚI) (TỪ FILE GỐC) */
        /* ****************************************** */
        .stats-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 1.5rem;
        }

        /* 1. Tổng Nhân Viên - Xanh Dương Nhạt */
        .stats-icon.light-blue {
            background-color: #e0f7fa; /* Light Cyan */
            color: #00bcd4; /* Cyan */
        }

        /* 2. Tổng Phòng Ban - Tím Lavender */
        .stats-icon.lavender {
            background-color: #f3e5f5; /* Light Purple */
            color: #ab47bc; /* Medium Purple */
        }
        
        /* 3. Tổng Chức vụ - Xanh Lá Mạ */
        .stats-icon.lime-green {
            background-color: #f1f8e9; /* Light Lime */
            color: #8bc34a; /* Lime Green */
        }

        /* 4. Lương TB Tháng Trước - Vàng Cam */
        .stats-icon.light-orange {
            background-color: #fff3e0; /* Light Orange */
            color: #ff9800; /* Orange */
        }

        /* 5. Công tác / Sắp đi - Xanh Biển */
        .stats-icon.deep-blue {
            background-color: #e3f2fd; /* Light Blue */
            color: #2196f3; /* Blue */
        }

        /* 6. Khen thưởng - Xanh Lá Cây Tươi */
        .stats-icon.vibrant-green {
            background-color: #e8f5e9; /* Light Green */
            color: #4caf50; /* Green */
        }
        
        /* 7. Kỷ luật - Hồng Đỏ Dịu */
        .stats-icon.soft-red {
            background-color: #fbe9e7; /* Light Red */
            color: #ff5722; /* Deep Orange */
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

                        <!-- CARD 4: Lương TB Tháng Trước -->
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
                                            <h6 class="text-muted font-semibold">Khen thưởng (<?= $current_month ?>)</h6>
                                            <h6 class="font-extrabold mb-0">
                                                <?= number_format($award_this_month) ?> 
                                                <span class="<?= $award_diff > 0 ? 'diff-up' : ($award_diff < 0 ? 'diff-down' : 'diff-equal') ?>">
                                                    (<?= $award_diff > 0 ? '↑' : ($award_diff < 0 ? '↓' : '≈') ?><?= abs($award_diff) ?>)
                                                </span>
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
                                            <h6 class="text-muted font-semibold">Kỷ luật (<?= $current_month ?>)</h6>
                                            <h6 class="font-extrabold mb-0">
                                                <?= number_format($discipline_this_month) ?> 
                                                <span class="<?= $discipline_indicator ?>">
                                                    (<?= $discipline_arrow ?><?= abs($discipline_diff) ?>)
                                                </span>
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
                                    <h4 class="card-title">Lương trung bình công ty 6 tháng gần nhất 💸 (Cột đứng)</h4>
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
                                    <h4 class="card-title">Lương cơ bản theo Chức vụ (TB) 💰 (Cột ngang)</h4>
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
                                <div class="card-header">
                                    <h4 class="card-title">Cơ cấu Trình độ Học vấn (SL và Tỷ lệ) 🎓</h4>
                                </div>
                                <div class="card-body">
                                    <div id="chart-education-level"></div>
                                </div>
                            </div>
                        </div>

                        <!-- PHÂN BỔ CÁC BIỂU ĐỒ NHỎ: PHÒNG BAN, ĐỘ TUỔI, GIỚI TÍNH, HÔN NHÂN -->
                        <div class="col-lg-6 col-md-12">
                            <div class="row">
                                <!-- Phân bổ Phòng Ban (Chiếm 50% hàng ngang trên màn hình vừa/lớn) -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header"><h4 class="card-title">Phân bổ theo Phòng ban 🏢</h4></div>
                                        <div class="card-body">
                                            <div id="chart-department-distribution"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Phân bổ Độ Tuổi (Chiếm 50% hàng ngang trên màn hình vừa/lớn) -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header"><h4 class="card-title">Cơ cấu Độ tuổi 🎂</h4></div>
                                        <div class="card-body">
                                            <div id="chart-age-distribution"></div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header"><h4 class="card-title">Giới tính 🚻</h4></div>
                                        <div class="card-body">
                                            <div id="chart-gender-distribution"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header"><h4 class="card-title">Cơ cấu Hôn nhân 💍</h4></div>
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
            var optionsDept = {
                series: DEPT_SERIES,
                chart: {type: 'donut', height: 350, toolbar: {show: false}},
                labels: DEPT_LABELS,
                colors: ['#435ebe', '#002152', '#4fbe87', '#eaca4a', '#f3616d', '#56b6f7'],
                responsive: [{breakpoint: 480, options: {chart: {width: '100%'}, legend: {position: 'bottom'}}}]
            };
            const chartDepartment = new ApexCharts(document.querySelector("#chart-department-distribution"), optionsDept);
            chartDepartment.render();
            apexChartsInstances.push(chartDepartment); // LƯU VÀO MẢNG

            // 5. BIỂU ĐỒ DONUT (CƠ CẤU ĐỘ TUỔI)
            var optionsAge = {
                series: AGE_SERIES,
                chart: {type: 'donut', height: 350, toolbar: {show: false}},
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