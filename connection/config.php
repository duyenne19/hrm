<?php

	class Database {
    private $host = "localhost";     // Địa chỉ server MySQL
    private $db_name = "quanlynhansu"; // Tên cơ sở dữ liệu
    private $username = "root";      // Tài khoản MySQL
    private $password = "";          // Mật khẩu MySQL
    public $conn;

    // Hàm khởi tạo kết nối
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            // Thiết lập chế độ lỗi (Error Mode)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "❌ Kết nối thất bại: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
	
	

?>