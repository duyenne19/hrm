<?php
class LoaiNhanVien {
    private $conn;
    private $table_name = "loai_nhanvien";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả
    public function getAll() {
        $sql = "SELECT 
                    lnv.id,
                    lnv.ma_lnv,
                    lnv.ten_lnv,
                    lnv.mota,
                    lnv.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM loai_nhanvien lnv
                LEFT JOIN tai_khoan tk ON lnv.id_nguoitao = tk.id
                ORDER BY lnv.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // Lấy chi tiết
    public function getById($id) {
        $sql = "SELECT 
                    lnv.id,
                    lnv.ma_lnv,
                    lnv.ten_lnv,
                    lnv.mota,
                    lnv.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM loai_nhanvien lnv
                LEFT JOIN tai_khoan tk ON lnv.id_nguoitao = tk.id
                WHERE lnv.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Kiểm tra trùng tên
    public function existsByName($ten_lnv) {
        $sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE ten_lnv = :ten_lnv";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten_lnv', $ten_lnv);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Thêm mới
    public function add($ma_lnv, $ten_lnv, $mota, $id_nguoitao) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO {$this->table_name} (ma_lnv, ten_lnv, mota, id_nguoitao, ngaytao)
                VALUES (:ma_lnv, :ten_lnv, :mota, :id_nguoitao, NOW())
            ");
            $stmt->bindParam(':ma_lnv', $ma_lnv);
            $stmt->bindParam(':ten_lnv', $ten_lnv);
            $stmt->bindParam(':mota', $mota);
            $stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi thêm loại nhân viên: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật
    public function update($id, $ten_lnv, $mota) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE {$this->table_name}
                SET ten_lnv = :ten_lnv, mota = :mota
                WHERE id = :id
            ");
            $stmt->bindParam(':ten_lnv', $ten_lnv);
            $stmt->bindParam(':mota', $mota);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật loại nhân viên: " . $e->getMessage());
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
            if ($e->getCode() === '23000') { // FK constraint
                return ['success' => false, 'error' => 'constraint'];
            }
            error_log("Lỗi xóa loại nhân viên: " . $e->getMessage());
            return ['success' => false, 'error' => 'other'];
        }
    }
}
?>
