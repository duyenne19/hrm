<?php
// File: models/Luong.php (Đã đồng bộ và kiểm tra CSDL)

class Luong {
    private $conn;
    private $table_name = "luong";

    public function __construct($db) {
        $this->conn = $db;
        // Phải có file ChinhLuong.php
        include_once 'ChinhLuong.php'; 
    }
    
    private function getCurrentUserId() {
        // Lấy ID người dùng từ Session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 1; 
    }

    
    public function getAllNhanVien_PhongBan($selected_id_pb, $ky_luong_filter = null) {
    
		$sql = "SELECT
					nv.*,
					pb.ten_bp AS phongban,
					cv.tencv AS chucvu
				FROM
					nhan_vien nv
				JOIN
					phong_ban pb ON nv.id_phongban = pb.id
				JOIN
					chuc_vu cv ON nv.id_chucvu = cv.id";

		// 1. CHỈ THỰC HIỆN LEFT JOIN VÀ LỌC KHI CÓ KỲ LƯƠNG ĐƯỢC CHỈ ĐỊNH ($ky_luong_filter != null)
		if ($ky_luong_filter) {
			$sql .= " LEFT JOIN
						luong l ON nv.id = l.id_nv AND l.ky_luong = :ky_luong_filter_db";
		}

		$sql .= " WHERE
					nv.id_phongban = :id_pb
					AND nv.trangthai = 1";
		
		// 2. CHỈ THÊM ĐIỀU KIỆN LOẠI TRỪ (IS NULL) KHI Ở CHẾ ĐỘ THÊM MỚI
		if ($ky_luong_filter) {
			$sql .= " AND l.id IS NULL"; 
		}
		
		$sql .= " ORDER BY nv.hoten ASC";
			
		$stmt = $this->conn->prepare($sql);		
		
		$stmt->bindParam(':id_pb', $selected_id_pb, PDO::PARAM_INT);
		
		// 3. CHỈ BIND THAM SỐ KỲ LƯƠNG KHI CÓ GIÁ TRỊ
		if ($ky_luong_filter) {
			$stmt->bindParam(':ky_luong_filter_db', $ky_luong_filter, PDO::PARAM_STR); 
		}
		
		$stmt->execute();
		
		// Nếu ở chế độ Sửa, chỉ cần lấy nhân viên đang sửa để hiển thị.
		if ($ky_luong_filter === null && $stmt->rowCount() > 0) {
			// Nếu không có filter (chế độ Sửa) và có kết quả, chúng ta cần tìm nhân viên đang sửa trong luong-action.php
			// NHƯNG để hàm này giữ chức năng lọc, chúng ta sẽ để $arrNhanVien = [] cho luong-action.php xử lý.
			// Tuy nhiên, để tránh lỗi, ta chỉ cần sửa luong-action.php (xem bên dưới)
			
			// GIỮ NGUYÊN CODE TRUY VẤN
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

    /**
     * Lấy thành phần lương mặc định cho một nhân viên (LCB, HS Lương, HS PC)
     */
    public function getLcb_Hsl_Hspc($id_nv) {
        $sql_default = "
            SELECT 
                cv.luong_coban, 
                cv.he_so_luong AS he_so_mac_dinh,
                cv.he_so_phu_cap
            FROM nhan_vien nv
            JOIN chuc_vu cv ON nv.id_chucvu = cv.id
            WHERE nv.id = :id_nv
            LIMIT 1";
        
        $stmt_default = $this->conn->prepare($sql_default);
        $stmt_default->bindParam(':id_nv', $id_nv, PDO::PARAM_INT);
        $stmt_default->execute();
        $default_info = $stmt_default->fetch(PDO::FETCH_ASSOC);

        if (!$default_info) return null;

        $chinhluong_model = new ChinhLuong($this->conn);
        $he_so_moi_nhat = $chinhluong_model->getLatestHeSoMoi($id_nv);

        return [
            'luong_co_ban' => floatval($default_info['luong_coban']),
            'he_so_luong' => floatval($he_so_moi_nhat ?? $default_info['he_so_mac_dinh']),
            'he_so_phu_cap' => floatval($default_info['he_so_phu_cap']),
        ];
    }
    
    /**
     * Hàm tính toán Thực lãnh theo công thức mới của người dùng
     */
    private function tinhToan_ThucLanh($data) {
        $luongCoBan = floatval($data['luong_co_ban']);
        $heSoLuong = floatval($data['he_so_luong']);
        $heSoPhuCap = floatval($data['he_so_phu_cap']);
        $ngayCong = floatval($data['ngay_cong']); 
        $tamUng = intval($data['tam_ung']);
        
        $NGAY_CHUAN = 26;
        $MUC_GIAM_TRU = 11000000;
        
        // 1. $luong_tinhtoan = chuc_vu->luong_coban * hệ số lương mới nhất
        $luong_tinhtoan = $luongCoBan * $heSoLuong; 

        // 2. $phu_cap = $luong_coban × he_so_phu_cap
        $phu_cap = round($luongCoBan * $heSoPhuCap);

        // 3. $luong_gop = ($luong_tinhtoan + $phu_cap) * ( ngay_cong / 26)
        $luong_gop = ($luong_tinhtoan + $phu_cap) * ($ngayCong / $NGAY_CHUAN);
        $luong_gop = round($luong_gop);

        // 4, 5, 6. Các khoản bảo hiểm tính trên Lương Gộp
        $bhxh = round(0.08 * $luong_gop); 
        $bhyt = round(0.015 * $luong_gop); 
        $bhtn = round(0.01 * $luong_gop); 

        // 7. $thue_tncn
        $thue_tncn = 0;
        if ($luong_gop > $MUC_GIAM_TRU) {
            $thue_tncn = ($luong_gop - $MUC_GIAM_TRU) * 0.05;
        }
        $thue_tncn = round($thue_tncn); 
        
        // Tổng các khoản khấu trừ bắt buộc
        $totalDeduction = $bhxh + $bhyt + $bhtn + $thue_tncn;
        
        // 8. $thuc_lanh = Lương gộp - Tổng khấu trừ bắt buộc - Tạm ứng 
        $thucLanh = $luong_gop - $totalDeduction - $tamUng; 
        $thucLanh = round($thucLanh);
        
        $data['phu_cap'] = $phu_cap; 
        $data['luong_gop'] = $luong_gop;
        $data['bhxh'] = $bhxh;
        $data['bhyt'] = $bhyt;
        $data['bhtn'] = $bhtn;
        $data['thue_tncn'] = $thue_tncn;
        $data['thuc_lanh'] = $thucLanh;
        $data['khoan_tru'] = $totalDeduction + $tamUng; 
        
        // ⭐ ĐÃ THÊM: Lưu các giá trị gốc cho cột mới (để đảm bảo tính nhất quán)
        $data['luong_co_ban_goc'] = $luongCoBan;
        $data['he_so_luong_goc'] = $heSoLuong;
        $data['he_so_phu_cap_goc'] = $heSoPhuCap;

        return $data;
    }
    
    // --- CRUD: INSERT mới ---
    // Trong class model Luong
	public function addLuong($data) {
		$data = $this->tinhToan_ThucLanh($data);
		$id_nguoitao = $this->getCurrentUserId();

		$sql_insert = "INSERT INTO " . $this->table_name . " (
				ma_luong, id_nv, ky_luong, ngay_cong, tam_ung, 
				luong_co_ban_goc, he_so_luong_goc, he_so_phu_cap_goc,
				phu_cap, luong_gop, bhxh, bhyt, bhtn, thue_tncn, thuc_lanh,
				id_nguoitao, ngaytao, 
				id_nguoisua, ngaysua 
			) 
			VALUES (
				:ma_luong, :id_nv, :ky_luong, :ngay_cong, :tam_ung,
				:luong_co_ban_goc, :he_so_luong_goc, :he_so_phu_cap_goc,
				:phu_cap, :luong_gop, :bhxh, :bhyt, :bhtn, :thue_tncn, :thuc_lanh,
				:id_nguoitao, NOW(), 
				:id_nguoisua, NOW()
			)";
		
		$stmt = $this->conn->prepare($sql_insert);

		// Bind data (giữ nguyên các bind cũ)
		$stmt->bindParam(':ma_luong', $data['ma_luong']);
		$stmt->bindParam(':id_nv', $data['id_nv'], PDO::PARAM_INT);
		$stmt->bindParam(':ky_luong', $data['ky_luong']); 
		$stmt->bindParam(':ngay_cong', $data['ngay_cong']); 
		$stmt->bindParam(':tam_ung', $data['tam_ung'], PDO::PARAM_INT);
		
		$stmt->bindParam(':luong_co_ban_goc', $data['luong_co_ban_goc']);
		$stmt->bindParam(':he_so_luong_goc', $data['he_so_luong_goc']);
		$stmt->bindParam(':he_so_phu_cap_goc', $data['he_so_phu_cap_goc']);

		$stmt->bindParam(':phu_cap', $data['phu_cap'], PDO::PARAM_INT);
		$stmt->bindParam(':luong_gop', $data['luong_gop'], PDO::PARAM_INT);
		$stmt->bindParam(':bhxh', $data['bhxh'], PDO::PARAM_INT);
		$stmt->bindParam(':bhyt', $data['bhyt'], PDO::PARAM_INT);
		$stmt->bindParam(':bhtn', $data['bhtn'], PDO::PARAM_INT);
		$stmt->bindParam(':thue_tncn', $data['thue_tncn'], PDO::PARAM_INT);
		$stmt->bindParam(':thuc_lanh', $data['thuc_lanh'], PDO::PARAM_INT);
		
		// Bind cho id_nguoitao
		$stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);
		
		// ⭐ ĐÃ THÊM: Bind cho id_nguoisua với cùng giá trị id_nguoitao
		$stmt->bindParam(':id_nguoisua', $id_nguoitao, PDO::PARAM_INT); 
		
		try {
			return $stmt->execute();
		} catch (PDOException $e) {
			//echo $e->getMessage();
			//exit();
			return false;
		}
	}
    
