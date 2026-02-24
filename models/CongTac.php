<?php
class CongTac {
    private $conn;
    private $table_name = "cong_tac";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ✅ Lấy tất cả
    public function getAll() {
        $sql = "SELECT 
                    ct.id,
                    ct.ma_ctac,
                    nv.hoten AS nhanvien_name,
                    ct.dd_ctac,
                    ct.bdau_ctac,
                    ct.kthuc_ctac,
                    ct.mucdich_ctac,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name,
                    ct.ngaytao
                FROM {$this->table_name} ct
                LEFT JOIN nhan_vien nv ON ct.id_nv = nv.id
                LEFT JOIN tai_khoan tk ON ct.id_nguoitao = tk.id
                ORDER BY ct.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // ✅ Lấy chi tiết
    public function getById($id) {
        $sql = "SELECT 
                    ct.*,
                    nv.hoten AS nhanvien_name,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM {$this->table_name} ct
                LEFT JOIN nhan_vien nv ON ct.id_nv = nv.id
                LEFT JOIN tai_khoan tk ON ct.id_nguoitao = tk.id
                WHERE ct.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Thêm mới
    public function add($data) {
        try {
            $sql = "INSERT INTO {$this->table_name} 
                    (ma_ctac, id_nv, bdau_ctac, kthuc_ctac, dd_ctac, mucdich_ctac, id_nguoitao, ngaytao)
                    VALUES (:ma_ctac, :id_nv, :bdau_ctac, :kthuc_ctac, :dd_ctac, :mucdich_ctac, :id_nguoitao, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ma_ctac', $data['ma_ctac']);
            $stmt->bindParam(':id_nv', $data['id_nv'], PDO::PARAM_INT);
            $stmt->bindParam(':bdau_ctac', $data['bdau_ctac']);
            $stmt->bindParam(':kthuc_ctac', $data['kthuc_ctac']);
            $stmt->bindParam(':dd_ctac', $data['dd_ctac']);
            $stmt->bindParam(':mucdich_ctac', $data['mucdich_ctac']);
            $stmt->bindParam(':id_nguoitao', $data['id_nguoitao'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("❌ Lỗi thêm nhóm công tác: " . $e->getMessage());
            return false;
        }
    }

    // ✅ Cập nhật
    public function update($id, $data) {
        try {
            $sql = "UPDATE {$this->table_name}
                    SET id_nv = :id_nv,
                        bdau_ctac = :bdau_ctac,
                        kthuc_ctac = :kthuc_ctac,
                        dd_ctac = :dd_ctac,
                        mucdich_ctac = :mucdich_ctac
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_nv', $data['id_nv'], PDO::PARAM_INT);
            $stmt->bindParam(':bdau_ctac', $data['bdau_ctac']);
            $stmt->bindParam(':kthuc_ctac', $data['kthuc_ctac']);
            $stmt->bindParam(':dd_ctac', $data['dd_ctac']);
            $stmt->bindParam(':mucdich_ctac', $data['mucdich_ctac']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("❌ Lỗi cập nhật nhóm công tác: " . $e->getMessage());
            return false;
        }
    }

    // ✅ Xóa
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM {$this->table_name} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return ['success' => false, 'error' => 'constraint'];
            }
            error_log("❌ Lỗi xóa nhóm công tác: " . $e->getMessage());
            return ['success' => false, 'error' => 'other'];
        }
    }
	// Kiểm tra lịch công tác không được phép trùng lặp, chống chéo lên nhau.
	public function kiem_tra_ngay($id_nv, $bdau_ctac, $kthuc_ctac, $id_congtac = null) {
		$sql = "SELECT COUNT(*) FROM {$this->table_name}
				WHERE id_nv = :id_nv
				AND (
					(bdau_ctac <= :new_end AND kthuc_ctac >= :new_start)
				)";
		
		// Nếu đang là thao tác Cập nhật, loại trừ chính công tác đang sửa
		if ($id_congtac !== null) {
			$sql .= " AND id != :id_congtac";
		}

		try {
			$stmt = $this->conn->prepare($sql);
			$stmt->bindParam(':id_nv', $id_nv, PDO::PARAM_INT);
			$stmt->bindParam(':new_start', $bdau_ctac);
			$stmt->bindParam(':new_end', $kthuc_ctac);
			
			if ($id_congtac !== null) {
				$stmt->bindParam(':id_congtac', $id_congtac, PDO::PARAM_INT);
			}

			$stmt->execute();
			
			// Nếu số lượng > 0, tức là có trùng lặp
			return $stmt->fetchColumn() > 0;
			
		} catch (PDOException $e) {
			error_log("❌ Lỗi kiểm tra trùng lặp công tác: " . $e->getMessage());
			return true; // Trả về True để ngăn chặn hành động nếu có lỗi DB
		}
	}
}
?>
