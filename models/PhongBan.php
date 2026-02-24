<?php
class PhongBan {
    private $conn;
    private $table_name = "phong_ban";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy toàn bộ phòng ban
    public function getAll() {
        $sql = "SELECT 
                    pb.id,
                    pb.ma_bp,
                    pb.ten_bp,
                    pb.mota,
                    pb.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM phong_ban pb
                LEFT JOIN tai_khoan tk ON pb.id_nguoitao = tk.id
                ORDER BY pb.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // Lấy 1 phòng ban theo ID
    public function getById($id) {
        $sql = "SELECT 
                    pb.id, pb.ma_bp, pb.ten_bp, pb.mota, pb.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM phong_ban pb
                LEFT JOIN tai_khoan tk ON pb.id_nguoitao = tk.id
                WHERE pb.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Kiểm tra tên phòng ban có tồn tại chưa
    public function existsByName($ten_bp) {
        $sql = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE ten_bp = :ten_bp";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten_bp', $ten_bp, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Thêm mới phòng ban
    public function add($ma_bp, $ten_bp, $mota, $id_nguoitao) {
        try {
            $sql = "INSERT INTO " . $this->table_name . " (ma_bp, ten_bp, mota, id_nguoitao, ngaytao)
                    VALUES (:ma_bp, :ten_bp, :mota, :id_nguoitao, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ma_bp', $ma_bp);
            $stmt->bindParam(':ten_bp', $ten_bp);
            $stmt->bindParam(':mota', $mota);
            $stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi thêm phòng ban: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật phòng ban
    public function update($id, $ten_bp, $mota) {
        try {
            $stmt = $this->conn->prepare("UPDATE " . $this->table_name . " 
                                          SET ten_bp = :ten_bp, mota = :mota 
                                          WHERE id = :id");
            $stmt->bindParam(':ten_bp', $ten_bp);
            $stmt->bindParam(':mota', $mota);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật phòng ban: " . $e->getMessage());
            return false;
        }
    }

    // Xóa phòng ban
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM " . $this->table_name . " WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                // lỗi khóa ngoại
                return ['success' => false, 'error' => 'constraint'];
            }
            error_log("Lỗi xóa phòng ban: " . $e->getMessage());
            return ['success' => false, 'error' => 'other'];
        }
    }
}
?>
