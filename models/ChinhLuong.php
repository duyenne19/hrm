<?php
class ChinhLuong {
    private $conn;
    private $table_name = "chinh_luong";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ⭐ 1.2 Lấy danh sách nhân viên đã được chỉnh lương (bản ghi gần nhất)
    public function getAllLatest() {
        // Bước 1: Tìm ID bản ghi chỉnh lương (cl) mới nhất cho mỗi nhân viên (id_nv)
        $subquery = "
            SELECT 
                MAX(cl.id) as max_id
            FROM 
                {$this->table_name} cl
            GROUP BY 
                cl.id_nv
        ";

        // Bước 2: Dùng kết quả (Max ID) để JOIN với bảng chinh_luong và các bảng liên quan
        $sql = "
            SELECT
                cl.id,
                cl.ma_chinhluong,
                cl.he_so_cu,
                cl.he_so_moi,
                cl.ngay_ky_ket,
                cl.ngay_hieu_luc,
				cl.so_quyet_dinh,
                nv.id AS id_nhanvien,
                nv.ma_nv,
                nv.hoten AS ten_nhanvien,
                pb.ten_bp AS phongban,
                cv.tencv AS chucvu,
                CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name,
                cl.ngaytao
            FROM 
                {$this->table_name} cl
            INNER JOIN 
                nhan_vien nv ON cl.id_nv = nv.id
            LEFT JOIN 
                phong_ban pb ON nv.id_phongban = pb.id
            LEFT JOIN 
                chuc_vu cv ON nv.id_chucvu = cv.id
            LEFT JOIN 
                tai_khoan tk ON cl.id_nguoitao = tk.id
            WHERE 
                cl.id IN ({$subquery})
            ORDER BY 
                cl.id DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // Lấy chi tiết theo ID bản ghi chỉnh lương
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table_name} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // ⭐ 1.1 Thêm mới (Create)
    public function add($ma_chinhluong, $id_nv, $he_so_cu, $he_so_moi, $so_quyet_dinh, $ngay_ky_ket, $ngay_hieu_luc, $id_nguoitao) {
        try {
            $sql = "INSERT INTO {$this->table_name} (ma_chinhluong, id_nv, he_so_cu, he_so_moi, so_quyet_dinh, ngay_ky_ket, ngay_hieu_luc, id_nguoitao, ngaytao)
                    VALUES (:ma_chinhluong, :id_nv, :he_so_cu, :he_so_moi, :so_quyet_dinh, :ngay_ky_ket, :ngay_hieu_luc, :id_nguoitao, NOW())";
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':ma_chinhluong', $ma_chinhluong);
            $stmt->bindParam(':id_nv', $id_nv, PDO::PARAM_INT);
            $stmt->bindParam(':he_so_cu', $he_so_cu);
            $stmt->bindParam(':he_so_moi', $he_so_moi);
            $stmt->bindParam(':so_quyet_dinh', $so_quyet_dinh);
            $stmt->bindParam(':ngay_ky_ket', $ngay_ky_ket);
            $stmt->bindParam(':ngay_hieu_luc', $ngay_hieu_luc);
            $stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi thêm chỉnh lương: " . $e->getMessage());
            return false;
        }
    }

    // ⭐ 1.1 Cập nhật (Update)
    public function update($id, $he_so_cu, $he_so_moi, $so_quyet_dinh, $ngay_ky_ket, $ngay_hieu_luc) {
        try {
            $sql = "UPDATE {$this->table_name} SET 
                    he_so_cu = :he_so_cu, 
                    he_so_moi = :he_so_moi, 
                    so_quyet_dinh = :so_quyet_dinh,
                    ngay_ky_ket = :ngay_ky_ket,
                    ngay_hieu_luc = :ngay_hieu_luc
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':he_so_cu', $he_so_cu);
            $stmt->bindParam(':he_so_moi', $he_so_moi);
            $stmt->bindParam(':so_quyet_dinh', $so_quyet_dinh);
            $stmt->bindParam(':ngay_ky_ket', $ngay_ky_ket);
            $stmt->bindParam(':ngay_hieu_luc', $ngay_hieu_luc);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật chỉnh lương: " . $e->getMessage());
            return false;
        }
    }

    // ⭐ 1.1 Xóa (Delete)
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM {$this->table_name} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("Lỗi xóa chỉnh lương: " . $e->getMessage());
            return ['success' => false, 'error' => 'other'];
        }
    }

    // Lấy hệ số lương mới nhất của một nhân viên (dùng để gợi ý HeSoCu cho lần chỉnh lương mới)
    public function getLatestHeSoMoi($id_nv) {
        $sql = "SELECT he_so_moi FROM {$this->table_name} WHERE id_nv = :id_nv ORDER BY ngaytao DESC, id DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_nv', $id_nv, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['he_so_moi'] : null;
    }
	// File: models/ChinhLuong.php (Thêm hàm này)

	// Lấy toàn bộ lịch sử chỉnh lương của một nhân viên
	public function getHistoryByNhanVienId($id_nv) {
		$sql = "
			SELECT
				cl.*,
				CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
			FROM 
				{$this->table_name} cl
			LEFT JOIN 
				tai_khoan tk ON cl.id_nguoitao = tk.id
			WHERE 
				cl.id_nv = :id_nv
			ORDER BY 
				cl.ngay_hieu_luc DESC, cl.id DESC
		";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(':id_nv', $id_nv, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	// Lấy thông tin chi tiết Nhân viên (dùng cho tiêu đề Modal)
	public function getNhanVienDetails($id_nv) {
		$sql = "
			SELECT
				nv.hoten,
				nv.ma_nv,
				pb.ten_bp AS phongban,
				cv.tencv AS chucvu
			FROM 
				nhan_vien nv
			LEFT JOIN 
				phong_ban pb ON nv.id_phongban = pb.id
			LEFT JOIN 
				chuc_vu cv ON nv.id_chucvu = cv.id
			WHERE 
				nv.id = :id_nv
			LIMIT 1
		";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(':id_nv', $id_nv, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	public function getHeSoMacDinh($id_nv) {
		$sql = "
			SELECT
				cv.he_so_luong
			FROM
				nhan_vien nv
			INNER JOIN
				chuc_vu cv ON nv.id_chucvu = cv.id
			WHERE
				nv.id = :id_nv
			LIMIT 1
		";
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(':id_nv', $id_nv, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		// Trả về he_so_luong nếu tìm thấy, ngược lại là null
		return $result ? $result['he_so_luong'] : null;
	}
}
?>