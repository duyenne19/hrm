<?php
class TaiKhoan {
    private $conn;
    private $table = "tai_khoan";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🔹 Lấy 1 tài khoản theo ID
    public function getById($id) {
        $sql = "SELECT id, ho, ten, hinhanh, email, sodt, quyen, trangthai, ngaytao, mk
                FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Đăng nhập
    public function login($email, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE trangthai = 1 and email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) return ['success' => false, 'error' => 'email'];
        if (password_verify($password, $user['mk'])) {
            unset($user['mk']);
            return ['success' => true, 'user' => $user];
        }
        return ['success' => false, 'error' => 'matkhau'];
    }

    // 🔹 Lấy danh sách tất cả tài khoản
    public function getAll() {
        $sql = "SELECT id, ho, ten, hinhanh, email, sodt, quyen, trangthai, ngaytao
                FROM {$this->table} ORDER BY id,trangthai DESC";
        return $this->conn->query($sql);
    }

    // 🔹 Kiểm tra trùng email
    private function existsEmail($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        if ($excludeId) $sql .= " AND id != :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        if ($excludeId) $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // 🔹 Thêm mới
    public function add($data) {
        if ($this->existsEmail($data['email'])) {
            return ['success' => false, 'error' => 'duplicate_email'];
        }

        $sql = "INSERT INTO {$this->table}
                (ho, ten, email, mk, sodt, quyen, trangthai, hinhanh, ngaytao)
                VALUES (:ho, :ten, :email, :mk, :sodt, :quyen, :trangthai, :hinhanh, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ho', $data['ho']);
        $stmt->bindValue(':ten', $data['ten']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':mk', password_hash($data['mk'] ?? '123456', PASSWORD_DEFAULT));
        $stmt->bindValue(':sodt', $data['sodt']);
        $stmt->bindValue(':quyen', $data['quyen'] ?? 'user');
        $stmt->bindValue(':trangthai', $data['trangthai'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':hinhanh', $data['hinhanh'] ?? null);
        $ok = $stmt->execute();

        return ['success' => $ok];
    }

    // 🔹 Cập nhật (dùng cho quản trị)
    public function update($data) {
        $sql = "UPDATE {$this->table}
                SET ho = :ho, ten = :ten, sodt = :sodt, quyen = :quyen, trangthai = :trangthai";
        if (!empty($data['hinhanh'])) $sql .= ", hinhanh = :hinhanh";
        $sql .= " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);
        $stmt->bindValue(':ho', $data['ho']);
        $stmt->bindValue(':ten', $data['ten']);
        $stmt->bindValue(':sodt', $data['sodt']);
        $stmt->bindValue(':quyen', $data['quyen']);
        $stmt->bindValue(':trangthai', $data['trangthai']);
        if (!empty($data['hinhanh'])) $stmt->bindValue(':hinhanh', $data['hinhanh']);
        return $stmt->execute();
    }

    // 🔹 Cập nhật thông tin cá nhân (người dùng)
    public function updateInfo($data, $file = null) {
        $id = (int)$data['id'];
        $user = $this->getById($id);
        if (!$user) return ['success' => false, 'message' => 'Tài khoản không tồn tại.'];

        $fileName = $user['hinhanh'] ?? null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = 'avatar_' . $id . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/anh/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            move_uploaded_file($file['tmp_name'], $uploadDir . $fileName);
        }

        $sql = "UPDATE {$this->table}
                SET ho = :ho, ten = :ten, sodt = :sodt, hinhanh = :hinhanh
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ho', $data['ho']);
        $stmt->bindValue(':ten', $data['ten']);
        $stmt->bindValue(':sodt', $data['sodt']);
        $stmt->bindValue(':hinhanh', $fileName);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $ok = $stmt->execute();
        return ['success' => $ok, 'message' => $ok ? 'Cập nhật thông tin thành công.' : 'Cập nhật thất bại.'];
    }

    // 🔹 Cập nhật mật khẩu
    public function updatePassword($id, $old, $new) {
        $user = $this->getById($id);
        if (!$user) return ['success' => false, 'message' => 'Không tồn tại tài khoản.'];
        if (!password_verify($old, $user['mk'])) return ['success' => false, 'message' => 'Mật khẩu hiện tại không đúng.'];

        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $sql = "UPDATE {$this->table} SET mk = :mk WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':mk', $newHash);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $ok = $stmt->execute();
        return ['success' => $ok, 'message' => $ok ? 'Đổi mật khẩu thành công.' : 'Thất bại.'];
    }

    // 🔹 Xóa tài khoản
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') return ['success' => false, 'error' => 'constraint'];
            error_log("Delete error: " . $e->getMessage());
            return ['success' => false];
        }
    }
}
?>
