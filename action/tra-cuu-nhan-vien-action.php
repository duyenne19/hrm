<?php
	//  NV041  012345678917
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	include(__DIR__ . '/../connection/config.php');
	include(__DIR__ . '/../models/NhanVien.php');
	include_once __DIR__ . '/../models/Luong.php';
	include(__DIR__ . '/../models/ChiTietNhom.php');
	include(__DIR__ . '/../models/KhenThuongKyLuat.php');	
	
	$database = new Database();
	$conn = $database->getConnection();
	
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_Tracuu'])) {
		
		
		$nhanVienModel = new NhanVien($conn);
		
		
		$maNhanVien = isset($_POST['maNhanVien']) ? htmlspecialchars(trim($_POST['maNhanVien'])) : '';
		$soCccd = isset($_POST['soCccd']) ? htmlspecialchars(trim($_POST['soCccd'])) : '';
		
		 
		$thongTinNhanVien = $nhanVienModel->tra_cuu($maNhanVien, $soCccd);
		//echo 'maNhanVien: '.$maNhanVien.' soCccd: '.$soCccd;
		//exit;
   
		if ($thongTinNhanVien) {
						
			$_SESSION['thongTinNV'] = $thongTinNhanVien;
			header("Location: ./../tra-cuu-nhan-vien.php?status=success&msg=" . urlencode("Tra cứu nhân viên thành công."));
			exit();
		} else {
			header("Location: ./../tra-cuu-nhan-vien.php?status=fail&msg=" . urlencode("Không tìm thấy nhân viên."));
		   exit();
		}
    
	}else{
		if (isset($_GET['ky_luong_chon'])) {
			$kyLuongMacDinh = $_GET['ky_luong_chon'];			
		}else{
			$kyLuongMacDinh = date('m/Y', strtotime('-1 month')); // Ví dụ: 10/2025
		}
		$thang_arr = explode('/', $kyLuongMacDinh);
		if (count($thang_arr) === 2) {
			// Chuyển MM/YYYY sang YYYY-MM-01
			$kyLuongMacDinhDB = $thang_arr[1] . '-' . $thang_arr[0] . '-01';
		}else
			$kyLuongMacDinhDB = date('Y-m-01', $timestamp);
		
		$thongTinNV_hienThi = null;
		
		
		
		
		
		if(isset($_SESSION['thongTinNV']))
		{
			$thongTinNV_hienThi = $_SESSION['thongTinNV'];
			$idNhanVien = $thongTinNV_hienThi['id'];
			
			// Khối 1: Hiển thị chi tiết các nhóm tham gia:
			$nhomModel = new ChiTietNhom($conn);
			$cacNhomNVThamGia = $nhomModel->getNhomByNhanVien($idNhanVien);
			foreach ($cacNhomNVThamGia as $nhom) {
				$id_nhom = $nhom['id_nhom'];
				
				$thanhVien = $nhomModel->getMembersByGroup($id_nhom);
				
				// Lưu trữ cả thông tin nhóm và danh sách thành viên
				$danhSachNhomVaThanhVien[] = [
					'thong_tin_nhom' => $nhom,
					'thanh_vien' => $thanhVien
				];
			}
			//***********************************************************************
			
			// Khối 2: truy vấn hiển thị kỳ lương mặc định là tháng trước.
			$luongChiTiet = null;

			
			$luongModel = new Luong($conn);
			$luongChiTiet = $luongModel->xemLuong_idVN_ky_luong($idNhanVien, $kyLuongMacDinhDB);
			//***********************************************************************
				
			// Khối 3: truy vấn hiển thị khen thưởng - kỷ luật mặc định là tháng trước.
			$khenThuongList = [];
			$kyLuatList = [];
			$ktklModel = new KhenThuongKyLuat($conn);
			$allEvents = $ktklModel->getKhenThuongKyLuatByNVAndMonth($idNhanVien, $kyLuongMacDinhDB);
			foreach ($allEvents as $event) {
				if ($event['ck_khenthuong']) {
					$khenThuongList[] = $event;
				} else {
					$kyLuatList[] = $event;
				}
			}
			//***********************************************************************
			
			
			//unset($_SESSION['thongTinNV']);			
		}
	}
	
	
?>