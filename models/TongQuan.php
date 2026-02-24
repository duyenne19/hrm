<?php
include(__DIR__ . '/../connection/config.php');

class TongQuan {
	
    private $conn;
	private $database;
    /**
     * Hàm khởi tạo: Thiết lập kết nối cơ sở dữ liệu.
     * @param PDO $db Kết nối CSDL.
     */
    public function __construct() {
        $this->database = new Database();
        $this->conn = $this->database->getConnection();
    }

    /**
     * Lấy dữ liệu tổng hợp cho trang chủ (Dashboard).
     * @return array Dữ liệu tổng hợp.
     */
    public function tongHop() {
        // Lấy ngày hiện tại
        $currentDate = date('Y-m-d');

        // Lấy tháng trước
        $currentMonthStart = date('Y-m-01');
        $prevMonthStart = date('Y-m-01', strtotime($currentMonthStart . ' -1 month'));
        $prevMonthEnd = date('Y-m-t', strtotime($prevMonthStart));

        $data = [];

        // 1. Tổng số nhân viên đang làm việc (trangthai = 1)
        $query_nv = "SELECT COUNT(id) AS tong_nv FROM nhan_vien WHERE trangthai = 1";
        $stmt_nv = $this->conn->prepare($query_nv);
        $stmt_nv->execute();
        $data['tong_nhan_vien'] = (int)$stmt_nv->fetch(PDO::FETCH_ASSOC)['tong_nv'];

        // 2. Tổng số phòng ban
        $query_pb = "SELECT COUNT(id) AS tong_pb FROM phong_ban";
        $stmt_pb = $this->conn->prepare($query_pb);
        $stmt_pb->execute();
        $data['tong_phong_ban'] = (int)$stmt_pb->fetch(PDO::FETCH_ASSOC)['tong_pb'];

        // 3. Tổng chức vụ
        $query_cv = "SELECT COUNT(id) AS tong_cv FROM chuc_vu";
        $stmt_cv = $this->conn->prepare($query_cv);
        $stmt_cv->execute();
        $data['tong_chuc_vu'] = (int)$stmt_cv->fetch(PDO::FETCH_ASSOC)['tong_cv'];
		// 4. Số lượng đang đi công tác và sắp đi công tác
        // Đang đi công tác: bdau_ctac <= ngày hiện tại <= kthuc_ctac
        $query_dang_ctac = "
            SELECT COUNT(id) AS so_luong_dang_ctac
            FROM cong_tac
            WHERE bdau_ctac <= :currentDate AND kthuc_ctac >= :currentDate
        ";
        $stmt_dang_ctac = $this->conn->prepare($query_dang_ctac);
        $stmt_dang_ctac->bindParam(':currentDate', $currentDate);
        $stmt_dang_ctac->execute();
        $data['dang_cong_tac'] = (int)$stmt_dang_ctac->fetch(PDO::FETCH_ASSOC)['so_luong_dang_ctac'];

        // Sắp đi công tác: ngày hiện tại < bdau_ctac
        $query_sap_ctac = "
            SELECT COUNT(id) AS so_luong_sap_ctac
            FROM cong_tac
            WHERE bdau_ctac > :currentDate
        ";
        $stmt_sap_ctac = $this->conn->prepare($query_sap_ctac);
        $stmt_sap_ctac->bindParam(':currentDate', $currentDate);
        $stmt_sap_ctac->execute();
        $data['sap_cong_tac'] = (int)$stmt_sap_ctac->fetch(PDO::FETCH_ASSOC)['so_luong_sap_ctac'];
        
        // Công tác đã kết thúc (ngày hiện tại > ngày kết thúc) sẽ không được tính.
        // 5. Trung bình lương của tháng trước (Lấy từ bảng luong)
        $query_luong_tb = "
            SELECT AVG(thuc_lanh) AS luong_trung_binh
            FROM luong
            WHERE ky_luong = :prevMonthStart
        ";
        $stmt_luong_tb = $this->conn->prepare($query_luong_tb);
        $stmt_luong_tb->bindParam(':prevMonthStart', $prevMonthStart);
        $stmt_luong_tb->execute();
        $data['luong_tb_thang_truoc'] = (float)$stmt_luong_tb->fetch(PDO::FETCH_ASSOC)['luong_trung_binh'];

        

        // 6. Số lượng khen thưởng, kỷ luật của tháng trước
        $query_ktkl = "
            SELECT
                SUM(CASE WHEN ck_khenthuong = 1 THEN 1 ELSE 0 END) AS so_luong_khenthuong,
                SUM(CASE WHEN ck_khenthuong = 0 THEN 1 ELSE 0 END) AS so_luong_kyluat
            FROM khenthuong_kyluat
            WHERE ngayqd BETWEEN :prevMonthStart AND :prevMonthEnd
        ";
        $stmt_ktkl = $this->conn->prepare($query_ktkl);
        $stmt_ktkl->bindParam(':prevMonthStart', $prevMonthStart);
        $stmt_ktkl->bindParam(':prevMonthEnd', $prevMonthEnd);
        $stmt_ktkl->execute();
        $ktkl_result = $stmt_ktkl->fetch(PDO::FETCH_ASSOC);
        $data['khen_thuong_thang_truoc'] = (int)$ktkl_result['so_luong_khenthuong'];
        $data['ky_luat_thang_truoc'] = (int)$ktkl_result['so_luong_kyluat'];

        return $data;
    }

