<?php

require_once 'db_connect.php';
require_once 'requires/lookup.php';
// // Load the database configuration file 
session_start();
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 

function formatDate($date) {
    $formatted = date('d/m/Y', strtotime($date));

    return $formatted;
}

// Excel file name for download 
if($_GET["plant"]){
    $plantName = searchPlantNameById($_GET["plant"], $db);

    if($_GET['rawMaterial'] != null && $_GET['rawMaterial'] != '' && $_GET['rawMaterial'] != '-'){
        $rawMatName = searchRawMatNameById($_GET["rawMaterial"], $db);
        $fileName = $plantName."-".$rawMatName."_" . date('Y-m-d') . ".xls";
    } else {
        $fileName = $plantName."_" . date('Y-m-d') . ".xls";
    }
}

## Search 
$searchQuery = " ";
if($_GET['fromDateSearch'] != null && $_GET['fromDateSearch'] != ''){
    $fromDate = new DateTime($_GET['fromDateSearch']);
    $fromDateTime = date_format($fromDate,"Y-m-d 00:00:00");
    $searchQuery = " WHERE declaration_datetime >= '".$fromDateTime."'";
}

if($_GET['toDateSearch'] != null && $_GET['toDateSearch'] != ''){
    $toDate = new DateTime($_GET['toDateSearch']);
    $toDateTime = date_format($toDate,"Y-m-d 23:59:59");
    $searchQuery .= " and declaration_datetime <= '".$toDateTime."'";
}

if($_GET['plant'] != null && $_GET['plant'] != '' && $_GET['plant'] != '-'){
	$searchQuery .= " and plant_id = '".$_GET['plant']."'";
}

$sql = "select * from Stock_Take_Log".$searchQuery;
$empRecords = mysqli_query($db, $sql);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
    if ($_GET['rawMaterial'] == '27') {
        $data[] = array( 
            "id"=>$row['id'],
            "Date"=>formatDate($row['declaration_datetime']),
            "Production"=>$row['sixty_seventy_production'],
            "O/S"=>$row['sixty_seventy_os'],
            "Incoming"=>$row['sixty_seventy_incoming'],
            "Usage"=>$row['sixty_seventy_usage'],
            "Book Stock"=>$row['sixty_seventy_bookstock'],
            "P/S"=>$row['sixty_seventy_ps'],
            "Diff Stock"=>$row['sixty_seventy_diffstock'],
            "Actual Bit Usage (%)"=>$row['sixty_seventy_actual_usage'],
        );

        $columnNames = ['Date', 'Production', 'O/S', 'Incoming', 'Usage', 'Book Stock', 'P/S', 'Diff Stock', 'Actual Bit Usage (%)'];
    } elseif ($_GET['rawMaterial'] == '32') {
        $data[] = array( 
            "id"=>$row['id'],
            "Date"=>formatDate($row['declaration_datetime']),
            "Production"=>$row['lfo_production'],
            "O/S"=>$row['lfo_os'],
            "Incoming"=>$row['lfo_incoming'],
            "Usage"=>$row['lfo_usage'],
            "Actual LFO Usage (%)"=>$row['lfo_actual_usage']
        );

        $columnNames = ['Date', 'Production', 'O/S', 'Incoming', 'Usage', 'Actual LFO Usage (%)'];
    } elseif ($_GET['rawMaterial'] == '31'){
        $data[] = array( 
            "id"=>$row['id'],
            "Date"=>formatDate($row['declaration_datetime']),
            "Production"=>$row['diesel_production'],
            "O/S"=>$row['diesel_os'],
            "Incoming"=>$row['diesel_incoming'],
            "M.Reading"=>$row['diesel_mreading'],
            "Transport"=>$row['diesel_transport'],
            "P/S"=>$row['diesel_ps'],
            "Actual Burner Usage"=>$row['diesel_usage'],
            "Actual Diesel Usage (%)"=>$row['diesel_actual_usage'],
        );

        $columnNames = ['Date', 'Production', 'O/S', 'Incoming', 'M.Reading', 'Transport', 'P/S', 'Actual Burner Usage', 'Actual Diesel Usage (%)'];
    } else {
        $columnNames = ['Date', 'Production', 'O/S', 'Incoming', 'Usage', 'Book Stock', 'P/S', 'Diff Stock', 'Actual Bit Usage (%)', ' ', 'Date', 'Production', 'O/S', 'Incoming', 'Usage', 'Actual LFO Usage (%)', ' ', 'Date', 'Production', 'O/S', 'Incoming', 'M.Reading', 'Transport', 'P/S', 'Actual Burner Usage', 'Actual Diesel Usage (%)'];

        $date = formatDate($row['declaration_datetime']);
        $data[] = array( 
            "id"=>$row['id'],
            "Bitumen Date"=>$date,
            "Production"=>$row['sixty_seventy_production'],
            "O/S"=>$row['sixty_seventy_os'],
            "Incoming"=>$row['sixty_seventy_incoming'],
            "Usage"=>$row['sixty_seventy_usage'],
            "Book Stock"=>$row['sixty_seventy_bookstock'],
            "P/S"=>$row['sixty_seventy_ps'],
            "Diff Stock"=>$row['sixty_seventy_diffstock'],
            "Actual Bit Usage (%)"=>$row['sixty_seventy_actual_usage'],
            "Bitumen Space"=>" ",
            "LFO Date"=>$date,
            "LFO Production"=>$row['lfo_production'],
            "LFO O/S"=>$row['lfo_os'],
            "LFO Incoming"=>$row['lfo_incoming'],
            "LFO Usage"=>$row['lfo_usage'],
            "LFO Actual LFO Usage (%)"=>$row['lfo_actual_usage'],
            "Diesel Space"=>" ",
            "Diesel Date"=>$date,
            "Diesel Production"=>$row['diesel_production'],
            "Diesel O/S"=>$row['diesel_os'],
            "Diesel Incoming"=>$row['diesel_incoming'],
            "Diesel M.Reading"=>$row['diesel_mreading'],
            "Diesel Transport"=>$row['diesel_transport'],
            "Diesel P/S"=>$row['diesel_ps'],
            "Diesel Actual Burner Usage"=>$row['diesel_usage'],
            "Diesel Actual Diesel Usage (%)"=>$row['diesel_actual_usage'],
        );
    }
}

