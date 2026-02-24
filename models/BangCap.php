<?php
class BangCap {
    private $conn;
    private $table = "bang_cap";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🔹 Lấy tất cả bản ghi
    public function getAll() {
        $sql = "SELECT 
                    bc.id,
                    bc.ma_bcap,
                    bc.ten_bcap,
                    bc.mota_bcap,
                    bc.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM bang_cap bc
                LEFT JOIN tai_khoan tk ON bc.id_nguoitao = tk.id
                ORDER BY bc.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // 🔹 Lấy chi tiết theo ID
    public function getById($id) {
        $sql = "SELECT 
                    bc.id,
                    bc.ma_bcap,
                    bc.ten_bcap,
                    bc.mota_bcap,
                    bc.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM bang_cap bc
                LEFT JOIN tai_khoan tk ON bc.id_nguoitao = tk.id
                WHERE bc.id = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Kiểm tra trùng tên bằng cấp
    public function existsByName($ten_bcap) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE ten_bcap = :ten_bcap";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten_bcap', $ten_bcap, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // 🔹 Thêm mới
    public function add($ma_bcap, $ten_bcap, $mota_bcap, $id_nguoitao) {
        try {
            $sql = "INSERT INTO {$this->table} (ma_bcap, ten_bcap, mota_bcap, id_nguoitao, ngaytao)
                    VALUES (:ma_bcap, :ten_bcap, :mota_bcap, :id_nguoitao, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ma_bcap', $ma_bcap);
            $stmt->bindParam(':ten_bcap', $ten_bcap);
            $stmt->bindParam(':mota_bcap', $mota_bcap);
            $stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi thêm bằng cấp: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Cập nhật
    public function update($id, $ten_bcap, $mota_bcap) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE {$this->table} SET ten_bcap = :ten_bcap, mota_bcap = :mota_bcap WHERE id = :id"
            );
            $stmt->bindParam(':ten_bcap', $ten_bcap);
            $stmt->bindParam(':mota_bcap', $mota_bcap);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật bằng cấp: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Xóa
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return ['success' => false, 'error' => 'constraint'];
            }
            error_log("Lỗi xóa bằng cấp: " . $e->getMessage());
            return ['success' => false, 'error' => 'other'];
        }
    }
}
?>
