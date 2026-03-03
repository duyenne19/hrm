<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Tạo Spreadsheet mới
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// 1. Đặt Tiêu đề Cột
$headers = ['Mã NV', 'Kỳ Lương (MM-YYYY)', 'Ngày Công', 'Tạm Ứng'];
$sheet->fromArray($headers, NULL, 'A1');

// 2. Format Header
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '435EBE'] // Màu xanh giống theme
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];
$sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);

// 3. Thêm dữ liệu mẫu
$sampleData = [
    ['NV001', date('m-Y'), 26, 0],
    ['NV002', date('m-Y'), 24.5, 500000],
];
$sheet->fromArray($sampleData, NULL, 'A2');

// 4. Set format text cho cột Kỳ Lương để tránh Excel tự convert sang Date sai
$sheet->getStyle('B2:B100')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);


// 5. Xuất file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Mau_Nhap_Luong.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
