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

$months = [];

if (!empty($_GET['fromDateSearch']) && !empty($_GET['toDateSearch'])) {
    $start = new DateTime($_GET['fromDateSearch']);
    $start->modify('first day of this month');
    $end = new DateTime($_GET['toDateSearch']);
    $end->modify('first day of next month'); // make the period inclusive

    $interval = new DateInterval('P1M');
    $period = new DatePeriod($start, $interval, $end);

    foreach ($period as $dt) {
        $months[] = strtoupper($dt->format('F'));
    }
}

$sql = "select * from Stock_Take".$searchQuery;
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
            "P/S"=>$row['lfo_ps'],
            "Usage"=>$row['lfo_usage'],
            "Actual LFO Usage (%)"=>$row['lfo_actual_usage']
        );

        $columnNames = ['Date', 'Production', 'O/S', 'Incoming', 'P/S', 'Usage', 'Actual LFO Usage (%)'];
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
        $columnNames = ['Date', 'Production', 'O/S', 'Incoming', 'Usage', 'Book Stock', 'P/S', 'Diff Stock', 'Actual Bit Usage (%)', ' ', 'Date', 'Production', 'O/S', 'Incoming', 'M.Reading', 'Transport', 'P/S', 'Actual Burner Usage', 'Actual Diesel Usage (%)'];

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
            "Diesel Date"=>$date,
            "Diesel Production"=>$row['diesel_production'],
            "Diesel O/S"=>$row['diesel_os'],
            "Diesel Incoming"=>$row['diesel_incoming'],
            "Diesel M.Reading"=>$row['diesel_mreading'],
            "Diesel Transport"=>$row['diesel_transport'],
            "Diesel P/S"=>$row['diesel_ps'],
            "Diesel Actual Burner Usage"=>$row['diesel_usage'],
            "Diesel Actual Diesel Usage (%)"=>$row['diesel_actual_usage'],
            // Store LFO data separately for table generation
            "LFO Production"=>$row['lfo_production'],
            "LFO O/S"=>$row['lfo_os'],
            "LFO Incoming"=>$row['lfo_incoming'],
            "LFO P/S"=>$row['lfo_ps'],
            "LFO Usage"=>$row['lfo_usage'],
            "LFO Actual LFO Usage (%)"=>$row['lfo_actual_usage'],
        );
    }
}