    /**
     * Lấy danh sách lương trong khoảng thời gian (dùng cho bảng list)
     */
    public function getSalaryList($fromDate, $toDate, $id_phongban) {
    // 1. Thêm biến WHERE condition
		$where_conditions = "l.ky_luong BETWEEN :from_date AND :to_date";
		
		// Nếu có lọc theo phòng ban (id_phongban > 0), thêm điều kiện vào WHERE
		if ($id_phongban > 0) {
			$where_conditions .= " AND nv.id_phongban = :id_phongban";
		}
		
		$sql = "
			SELECT 
				l.*,
				nv.ma_nv, nv.hoten, 
				cv.tencv AS chucvu,
				pb.ten_bp AS phongban,
				cv.luong_coban
			FROM " . $this->table_name . " l
			JOIN nhan_vien nv ON l.id_nv = nv.id
			LEFT JOIN chuc_vu cv ON nv.id_chucvu = cv.id
			LEFT JOIN phong_ban pb ON nv.id_phongban = pb.id  -- ⭐ THÊM JOIN BẢNG PHÒNG BAN ⭐
			WHERE " . $where_conditions . "  -- ⭐ SỬ DỤNG ĐIỀU KIỆN WHERE ĐÃ XỬ LÝ LỌC ⭐
			ORDER BY l.ky_luong DESC, l.id DESC";
		
		$stmt = $this->conn->prepare($sql);
		
		$stmt->bindParam(':from_date', $fromDate);
		$stmt->bindParam(':to_date', $toDate);
		
		// 2. Bind Tham số lọc (Chỉ khi có id_phongban)
		if ($id_phongban > 0) {
			$stmt->bindParam(':id_phongban', $id_phongban, PDO::PARAM_INT);
		}
		
		$stmt->execute();
		
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		foreach ($results as &$row) {
			// Cập nhật tính toán Khoản Trừ (nếu cần)
			$totalDeduction = $row['bhxh'] + $row['bhyt'] + $row['bhtn'] + $row['thue_tncn'];
			$row['khoan_tru'] = $totalDeduction + $row['tam_ung'];
		}
		return $results;
	}
    
