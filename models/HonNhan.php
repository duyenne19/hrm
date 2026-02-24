<?php
class HonNhan {
    private $conn;
    private $table_name = "tt_hon_nhan";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🔹 Lấy tất cả
    public function getAll() {
        $sql = "SELECT 
                    hn.id,
                    hn.ma_hn,
                    hn.ten_hn,
                    hn.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM tt_hon_nhan hn
                LEFT JOIN tai_khoan tk ON hn.id_nguoitao = tk.id
                ORDER BY hn.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // 🔹 Lấy chi tiết
    public function getById($id) {
        $sql = "SELECT 
                    hn.id,
                    hn.ma_hn,
                    hn.ten_hn,
                    hn.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM tt_hon_nhan hn
                LEFT JOIN tai_khoan tk ON hn.id_nguoitao = tk.id
                WHERE hn.id = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Kiểm tra trùng tên
    public function existsByName($ten_hn) {
        $sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE ten_hn = :ten_hn";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten_hn', $ten_hn);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // 🔹 Thêm mới
    public function add($ma_hn, $ten_hn, $id_nguoitao) {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO {$this->table_name} (ma_hn, ten_hn, id_nguoitao, ngaytao)
                 VALUES (:ma_hn, :ten_hn, :id_nguoitao, NOW())"
            );
            $stmt->bindParam(':ma_hn', $ma_hn);
            $stmt->bindParam(':ten_hn', $ten_hn);
            $stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Lỗi thêm tình trạng hôn nhân: ' . $e->getMessage());
            return false;
        }
    }

    // 🔹 Cập nhật
    public function update($id, $ten_hn) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE {$this->table_name} SET ten_hn = :ten_hn WHERE id = :id"
            );
            $stmt->bindParam(':ten_hn', $ten_hn);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Lỗi cập nhật tình trạng hôn nhân: ' . $e->getMessage());
            return false;
        }
    }

    // 🔹 Xóa (xử lý ràng buộc khóa ngoại)
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
            error_log('Lỗi xóa tình trạng hôn nhân: ' . $e->getMessage());
            return ['success' => false, 'error' => 'other'];
        }
    }
}
?>
