<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Tạo spreadsheet mới
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Định nghĩa tiêu đề cột
$headers = [
    'A' => 'Mã Nhân Viên (*)',
    'B' => 'Họ và Tên (*)',
    'C' => 'Giới Tính (Nam/Nữ)',
    'D' => 'Ngày Sinh (YYYY-MM-DD)',
    'E' => 'Nơi Sinh',
    'F' => 'Số Điện Thoại',
    'G' => 'Email',
    'H' => 'Địa Chỉ',
    'I' => 'CCCD/CMND'
];

// Ghi tiêu đề vào dòng 1
foreach ($headers as $col => $text) {
    $sheet->setCellValue($col . '1', $text);
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Style cho dòng tiêu đề
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['argb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => '435EBE'], // Màu xanh theme
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];
$sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
$sheet->getRowDimension('1')->setRowHeight(30);

// Thêm dữ liệu mẫu (Dòng 2)
$sampleData = [
    'NV001', 
    'Nguyen Van A', 
    'Nam', 
    '1995-05-20', 
    'Ha Noi', 
    '0901234567', 
    'nguyenvanA@example.com', 
    '123 Cau Giay, Ha Noi', 
    '001095012345'
];

$colIndex = 'A';
foreach ($sampleData as $value) {
    $sheet->setCellValue($colIndex . '2', $value);
    $colIndex++;
}

// Thiết lập header để download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="mau_nhan_vien.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1'); // If you're serving to IE 9/10/11

// Clean output buffer to avoid corrupt file
if (ob_get_length()) ob_end_clean();

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>