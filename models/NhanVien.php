<?php
class NhanVien {
    private $conn;
    private $table = "nhan_vien";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ===============================
    // THÊM NHÂN VIÊN
    // ===============================
    public function add($data) {
    // Bắt đầu Transaction để đảm bảo tính toàn vẹn (INSERT và UPDATE phải thành công)
		$this->conn->beginTransaction();
		
		try {
			$sql = "INSERT INTO {$this->table} 
						(ma_nv, anhdaidien, hoten, sodt, email, gtinh, ngsinh, noisinh, id_honnhan,
						 so_cccd, noicap_cccd, ngaycap_cccd, id_quoctich, id_tongiao, id_dantoc,
						 hokhau, tamtru, id_loainv, id_trinhdo, id_chuyenmon,
						 id_phongban, id_chucvu, trangthai, id_nguoitao, ngaytao)
					VALUES
						(:ma_nv, :anhdaidien, :hoten, :sodt, :email, :gtinh, :ngsinh, :noisinh, :id_honnhan,
						 :so_cccd, :noicap_cccd, :ngaycap_cccd, :id_quoctich, :id_tongiao, :id_dantoc,
						 :hokhau, :tamtru, :id_loainv, :id_trinhdo, :id_chuyenmon,
						 :id_phongban, :id_chucvu, :trangthai, :id_nguoitao, NOW())";

			$stmt = $this->conn->prepare($sql);
			
			// Gán giá trị NULL cho ma_nv trong lần INSERT đầu tiên
			$stmt->bindValue(':ma_nv', null);

			// Gán các giá trị còn lại từ $data
			foreach ($data as $k => $v) {
				$stmt->bindValue(":$k", $v);
			}
			
			// Thực thi INSERT
			$stmt->execute();
			
			// Lấy ID vừa được tạo
			$new_id = $this->conn->lastInsertId();
			$new_ma_nv = '';
			if ($new_id <= 999) {
				$new_ma_nv = 'NV' . str_pad($new_id, 3, '0', STR_PAD_LEFT);
			} else {
				$new_ma_nv = 'NV' . $new_id;
			}

			$update_sql = "UPDATE {$this->table} SET ma_nv = :new_ma_nv WHERE id = :id";
			$update_stmt = $this->conn->prepare($update_sql);
			$update_stmt->bindParam(':new_ma_nv', $new_ma_nv);
			$update_stmt->bindParam(':id', $new_id);
			
			
			$update_stmt->execute();

			// Nếu cả hai lệnh thành công, COMMIT (Lưu) Transaction
			$this->conn->commit();
			return true; 

		} catch (Exception $e) {
			// Nếu có lỗi, ROLLBACK (Hủy bỏ) Transaction
			if ($this->conn->inTransaction()) {
				$this->conn->rollBack();
			}
			error_log("Lỗi thêm nhân viên: " . $e->getMessage());
			return false;
		}
	}

