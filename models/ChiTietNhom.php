<?php
class ChiTietNhom {
    private $conn;
    private $table = "chi_tiet_nhom";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy thông tin nhóm
    public function getNhomInfo($id_nhom) {
        $sql = "SELECT n.*, CONCAT(tk.ho, ' ', tk.ten) AS nguoitao_name
                FROM nhom n
                LEFT JOIN tai_khoan tk ON n.id_nguoitao = tk.id
                WHERE n.id = :id_nhom LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_nhom', $id_nhom, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách nhân viên trong nhóm
    public function getMembersByGroup($id_nhom) {
        $sql = "SELECT 
                    ctn.id_nhom,
                    ctn.id_nv,
                    ctn.ngaytao AS ngaytao,
                    nv.ma_nv,
                    nv.anhdaidien,
                    nv.hoten,
                    nv.gtinh,
                    nv.ngsinh,
                    pb.ten_bp AS phongban,
                    cv.tencv AS chucvu,
                    nv.trangthai,
					SUBSTRING_INDEX(TRIM(nv.hoten), ' ', -1) AS ten_rieng
                FROM chi_tiet_nhom ctn
                INNER JOIN nhan_vien nv ON ctn.id_nv = nv.id
                LEFT JOIN phong_ban pb ON nv.id_phongban = pb.id
                LEFT JOIN chuc_vu cv ON nv.id_chucvu = cv.id
                WHERE ctn.id_nhom = :id_nhom
                ORDER BY ten_rieng ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_nhom', $id_nhom, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách nhân viên có thể thêm vào nhóm
    public function getAvailableMembers($id_nhom) {
		$sql =	"SELECT
				nv.id,
				nv.ma_nv,
				nv.hoten,
				pb.ten_bp,
				cv.tencv
			FROM
				nhan_vien nv			
			JOIN
				phong_ban pb ON nv.id_phongban = pb.id			
			JOIN
				chuc_vu cv ON nv.id_chucvu = cv.id
			WHERE
				nv.trangthai = 1				
				AND nv.id NOT IN (
					SELECT id_nv FROM {$this->table} WHERE id_nhom = :id_nhom
				)
			ORDER BY
				nv.hoten ASC";
       
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_nhom', $id_nhom, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm nhân viên vào nhóm
    public function addMember($id_nhom, $id_nv, $id_nguoitao) {
        $sql = "INSERT INTO {$this->table} (id_nhom, id_nv, id_nguoitao, ngaytao)
                VALUES (:id_nhom, :id_nv, :id_nguoitao, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_nhom', $id_nhom, PDO::PARAM_INT);
        $stmt->bindParam(':id_nv', $id_nv, PDO::PARAM_INT);
        $stmt->bindParam(':id_nguoitao', $id_nguoitao, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Xóa nhân viên khỏi nhóm
    public function deleteMember($id_nhom, $id_nv) {
        $sql = "DELETE FROM {$this->table} WHERE id_nhom = :id_nhom AND id_nv = :id_nv";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_nhom', $id_nhom, PDO::PARAM_INT);
        $stmt->bindParam(':id_nv', $id_nv, PDO::PARAM_INT);
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("ChiTietNhom::deleteMember error: " . $e->getMessage());
            return false;
        }
    }

	public function getNhomByNhanVien($id_nv) {
    // Lấy thông tin nhóm mà nhân viên tham gia
		$sql = "
			SELECT 
				n.id AS id_nhom, 
				n.manhom, 
				n.mota,
				n.tennhom
			FROM chi_tiet_nhom ctn
			JOIN nhom n ON ctn.id_nhom = n.id
			WHERE ctn.id_nv = :id_nv			
			ORDER BY ctn.id DESC";
			
		$stmt = $this->conn->prepare($sql);
		$stmt->bindParam(':id_nv', $id_nv, PDO::PARAM_INT);
		$stmt->execute();
		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

}
?>
