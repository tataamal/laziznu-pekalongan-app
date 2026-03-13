<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set Header
$sheet->setCellValue('A1', 'nama');
$sheet->setCellValue('B1', 'email');
$sheet->setCellValue('C1', 'password');
$sheet->setCellValue('D1', 'role');
$sheet->setCellValue('E1', 'telpon');

// Add Dummy Row
$sheet->setCellValue('A2', 'Ranting B');
$sheet->setCellValue('B2', 'rantingb@example.com');
$sheet->setCellValue('C2', 'password123');
$sheet->setCellValue('D2', 'ranting');
$sheet->setCellValue('E2', '081234567890');

$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/public/templates/template_import_user.xlsx');

echo "Template created successfully.\n";