    /**
     * Lấy chi tiết lương và thông tin nhân viên (dùng cho Modal Xem)
     */
    public function getLuongNhanVien($id_luong) {
        $sql = "
            SELECT 
                l.*,
                nv.ma_nv, nv.hoten, nv.sodt, nv.email,nv.id_phongban,
                pb.ten_bp AS phongban, 
                cv.tencv AS chucvu, 
                tk_tao.ho AS ho_tao, tk_tao.ten AS ten_tao,
                tk_sua.ho AS ho_sua, tk_sua.ten AS ten_sua
            FROM " . $this->table_name . " l
            JOIN nhan_vien nv ON l.id_nv = nv.id
            LEFT JOIN phong_ban pb ON nv.id_phongban = pb.id
            LEFT JOIN chuc_vu cv ON nv.id_chucvu = cv.id
            LEFT JOIN tai_khoan tk_tao ON l.id_nguoitao = tk_tao.id
            LEFT JOIN tai_khoan tk_sua ON l.id_nguoisua = tk_sua.id
            WHERE l.id = :id_luong
            LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_luong', $id_luong, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $result['nguoitao_name'] = $result['ho_tao'] . ' ' . $result['ten_tao'];
            
            
            $result['nguoisua_name'] = $result['id_nguoisua'] ? ($result['ho_sua'] . ' ' . $result['ten_sua']) : 'N/A';

            unset($result['ho_tao'], $result['ten_tao'], $result['ho_sua'], $result['ten_sua']);
        }
        
