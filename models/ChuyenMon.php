<?php
class ChuyenMon {
    private $conn;
    private $table_name = "chuyen_mon";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🔹 Lấy tất cả chuyên môn
    public function getAll() {
        $sql = "SELECT 
                    cm.id,
                    cm.ma_cm,
                    cm.ten_cm,
                    cm.mota,
                    cm.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM chuyen_mon cm
                LEFT JOIN tai_khoan tk ON cm.id_nguoitao = tk.id
                ORDER BY cm.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // 🔹 Lấy chi tiết theo ID
    public function getById($id) {
        $sql = "SELECT 
                    cm.id,
                    cm.ma_cm,
                    cm.ten_cm,
                    cm.mota,
                    cm.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM chuyen_mon cm
                LEFT JOIN tai_khoan tk ON cm.id_nguoitao = tk.id
                WHERE cm.id = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Kiểm tra trùng tên chuyên môn
    public function existsByName($ten_cm) {
        $sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE ten_cm = :ten_cm";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten_cm', $ten_cm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // 🔹 Thêm mới
    public function add($ma_cm, $ten_cm, $mota, $id_nguoitao) {
        try {
            $sql = "INSERT INTO {$this->table_name} (ma_cm, ten_cm, mota, id_nguoitao, ngaytao)
                    VALUES (:ma_cm, :ten_cm, :mota, :id_nguoitao, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ma_cm', $ma_cm);
            $stmt->bindParam(':ten_cm', $ten_cm);
            $stmt->bindParam(':mota', $mota);
            $stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi thêm chuyên môn: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Cập nhật
    public function update($id, $ten_cm, $mota) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE {$this->table_name} SET ten_cm = :ten_cm, mota = :mota WHERE id = :id"
            );
            $stmt->bindParam(':ten_cm', $ten_cm);
            $stmt->bindParam(':mota', $mota);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật chuyên môn: " . $e->getMessage());
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
            error_log("Lỗi xóa chuyên môn: " . $e->getMessage());
            return ['success' => false, 'error' => 'other'];
        }
    }
}
?>
