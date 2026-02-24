<?php
// models/KhenThuongKyLuat.php

class KhenThuongKyLuat {
    private $conn;
    private $table = "khenthuong_kyluat";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả bản ghi, kèm tên NV và 1 vài thông tin NV (dùng cho bảng + modal)
    public function getAll($ck_khenthuong = null) {
        try {
            $where = "";
            if ($ck_khenthuong !== null) {
                $where = " WHERE kt.ck_khenthuong = :ck ";
            }

            $sql = "SELECT 
                        kt.*,
                        nv.hoten AS nhanvien_name,
                        nv.anhdaidien,
                        nv.sodt,
                        nv.email,
                        nv.gtinh,
                        cv.tencv      AS chuc_vu,
                        pb.ten_bp     AS phong_ban,
						CONCAT(tk_tao.ho, ' ', tk_tao.ten) AS nguoitao
                    FROM {$this->table} kt
                    LEFT JOIN nhan_vien nv ON kt.id_nv = nv.id
                    LEFT JOIN chuc_vu cv ON nv.id_chucvu = cv.id
                    LEFT JOIN phong_ban pb ON nv.id_phongban = pb.id
					LEFT JOIN tai_khoan tk_tao ON kt.id_nguoitao = tk_tao.id 
                    $where
                    ORDER BY kt.ngaytao DESC, kt.id DESC";
            $stmt = $this->conn->prepare($sql);
            if ($ck_khenthuong !== null) {
                $stmt->bindValue(':ck', (int)$ck_khenthuong, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            error_log("KhenThuongKyLuat::getAll error: " . $e->getMessage());
            return false;
        }
    }

    // Lấy 1 bản ghi theo id
    public function getById($id) {
        try {
            $sql = "SELECT 
                        kt.*,
                        nv.hoten AS nhanvien_name,
                        nv.anhdaidien,
                        nv.sodt,
                        nv.email,
                        nv.gtinh,
                        cv.tencv      AS chuc_vu,
                        pb.ten_bp     AS phong_ban
                    FROM {$this->table} kt
                    LEFT JOIN nhan_vien nv ON kt.id_nv = nv.id
                    LEFT JOIN chuc_vu cv ON nv.id_chucvu = cv.id
                    LEFT JOIN phong_ban pb ON nv.id_phongban = pb.id
                    WHERE kt.id = :id
                    LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("KhenThuongKyLuat::getById error: " . $e->getMessage());
            return false;
        }
    }

    // Thêm mới: $data associative keys: ma_ktkl, ten_ktkl, id_nv, so_tien, hinh_thuc, ngayqd, noidung, ck_khenthuong, id_nguoitao
    public function add($data) {
        try {
            $sql = "INSERT INTO {$this->table}
                    (ma_ktkl, ten_ktkl, id_nv, so_tien, hinh_thuc, ngayqd, noidung, ck_khenthuong, id_nguoitao, ngaytao)
                    VALUES (:ma_ktkl, :ten_ktkl, :id_nv, :so_tien, :hinh_thuc, :ngayqd, :noidung, :ck_khenthuong, :id_nguoitao, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':ma_ktkl', $data['ma_ktkl'] ?? '', PDO::PARAM_STR);
            $stmt->bindValue(':ten_ktkl', $data['ten_ktkl'] ?? '', PDO::PARAM_STR);
            $stmt->bindValue(':id_nv', (int)($data['id_nv'] ?? 0), PDO::PARAM_INT);
            $stmt->bindValue(':so_tien', $data['so_tien'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':hinh_thuc', $data['hinh_thuc'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':ngayqd', $data['ngayqd'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':noidung', $data['noidung'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':ck_khenthuong', (int)($data['ck_khenthuong'] ?? 0), PDO::PARAM_INT);
            $stmt->bindValue(':id_nguoitao', (int)($data['id_nguoitao'] ?? 0), PDO::PARAM_INT);

            $ok = $stmt->execute();
            return ['success' => (bool)$ok];
        } catch (PDOException $e) {
            error_log("KhenThuongKyLuat::add error: " . $e->getMessage());
            return ['success' => false, 'error' => 'exception'];
        }
    }

    // Cập nhật: $id, $data (các key giống add)
    public function update($id, $data) {
        try {
            $sql = "UPDATE {$this->table} SET
                        ten_ktkl = :ten_ktkl,
                        id_nv = :id_nv,
                        so_tien = :so_tien,
                        hinh_thuc = :hinh_thuc,
                        ngayqd = :ngayqd,
                        noidung = :noidung,
                        ck_khenthuong = :ck_khenthuong
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':ten_ktkl', $data['ten_ktkl'] ?? '', PDO::PARAM_STR);
            $stmt->bindValue(':id_nv', (int)($data['id_nv'] ?? 0), PDO::PARAM_INT);
            $stmt->bindValue(':so_tien', $data['so_tien'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':hinh_thuc', $data['hinh_thuc'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':ngayqd', $data['ngayqd'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':noidung', $data['noidung'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':ck_khenthuong', (int)($data['ck_khenthuong'] ?? 0), PDO::PARAM_INT);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("KhenThuongKyLuat::update error: " . $e->getMessage());
            return false;
        }
    }

    // Xóa
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return ['success' => false, 'error' => 'constraint'];
            }
            error_log("KhenThuongKyLuat::delete error: " . $e->getMessage());
            return ['success' => false, 'error' => 'exception'];
        }
    }

	public function getKhenThuongKyLuatByNVAndMonth($id_nv, $ky_luong_db) {
		// $ky_luong_db có định dạng YYYY-MM-01
		$ngay_dau_thang = $ky_luong_db; 
		// Lấy ngày cuối tháng: ví dụ: 2025-10-31
		$ngay_cuoi_thang = date('Y-m-t', strtotime($ngay_dau_thang)); 
		
		$sql = "
			SELECT 
				*
			FROM khenthuong_kyluat ktkl
			WHERE ktkl.id_nv = :id_nv
			  -- Lọc theo Ngày quyết định (ngayqd) nằm trong tháng
			  AND ktkl.ngayqd >= :ngay_dau
			  AND ktkl.ngayqd <= :ngay_cuoi
			ORDER BY ktkl.ngayqd DESC";

		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(':id_nv', $id_nv, PDO::PARAM_INT);
		$stmt->bindParam(':ngay_dau', $ngay_dau_thang, PDO::PARAM_STR);
		$stmt->bindParam(':ngay_cuoi', $ngay_cuoi_thang, PDO::PARAM_STR);
		$stmt->execute();
		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
?>
