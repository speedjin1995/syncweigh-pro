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
        $plant = [27, 31, 32];

        // Loop through each plant
        foreach ($plant as $plantId) {
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

            // get Inventory for all raw materials
            $rawMatQuery = "SELECT * FROM Inventory WHERE plant_id = '$plantId' AND raw_mat_id IN (27,31,32) AND status = '0'";
            $rawMatResult = mysqli_query($db, $rawMatQuery);
            $rawMaterials = array();
            if ($rawMatResult) {
                while ($rawMatRow = mysqli_fetch_assoc($rawMatResult)) {
                    $rawMaterials[$rawMatRow['raw_mat_id']] = $rawMatRow;
                }
            }

            $sixtySeventyData = json_decode($row['60/70'], true);
            if (!empty($sixtySeventyData)) {
                // get total incoming for 60/70
                $sixtySeventyIncoming = 0;
                $sixtySeventyIncomingQuery = "SELECT * FROM Purchase_Order WHERE raw_mat_code = 'BTBI001' AND plant_code = '$plantCode' AND order_date >= '$startDate' AND order_date <= '$endDate' AND deleted = '0'";
                $sixtySeventyIncomingResult = mysqli_query($db, $sixtySeventyIncomingQuery);
                if ($sixtySeventyIncomingResult) {
                    while ($sixtySeventyIncomingRow = mysqli_fetch_assoc($sixtySeventyIncomingResult)) {
                        $sixtySeventyIncoming += $sixtySeventyIncomingRow['converted_order_qty']/1000 ?? 0; // Sum up the order weights
                    }
                }

                // get total usage for 60/70
                $sixtySeventyUsage = 0;
                if (!empty($rawMaterials[27]['raw_mat_basic_uom'])){
                    $sixtySeventyUsage = $rawMaterials[27]['raw_mat_basic_uom'] - $sixtySeventyIncoming ?? 0;
                }          

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
                $sixty_seventy_production = $productionWeight ?? 0;
                $sixty_seventy_os = $previous6070PS ?? 0;
                $sixty_seventy_incoming = 0;
                $sixty_seventy_usage = 0;
                $sixty_seventy_bookstock = $previous6070PS ?? 0;
                $sixty_seventy_ps = $previous6070PS ?? 0;
                $sixty_seventy_diffstock = 0;
                $sixty_seventy_actual_usage = 0;
            }

            $lfoData = json_decode($row['lfo'], true); 
            if (!empty($lfoData)) {
                $lfo_production = (float) $productionWeight ?? 0;
                $lfo_os = (float) $previousLfoPS ?? 0;
                $lfo_incoming = (float) $lfoData['incoming'] ?? 0;
                $lfo_ps = (float) $lfoData['totalLfo'] ?? 0;
                $lfo_usage = $lfo_os + $lfo_incoming - $lfo_ps ?? 0;
                $lfo_actual_usage = $lfo_ps/($lfo_production ?: 1) ?? 0;
            } else {
                // Default values if data is empty
                $lfo_production = $productionWeight ?? 0;
                $lfo_os = 0;
                $lfo_incoming = 0;
                $lfo_ps = 0;
                $lfo_usage = 0;
                $lfo_actual_usage = 0;
            }

            $dieselData = json_decode($row['diesel'], true);
            if (!empty($dieselData)) {
                $diesel_production = (float) $productionWeight ?? 0;
                $diesel_os = $previousDieselPS ?? 0;
                $diesel_incoming = $dieselData['incoming'] ?? 0;
                $diesel_mreading = $dieselData['mreading'] ?? 0;
                $diesel_transport = $dieselData['transport'] ?? 0;
                $diesel_ps = $dieselData['ps'] ?? 0;
                $diesel_usage = $dieselData['usage'] ?? 0;
                $diesel_actual_usage = $dieselData['actual_usage'] ?? 0;
            } else {
                // Default values if data is empty
                $diesel_production = (float) $productionWeight ?? 0;
                $diesel_os = $previousDieselPS ?? 0;
                $diesel_incoming = NULL;
                $diesel_mreading = NULL;
                $diesel_transport = NULL;
                $diesel_ps = NULL;
                $diesel_usage = NULL;
                $diesel_actual_usage = NULL;
            }

            // Insert into StockTakeLog table
            // if ($insert_stmt = $db->prepare("INSERT INTO Stock_Take_Log (declaration_datetime, plant_id, sixty_seventy_production, sixty_seventy_os, sixty_seventy_incoming, sixty_seventy_usage, sixty_seventy_bookstock, sixty_seventy_ps, sixty_seventy_diffstock, sixty_seventy_actual_usage, lfo_production, lfo_os, lfo_incoming, lfo_ps, lfo_usage, lfo_actual_usage, diesel_production, diesel_os, diesel_incoming, diesel_mreading, diesel_transport, diesel_ps, diesel_usage, diesel_actual_usage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            //     $insert_stmt->bind_param("ssssssssssssssssssssssss", $declarationDateTime, $plantId, $sixty_seventy_production, $sixty_seventy_os, $sixty_seventy_incoming, $sixty_seventy_usage, $sixty_seventy_bookstock, $sixty_seventy_ps, $sixty_seventy_diffstock, $sixty_seventy_actual_usage, $lfo_production, $lfo_os, $lfo_incoming, $lfo_ps, $lfo_usage, $lfo_actual_usage, $diesel_production, $diesel_os, $diesel_incoming, $diesel_mreading, $diesel_transport, $diesel_ps, $diesel_usage, $diesel_actual_usage);

            //     // Execute the prepared query.
            //     if (! $insert_stmt->execute()) {
            //         echo json_encode(
            //             array(
            //                 "status"=> "failed", 
            //                 "message"=> $update_stmt->error
            //             )
            //         );
            //     }else{
            //         $insert_stmt->close();
            //     }
            // }
        }
        die;
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