if (!empty($data)) {
    if ($_GET['rawMaterial'] == '27') {
        // Footer Header Fields
        $data[] = array( 
            "Month"=>"Month",
            "Production"=>"Production",
            "O/S"=>"O/S",
            "Incoming"=>"Incoming",
            "P/S"=>"P/S",
            "Usage"=>"Usage",
            "% usage / ton (+/-)"=>"% usage / ton (+/-)"
        );

        $totalProduction = 0;
        $totalOS = 0;
        $totalIncoming = 0;
        $totalPS = 0;
        $totalUsage = 0;
        
        foreach ($data as $row) {
            $totalProduction += (float) $row['Production'];
            $totalOS += (float) $row['O/S'];
            $totalIncoming += (float) $row['Incoming'];
            $totalPS += (float) $row['P/S'];
            $totalUsage += (float) $row['Usage'];
        }

        $data[] = array( 
            "Month Value"=>'MARCH',
            "Production Value"=>$totalProduction,
            "O/S Value"=>$totalOS,
            "Incoming Value"=>$totalIncoming,
            "P/S Value"=>$totalPS,
            "Usage Value"=>$totalUsage,
        );
    } elseif ($_GET['rawMaterial'] == '32') {
        $data[] = array( 
            "id"=>$row['id'],
            "Date"=>formatDate($row['declaration_datetime']),
            "Production"=>$row['lfo_production'],
            "O/S"=>$row['lfo_os'],
            "Incoming"=>$row['lfo_incoming'],
            "Usage"=>$row['lfo_usage'],
            "Actual LFO Usage (%)"=>$row['lfo_actual_usage']
        );
    } elseif ($_GET['rawMaterial'] == '31'){
        $data[] = array( 
            "id"=>$row['id'],
            "Date"=>formatDate($row['declaration_datetime']),
            "Production"=>$row['diesel_production'],
            "O/S"=>$row['diesel_os'],
            "Incoming"=>$row['diesel_incoming'],
            "M.Reading"=>$row['diesel_mreading'],
            "Transport"=>$row['diesel_transport'],
            "P/S"=>$row['diesel_ps'],
            "Actual Burner Usage"=>$row['diesel_usage'],
            "Actual Diesel Usage (%)"=>$row['diesel_actual_usage'],
        );
    } else {
        $date = formatDate($row['declaration_datetime']);
        $data[] = array( 
            "id"=>$row['id'],
            "Bitumen Date"=>$date,
            "Production"=>$row['sixty_seventy_production'],
            "O/S"=>$row['sixty_seventy_os'],
            "Incoming"=>$row['sixty_seventy_incoming'],
            "Usage"=>$row['sixty_seventy_usage'],
            "Book Stock"=>$row['sixty_seventy_bookstock'],
            "P/S"=>$row['sixty_seventy_ps'],
            "Diff Stock"=>$row['sixty_seventy_diffstock'],
            "Actual Bit Usage (%)"=>$row['sixty_seventy_actual_usage'],
            "Bitumen Space"=>" ",
            "LFO Date"=>$date,
            "LFO Production"=>$row['lfo_production'],
            "LFO O/S"=>$row['lfo_os'],
            "LFO Incoming"=>$row['lfo_incoming'],
            "LFO Usage"=>$row['lfo_usage'],
            "LFO Actual LFO Usage (%)"=>$row['lfo_actual_usage'],
            "Diesel Space"=>" ",
            "Diesel Date"=>$date,
            "Diesel Production"=>$row['diesel_production'],
            "Diesel O/S"=>$row['diesel_os'],
            "Diesel Incoming"=>$row['diesel_incoming'],
            "Diesel M.Reading"=>$row['diesel_mreading'],
            "Diesel Transport"=>$row['diesel_transport'],
            "Diesel P/S"=>$row['diesel_ps'],
            "Diesel Actual Burner Usage"=>$row['diesel_usage'],
            "Diesel Actual Diesel Usage (%)"=>$row['diesel_actual_usage'],
        );
    }
}

// Display Headers Fields
$excelData = implode("\t", array_values(['BLACKTOP LANCHANG S/B'])) . "\n";
$excelData .= implode("\t", array_values([$plantName.' DRUM PLANT', '','','PHYSICAL STOCK FOR '])) . "\n";
$excelData .= implode("\t", array_values(['Bitumen (60/70)','','','','','','','','','','LFO', 'PENTAS FLORA','','','','','','BD'])) . "\n\n";
// $excelData = implode("\t", array_values([''])) . "\n";

// Display column names as first row 
$excelData .= implode("\t", array_values($columnNames)) . "\n";

if(count($data) > 0){
    foreach ($data as $row){
        unset($row['id']);
        $lineData = []; // Ensure it starts as an empty array each iteration

        foreach ($row as $rowData) { 
            $lineData[] = $rowData; 
        }

        # Added checking to fix duplicated issue
        if (!empty($lineData)) {
            array_walk($lineData, 'filterData'); 
            $excelData .= implode("\t", array_values($lineData)) . "\n"; 
        }
    }
}else{
    $excelData .= 'No records found...'. "\n"; 
}
 
// Headers for download 
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData;
 
exit;
?>