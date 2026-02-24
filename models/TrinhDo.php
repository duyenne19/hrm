<?php
class TrinhDo {
    private $conn;
    private $table_name = "trinh_do";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🔹 Lấy tất cả trình độ
    public function getAll() {
        $sql = "SELECT 
                    td.id,
                    td.ma_td,
                    td.ten_td,
					td.mota_td,
                    td.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM trinh_do td
                LEFT JOIN tai_khoan tk ON td.id_nguoitao = tk.id
                ORDER BY td.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // 🔹 Lấy chi tiết theo ID
    public function getById($id) {
        $sql = "SELECT 
                    td.id,
                    td.ma_td,
                    td.ten_td,
					td.mota_td,
                    td.ngaytao,
                    CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM trinh_do td
                LEFT JOIN tai_khoan tk ON td.id_nguoitao = tk.id
                WHERE td.id = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Kiểm tra trùng tên trình độ
    public function existsByName($ten_td) {
        $sql = "SELECT COUNT(*) FROM {$this->table_name} WHERE ten_td = :ten_td";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten_td', $ten_td, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // 🔹 Thêm mới
    public function add($ma_td, $ten_td, $mota_td, $id_nguoitao) {
        try {
            $sql = "INSERT INTO {$this->table_name} (ma_td, ten_td, mota_td,id_nguoitao, ngaytao)
                    VALUES (:ma_td, :ten_td, :mota_td, :id_nguoitao, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ma_td', $ma_td);
            $stmt->bindParam(':ten_td', $ten_td);
			$stmt->bindParam(':mota_td', $mota_td);
            $stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log('Lỗi thêm trình độ: ' . $e->getMessage());
            return false;
        }
    }

    // 🔹 Cập nhật
    public function update($id, $ten_td, $mota_td) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE {$this->table_name} SET ten_td = :ten_td, mota_td= :mota_td WHERE id = :id"
            );
            $stmt->bindParam(':ten_td', $ten_td);
			$stmt->bindParam(':mota_td', $mota_td);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Lỗi cập nhật trình độ: ' . $e->getMessage());
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
                // Lỗi ràng buộc khóa ngoại
                return ['success' => false, 'error' => 'constraint'];
            }
            error_log('Lỗi xóa trình độ: ' . $e->getMessage());
            return ['success' => false, 'error' => 'other'];
        }
    }
}
?>