    // ===============================
    // CẬP NHẬT NHÂN VIÊN
    // ===============================
    public function update($id, $data) {
        try {
            $setPart = [];
            foreach ($data as $k => $v) {
                $setPart[] = "$k = :$k";
            }
            $sql = "UPDATE {$this->table} SET " . implode(", ", $setPart) . " WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            foreach ($data as $k => $v) {
                $stmt->bindValue(":$k", $v);
            }
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Lỗi cập nhật nhân viên: " . $e->getMessage());
            return false;
        }
    }

    // ===============================
    // XÓA NHÂN VIÊN
    // ===============================
   public function delete($id) {
		try {
			// Kiểm tra xem nhân viên có tồn tại không
			$stmt = $this->conn->prepare("SELECT id FROM {$this->table} WHERE id = :id");
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			if ($stmt->rowCount() === 0) {
				return ['success' => false, 'message' => 'Không tìm thấy nhân viên cần xóa.'];
			}

			// Thực hiện xóa
			$stmtDel = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
			$stmtDel->bindParam(':id', $id, PDO::PARAM_INT);
			$stmtDel->execute();

			// Nếu có lỗi ràng buộc FK, MySQL sẽ ném exception (PDOException)
			return ['success' => true, 'message' => 'Đã xóa nhân viên thành công.'];
		} 
		catch (PDOException $e) {
			// Kiểm tra lỗi ràng buộc khóa ngoại (SQLSTATE 23000)
			if ($e->getCode() == '23000') {
				return [
					'success' => false,
					'message' => 'Không thể xóa nhân viên này vì đang được liên kết với dữ liệu khác (VD: bảng lương, khen thưởng, kỷ luật, công tác, ...).'
				];
			}

			error_log("Lỗi xóa nhân viên: " . $e->getMessage());
			return ['success' => false, 'message' => 'Đã xảy ra lỗi khi xóa nhân viên.'];
		}
	}
	
	public function getAll() {
		$sql = "SELECT 
					nv.id,
					nv.ma_nv,
					nv.anhdaidien,
					nv.hoten,
					TRIM(SUBSTRING_INDEX(TRIM(nv.hoten), ' ', -1)) AS ten,
					nv.gtinh,
					DATE_FORMAT(nv.ngsinh, '%d-%m-%Y') AS ngsinh,
					nv.noisinh,
					nv.so_cccd,
					nv.trangthai,
					CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
				FROM nhan_vien nv
				LEFT JOIN tai_khoan tk ON nv.id_nguoitao = tk.id
				ORDER BY nv.trangthai DESC, ten ASC, nv.id DESC"; // Ưu tiên "Đang làm việc"
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		return $stmt;
	}
	public function getFilter_NV_PB($filter_id_pb) { 
		$sql = "SELECT 
                nv.id,
                nv.ma_nv,
                nv.anhdaidien,
                nv.hoten,
				TRIM(SUBSTRING_INDEX(TRIM(nv.hoten), ' ', -1)) AS ten,
                nv.sodt,
                nv.email,
                nv.gtinh,
                DATE_FORMAT(nv.ngsinh, '%d-%m-%Y') AS ngsinh,
                nv.noisinh,
                nv.so_cccd,
                DATE_FORMAT(nv.ngaycap_cccd, '%d-%m-%Y') AS ngaycap_cccd,
                nv.noicap_cccd,
                nv.hokhau,
                nv.tamtru,
                nv.trangthai,
                
                qt.ten_qt    AS quoc_tich, 
                tg.ten_tg    AS ton_giao,
                dt.ten_dt    AS dan_toc,
                hn.ten_hn    AS hon_nhan,  -- Đã dùng tên cột 'ten_hn' từ tt_hon_nhan
                td.ten_td    AS trinh_do,
                cm.ten_cm    AS chuyen_mon,
                pb.ten_bp    AS phong_ban, 
                cv.tencv     AS chuc_vu,
                lnv.ten_lnv  AS loai_nv
            FROM nhan_vien nv
            -- Thông tin liên quan
            LEFT JOIN quoc_tich qt ON nv.id_quoctich = qt.id
            LEFT JOIN ton_giao tg ON nv.id_tongiao = tg.id
            LEFT JOIN dan_toc dt ON nv.id_dantoc = dt.id
            LEFT JOIN tt_hon_nhan hn ON nv.id_honnhan = hn.id  -- Đã sửa: tt_hon_nhan
            LEFT JOIN trinh_do td ON nv.id_trinhdo = td.id
            LEFT JOIN chuyen_mon cm ON nv.id_chuyenmon = cm.id
            
            -- Thông tin tổ chức
            LEFT JOIN phong_ban pb ON nv.id_phongban = pb.id
            LEFT JOIN chuc_vu cv ON nv.id_chucvu = cv.id
            LEFT JOIN loai_nhanvien lnv ON nv.id_loainv = lnv.id";
		$params = [];
		
		if (!empty($filter_id_pb) && $filter_id_pb !== '0') {
			$sql .= " WHERE nv.id_phongban = ?"; 
			$params[] = $filter_id_pb;
		}
		
		$sql .= " ORDER BY nv.trangthai DESC, ten ASC, nv.id DESC";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute($params); 
		
		return $stmt;
	}
	public function getAllNV_danglam() {
		$sql = "SELECT
				nv.id,
				nv.ma_nv,
				nv.hoten,
				TRIM(SUBSTRING_INDEX(TRIM(nv.hoten), ' ', -1)) AS ten,
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
				nv.trangthai = 1 
			ORDER BY 
				ten ASC"; //
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		return $stmt;
	}
	public function getById($id) {
		try {
			$sql = "SELECT 
						nv.*,
						-- thông tin liên quan
						qt.ten_qt         AS quoc_tich,
						tg.ten_tg         AS ton_giao,
						dt.ten_dt         AS dan_toc,
						hn.ten_hn         AS hon_nhan,
						td.ten_td         AS trinh_do,
						cm.ten_cm         AS chuyen_mon,
						
						pb.ten_bp         AS phong_ban,
						cv.tencv          AS chuc_vu,
						lnv.ten_lnv       AS loai_nv,
						CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
					FROM {$this->table} nv
					LEFT JOIN quoc_tich qt      ON nv.id_quoctich  = qt.id
					LEFT JOIN ton_giao tg       ON nv.id_tongiao   = tg.id
					LEFT JOIN dan_toc dt        ON nv.id_dantoc    = dt.id
					LEFT JOIN tt_hon_nhan hn    ON nv.id_honnhan   = hn.id
					LEFT JOIN trinh_do td       ON nv.id_trinhdo   = td.id
					LEFT JOIN chuyen_mon cm     ON nv.id_chuyenmon = cm.id
					
					LEFT JOIN phong_ban pb      ON nv.id_phongban  = pb.id
					LEFT JOIN chuc_vu cv        ON nv.id_chucvu    = cv.id
					LEFT JOIN loai_nhanvien lnv ON nv.id_loainv    = lnv.id
					LEFT JOIN tai_khoan tk      ON nv.id_nguoitao  = tk.id
					WHERE nv.id = :id
					LIMIT 1";

			$stmt = $this->conn->prepare($sql);
			$stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (Exception $e) {
			error_log("Lỗi getById nhân viên: " . $e->getMessage());
			return false;
		}
	}
	public function tra_cuu($maNhanVien, $soCccd) {
		try {
			$sql = "SELECT 
						nv.*,
						qt.ten_qt AS quoc_tich,
						tg.ten_tg AS ton_giao,
						dt.ten_dt AS dan_toc,
						hn.ten_hn AS hon_nhan,
						td.ten_td AS trinh_do,
						cm.ten_cm AS chuyen_mon,
						
						pb.ten_bp AS phong_ban,
						cv.tencv AS chuc_vu,
						lnv.ten_lnv AS loai_nv,
						CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
					FROM {$this->table} nv
					LEFT JOIN quoc_tich qt ON nv.id_quoctich = qt.id
					LEFT JOIN ton_giao tg ON nv.id_tongiao = tg.id
					LEFT JOIN dan_toc dt ON nv.id_dantoc = dt.id
					LEFT JOIN tt_hon_nhan hn ON nv.id_honnhan = hn.id
					LEFT JOIN trinh_do td ON nv.id_trinhdo = td.id
					LEFT JOIN chuyen_mon cm ON nv.id_chuyenmon = cm.id
					
					LEFT JOIN phong_ban pb ON nv.id_phongban = pb.id
					LEFT JOIN chuc_vu cv ON nv.id_chucvu = cv.id
					LEFT JOIN loai_nhanvien lnv ON nv.id_loainv = lnv.id
					LEFT JOIN tai_khoan tk ON nv.id_nguoitao = tk.id
					WHERE nv.ma_nv = :ma_nv AND nv.so_cccd = :cccd
					LIMIT 1";

			$stmt = $this->conn->prepare($sql);
			
			
			$stmt->bindValue(':ma_nv', $maNhanVien, PDO::PARAM_STR);
			$stmt->bindValue(':cccd', $soCccd, PDO::PARAM_STR);
			$stmt->execute();
			
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (Exception $e) {				
			return false;
		}
	}
}
?>
