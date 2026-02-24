<?php
class TonGiao {
    private $conn;
    private $table_name = "ton_giao";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🔹 Lấy tất cả tôn giáo
    public function getAll() {
        $sql = "SELECT 
                    tg.id,
                    tg.ma_tg,
                    tg.ten_tg,
                    tg.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM ton_giao tg
                LEFT JOIN tai_khoan tk ON tg.id_nguoitao = tk.id
                ORDER BY tg.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // 🔹 Lấy chi tiết theo ID
    public function getById($id) {
        $sql = "SELECT 
                    tg.id,
                    tg.ma_tg,
                    tg.ten_tg,
                    tg.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM ton_giao tg
                LEFT JOIN tai_khoan tk ON tg.id_nguoitao = tk.id
                WHERE tg.id = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Kiểm tra tên trùng
    public function existsByName($ten_tg) {
        $sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE ten_tg = :ten_tg";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten_tg', $ten_tg);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // 🔹 Thêm mới
    public function add($ma_tg, $ten_tg, $id_nguoitao) {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO {$this->table_name} (ma_tg, ten_tg, id_nguoitao, ngaytao)
                 VALUES (:ma_tg, :ten_tg, :id_nguoitao, NOW())"
            );
            $stmt->bindParam(':ma_tg', $ma_tg);
            $stmt->bindParam(':ten_tg', $ten_tg);
            $stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi thêm tôn giáo: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Cập nhật
    public function update($id, $ten_tg) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE {$this->table_name} SET ten_tg = :ten_tg WHERE id = :id"
            );
            $stmt->bindParam(':ten_tg', $ten_tg);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật tôn giáo: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Xóa
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
            error_log("Lỗi xóa tôn giáo: " . $e->getMessage());
            return ['success' => false, 'error' => 'other'];
        }
    }
}
?>