if (!empty($data)) {
    if ($_GET['rawMaterial'] == '27') {
        $totalProduction = 0;
        $totalOS = (float) str_replace(',', '', $data[0]['O/S']) ?? 0;
        $totalIncoming = 0;
        $totalPS = (float) str_replace(',', '', end($data)['P/S']) ?? 0; 
        $totalUsage = 0;
        
        foreach ($data as $row) {
            $totalProduction += (float) str_replace(',', '', $row['Production']);
            $totalIncoming += (float) str_replace(',', '', $row['Incoming']);
            $totalUsage += (float) str_replace(',', '', $row['Usage']);
        } 

        $firstTotalUsage = $totalOS + $totalIncoming - $totalPS;
        $secondTotalUsage = $totalUsage;
        $firstUsageTon = ($firstTotalUsage / $totalProduction)*100;
        $secondUsageTon = ($secondTotalUsage / $totalProduction)*100;
        $diffStock = $secondTotalUsage - $firstTotalUsage;

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

        $data[] = array( 
            "Month Value"=>'MARCH',
            "Production Value"=>$totalProduction,
            "O/S Value"=>$totalOS,
            "Incoming Value"=>$totalIncoming,
            "P/S Value"=>$totalPS,
            "Usage Value 1"=>$firstTotalUsage,
            "Usage Ton 1"=>$firstUsageTon,
            "Diff Stock Value"=>$diffStock,
        );

        $data[] = array( 
            "Month Value2"=>'',
            "Production Value2"=>'',
            "O/S Value2"=>'',
            "Incoming Value2"=>'',
            "P/S Value2"=>'',
            "Usage Value 2"=>$secondTotalUsage,
            "Usage Ton 2"=>$secondUsageTon,
        );
    } elseif ($_GET['rawMaterial'] == '32') {
        // Determine how many LFO sets exist by looking at first row
        $sample = json_decode($data[0]['Production'], true);
        $lfoCount = is_array($sample) ? count($sample) : 1;

        // Build datasets for each LFO index
        $tables = [];
        for ($i = 0; $i < $lfoCount; $i++) {
            $lfoData = [];
            foreach ($data as $row) {
                $lfoRow = [];
                $lfoRow['Date'] = $row['Date'];
                $lfoRow['Production'] = json_decode($row['Production'], true)[$i] ?? 0;
                $lfoRow['O/S'] = json_decode($row['O/S'], true)[$i] ?? 0;
                $lfoRow['Incoming'] = json_decode($row['Incoming'], true)[$i] ?? 0;
                $lfoRow['P/S'] = json_decode($row['P/S'], true)[$i] ?? 0;
                $lfoRow['Usage'] = json_decode($row['Usage'], true)[$i] ?? 0;
                $lfoRow['Actual LFO Usage (%)'] = json_decode($row['Actual LFO Usage (%)'], true)[$i] ?? 0;

                $lfoData[] = $lfoRow;
            }
            $tables["LFO (" . ($i+1) . ")"] = $lfoData;
        }

        $excelData = $tables;
    } elseif ($_GET['rawMaterial'] == '31'){
        $totalProduction = 0;
        $totalOS = (float) str_replace(',', '', $data[0]['O/S']) ?? 0;
        $totalIncoming = 0;
        $totalPS = (float) str_replace(',', '', end($data)['P/S']) ?? 0; 
        $totalTransport = 0;
        $totalActualBurnerUsage = 0;
        
        foreach ($data as $row) {
            $totalProduction += (float) str_replace(',', '', $row['Production']);
            $totalIncoming += (float) str_replace(',', '', $row['Incoming']);
            $totalTransport += (float) str_replace(',', '', $row['Transport']);
            $totalActualBurnerUsage += (float) str_replace(',', '', $row['Actual Burner Usage']);
        }

        $usage_day = $totalActualBurnerUsage/31;

        $data[] = array( 
            "Month"=>"Month",
            "Production"=>"Production",
            "O/S"=>"O/S",
            "Incoming"=>"Incoming",
            "P/S"=>"P/S",
            "Shovel Usage"=>"Shovel Usage",
            "Roadcare"=>"Roadcare",
            "Burner Usage"=>"Burner Usage",
            "Usage/Day"=>"Usage/Day",
        );

        $data[] = array( 
            "Month Value"=>$months[0],
            "Production Value"=>$totalProduction,
            "O/S Value"=>$totalOS,
            "Incoming Value"=>$totalIncoming,
            "P/S Value"=>$totalPS,
            "Shovel Usage"=>$totalTransport,
            "Roadcare"=>0,
            "Burner Usage"=>$totalActualBurnerUsage,
            "Usage/Day"=>$usage_day
        );
    } else {
        // 60/70
        $totalProduction = 0;
        $totalOS = (float) str_replace(',', '', $data[0]['O/S']) ?? 0;
        $totalIncoming = 0;
        $totalPS = (float) str_replace(',', '', end($data)['P/S']) ?? 0; 
        $totalUsage = 0;

        // Diesel
        $totalDieselProduction = 0;
        $totalDieselOS = (float) str_replace(',', '', $data[0]['Diesel O/S']) ?? 0;
        $totalDieselIncoming = 0;
        $totalDieselPS = (float) str_replace(',', '', end($data)['Diesel P/S']) ?? 0; 
        $totalDieselTransport = 0;
        $totalDieselActualBurnerUsage = 0;

        // Determine LFO count from first row
        $firstLfoData = json_decode($data[0]['LFO Production'], true) ?: [];
        $lfoCount = count($firstLfoData);

        // Build LFO datasets for each LFO index (similar to LFO-only export)
        $lfoTables = [];
        for ($i = 0; $i < $lfoCount; $i++) {
            $lfoData = [];
            foreach ($data as $row) {
                $lfoRow = [];
                $lfoRow['Date'] = $row['Bitumen Date'];
                $lfoRow['Production'] = json_decode($row['LFO Production'], true)[$i] ?? 0;
                $lfoRow['O/S'] = json_decode($row['LFO O/S'], true)[$i] ?? 0;
                $lfoRow['Incoming'] = json_decode($row['LFO Incoming'], true)[$i] ?? 0;
                $lfoRow['P/S'] = json_decode($row['LFO P/S'], true)[$i] ?? 0;
                $lfoRow['Usage'] = json_decode($row['LFO Usage'], true)[$i] ?? 0;
                $lfoRow['Actual LFO Usage (%)'] = json_decode($row['LFO Actual LFO Usage (%)'], true)[$i] ?? 0;

                $lfoData[] = $lfoRow;
            }
            $lfoTables["LFO (" . ($i+1) . ")"] = $lfoData;
        }
        // $totalLfoProduction = 0;
        // $totalLfoOS = (float) str_replace(',', '', $data[0]['LFO O/S']) ?? 0;
        // $totalLfoIncoming = 0;
        // $totalLfoPS = (float) str_replace(',', '', end($data)['LFO P/S']) ?? 0; 
        // $totalLfoUsage = 0;

        foreach ($data as $row) {
            $totalProduction += (float) str_replace(',', '', $row['Production']);
            $totalIncoming += (float) str_replace(',', '', $row['Incoming']);
            $totalUsage += (float) str_replace(',', '', $row['Usage']);
            $totalDieselProduction += (float) str_replace(',', '', $row['Diesel Production']);
            $totalDieselIncoming += (float) str_replace(',', '', $row['Diesel Incoming']);
            $totalDieselTransport += (float) str_replace(',', '', $row['Diesel Transport']);
            $totalDieselActualBurnerUsage += (float) str_replace(',', '', $row['Diesel Actual Burner Usage']);
        } 

        // 60/70 calculations
        $firstTotalUsage = $totalOS + $totalIncoming - $totalPS;
        $secondTotalUsage = $totalUsage;
        $firstUsageTon = $totalProduction > 0 ? ($firstTotalUsage / $totalProduction) * 100 : 0;
        $secondUsageTon = $totalProduction > 0 ? ($secondTotalUsage / $totalProduction) * 100 : 0;
        $diffStock = $secondTotalUsage - $firstTotalUsage;

        // Diesel calculations
        $diesel_usage_day = $totalDieselActualBurnerUsage/31;

        // Footer Header Fields
        $data[] = array( 
            "Month"=>"Month",
            "Production"=>"Production",
            "O/S"=>"O/S",
            "Incoming"=>"Incoming",
            "P/S"=>"P/S",
            "Usage"=>"Usage",
            "% usage / ton (+/-)"=>"% usage / ton (+/-)",
            "empty space"=>" ",
            "empty space2"=>" ",
            "empty space3"=>" ",
            "Diesel Month"=>"Month",
            "Diesel Production"=>"Production",
            "Diesel O/S"=>"O/S",
            "Diesel Incoming"=>"Incoming",
            "Diesel P/S"=>"P/S",
            "Diesel Shovel Usage"=>"Shovel Usage",
            "Diesel Roadcare"=>"Roadcare",
            "Diesel Burner Usage"=>"Burner Usage",
            "Diesel Usage/Day"=>"Usage/Day",
        );

        $data[] = array( 
            "Month Value"=>$months[0],
            "Production Value"=>$totalProduction,
            "O/S Value"=>$totalOS,
            "Incoming Value"=>$totalIncoming,
            "P/S Value"=>$totalPS,
            "Usage Value 1"=>$firstTotalUsage,
            "Usage Ton 1"=>$firstUsageTon,
            "Diff Stock Value"=>$diffStock,
            "value empty space"=>'',
            "value empty space2"=>'',
            "Diesel Month Value"=>$months[0],
            "Diesel Production Value"=>$totalDieselProduction,
            "Diesel O/S Value"=>$totalDieselOS,
            "Diesel Incoming Value"=>$totalDieselIncoming,
            "Diesel P/S Value"=>$totalDieselPS,
            "Diesel Shovel Usage"=>$totalDieselTransport,
            "Diesel Roadcare"=>0,
            "Diesel Burner Usage"=>$totalDieselActualBurnerUsage,
            "Diesel Usage/Day"=>$diesel_usage_day
        );

        $data[] = array( 
            "Month Value2"=>'',
            "Production Value2"=>'',
            "O/S Value2"=>'',
            "Incoming Value2"=>'',
            "P/S Value2"=>'',
            "Usage Value 2"=>$secondTotalUsage,
            "Usage Ton 2"=>$secondUsageTon,
        );
    }
}

