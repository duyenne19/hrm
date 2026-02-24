<?php
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
?>
<!-- SỬ DỤNG CLASS info-card-row ĐÃ SỬA LỖI FLEX-WRAP -->
                    <section class="row mb-4 info-card-row"> 
                        
                        <!-- CARD 1: Tổng Nhân Viên -->
                        <div class="info-card-col">
                            <div class="card">
                                <div class="card-body px-3 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4"><div class="stats-icon light-blue"><i class="iconly-boldProfile"></i></div></div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Tổng NV</h6>
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
                                            <h6 class="text-muted font-semibold">Tổng PB</h6>
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
                                            <h6 class="text-muted font-semibold">Lương TB (<?= $previous_month ?>)</h6>
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
                    