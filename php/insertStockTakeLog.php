<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';

session_start();

$id = $_SESSION['id'];
$startDate = date("Y-m-d 00:00:00");
$endDate = date("Y-m-d 23:59:59");

// Query Bitumen table to get all records where the declaration_datetime is today
$query = "SELECT * FROM Bitumen WHERE declaration_datetime >= '$startDate' AND declaration_datetime <= '$endDate' AND status = '0'";
$result = mysqli_query($db, $query);
$count = 0;
if ($result) {
    if (mysqli_num_rows($result) === 0) {
        ## If no records found for today, insert a new record with default values for all plants
        $declarationDateTime = date("Y-m-d H:i:s");
        $sixty_seventy_production = 0;
        $sixty_seventy_os = 0;
        $sixty_seventy_incoming = 0;
        $sixty_seventy_usage = 0;
        $sixty_seventy_bookstock = 0;
        $sixty_seventy_ps = 0;
        $sixty_seventy_diffstock = 0;
        $sixty_seventy_actual_usage = 0;
        $lfo_production = 0;
        $lfo_os = 0;
        $lfo_incoming = 0;
        $lfo_ps = 0;
        $lfo_usage = 0;
        $lfo_actual_usage = 0;
        $diesel_production = 0;
        $diesel_os = 0;
        $diesel_incoming = 0;
        $diesel_mreading = 0;
        $diesel_transport = 0;
        $diesel_ps = 0;
        $diesel_usage = 0;
        $diesel_actual_usage = 0;
        $plant = [26, 27, 28];
        $plantList = implode(',', $plant);

        // Loop through each plant
        foreach ($plant as $plantId) {
            // No records for today, get the latest Bitumen record for each plant before today
            $bitumenQuery = " SELECT * FROM Bitumen WHERE plant_id = '$plantId' AND declaration_datetime < '$startDate' ORDER BY declaration_datetime DESC LIMIT 1";
            $bitumenResult = mysqli_query($db, $bitumenQuery);
            $latestBitumenPerPlant = [];
            if ($bitumenResult) {
                while ($bitumenRow = mysqli_fetch_assoc($bitumenResult)) {
                    $latestBitumenPerPlant = $bitumenRow;
                }
            }

            if (!empty($latestBitumenPerPlant)){
                ### Process 60/70 Data ###
                $sixtySeventyData = json_decode($latestBitumenPerPlant['60/70'], true);
                if (!empty($sixtySeventyData)) {
                    $totalSixtySeventy = $sixtySeventyData['totalSixtySeventy'] ?? 0;
                } else{
                    $totalSixtySeventy = 0;
                }

                $sixty_seventy_os = (float) number_format($totalSixtySeventy,2) ?? 0.00;
                $sixty_seventy_bookstock = (float) number_format($totalSixtySeventy,2) ?? 0.00;
                $sixty_seventy_ps = (float) number_format($totalSixtySeventy,2) ?? 0.00;

                ### Process LFO Data ###
                $lfoData = json_decode($latestBitumenPerPlant['lfo'], true);
                if (!empty($lfoData)) {
                    $totalLfo = $lfoData['totalLfo'] ?? 0;
                } else {
                    $totalLfo = 0;
                }

                $lfo_os = (float) number_format($totalLfo,2) ?? 0.00;
                $lfo_ps = (float) number_format($totalLfo,2) ?? 0.00;


                ### Process Diesel Data ###
                $dieselData = json_decode($latestBitumenPerPlant['diesel'], true);
                if (!empty($dieselData)) {
                    $totalDiesel = $dieselData['totalDiesel'] ?? 0;
                } else {
                    $totalDiesel = 0;
                }
                
                $diesel_os = (float) number_format($totalDiesel,2) ?? 0.00;
                $diesel_ps = (float) number_format($totalDiesel,2) ?? 0.00;

            }
            
            // Insert into StockTakeLog table
            if ($insert_stmt = $db->prepare("INSERT INTO Stock_Take_Log (declaration_datetime, plant_id, sixty_seventy_production, sixty_seventy_os, sixty_seventy_incoming, sixty_seventy_usage, sixty_seventy_bookstock, sixty_seventy_ps, sixty_seventy_diffstock, sixty_seventy_actual_usage, lfo_production, lfo_os, lfo_incoming, lfo_ps, lfo_usage, lfo_actual_usage, diesel_production, diesel_os, diesel_incoming, diesel_mreading, diesel_transport, diesel_ps, diesel_usage, diesel_actual_usage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param("ssssssssssssssssssssssss", $declarationDateTime, $plantId, $sixty_seventy_production, $sixty_seventy_os, $sixty_seventy_incoming, $sixty_seventy_usage, $sixty_seventy_bookstock, $sixty_seventy_ps, $sixty_seventy_diffstock, $sixty_seventy_actual_usage, $lfo_production, $lfo_os, $lfo_incoming, $lfo_ps, $lfo_usage, $lfo_actual_usage, $diesel_production, $diesel_os, $diesel_incoming, $diesel_mreading, $diesel_transport, $diesel_ps, $diesel_usage, $diesel_actual_usage);

                // Execute the prepared query.
                if (! $insert_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> $update_stmt->error
                        )
                    );
                }else{
                    $insert_stmt->close();
                }
            }
        }
    }else{
        while ($row = mysqli_fetch_assoc($result)) { 
            // Process each row
            $plantId = $row['plant_id'];
            $plantCode = searchPlantCodeById($plantId, $db);
            $productCode = searchRawMatCodeById($plantId, $db);
            $declarationDateTime = $row['declaration_datetime'];

            // Get Production Weight
            $productionWeight = 0; // Default value if no production weight found
            $doQuery = "SELECT * FROM Weight WHERE is_complete = 'Y' AND is_cancel <> 'Y' AND transaction_status = 'Sales' AND plant_code = '$plantCode' AND tare_weight1_date >= '$startDate' AND tare_weight1_date <= '$endDate' AND status = '0'";
            $doResult = mysqli_query($db, $doQuery);
            if ($doResult) {
                while ($doRow = mysqli_fetch_assoc($doResult)) {
                    $productionWeight += $doRow['nett_weight1']/1000 ?? 0; // Sum up the order weights
                }
            }

            // 1. Get previous day's last record for ALL raw_mat_id in one query (MySQL 8+)
            $prevRawMatQuery = "
                SELECT *
                FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY raw_mat_id ORDER BY event_date DESC) as rn
                    FROM Inventory_Log
                    WHERE plant_id = '$plantId'
                    AND raw_mat_id IN (27,31,32)
                    AND event_date < '$startDate'
                ) t
                WHERE t.rn = 1
            ";
            $prevRawMatResult = mysqli_query($db, $prevRawMatQuery);
            $previousRawMaterials = [];
            if ($prevRawMatResult) {
                while ($prevRawMatRow = mysqli_fetch_assoc($prevRawMatResult)) {
                    $previousRawMaterials[$prevRawMatRow['raw_mat_id']]['previous_balance'] = (float) $prevRawMatRow['raw_mat_weight']/1000 ?? 0;
                }
            }

            // 2. Get today's latest record for all raw_mat_id in one query (MySQL 8+)
            $currRawMatQuery = "
                SELECT *
                FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY raw_mat_id ORDER BY event_date DESC) as rn
                    FROM Inventory_Log
                    WHERE plant_id = '$plantId'
                    AND raw_mat_id IN (27,31,32)
                    AND event_date >= '$startDate'
                    AND event_date <= '$endDate'
                ) t
                WHERE t.rn = 1
            ";
            $currRawMatResult = mysqli_query($db, $currRawMatQuery);
            $currentRawMaterials = [];
            if ($currRawMatResult) {
                while ($currRawMatRow = mysqli_fetch_assoc($currRawMatResult)) {
                    $currentRawMaterials[$currRawMatRow['raw_mat_id']]['current_balance'] = $currRawMatRow['raw_mat_weight']/1000 ?? 0;
                }
            }

            // Get previous day's P/S
            $previous6070PS = 0; 
            $previousLfoPS = 0; 
            $previousDieselPS = 0; 
            if ($prev_day_stmt = $db->prepare("SELECT * FROM Bitumen WHERE plant_id = ? AND declaration_datetime < ? ORDER BY declaration_datetime DESC LIMIT 1")) {
                $prev_day_stmt->bind_param("ss", $plantId, $declarationDateTime);
                $prev_day_stmt->execute();
                $prev_day_result = $prev_day_stmt->get_result();
                if ($prev_day_row = $prev_day_result->fetch_assoc()) {
                    $previous6070PS = json_decode($prev_day_row['60/70'], true)['totalSixtySeventy'] ?? 0;
                    $previousLfoPS = json_decode($prev_day_row['lfo'], true)['totalLfo'] ?? 0;
                    $previousDieselPS = json_decode($prev_day_row['diesel'], true)['totalDiesel'] ?? 0;
                } else {
                    $previous6070PS = 0; // Default value if no previous record found
                    $previousLfoPS = 0; // Default value if no previous record found
                    $previousDieselPS = 0; // Default value if no previous record found
                }
                $prev_day_stmt->close();
            } else {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> "Query failed: " . mysqli_error($db)
                    )
                );
            }

            $sixtySeventyData = json_decode($row['60/70'], true); 
            if (!empty($sixtySeventyData)) {
                // get total incoming for 60/70
                $sixtySeventyIncoming = 0;
                $sixtySeventyIncomingQuery = "SELECT * FROM Weight WHERE is_complete = 'Y' AND is_cancel <> 'Y' AND transaction_status = 'Purchase' AND plant_code = '$plantCode' AND tare_weight1_date >= '$startDate' AND tare_weight1_date <= '$endDate' AND raw_mat_code = 'BTBI001' AND status = '0'";
                $sixtySeventyIncomingResult = mysqli_query($db, $sixtySeventyIncomingQuery);
                if ($sixtySeventyIncomingResult) {
                    while ($sixtySeventyIncomingRow = mysqli_fetch_assoc($sixtySeventyIncomingResult)) {
                        $sixtySeventyIncoming += $sixtySeventyIncomingRow['supplier_weight']/1000 ?? 0; // Sum up the supplier weights
                    }
                }

                // get total usage for 60/70
                $sixty70PrevBalance = isset($previousRawMaterials[27]['previous_balance']) ? $previousRawMaterials[27]['previous_balance'] : 0;
                $sixty70CurrBalance = isset($currentRawMaterials[27]['current_balance']) ? $currentRawMaterials[27]['current_balance'] : 0;
                $sixtySeventyUsage = ($sixty70PrevBalance + $sixtySeventyIncoming) - $sixty70CurrBalance ?? 0; // Calculate usage in tonnes

                $sixty_seventy_production = (float) number_format($productionWeight, 2) ?? 0.00;
                $sixty_seventy_os = (float) number_format($previous6070PS, 2) ?? 0.00;
                $sixty_seventy_incoming = (float) number_format($sixtySeventyIncoming, 2) ?? 0.00;
                $sixty_seventy_usage = (float) number_format($sixtySeventyUsage, 2) ?? 0.00;
                $sixty_seventy_bookstock = (float) number_format($sixty_seventy_os + $sixty_seventy_incoming - $sixty_seventy_usage, 2) ?? 0.00;
                $sixty_seventy_ps = (float) number_format($sixtySeventyData['totalSixtySeventy'], 2) ?? 0.00;
                $sixty_seventy_diffstock = (float) number_format($sixty_seventy_ps - $sixty_seventy_bookstock, 2) ?? 0.00;
                $sixty_seventy_actual_usage = (float) number_format((($sixty_seventy_os + $sixty_seventy_incoming) - $sixty_seventy_ps)/($sixty_seventy_production ?: 1) * 100, 2) ?? 0.00;
            } else {
                // Default values if data is empty
                $sixty_seventy_production = (float) number_format($productionWeight, 2) ?? 0;
                $sixty_seventy_os = (float) number_format($previous6070PS, 2) ?? 0;
                $sixty_seventy_incoming = 0;
                $sixty_seventy_usage = 0;
                $sixty_seventy_bookstock = (float) number_format($previous6070PS, 2) ?? 0;
                $sixty_seventy_ps = (float) number_format($previous6070PS, 2) ?? 0;
                $sixty_seventy_diffstock = 0;
                $sixty_seventy_actual_usage = 0;
            }

            $lfoData = json_decode($row['lfo'], true);
            if (!empty($lfoData)) {
                // get total incoming for lfo
                $lfoIncoming = 0;
                $lfoIncomingQuery = "SELECT * FROM Weight WHERE is_complete = 'Y' AND is_cancel <> 'Y' AND transaction_status = 'Purchase' AND plant_code = '$plantCode' AND tare_weight1_date >= '$startDate' AND tare_weight1_date <= '$endDate' AND raw_mat_code = 'LFFO001' AND status = '0'";
                $lfoIncomingResult = mysqli_query($db, $lfoIncomingQuery);
                if ($lfoIncomingResult) {
                    while ($lfoIncomingRow = mysqli_fetch_assoc($lfoIncomingResult)) {
                        $lfoIncoming += $lfoIncomingRow['supplier_weight']/1000 ?? 0; // Sum up the supplier weights
                    }
                } 

                $lfo_production = (float) number_format($productionWeight, 2) ?? 0;
                $lfo_os = (float) number_format($previousLfoPS, 2) ?? 0;
                $lfo_incoming = (float) number_format($lfoIncoming, 2) ?? 0;
                $lfo_ps = (float) number_format($lfoData['totalLfo'], 2) ?? 0;
                $lfo_usage = (float) number_format(($lfo_os + $lfo_incoming - $lfo_ps), 2) ?? 0;
                $lfo_actual_usage = (float) number_format(($lfo_usage/($lfo_production ?: 1)), 2) ?? 0;
            } else {
                // Default values if data is empty
                $lfo_production = (float) number_format($productionWeight, 2) ?? 0;
                $lfo_os = (float) number_format($previousLfoPS, 2) ?? 0;
                $lfo_incoming = 0;
                $lfo_ps = (float) number_format($previousLfoPS, 2) ?? 0;
                $lfo_usage = 0;
                $lfo_actual_usage = 0;
            }

            $dieselData = json_decode($row['diesel'], true);
            if (!empty($dieselData)) {
                // get total incoming for lfo
                $dieselIncoming = 0;
                $dieselIncomingQuery = "SELECT * FROM Weight WHERE is_complete = 'Y' AND is_cancel <> 'Y' AND transaction_status = 'Purchase' AND plant_code = '$plantCode' AND tare_weight1_date >= '$startDate' AND tare_weight1_date <= '$endDate' AND raw_mat_code = 'DIE001' AND status = '0'";
                $dieselIncomingResult = mysqli_query($db, $dieselIncomingQuery);
                if ($dieselIncomingResult) {
                    while ($dieselIncomingRow = mysqli_fetch_assoc($dieselIncomingResult)) {
                        $dieselIncoming += $dieselIncomingRow['supplier_weight']/1000 ?? 0; // Sum up the supplier weights
                    }
                } 

                $diesel_production = (float) number_format($productionWeight, 2) ?? 0;
                $diesel_os = (float) number_format($previousDieselPS, 2) ?? 0;
                $diesel_incoming = (float) number_format($dieselIncoming, 2) ?? 0;
                $diesel_mreading = 0;
                $diesel_transport = 0;
                $diesel_ps = (float) number_format($dieselData['totalDiesel'], 2) ?? 0;
                $diesel_usage = (float) number_format(($diesel_os + $diesel_incoming - $diesel_ps), 2) ?? 0;
                $diesel_actual_usage = (float) number_format(($diesel_usage/$diesel_production), 2) ?? 0;
            } else {
                // Default values if data is empty
                $diesel_production = (float) number_format($productionWeight. 2) ?? 0;
                $diesel_os = (float) number_format($previousDieselPS, 2) ?? 0;
                $diesel_incoming = 0;
                $diesel_mreading = 0;
                $diesel_transport = 0;
                $diesel_ps = (float) number_format($previousDieselPS, 2) ?? 0;
                $diesel_usage = 0;
                $diesel_actual_usage = 0;
            }

            // Insert into StockTakeLog table
            if ($insert_stmt = $db->prepare("INSERT INTO Stock_Take_Log (declaration_datetime, plant_id, sixty_seventy_production, sixty_seventy_os, sixty_seventy_incoming, sixty_seventy_usage, sixty_seventy_bookstock, sixty_seventy_ps, sixty_seventy_diffstock, sixty_seventy_actual_usage, lfo_production, lfo_os, lfo_incoming, lfo_ps, lfo_usage, lfo_actual_usage, diesel_production, diesel_os, diesel_incoming, diesel_mreading, diesel_transport, diesel_ps, diesel_usage, diesel_actual_usage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param("ssssssssssssssssssssssss", $declarationDateTime, $plantId, $sixty_seventy_production, $sixty_seventy_os, $sixty_seventy_incoming, $sixty_seventy_usage, $sixty_seventy_bookstock, $sixty_seventy_ps, $sixty_seventy_diffstock, $sixty_seventy_actual_usage, $lfo_production, $lfo_os, $lfo_incoming, $lfo_ps, $lfo_usage, $lfo_actual_usage, $diesel_production, $diesel_os, $diesel_incoming, $diesel_mreading, $diesel_transport, $diesel_ps, $diesel_usage, $diesel_actual_usage);

                // Execute the prepared query.
                if (! $insert_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> $update_stmt->error
                        )
                    );
                }else{
                    $insert_stmt->close();
                }
            }
        }
    }
    $db->close();
} else {
    // Handle query error
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Query failed: " . mysqli_error($db)
        )
    );
}

?>