<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['username'];

// Read the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) { 
    foreach ($data as $rows) {
        $CompanyCode = !empty($rows['CompanyCode']) ? trim($rows['CompanyCode']) : '';
        $CompanyName = !empty($rows['CompanyName']) ? trim($rows['CompanyName']) : '';
        $CustomerCode = !empty($rows['CustomerCode']) ? trim($rows['CustomerCode']) : '';
        $CustomerName = !empty($rows['CustomerName']) ? trim($rows['CustomerName']) : '';
        $SiteCode = !empty($rows['SiteCode']) ? trim($rows['SiteCode']) : '';
        $SiteName = !empty($rows['SiteName']) ? trim($rows['SiteName']) : '';
        $OrderDate = !empty($rows['OrderDateDDMMYYYY']) ? DateTime::createFromFormat('d-m-Y', $rows["OrderDateDDMMYYYY"])->format('Y-m-d H:i:s') : '';
        $OrderNumber = !empty($rows['OrderNumber']) ? trim($rows['OrderNumber']) : '';
        $SONumber = !empty($rows['SONumber']) ? trim($rows['SONumber']) : '';
        $DeliveryDate = !empty($rows['DeliveryDateDDMMYYYY']) ? DateTime::createFromFormat('d-m-Y', $rows["DeliveryDateDDMMYYYY"])->format('Y-m-d H:i:s') : '';
        $SalesrepCode = !empty($rows['SalesrepCode']) ? trim($rows['SalesrepCode']) : '';
        $SalesrepName = !empty($rows['SalesrepName']) ? trim($rows['SalesrepName']) : '';
        $DestinationCode = !empty($rows['DestinationCode']) ? trim($rows['DestinationCode']) : '';
        $DestinationName = !empty($rows['DestinationName']) ? trim($rows['DestinationName']) : '';
        $DeliverToName = !empty($rows['DeliverToName']) ? trim($rows['DeliverToName']) : '';
        $ProductCode = !empty($rows['ProductCode']) ? trim($rows['ProductCode']) : '';
        $ProductName = !empty($rows['ProductName']) ? trim($rows['ProductName']) : '';
        $OrderLoad = !empty($rows['OrderLoad']) ? trim($rows['OrderLoad']) : '';
        $OrderQuantity = !empty($rows['OrderQuantity']) ? trim($rows['OrderQuantity']) : '';
        $Remarks = !empty($rows['Remarks']) ? trim($rows['Remarks']) : '';
        $status = 'Open';
        $actionId = 1;

        # Company Checking & Processing
        if($CompanyCode != null && $CompanyCode != ''){
            $companyQuery = "SELECT * FROM Company WHERE company_code = '$CompanyCode'";
            $companyDetail = mysqli_query($db, $companyQuery);
            $companyRow = mysqli_fetch_assoc($companyDetail);
            
            if(empty($companyRow)){
                if($insert_company = $db->prepare("INSERT INTO Company (company_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_company->bind_param('ssss', $CompanyCode, $CompanyName, $uid, $uid);
                    $insert_company->execute();
                    $companyId = $insert_company->insert_id; // Get the inserted company ID
                    $insert_company->close();
                    
                    if ($insert_company_log = $db->prepare("INSERT INTO Company_Log (company_id, company_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_company_log->bind_param('sssss', $companyId, $CompanyCode, $CompanyName, $actionId, $uid);
                        $insert_company_log->execute();
                        $insert_company_log->close();
                    }    
                }
            }
        }

        # Customer Checking & Processing
        if($CustomerCode != null && $CustomerCode != ''){
            $customerQuery = "SELECT * FROM Customer WHERE customer_code = '$CustomerCode'";
            $customerDetail = mysqli_query($db, $customerQuery);
            $customerRow = mysqli_fetch_assoc($customerDetail);
            
            if(empty($customerRow)){
                if($insert_customer = $db->prepare("INSERT INTO Customer (customer_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_customer->bind_param('ssss', $CustomerCode, $CustomerName, $uid, $uid);
                    $insert_customer->execute();
                    $customerId = $insert_customer->insert_id; // Get the inserted customer ID
                    $insert_customer->close();
                    
                    if ($insert_customer_log = $db->prepare("INSERT INTO Customer_Log (customer_id, customer_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_customer_log->bind_param('sssss', $customerId, $CustomerCode, $CustomerName, $actionId, $uid);
                        $insert_customer_log->execute();
                        $insert_customer_log->close();
                    }    
                }
            }
        }

        # Site Checking & Processing
        if($SiteCode != null && $SiteCode != ''){
            $siteQuery = "SELECT * FROM Site WHERE site_code = '$SiteCode'";
            $siteDetail = mysqli_query($db, $siteQuery);
            $siteRow = mysqli_fetch_assoc($siteDetail);
            
            if(empty($siteRow)){
                if($insert_site = $db->prepare("INSERT INTO Site (site_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_site->bind_param('ssss', $SiteCode, $SiteName, $uid, $uid);
                    $insert_site->execute();
                    $siteId = $insert_site->insert_id; // Get the inserted site ID
                    $insert_site->close();
                    
                    if ($insert_site_log = $db->prepare("INSERT INTO Site_Log (site_id, site_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_site_log->bind_param('sssss', $siteId, $SiteCode, $SiteName, $actionId, $uid);
                        $insert_site_log->execute();
                        $insert_site_log->close();
                    }    
                }
            }
        }

        # Agent Checking & Processing
        if($SalesrepCode != null && $SalesrepCode != ''){
            $agentQuery = "SELECT * FROM Agents WHERE agent_code = '$SalesrepCode'";
            $agentDetail = mysqli_query($db, $agentQuery);
            $agentRow = mysqli_fetch_assoc($agentDetail);
            
            if(empty($agentRow)){
                if($insert_agent = $db->prepare("INSERT INTO Agents (agent_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_agent->bind_param('ssss', $SalesrepCode, $SalesrepName, $uid, $uid);
                    $insert_agent->execute();
                    $agentId = $insert_agent->insert_id; // Get the inserted agent ID
                    $insert_agent->close();
                    
                    if ($insert_agent_log = $db->prepare("INSERT INTO Agents_Log (agent_id, agent_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_agent_log->bind_param('sssss', $agentId, $SalesrepCode, $SalesrepName, $actionId, $uid);
                        $insert_agent_log->execute();
                        $insert_agent_log->close();
                    }    
                }
            }
        }

        # Destination Checking & Processing
        if($DestinationCode != null && $DestinationCode != ''){
            $destinationQuery = "SELECT * FROM Destination WHERE destination_code = '$DestinationCode'";
            $destinationDetail = mysqli_query($db, $destinationQuery);
            $destinationRow = mysqli_fetch_assoc($destinationDetail);
            
            if(empty($destinationRow)){
                if($insert_destination = $db->prepare("INSERT INTO Destination (destination_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_destination->bind_param('ssss', $DestinationCode, $DestinationName, $uid, $uid);
                    $insert_destination->execute();
                    $destinationId = $insert_destination->insert_id; // Get the inserted destination ID
                    $insert_destination->close();
                    
                    if ($insert_destination_log = $db->prepare("INSERT INTO Destination_Log (destination_id, destination_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_destination_log->bind_param('sssss', $destinationId, $DestinationCode, $DestinationName, $actionId, $uid);
                        $insert_destination_log->execute();
                        $insert_destination_log->close();
                    }    
                }
            }
        }

        # Product Checking & Processing
        if($ProductCode != null && $ProductCode != ''){
            $productQuery = "SELECT * FROM Product WHERE product_code = '$ProductCode'";
            $productDetail = mysqli_query($db, $productQuery);
            $productRow = mysqli_fetch_assoc($productDetail);
            
            if(empty($productRow)){
                if($insert_product = $db->prepare("INSERT INTO Product (product_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_product->bind_param('ssss', $ProductCode, $ProductName, $uid, $uid);
                    $insert_product->execute();
                    $productId = $insert_product->insert_id; // Get the inserted destination ID
                    $insert_product->close();
                    
                    if ($insert_product_log = $db->prepare("INSERT INTO Product_Log (product_id, product_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_product_log->bind_param('sssss', $productId, $ProductCode, $ProductName, $actionId, $uid);
                        $insert_product_log->execute();
                        $insert_product_log->close();
                    }    
                }
            }
        }

        # Checking for existing PO No.
        if($OrderNumber != null && $OrderNumber != ''){
            $soQuery = "SELECT * FROM Sales_Order WHERE order_no = '$OrderNumber' AND deleted = '0'";
            $soDetail = mysqli_query($db, $soQuery);
            $soRow = mysqli_fetch_assoc($soDetail);

            if(empty($soRow)){
                if ($insert_stmt = $db->prepare("INSERT INTO Sales_Order (company_code, company_name, customer_code, customer_name, site_code, site_name, order_date, order_no, so_no, delivery_date, agent_code, agent_name, destination_code, destination_name, deliver_to_name, product_code, product_name, order_load, order_quantity, remarks, status, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                    $insert_stmt->bind_param('sssssssssssssssssssssss', $CompanyCode, $CompanyName, $CustomerCode, $CustomerName, $SiteCode, $SiteName, $OrderDate, $OrderNumber, $SONumber, $DeliveryDate, $SalesrepCode, $SalesrepName, $DestinationCode, $DestinationName, $DeliverToName, $ProductCode, $ProductName, $OrderLoad, $OrderQuantity, $Remarks, $status, $uid, $uid);
                    $insert_stmt->execute();
                    $insert_stmt->close(); 
                }
            }
        }
    }

    $db->close();

    echo json_encode(
        array(
            "status"=> "success", 
            "message"=> "Added Successfully!!" 
        )
    );
} else {
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}
?>
