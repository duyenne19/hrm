<?php
class DanToc {
    private $conn;
    private $table_name = "dan_toc";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🔹 Lấy tất cả dân tộc
    public function getAll() {
        $sql = "SELECT 
                    dt.id,
                    dt.ma_dt,
                    dt.ten_dt,
                    dt.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM dan_toc dt
                LEFT JOIN tai_khoan tk ON dt.id_nguoitao = tk.id
                ORDER BY dt.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // 🔹 Lấy chi tiết
    public function getById($id) {
        $sql = "SELECT 
                    dt.id,
                    dt.ma_dt,
                    dt.ten_dt,
                    dt.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM dan_toc dt
                LEFT JOIN tai_khoan tk ON dt.id_nguoitao = tk.id
                WHERE dt.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Kiểm tra trùng tên
    public function existsByName($ten_dt) {
        $sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE ten_dt = :ten_dt";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten_dt', $ten_dt);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // 🔹 Thêm mới
    public function add($ma_dt, $ten_dt, $id_nguoitao) {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO {$this->table_name} (ma_dt, ten_dt, id_nguoitao, ngaytao)
                 VALUES (:ma_dt, :ten_dt, :id_nguoitao, NOW())"
            );
            $stmt->bindParam(':ma_dt', $ma_dt);
            $stmt->bindParam(':ten_dt', $ten_dt);
            $stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi thêm dân tộc: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Cập nhật
    public function update($id, $ten_dt) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE {$this->table_name} SET ten_dt = :ten_dt WHERE id = :id"
            );
            $stmt->bindParam(':ten_dt', $ten_dt);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật dân tộc: " . $e->getMessage());
            return false;
        }
    }

    // 🔹 Xóa (kèm xử lý ràng buộc)
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
            error_log("Lỗi xóa dân tộc: " . $e->getMessage());
            return ['success' => false, 'error' => 'other'];
        }
    }
}
?>
