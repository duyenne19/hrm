<?php
class QuocTich {
    private $conn;
    private $table_name = "quoc_tich";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🔹 Lấy tất cả quốc tịch
    public function getAll() {
        $sql = "SELECT 
                    qt.id,
                    qt.ma_qt,
                    qt.ten_qt,
                    qt.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM quoc_tich qt
                LEFT JOIN tai_khoan tk ON qt.id_nguoitao = tk.id
                ORDER BY qt.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // 🔹 Lấy chi tiết theo ID
    public function getById($id) {
        $sql = "SELECT 
                    qt.id,
                    qt.ma_qt,
                    qt.ten_qt,
                    qt.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM quoc_tich qt
                LEFT JOIN tai_khoan tk ON qt.id_nguoitao = tk.id
                WHERE qt.id = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Kiểm tra trùng tên quốc tịch
    public function existsByName($ten_qt) {
        $sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE ten_qt = :ten_qt";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten_qt', $ten_qt, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // 🔹 Thêm mới
    public function add($ma_qt, $ten_qt, $id_nguoitao) {
        try {
            $sql = "INSERT INTO {$this->table_name} (ma_qt, ten_qt, id_nguoitao, ngaytao)
                    VALUES (:ma_qt, :ten_qt, :id_nguoitao, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ma_qt', $ma_qt);
            $stmt->bindParam(':ten_qt', $ten_qt);
            $stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi thêm quốc tịch: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Cập nhật
    public function update($id, $ten_qt) {
        try {
            $stmt = $this->conn->prepare("UPDATE {$this->table_name} SET ten_qt = :ten_qt WHERE id = :id");
            $stmt->bindParam(':ten_qt', $ten_qt);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật quốc tịch: " . $e->getMessage());
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
                // Ràng buộc khóa ngoại
                return ['success' => false, 'error' => 'constraint'];
            }
            error_log("Lỗi xóa quốc tịch: " . $e->getMessage());
            return ['success' => false, 'error' => 'other'];
        }
    }
}
?>