    // Các hàm khác (luong_trung_binh_6_thang, luong_chuc_vu, co_cau_trinh_do, co_cau_phong_ban, co_cau_do_tuoi, co_cau_gioi_tinh, co_cau_hon_nhan) giữ nguyên như đã cung cấp ở câu trả lời trước.
    // ...
    public function luong_trung_binh_6_thang() {
		$result = [];
		$currentMonth = date('Y-m-01'); // Bắt đầu từ tháng hiện tại (ví dụ: 2025-11-01)

		// Lấy dữ liệu 6 tháng gần nhất (tính từ tháng 11/2025 trở về 6 tháng)
		for ($i = 1; $i <= 6; $i++) {
			// Lấy ngày bắt đầu của tháng trước đó (ví dụ: i=1 là 2025-10-01)
			$monthStart = date('Y-m-01', strtotime($currentMonth . " -$i month")); 
			
			// Định dạng tháng/năm (ví dụ: 10/2025)
			$monthLabel = date('m/Y', strtotime($monthStart));

			// Dùng ky_luong để lọc theo tháng
			$query = "
				SELECT AVG(thuc_lanh) AS avg_luong
				FROM luong
				WHERE ky_luong = :monthStart
			";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':monthStart', $monthStart);
			$stmt->execute();
			$avg_luong = (float)$stmt->fetch(PDO::FETCH_ASSOC)['avg_luong'];

			$result[] = [
				// 💡 Tên khóa đã có đủ thông tin tháng/năm
				'thang_nam' => $monthLabel, 
				
				// Giá trị lương trung bình (dạng số)
				'trung_binh_luong_so' => $avg_luong,
				
				// Giá trị lương trung bình (đã format hàng nghìn bằng dấu ',')
				'trung_binh_luong_format' => number_format($avg_luong, 0, '.', ',')
			];
		}

		return array_reverse($result); // Trả về theo thứ tự tháng cũ đến mới
	}