        return $result;
    }
    
    public function deleteLuong($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Có thể log lỗi $e->getMessage() ở đây
            return false;
        }
    }
    
    public function updateLuong($id_luong, $data) {
        // 1. Tính toán lại toàn bộ các thành phần lương dựa trên dữ liệu mới
        $data = $this->tinhToan_ThucLanh($data);
        $id_nguoisua = $this->getCurrentUserId(); // ⭐ ĐÃ THÊM: Lấy ID người sửa
        
        // ⭐ ĐÃ SỬA: Thêm các cột *_goc, id_nguoisua và ngaysua vào SQL UPDATE
        $sql_update = "
            UPDATE " . $this->table_name . "
            SET
                
                ngay_cong = :ngay_cong,
                tam_ung = :tam_ung,
                luong_co_ban_goc = :luong_co_ban_goc,
                he_so_luong_goc = :he_so_luong_goc,
                he_so_phu_cap_goc = :he_so_phu_cap_goc,
                phu_cap = :phu_cap,
                luong_gop = :luong_gop,
                bhxh = :bhxh,
                bhyt = :bhyt,
                bhtn = :bhtn,
                thue_tncn = :thue_tncn,
                thuc_lanh = :thuc_lanh,
                id_nguoisua = :id_nguoisua,
                ngaysua = NOW()
            WHERE id = :id_luong";

        $stmt = $this->conn->prepare($sql_update);

        // 3. Bind Parameters
        $stmt->bindParam(':id_luong', $id_luong, PDO::PARAM_INT);
        
        $stmt->bindParam(':ngay_cong', $data['ngay_cong']); 
        $stmt->bindParam(':tam_ung', $data['tam_ung'], PDO::PARAM_INT);

        // ⭐ ĐÃ SỬA: Bind các giá trị gốc
        $stmt->bindParam(':luong_co_ban_goc', $data['luong_co_ban_goc']);
        $stmt->bindParam(':he_so_luong_goc', $data['he_so_luong_goc']);
        $stmt->bindParam(':he_so_phu_cap_goc', $data['he_so_phu_cap_goc']);

        // Bind Các Giá Trị Tính Toán
        $stmt->bindParam(':phu_cap', $data['phu_cap'], PDO::PARAM_INT);
        $stmt->bindParam(':luong_gop', $data['luong_gop'], PDO::PARAM_INT);
        $stmt->bindParam(':bhxh', $data['bhxh'], PDO::PARAM_INT);
        $stmt->bindParam(':bhyt', $data['bhyt'], PDO::PARAM_INT);
        $stmt->bindParam(':bhtn', $data['bhtn'], PDO::PARAM_INT);
        $stmt->bindParam(':thue_tncn', $data['thue_tncn'], PDO::PARAM_INT);
        $stmt->bindParam(':thuc_lanh', $data['thuc_lanh'], PDO::PARAM_INT);
        
        // ⭐ ĐÃ SỬA: Bind id_nguoisua
        $stmt->bindParam(':id_nguoisua', $id_nguoisua, PDO::PARAM_INT);

        // 4. Execute
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Có thể log lỗi $e->getMessage() ở đây
            return false;
        }
    }

	public function xemLuongNhanVien($id_luong) {
    // Truy vấn kết hợp nhiều bảng để lấy tất cả thông tin chi tiết cần thiết
    $query = "
        SELECT 
			l.*, 
			nv.hoten, nv.ma_nv, nv.sodt, nv.email, nv.gtinh,nv.anhdaidien,
			pb.ten_bp AS ten_pb,
			cv.tencv AS ten_cv,
			
			-- Thông tin NGƯỜI TẠO
			CONCAT(tk_tao.ho, ' ', tk_tao.ten) AS nguoitao_name,
			
			-- Thông tin NGƯỜI SỬA
			l.ngaysua,
			CONCAT(tk_sua.ho, ' ', tk_sua.ten) AS nguoisua_name 
		FROM 
			luong l
		LEFT JOIN 
			nhan_vien nv ON l.id_nv = nv.id
		LEFT JOIN
			phong_ban pb ON nv.id_phongban = pb.id
		LEFT JOIN
			chuc_vu cv ON nv.id_chucvu = cv.id
		LEFT JOIN
			tai_khoan tk_tao ON l.id_nguoitao = tk_tao.id 
		LEFT JOIN
			tai_khoan tk_sua ON l.id_nguoisua = tk_sua.id 
		WHERE 
            l.id = :id_luong
        LIMIT 0,1";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id_luong', $id_luong, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

	public function xemLuong_idVN_ky_luong($id_nv, $ky_luong) {
    $sql = "
        SELECT 
            l.*,
            nv.ma_nv, nv.hoten, nv.sodt, nv.email, nv.gtinh, nv.anhdaidien,
            pb.ten_bp AS phongban, 
            cv.tencv AS chucvu, 
            tk_tao.ho AS ho_tao, tk_tao.ten AS ten_tao,
            tk_sua.ho AS ho_sua, tk_sua.ten AS ten_sua
        FROM " . $this->table_name . " l
        JOIN nhan_vien nv ON l.id_nv = nv.id
        LEFT JOIN phong_ban pb ON nv.id_phongban = pb.id
        LEFT JOIN chuc_vu cv ON nv.id_chucvu = cv.id
        LEFT JOIN tai_khoan tk_tao ON l.id_nguoitao = tk_tao.id
        LEFT JOIN tai_khoan tk_sua ON l.id_nguoisua = tk_sua.id
        WHERE l.id_nv = :id_nv AND l.ky_luong = :ky_luong
        LIMIT 1";

    $stmt = $this->conn->prepare($sql);
    
    // Liên kết các giá trị
    $stmt->bindParam(':id_nv', $id_nv, PDO::PARAM_INT);
    $stmt->bindParam(':ky_luong', $ky_luong, PDO::PARAM_STR); // Giả định ky_luong là chuỗi YYYY-MM-01
    
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // Xử lý tên người tạo/người sửa
        $result['nguoitao_name'] = ($result['ho_tao'] ?? '') . ' ' . ($result['ten_tao'] ?? '');
        $result['nguoisua_name'] = $result['id_nguoisua'] ? (($result['ho_sua'] ?? '') . ' ' . ($result['ten_sua'] ?? '')) : 'N/A';

        // Loại bỏ các cột không cần thiết
        unset($result['ho_tao'], $result['ten_tao'], $result['ho_sua'], $result['ten_sua']);
    }
    
    return $result;
}
}