// Display Headers Fields
$excelData = implode("\t", array_values(['BLACKTOP LANCHANG S/B'])) . "\n";
$excelData .= implode("\t", array_values([$plantName.' DRUM PLANT', '','','PHYSICAL STOCK FOR '.$months[0]])) . "\n";

if ($_GET['rawMaterial'] == '27') {
    $excelData .= implode("\t", array_values(['BITUMEN (60/70)'])) . "\n\n";
} elseif ($_GET['rawMaterial'] == '31') {
    $excelData .= implode("\t", array_values(['DIESEL'])) . "\n\n";
} else {
    $excelData .= implode("\t", array_values(['Bitumen (60/70)','','','','','','','','','','Diesel'])) . "\n\n";
}

// Display column names as first row 
$excelData .= implode("\t", array_values($columnNames)) . "\n";

if(count($data) > 0){
    if ($_GET['rawMaterial'] == '32') {
        // Handle LFO case with multiple tables
        $excelData = implode("\t", array_values(['BLACKTOP LANCHANG S/B'])) . "\n";
        $excelData .= implode("\t", array_values([$plantName.' DRUM PLANT', '','','PHYSICAL STOCK FOR '.$months[0]])) . "\n";
        $excelData .= implode("\t", array_values(['LFO'])) . "\n\n";

        // Process each LFO table
        foreach ($tables as $tableName => $tableData) {
            $excelData .= $tableName . "\n";
            $excelData .= implode("\t", array_values($columnNames)) . "\n";
            
            // Add table data
            foreach ($tableData as $row) {
                $lineData = [];
                foreach ($row as $cellData) {
                    $lineData[] = $cellData;
                }
                
                if (!empty($lineData)) {
                    array_walk($lineData, 'filterData');
                    $excelData .= implode("\t", array_values($lineData)) . "\n";
                }
            }
            
            $excelData .= "\n";
        }
    }else {
        // Handle other cases including combined materials
        foreach ($data as $row){
            unset($row['id']);
            
            // For combined materials, remove LFO data from main export since it will be in separate tables
            if (empty($_GET['rawMaterial']) || $_GET['rawMaterial'] == '' || $_GET['rawMaterial'] == '-') {
                unset($row['LFO Production']);
                unset($row['LFO O/S']);
                unset($row['LFO Incoming']);
                unset($row['LFO P/S']);
                unset($row['LFO Usage']);
                unset($row['LFO Actual LFO Usage (%)']);
            }
            
            $lineData = [];

            foreach ($row as $rowData) { 
                $lineData[] = $rowData; 
            }

            if (!empty($lineData)) {
                array_walk($lineData, 'filterData'); 
                $excelData .= implode("\t", array_values($lineData)) . "\n"; 
            }
        }
        
        // For combined materials, add LFO tables at the end
        if (empty($_GET['rawMaterial']) || $_GET['rawMaterial'] == '' || $_GET['rawMaterial'] == '-') {
            $excelData .= "\n\n";
            foreach ($lfoTables as $tableName => $tableData) {
                $excelData .= $tableName . "\t" . "PENTAS FLORA" . "\n";
                $excelData .= implode("\t", array_values(['Date', 'Production', 'O/S', 'Incoming', 'P/S', 'Usage', 'Actual LFO Usage (%)'])) . "\n";
                
                foreach ($tableData as $row) {
                    $lineData = [];
                    foreach ($row as $cellData) {
                        $lineData[] = $cellData;
                    }
                    
                    if (!empty($lineData)) {
                        array_walk($lineData, 'filterData');
                        $excelData .= implode("\t", array_values($lineData)) . "\n";
                    }
                }
                
                $excelData .= "\n";
            }
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