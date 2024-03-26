<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory; 

function readExcel($file) {
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $data = [];
    foreach ($sheet->getRowIterator() as $row) {
        $rowData = [];
        foreach ($row->getCellIterator() as $cell) {
            $rowData[] = $cell->getValue();
        }
        $data[] = $rowData;
    }
    return $data;
}

function writeExcel($data, $outputFile) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    foreach ($data as $rowIndex => $rowData) {
        foreach ($rowData as $columnIndex => $value) {
           
            $sheet->setCellValue([$columnIndex + 1, $rowIndex + 1], $value);
        }
    }
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($outputFile);
}

if (isset($_FILES['database_file']) && isset($_FILES['excel_file'])) {
    $databaseFile = $_FILES['database_file']['tmp_name'];
    $uploadedFile = $_FILES['excel_file']['tmp_name'];
    
    $databaseData = readExcel($databaseFile);

    $uploadedData = readExcel($uploadedFile);

    
    $mergedData = array_merge($databaseData, $uploadedData);

    $mergedData = array_map("unserialize", array_unique(array_map("serialize", $mergedData)));

    sort($mergedData);

    $outputFile = "output.xlsx";
    writeExcel($mergedData, $outputFile);

    echo "Populated file created successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel Files</title>
</head>
<body>
    <h2>Upload Excel Files</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="database_file">Upload Database Excel File:</label>
        <input type="file" name="database_file" id="database_file" accept=".xlsx"><br><br>
        <label for="excel_file">Upload Second Excel File:</label>
        <input type="file" name="excel_file" id="excel_file" accept=".xlsx"><br><br>
        <input type="submit" name="submit" value="Populate Fields">
    </form>
</body>
</html>
