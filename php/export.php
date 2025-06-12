<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// // Load the database configuration file 
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 
 
// Excel file name for download 
if($_GET["file"] == 'weight'){
    if ($_GET["type"] == 'Weighing'){
        $weightStatus = '';
        if($_GET['weightStatus'] != null && $_GET['weightStatus'] != ''){
            $weightStatus = $_GET["weightStatus"];
        }

        $fileName = $weightStatus."-Weight-data_" . date('Y-m-d') . ".xls";
    }else{
        $fileName = "Weight-data_" . date('Y-m-d') . ".xls";
    }
}else{
    $fileName = "Count-data_" . date('Y-m-d') . ".xls";
}

## Search 
$searchQuery = "";

if($_GET['fromDate'] != null && $_GET['fromDate'] != ''){
    $date = DateTime::createFromFormat('d-m-Y', $_GET['fromDate']);
    $formatted_date = $date->format('Y-m-d 00:00:00');

    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_date >= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.transaction_date >= '".$formatted_date."'";
    }
}

if($_GET['toDate'] != null && $_GET['toDate'] != ''){
    $date = DateTime::createFromFormat('d-m-Y', $_GET['toDate']);
    $formatted_date = $date->format('Y-m-d 23:59:59');

    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_date <= '".$formatted_date."'";
    }
    else{
        $searchQuery .= " and count.transaction_date <= '".$formatted_date."'";
    }
}

if($_GET['status'] != null && $_GET['status'] != '' && $_GET['status'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_status = '".$_GET['status']."'";
    }
    else{
        $searchQuery .= " and count.transaction_status = '".$_GET['status']."'";
    }	
}

if($_GET['customer'] != null && $_GET['customer'] != '' && $_GET['customer'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.customer_code = '".$_GET['customer']."'";
    }
    else{
        $searchQuery .= " and count.customer_code = '".$_GET['customer']."'";
    }
}

if($_GET['vehicle'] != null && $_GET['vehicle'] != '' && $_GET['vehicle'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.lorry_plate_no1 = '".$_GET['vehicle']."'";
    }
    else{
        $searchQuery .= " and count.lorry_plate_no1 = '".$_GET['vehicle']."'";
    }
}

if($_GET['weighingType'] != null && $_GET['weighingType'] != '' && $_GET['weighingType'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.weight_type like '%".$_GET['weighingType']."%'";
    }
    else{
        $searchQuery .= " and count.weight_type like '%".$_GET['weighingType']."%'";
    }
}

if($_GET['transactionId'] != null && $_GET['transactionId'] != '' && $_GET['transactionId'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.transaction_id like '%".$_GET['transactionId']."%'";
    }
    else{
        $searchQuery .= " and count.weight_type like '%".$_GET['weighingType']."%'";
    }
}

if($_GET['product'] != null && $_GET['product'] != '' && $_GET['product'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.product_code = '".$_GET['product']."'";
    }
    else{
        $searchQuery .= " and count.product_code = '".$_GET['product']."'";
    }
}

if(isset($_GET['plant']) && $_GET['plant'] != null && $_GET['plant'] != '' && $_GET['plant'] != '-'){
    if($_GET["file"] == 'weight'){
        $searchQuery .= " and Weight.plant_code = '".$_GET['plant']."'";
    }
    else{
        $searchQuery .= " and count.raw_mat_code = '".$_GET['plant']."'";
    }
}