    public function luong_chuc_vu() {
        $query = "
            SELECT tencv, luong_coban
            FROM chuc_vu
            ORDER BY luong_coban DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function co_cau_trinh_do() {
        $data = [];

        // 1. Tổng số nhân viên đang làm việc
        $query_tong = "SELECT COUNT(id) AS tong_nv FROM nhan_vien WHERE trangthai = 1";
        $stmt_tong = $this->conn->prepare($query_tong);
        $stmt_tong->execute();
        $data['tong_nv_dang_lam'] = (int)$stmt_tong->fetch(PDO::FETCH_ASSOC)['tong_nv'];

        // 2. Số nhân viên của mỗi loại trình độ
        $query_co_cau = "
            SELECT td.ten_td AS ten_trinh_do, COUNT(nv.id) AS so_luong
            FROM nhan_vien nv
            JOIN trinh_do td ON nv.id_trinhdo = td.id
            WHERE nv.trangthai = 1
            GROUP BY td.ten_td
            ORDER BY so_luong DESC
        ";
        $stmt_co_cau = $this->conn->prepare($query_co_cau);
        $stmt_co_cau->execute();
        $data['co_cau_trinh_do'] = $stmt_co_cau->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    public function co_cau_phong_ban() {
        $data = [];

        // 1. Tổng số nhân viên đang làm việc
        $query_tong = "SELECT COUNT(id) AS tong_nv FROM nhan_vien WHERE trangthai = 1";
        $stmt_tong = $this->conn->prepare($query_tong);
        $stmt_tong->execute();
        $data['tong_nv_dang_lam'] = (int)$stmt_tong->fetch(PDO::FETCH_ASSOC)['tong_nv'];

        // 2. Số nhân viên của mỗi loại phòng ban
        $query_co_cau = "
            SELECT pb.ten_bp AS ten_phong_ban, COUNT(nv.id) AS so_luong
            FROM nhan_vien nv
            JOIN phong_ban pb ON nv.id_phongban = pb.id
            WHERE nv.trangthai = 1
            GROUP BY pb.ten_bp
            ORDER BY so_luong DESC
        ";
        $stmt_co_cau = $this->conn->prepare($query_co_cau);
        $stmt_co_cau->execute();
        $data['co_cau_phong_ban'] = $stmt_co_cau->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    public function co_cau_do_tuoi() {
        $currentYear = date('Y');

        $query = "
            SELECT
                SUM(CASE WHEN (:currentYear - YEAR(ngsinh)) < 25 THEN 1 ELSE 0 END) AS duoi_25,
                SUM(CASE WHEN (:currentYear - YEAR(ngsinh)) BETWEEN 25 AND 35 THEN 1 ELSE 0 END) AS tu_25_den_35,
                SUM(CASE WHEN (:currentYear - YEAR(ngsinh)) BETWEEN 36 AND 45 THEN 1 ELSE 0 END) AS tu_36_den_45,
                SUM(CASE WHEN (:currentYear - YEAR(ngsinh)) > 45 THEN 1 ELSE 0 END) AS tren_45
            FROM nhan_vien
            WHERE trangthai = 1
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $data = [
            '<25' => (int)$result['duoi_25'],
            '25-35' => (int)$result['tu_25_den_35'],
            '36-45' => (int)$result['tu_36_den_45'],
            '>45' => (int)$result['tren_45']
        ];

        return $data;
    }

    public function co_cau_gioi_tinh() {
        $query = "
        SELECT gtinh, COUNT(id) AS so_luong
        FROM nhan_vien
        WHERE trangthai = 1
        GROUP BY gtinh
		";
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

		$data = [
			'Nam' => (int)($results['Nam'] ?? 0),
			'Nữ' => (int)($results['Nữ'] ?? 0),
			'Khác' => (int)($results['Khác'] ?? 0), // Đã bổ sung Khác
		];
		return $data;
    }

    public function co_cau_hon_nhan() {
        $query = "
            SELECT tthn.ten_hn AS tinh_trang, COUNT(nv.id) AS so_luong
            FROM nhan_vien nv
            JOIN tt_hon_nhan tthn ON nv.id_honnhan = tthn.id
            WHERE nv.trangthai = 1
            GROUP BY tthn.ten_hn
            ORDER BY so_luong DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}