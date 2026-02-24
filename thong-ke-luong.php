<!-- 2. Lương -->
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