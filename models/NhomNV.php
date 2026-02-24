<?php
class NhomNV {
    private $conn;
    private $table = "nhom";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả nhóm nhân viên
    public function getAll() {
        $sql = "SELECT n.*, CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM {$this->table} n
                LEFT JOIN tai_khoan tk ON n.id_nguoitao = tk.id
                ORDER BY n.id DESC";
        return $this->conn->query($sql);
    }

    // Lấy chi tiết
    public function getById($id) {
        $sql = "SELECT n.*, CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM {$this->table} n
                LEFT JOIN tai_khoan tk ON n.id_nguoitao = tk.id
                WHERE n.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Kiểm tra trùng tên
    public function existsByName($tennhom, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE tennhom = :tennhom";
        if ($excludeId) $sql .= " AND id != :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tennhom', $tennhom);
        if ($excludeId) $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Thêm
    public function add($data) {
        try {
            $sql = "INSERT INTO {$this->table} (manhom, tennhom, mota, id_nguoitao, ngaytao)
                    VALUES (:manhom, :tennhom, :mota, :id_nguoitao, NOW())";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') return ['error' => 'duplicate'];
            return false;
        }
    }

    // Cập nhật
    public function update($data) {
        $sql = "UPDATE {$this->table} 
                SET tennhom = :tennhom, mota = :mota
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Xóa
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') return ['error' => 'constraint'];
            return ['success' => false];
        }
    }
	
}
?>
