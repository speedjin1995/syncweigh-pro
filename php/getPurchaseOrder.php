<?php
session_start();
require_once "db_connect.php";
require_once "requires/lookup.php";

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
    $format = '';
    $type = '';

    if (isset($_POST['format']) && $_POST['format'] != ''){
        $format = $_POST['format'];
    }

    if (isset($_POST['type']) && $_POST['type'] != ''){
        $type = $_POST['type'];
    }

    if ($type == 'StockTake'){
        $declarationDate = '';
        $plantCode = '';
        $batchDrum = '';
        $rawMatCode = ['BTBI001', 'LFFO001', 'DIE001'];

        if (isset($_POST['declarationDate']) && $_POST['declarationDate'] != ''){
            $declarationDate = $_POST['declarationDate'];
            $startDate = DateTime::createFromFormat('d-m-Y H:i', $declarationDate)->format('Y-m-d 00:00:00');
            $endDate = DateTime::createFromFormat('d-m-Y H:i', $declarationDate)->format('Y-m-d 23:59:59');
        }

        if (isset($_POST['userID']) && $_POST['userID'] != ''){
            $plantCode = $_POST['userID'];
        }

        if (isset($_POST['batchDrum']) && $_POST['batchDrum'] != ''){
            $batchDrum = $_POST['batchDrum'];
        }

        if (isset($declarationDate, $plantCode, $batchDrum) && $declarationDate != '' && $plantCode != '' && $batchDrum != ''){
            if ($update_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE plant_code=? AND batch_drum=? AND raw_mat_code IN (?,?,?) AND order_date BETWEEN ? AND ? AND deleted = 0 ORDER BY order_date ASC")) {
                $update_stmt->bind_param('sssssss', $plantCode, $batchDrum, $rawMatCode[0], $rawMatCode[1], $rawMatCode[2], $startDate, $endDate);
                
                // Execute the prepared query.
                if (! $update_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $update_stmt->get_result();
                    $message = array();
                    
                    $bitumenIncoming = 0;
                    $dieselIncoming = 0;
                    $lfoIncoming = 0;

                    while ($row = $result->fetch_assoc()) {
                        if ($row['raw_mat_code'] == 'BTBI001'){
                            $bitumenIncoming += floatval($row['converted_order_qty']);
                        } else if ($row['raw_mat_code'] == 'DIE001'){
                            $dieselIncoming += floatval($row['converted_order_qty']);
                        } else if ($row['raw_mat_code'] == 'LFFO001'){
                            $lfoIncoming += floatval($row['converted_order_qty']);
                        }
                    }

                    $message['bitumenIncoming'] = $bitumenIncoming;
                    $message['dieselIncoming'] = $dieselIncoming;
                    $message['lfoIncoming'] = $lfoIncoming;
                    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        ));   
                }
            }
            exit();
        }else{

        }
    }else{
        if ($format == 'EXPANDABLE'){
            if ($update_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE id=?")) {
                $update_stmt->bind_param('s', $id);
                
                // Execute the prepared query.
                if (! $update_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $update_stmt->get_result();
                    $message = array();
                    
                    while ($row = $result->fetch_assoc()) {
                        $message['id'] = $row['id'];
                        $message['company_code'] = $row['company_code'] ?? '';
                        $message['company_name'] = $row['company_name'] ?? '';
                        $message['supplier_code'] = $row['supplier_code'] ?? '';
                        $message['supplier_name'] = $row['supplier_name'] ?? '';
                        $message['site_code'] = $row['site_code'] ?? '';
                        $message['site_name'] = $row['site_name'] ?? '';
                        $message['order_date'] = $row['order_date'];
                        $message['order_no'] = $row['order_no'];
                        $message['po_no'] = $row['po_no'];
                        $message['agent_code'] = $row['agent_code'] ?? '';
                        $message['agent_name'] = $row['agent_name'] ?? '';
                        $message['destination_code'] = $row['destination_code'] ?? '';
                        $message['destination_name'] = $row['destination_name'] ?? '';
                        $message['raw_mat_code'] = $row['raw_mat_code'] ?? '';
                        $message['raw_mat_name'] = $row['raw_mat_name'] ?? '';
                        $message['plant_code'] = $row['plant_code'] ?? '';
                        $message['plant_name'] = $row['plant_name'] ?? '';
                        $message['transporter_code'] = $row['transporter_code'] ?? '';
                        $message['transporter_name'] = $row['transporter_name'] ?? '';
                        $message['veh_number'] = $row['veh_number'];
                        $message['batch_drum'] = $row['batch_drum'];
                        if ($row['exquarry_or_delivered'] == 'E'){
                            $message['exquarry_or_delivered'] = 'EX-QUARRY';
                        }else{
                            $message['exquarry_or_delivered'] = 'DELIVERED';
                        }
                        $message['converted_order_qty'] = $row['converted_order_qty'] ?? '';
                        $message['converted_balance'] = $row['converted_balance'] ?? '';
                        $message['converted_unit'] = $row['converted_unit'] ?? '';
                        $message['converted_unit_label'] = searchUnitById($row['converted_unit'], $db) ?? '';
                        $message['order_quantity'] = $row['order_quantity'] ?? '';
                        $message['balance'] = $row['balance'] ?? '';
                        $message['unit_price'] = $row['unit_price'] ?? 0;
                        $message['total_price'] = $row['total_price'] ?? 0;
                        $message['remarks'] = $row['remarks'] ?? '';
                        $message['balance'] = $row['balance'];

                        $weightData = array();

                        if($row['po_no'] != null && $row['po_no'] != ''){
                            $poNo = $row['po_no'];
                            $rawMatCode = $row['raw_mat_code'];
                            $plantCode = $row['plant_code'];
                            $weight_stmt = $db->prepare("SELECT * FROM Weight WHERE purchase_order = ? AND raw_mat_code = ? AND status = '0' AND transaction_status = 'Purchase' AND is_cancel <> 'Y' ORDER BY id ASC");
                            $weight_stmt->bind_param('ss', $poNo, $rawMatCode);
                            $weight_stmt->execute();
                            $weightRecords = $weight_stmt->get_result();

                            while($weightRow = $weightRecords->fetch_assoc()) {
                                $weightData[] = array(
                                    "id" => $weightRow['id'],
                                    "transaction_id" => $weightRow['transaction_id'],
                                    "raw_mat_code" => $weightRow['raw_mat_code'],
                                    "raw_mat_name" => $weightRow['raw_mat_name'],
                                    "delivery_no" => $weightRow['delivery_no'] ?? '',
                                    "lorry_plate_no1" => $weightRow['lorry_plate_no1'],
                                    "nett_weight1" => $weightRow['nett_weight1'],
                                    "created_by" => searchNamebyId($weightRow['created_by'], $db)
                                );
                            }
                            $weight_stmt->close();
                        }

                        $message['weights'] = $weightData;
                    }
                    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        ));   
                }
            }
        }else{
            if ($update_stmt = $db->prepare("SELECT * FROM Purchase_Order WHERE id=?")) {
                $update_stmt->bind_param('s', $id);
                
                // Execute the prepared query.
                if (! $update_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status" => "failed",
                            "message" => "Something went wrong"
                        )); 
                }
                else{
                    $result = $update_stmt->get_result();
                    $message = array();
                    
                    while ($row = $result->fetch_assoc()) {
                        $message['id'] = $row['id'];
                        $message['company_code'] = $row['company_code'];
                        $message['supplier_code'] = $row['supplier_code'];
                        $message['site_code'] = $row['site_code'];
                        $message['order_date'] = $row['order_date'];
                        $message['order_no'] = $row['order_no'];
                        $message['po_no'] = $row['po_no'];
                        $message['agent_code'] = $row['agent_code'];
                        $message['destination_code'] = $row['destination_code'];
                        $message['raw_mat_code'] = $row['raw_mat_code'];
                        $message['raw_mat_name'] = $row['raw_mat_name'];
                        $message['plant_code'] = $row['plant_code'];
                        $message['transporter_code'] = $row['transporter_code'];
                        $message['veh_number'] = $row['veh_number'];
                        $message['batch_drum'] = $row['batch_drum'];
                        $message['exquarry_or_delivered'] = $row['exquarry_or_delivered'];
                        $message['converted_order_qty'] = $row['converted_order_qty'];
                        $message['converted_balance'] = $row['converted_balance'];
                        $message['converted_unit'] = $row['converted_unit'];
                        $message['converted_unit_text'] = searchUnitById($row['converted_unit'], $db);
                        $message['order_quantity'] = $row['order_quantity'];
                        $message['balance'] = $row['balance'];
                        $message['unit_price'] = $row['unit_price'];
                        $message['total_price'] = $row['total_price'];
                        $message['remarks'] = $row['remarks'];
                    }
                    
                    echo json_encode(
                        array(
                            "status" => "success",
                            "message" => $message
                        ));   
                }
            }
        }
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>