<?php
class ChucVu {
    private $conn;
    private $table_name = "chuc_vu";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả
    public function getAll() {
        $sql = "SELECT 
                    cv.*,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM chuc_vu cv
                LEFT JOIN tai_khoan tk ON cv.id_nguoitao = tk.id
                ORDER BY cv.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // Lấy theo ID
    public function getById($id) {
        $sql = "SELECT 
                    cv.*,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM chuc_vu cv
                LEFT JOIN tai_khoan tk ON cv.id_nguoitao = tk.id
                WHERE cv.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Kiểm tra tên chức vụ có tồn tại chưa
    public function existsByName($tencv) {
        $sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE tencv = :tencv";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tencv', $tencv);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Thêm mới
    public function add($macv, $tencv, $luong_coban,$he_so_luong,$he_so_phu_cap, $mota, $id_nguoitao) {
        try {
            $sql = "INSERT INTO {$this->table_name} (macv, tencv, luong_coban, he_so_luong, he_so_phu_cap, mota, id_nguoitao, ngaytao)
                    VALUES (:macv, :tencv, :luong_coban, :he_so_luong, :he_so_phu_cap, :mota, :id_nguoitao, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':macv', $macv);
            $stmt->bindParam(':tencv', $tencv);
			$stmt->bindParam(':luong_coban', $luong_coban);
			$stmt->bindParam(':he_so_luong', $he_so_luong	);
			$stmt->bindParam(':he_so_phu_cap', $he_so_phu_cap);
            $stmt->bindParam(':mota', $mota);
            $stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi thêm chức vụ: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật
    public function update($id, $tencv, $luong_coban,$he_so_luong,$he_so_phu_cap, $mota) {
        try {
            $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET tencv = :tencv, luong_coban = :luong_coban, he_so_luong = :he_so_luong, he_so_phu_cap = :he_so_phu_cap, mota = :mota WHERE id = :id");
            $stmt->bindParam(':tencv', $tencv);
			$stmt->bindParam(':luong_coban', $luong_coban);
			$stmt->bindParam(':he_so_luong', $he_so_luong);
			$stmt->bindParam(':he_so_phu_cap', $he_so_phu_cap);
            $stmt->bindParam(':mota', $mota);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật chức vụ: " . $e->getMessage());
            return false;
        }
    }

    // Xóa
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM {$this->table_name} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return ['success' => false, 'error' => 'constraint'];
            }
            error_log("Lỗi xóa chức vụ: " . $e->getMessage());
            return ['success' => false, 'error' => 'other'];
        }
    }
}
?>