// Column names 
if ($_GET["type"] == 'Weighing'){
    $fields = array('TRANSACTION ID', 'WEIGHT STATUS', 'WEIGHT TYPE', 'VEHICLE', 'GROSS INCOMING (KG)', 'INCOMING DATE', 'TARE OUTGOING (KG)', 
    'OUTGOING DATE', 'NETT WEIGHT (KG)'); 

    // Fetch records from database
    if($_GET["file"] == 'weight'){
        if ($_GET["isMulti"] == 'Y'){
            $ids = $_GET['id'];
            if ($weightStatus == 'Pending'){
                $sql = "select * from Weight WHERE Weight.status = '0' and Weight.is_complete = 'N' and Weight.is_cancel <> 'Y' and id IN ($ids)";
            }elseif ($weightStatus == 'Complete'){
                $sql = "select * from Weight WHERE Weight.status = '0' and Weight.is_complete = 'Y' and Weight.is_cancel <> 'Y' and id IN ($ids)";
            }else{
                $sql = "select * from Weight WHERE Weight.status = '0' and Weight.is_cancel = 'Y' and id IN ($ids)";
            }
        }else{
            if ($weightStatus == 'Pending'){
                $sql = "select * from Weight WHERE Weight.status = '0' and Weight.is_complete = 'N' and Weight.is_cancel <> 'Y'".$searchQuery;
            }elseif ($weightStatus == 'Complete'){
                $sql = "select * from Weight WHERE Weight.status = '0' and Weight.is_complete = 'Y' and Weight.is_cancel <> 'Y'".$searchQuery;
            }else{
                $sql = "select * from Weight WHERE Weight.status = '0' and Weight.is_cancel = 'Y'".$searchQuery;
            }
        }
    }
}else{
    $fields = [];
    // $fields = array('TRANSACTION ID', 'WEIGHT STATUS', 'WEIGHT TYPE', 'TRANSACTION DATE', 'LORRY NO.', 'CUSTOMER CODE', 'CUSTOMER NAME', 
    // 'SUPPLIER NODE', 'SUPPLIER NAME', 'PRODUCT CODE', 'PRODUCT NAME', 'PRODUCT DESCRIPTION', 'PO NO.', 'DO NO.', 'GROSS WEIGHT', 'TARE WEIGHT', 
    // 'NETT WEIGHT', 'IN TIME', 'OUT TIME', 'MANUAL', 'WEIGHTED BY'); 

    if($_GET["file"] == 'weight'){
        $sql = "select * from Weight WHERE Weight.status = '0' and Weight.is_complete = 'Y' and Weight.is_cancel <> 'Y'".$searchQuery;
    }
    else{
        $sql = "select count.id, count.serialNo, vehicles.veh_number, lots.lots_no, count.batchNo, count.invoiceNo, count.deliveryNo, 
        count.purchaseNo, customers.customer_name, products.product_name, packages.packages, count.unitWeight, count.tare, count.totalWeight, 
        count.actualWeight, count.currentWeight, units.units, count.moq, count.dateTime, count.unitPrice, count.totalPrice,count.totalPCS, 
        count.remark, count.deleted, status.status from count, vehicles, packages, lots, customers, products, units, status WHERE 
        count.vehicleNo = vehicles.id AND count.package = packages.id AND count.lotNo = lots.id AND count.customer = customers.id AND 
        count.productName = products.id AND status.id=count.status AND units.id=count.unit ".$searchQuery."";
    }
}

// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n";

// Fetch records from database
$query = $db->query($sql);

// Begin spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set column widths
$columns = ['A' => 18, 'B' => 20, 'C' => 16, 'D' => 24, 'E' => 18, 'F' => 18, 'G' => 16, 'H' => 10, 'I' => 16, 'J' => 16, 'K' => 20];
foreach ($columns as $col => $width) {
    $sheet->getColumnDimension($col)->setWidth($width);
}

$rowNum = 1;

