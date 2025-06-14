<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];
// Processing form data when form is submitted
if (isset($_POST['orderNo'])) {

    if (empty($_POST["id"])) {
        $poId = null;
    } else {
        $poId = trim($_POST["id"]);
    }

    if (empty($_POST["company"])) {
        $companyId = null;
    } else {
        $companyId = trim($_POST["company"]);
    }

    if (empty($_POST["companyCode"])) {
        $companyCode = null;
    } else {
        $companyCode = trim($_POST["companyCode"]);
    }

    if (empty($_POST["companyName"])) {
        $companyName = null;
    } else {
        $companyName = trim($_POST["companyName"]);
    }

    if (empty($_POST["customer"])) {
        $customerId = null;
    } else {
        $customerId = trim($_POST["customer"]);
    }

    if (empty($_POST["customerCode"])) {
        $customerCode = null;
    } else {
        $customerCode = trim($_POST["customerCode"]);
    }

    if (empty($_POST["customerName"])) {
        $customerName = null;
    } else {
        $customerName = trim($_POST["customerName"]);
    }

    if (empty($_POST["site"])) {
        $siteId = null;
    } else {
        $siteId = trim($_POST["site"]);
    }

    if (empty($_POST["siteCode"])) {
        $siteCode = null;
    } else {
        $siteCode = trim($_POST["siteCode"]);
    }

    if (empty($_POST["siteName"])) {
        $siteName = null;
    } else {
        $siteName = trim($_POST["siteName"]);
    }

    if (empty($_POST["orderDate"])) {
        $orderDate = null;
    } else {
        $orderDate = DateTime::createFromFormat('d-m-Y', $_POST["orderDate"])->format('Y-m-d H:i:s');
    }

    if (empty($_POST["orderNo"])) {
        $orderNo = null;
    } else {
        $orderNo = trim($_POST["orderNo"]);
    }

    if (empty($_POST["soNo"])) {
        $soNo = null;
    } else {
        $soNo = trim($_POST["soNo"]);
    }

    if (empty($_POST["deliveryDate"])) {
        $deliveryDate = null;
    } else {
        $deliveryDate = DateTime::createFromFormat('d-m-Y', $_POST["deliveryDate"])->format('Y-m-d H:i:s');
    }

    if (empty($_POST["agent"])) {
        $agentId = null;
    } else {
        $agentId = trim($_POST["agent"]);
    }

    if (empty($_POST["agentCode"])) {
        $agentCode = null;
    } else {
        $agentCode = trim($_POST["agentCode"]);
    }
    
    if (empty($_POST["agentName"])) {
        $agentName = null;
    } else {
        $agentName = trim($_POST["agentName"]);
    }

    if (empty($_POST["destination"])) {
        $destinationId = null;
    } else {
        $destinationId = trim($_POST["destination"]);
    }

    if (empty($_POST["destinationCode"])) {
        $destinationCode = null;
    } else {
        $destinationCode = trim($_POST["destinationCode"]);
    }

    if (empty($_POST["destinationName"])) {
        $destinationName = null;
    } else {
        $destinationName = trim($_POST["destinationName"]);
    }

    if (empty($_POST["deliverToName"])) {
        $deliverToName = null;
    } else {
        $deliverToName = trim($_POST["deliverToName"]);
    }
    
    if (empty($_POST["product"])) {
        $productId = null;
    } else {
        $productId = trim($_POST["product"]);
    }
    
    if (empty($_POST["productCode"])) {
        $productCode = null;
    } else {
        $productCode = trim($_POST["productCode"]);
    }

    if (empty($_POST["productName"])) {
        $productName = null;
    } else {
        $productName = trim($_POST["productName"]);
    }
    
    if (empty($_POST["plant"])) {
        $plantId = null;
    } else {
        $plantId = trim($_POST["plant"]);
    }

    if (empty($_POST["plantCode"])) {
        $plantCode = null;
    } else {
        $plantCode = trim($_POST["plantCode"]);
    }
    
    if (empty($_POST["plantName"])) {
        $plantName = null;
    } else {
        $plantName = trim($_POST["plantName"]);
    }

    if (empty($_POST["transporter"])) {
        $transporterId = null;
    } else {
        $transporterId = trim($_POST["transporter"]);
    }

    if (empty($_POST["transporterCode"])) {
        $transporterCode = null;
    } else {
        $transporterCode = trim($_POST["transporterCode"]);
    }

    if (empty($_POST["transporterName"])) {
        $transporterName = null;
    } else {
        $transporterName = trim($_POST["transporterName"]);
    }

    if (empty($_POST["vehicle"])) {
        $vehicle = null;
    } else {
        $vehicle = trim($_POST["vehicle"]);
    }

    if (empty($_POST["exDel"])) {
        $exDel = null;
    } else {
        $exDel = trim($_POST["exDel"]);
    }

    if (empty($_POST["orderLoad"])) {
        $orderLoad = null;
    } else {
        $orderLoad = trim($_POST["orderLoad"]);
    }

    if (empty($_POST["convertedOrderQty"])) {
        $convertedOrderQty = null;
    } else {
        $convertedOrderQty = trim($_POST["convertedOrderQty"]);
    }

    if (empty($_POST["convertedOrderQtyUnit"])) {
        $convertedQtyUnit = null;
    } else {
        $convertedQtyUnit = trim($_POST["convertedOrderQtyUnit"]);
    }

    if (empty($_POST["balance"])) {
        $balance = null;
    } else {
        $balance = trim($_POST["balance"]);
    }

    if (empty($_POST["orderQty"])) {
        $orderQty = null;
    } else {
        $orderQty = trim($_POST["orderQty"]);
    }

    if (empty($_POST["convertedBal"])) {
        $convertedBal = null;
    } else {
        $convertedBal = trim($_POST["convertedBal"]);
    }

    if (empty($_POST["unitPrice"])) {
        $unitPrice = null;
    } else {
        $unitPrice = trim($_POST["unitPrice"]);
    }

    if (empty($_POST["totalPrice"])) {
        $totalPrice = null;
    } else {
        $totalPrice = trim($_POST["totalPrice"]);
    }

    if (empty($_POST["remarks"])) {
        $remarks = null;
    } else {
        $remarks = trim($_POST["remarks"]);
    }

    if(!empty($poId))
    {
        if ($update_stmt = $db->prepare("UPDATE Sales_Order SET company_id=?, company_code=?, company_name=?, customer_id=?, customer_code=?, customer_name=?, site_id=?, site_code=?, site_name=?, order_date=?, order_no=?, so_no=?, delivery_date=?, agent_id=?, agent_code=?, agent_name=?, destination_id=?, destination_code=?, destination_name=?, deliver_to_name=?, product_id=?, product_code=?, product_name=?, plant_id=?, plant_code=?, plant_name=?, transporter_id=?, transporter_code=?, transporter_name=?, veh_number=?, exquarry_or_delivered=?, order_load=?, converted_order_qty=?, converted_balance=?, converted_unit=?, order_quantity=?, balance=?, unit_price=?, total_price=?, remarks=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('sssssssssssssssssssssssssssssssssssssssssss', $companyId, $companyCode, $companyName, $customerId, $customerCode, $customerName, $siteId,$siteCode, $siteName, $orderDate, $orderNo, $soNo, $deliveryDate, $agentId, $agentCode, $agentName, $destinationId, $destinationCode, $destinationName, $deliverToName, $productId, $productCode, $productName, $plantId, $plantCode, $plantName, $transporterId, $transporterCode, $transporterName, $vehicle, $exDel, $orderLoad, $convertedOrderQty, $balance, $convertedQtyUnit, $orderQty, $convertedBal, $unitPrice, $totalPrice, $remarks, $username, $username, $poId);

            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else{
                $update_stmt->close();
                $db->close();

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!",
                    )
                );
            }
        }
    }
    else
    {
        $status = 'Open';

        # Check if SO with customer p/o no and product exists
        $soQuery = "SELECT COUNT(*) AS count FROM Sales_Order WHERE order_no = '$orderNo' AND product_id = '$productId' AND plant_id = '$plantId' AND deleted = '0'";
        $soDetail = mysqli_query($db, $soQuery);
        $soRow = mysqli_fetch_assoc($soDetail);
        $soCount = (int) $soRow['count'];

        if ($soCount > 0){
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> "Sales order for Customer P/O No: ".$orderNo." + Product: ".$productName." + Plant: ".$plantName." already exist."
                )
            );
        }else{
            if ($insert_stmt = $db->prepare("INSERT INTO Sales_Order (company_id, company_code, company_name, customer_id, customer_code, customer_name, site_id, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_id, agent_code, agent_name, destination_id, destination_code, destination_name, deliver_to_name, product_id, product_code, product_name, plant_id, plant_code, plant_name, transporter_id, transporter_code, transporter_name, veh_number, exquarry_or_delivered, order_load, converted_order_qty, converted_balance, converted_unit, order_quantity, balance, unit_price, total_price, remarks, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_stmt->bind_param('sssssssssssssssssssssssssssssssssssssssssss', $companyId, $companyCode, $companyName, $customerId, $customerCode, $customerName, $siteId, $siteCode, $siteName, $orderDate, $orderNo, $soNo, $deliveryDate, $agentId, $agentCode, $agentName, $destinationId, $destinationCode, $destinationName, $deliverToName, $productId, $productCode, $productName, $plantId, $plantCode, $plantName, $transporterId, $transporterCode, $transporterName, $vehicle, $exDel, $orderLoad, $convertedOrderQty, $balance, $convertedQtyUnit, $orderQty, $convertedBal, $unitPrice, $totalPrice, $remarks, $status, $username, $username);
    
                // Execute the prepared query.
                if (! $insert_stmt->execute()) {
                    echo json_encode(
                        array(
                            "status"=> "failed", 
                            "message"=> $insert_stmt->error
                        )
                    );
                }
                else{
                    $insert_stmt->close();
                    $db->close();
    
                    echo json_encode(
                        array(
                            "status"=> "success", 
                            "message"=> "Added Successfully!!" 
                        )
                    );
    
                }
            }
        }
    }
}
else
{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>