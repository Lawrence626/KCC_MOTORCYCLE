<?php
require 'vendor/autoload.php';

$file = 'C:\\Users\\ilano\\Downloads\\kcc feb inventory.xlsx';
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
$worksheet = $spreadsheet->getActiveSheet();
$data = $worksheet->toArray();

echo "=== EXCEL FILE ANALYSIS ===\n\n";
echo "HEADERS (Row 1):\n";
foreach (array_slice($data[0], 0, 20) as $idx => $header) {
    if (!empty($header)) {
        echo "[$idx] " . $header . "\n";
    }
}

echo "\n\nFIRST DATA ROW (Row 2):\n";
foreach (array_slice($data[1], 0, 20) as $idx => $value) {
    $header = $data[0][$idx] ?? 'Unknown';
    echo "[$header] = " . substr($value ?? '', 0, 50) . "\n";
}

echo "\n\nTOTAL ROWS: " . count($data);
?>