if($query->num_rows > 0){ 
    $no = 1;
    // Output each row of the data 
    while($row = $query->fetch_assoc()){ 
        $lineData = [];
        if ($_GET["type"] == 'Weighing'){
            if($_GET["file"] == 'weight'){
                $lineData = array($row['transaction_id'], $row['transaction_status'], $row['weight_type'], $row['lorry_plate_no1'], $row['gross_weight1'], 
                $row['gross_weight1_date'], $row['tare_weight1'], $row['tare_weight1_date'], $row['nett_weight1']);
            }

            array_walk($lineData, 'filterData'); 
            $excelData .= implode("\t", array_values($lineData)) . "\n"; 

        }else{
            // --- Add a uniform separator row ---
            $sheet->mergeCells("A$rowNum:G$rowNum");
            $sheet->setCellValue("A$rowNum", "Record $no");
            $sheet->getStyle("A$rowNum:G$rowNum")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '444444'], // dark consistent gray
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $rowNum++; // Move to the next row for the next block
            $no++;

            // --- HEADER BLOCK (Customer/Supplier Info) ---
            $sheet->setCellValue("A$rowNum", 'SERIAL NO');
            $sheet->setCellValue("B$rowNum", 'STATUS');
            $sheet->setCellValue("C$rowNum", 'TYPE');
            $sheet->setCellValue("D$rowNum", 'CONTACT NAME');
            $sheet->setCellValue("E$rowNum", 'IC NO. / REG NO.');
            $sheet->setCellValue("F$rowNum", 'TIN NO');
            $sheet->setCellValue("G$rowNum", 'CONTACT NO');
            $sheet->mergeCells("A$rowNum:A$rowNum");
            $sheet->mergeCells("B$rowNum:B$rowNum");
            $sheet->mergeCells("C$rowNum:C$rowNum");
            $sheet->mergeCells("D$rowNum:D$rowNum");
            $sheet->mergeCells("E$rowNum:E$rowNum");
            $sheet->mergeCells("F$rowNum:F$rowNum");
            $sheet->mergeCells("G$rowNum:G$rowNum");
            $sheet->getStyle("A$rowNum:G$rowNum")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2172B9'],
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);
            $rowNum++;

            // Data row (fill with your query data, as in your business logic)
            $status = '';
            $customerContactName = '';
            $customerIcNo = '';
            $customerTinNo = '';
            $customerContactNo = '';
            if ($row['transaction_status'] == 'Sales' || $row['transaction_status'] == 'Misc') {
                if ($row['customer_is_manual'] == 'Y') {
                    $status = 'Walk In Customer';
                } else {
                    $status = 'Existing Customer';
                    if ($select_stmt = $db->prepare("SELECT * FROM Customer WHERE customer_code=? AND status='0'")) {
                        $select_stmt->bind_param('s', $row['customer_code']);
                        $select_stmt->execute();
                        $result = $select_stmt->get_result();
                        if ($row2 = $result->fetch_assoc()) {
                            $customerContactName = $row2['contact_name'];
                            $customerIcNo = $row2['ic_no'];
                            $customerTinNo = $row2['tin_no'];
                            $customerContactNo = $row2['phone_no'];
                        }
                        $select_stmt->close();
                    }
                }
            } else {
                if ($row['supplier_is_manual'] == 'Y') {
                    $status = 'Walk In Supplier';
                } else {
                    $status = 'Existing Supplier';
                    if ($select_stmt = $db->prepare("SELECT * FROM Supplier WHERE supplier_code=? AND status='0'")) {
                        $select_stmt->bind_param('s', $row['supplier_code']);
                        $select_stmt->execute();
                        $result = $select_stmt->get_result();
                        if ($row2 = $result->fetch_assoc()) {
                            $customerContactName = $row2['contact_name'];
                            $customerIcNo = $row2['ic_no'];
                            $customerTinNo = $row2['tin_no'];
                            $customerContactNo = $row2['phone_no'];
                        }
                        $select_stmt->close();
                    }
                }
            }
            $sheet->fromArray([
                $row['transaction_id'], $status, $row['transaction_status'],
                $customerContactName, $customerIcNo, $customerTinNo, $customerContactNo
            ], null, "A$rowNum");
            $sheet->getStyle("A$rowNum:G$rowNum")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            $rowNum += 2;

            // --- PRODUCT TABLE HEADER ---
            $sheet->setCellValue("A$rowNum", 'DESCRIPTION');
            $sheet->setCellValue("B$rowNum", 'WEIGHT');
            $sheet->setCellValue("C$rowNum", 'REDUCE WEIGHT');
            $sheet->setCellValue("D$rowNum", 'NETT');
            $sheet->setCellValue("E$rowNum", 'UNIT PRICE');
            $sheet->setCellValue("F$rowNum", 'TOTAL PRICE');
            $sheet->setCellValue("G$rowNum", 'SUB TOTAL PRICE');
            $sheet->getStyle("A$rowNum:G$rowNum")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2172B9'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $rowNum++;

            // --- PRODUCT ROWS ---
            if ($product_stmt = $db->prepare("SELECT * FROM Weight_Product WHERE weight_id=? AND deleted='0'")) {
                $product_stmt->bind_param('s', $row['id']);
                $product_stmt->execute();
                $result2 = $product_stmt->get_result();
                while ($row3 = $result2->fetch_assoc()) {
                    $sheet->fromArray([
                        $row3['product_name'], $row3['item_weight'], $row3['reduce_weight'],
                        $row3['total_weight'], $row3['unit_price'], $row3['total_price'], $row3['sub_total_price'] ?? ''
                    ], null, "A$rowNum");
                    $sheet->getStyle("A$rowNum:G$rowNum")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $rowNum++;
                }
                $product_stmt->close();
            }

            $rowNum += 1;

            // --- WEIGHING PERSON DETAIL ---
            $isManual = ($row['manual_weight'] == 'true' ? 'MANUAL WEIGHT' : 'AUTO WEIGHT');
            $sheet->setCellValue("A$rowNum", 'PIC USE ON');
            $sheet->setCellValue("B$rowNum", 'LAST LOGIN BY');
            $sheet->setCellValue("C$rowNum", 'STATUS');
            $sheet->setCellValue("D$rowNum", 'LAST LOGIN DATE / TIME');
            // Do NOT merge D:G here
            $sheet->getStyle("A$rowNum:D$rowNum")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2172B9'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $rowNum++;

            $sheet->setCellValue("A$rowNum", $row['created_by']);
            $sheet->setCellValue("B$rowNum", $row['modified_by']);
            $sheet->setCellValue("C$rowNum", $isManual);
            $sheet->setCellValue("D$rowNum", $row['modified_date']);
            // Do NOT merge D:G here
            $sheet->getStyle("A$rowNum:D$rowNum")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $rowNum += 3; // space before next block
        }
    } 
}else{ 
    if ($_GET['type'] == 'Weighing'){
        $excelData .= 'No records found...'. "\n"; 
    }else{
        $sheet->setCellValue("A1", "No records found...");
    }
} 

if ($_GET["type"] == 'Weighing'){
    // Headers for download 
    header("Content-Type: application/vnd.ms-excel"); 
    header("Content-Disposition: attachment; filename=\"$fileName\""); 
    
    // Render excel data 
    echo $excelData;
    
    exit;
}else{
    // Output
    $fileName = (isset($fileName) ? $fileName : "Report-data_" . date('Y-m-d') . ".xlsx");
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>