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
        $OrderDate = !empty($rows['OrderDateDDMMYYYY']) ? trim($rows['CustomerName']) : '';
        $OrderNumber = !empty($rows['OrderNumber']) ? trim($rows['OrderNumber']) : '';
        $PONumber = !empty($rows['PONumber']) ? trim($rows['PONumber']) : '';
        $DeliveryDate = !empty($rows['DeliveryDateDDMMYYYY']) ? trim($rows['CustomerName']) : '';
        $SalesrepCode = !empty($rows['SalesrepCode']) ? trim($rows['SalesrepCode']) : '';
        $SalesrepName = !empty($rows['SalesrepName']) ? trim($rows['SalesrepName']) : '';
        $DelivertoName = !empty($rows['DelivertoName']) ? trim($rows['DelivertoName']) : '';
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
        if($CustomerCode != null && $CustomerCode != ''){
            $customerQuery = "SELECT * FROM Customer WHERE customer_code = '$CustomerCode'";
            $customerDetail = mysqli_query($db, $customerQuery);
            $customerRow = mysqli_fetch_assoc($customerDetail);
            
            if(empty($customerRow)){
                if($insert_customer = $db->prepare("INSERT INTO Customer (customer_code, name, created_by, modified_by) VALUES (?, ?, ?, ?)")) {
                    $insert_customer->bind_param('ssss', $CustomerCode, $CustomerName, $uid, $uid);
                    $insert_customer->execute();
                    $customerId = $insert_customer->insert_id; // Get the inserted company ID
                    $insert_customer->close();
                    
                    if ($insert_customer_log = $db->prepare("INSERT INTO Customer_Log (company_id, customer_code, name, action_id, action_by) VALUES (?, ?, ?, ?, ?)")) {
                        $insert_customer_log->bind_param('sssss', $customerId, $CustomerCode, $CustomerName, $actionId, $uid);
                        $insert_customer_log->execute();
                        $insert_customer_log->close();
                    }    
                }
            }
        }

        die;

        if ($insert_stmt = $db->prepare("INSERT INTO Customer (customer_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('sssssssssss', $Code, $RegNo, $Name, $Address1, $Address2, $Address3, $Address4, $Phone, $Fax, $uid, $uid);
            $insert_stmt->execute();
            $invid = $insert_stmt->insert_id; // Get the inserted reseller ID
            $insert_stmt->close();

            $sel = mysqli_query($db,"select count(*) as allcount from Customer");
            $records = mysqli_fetch_assoc($sel);
            $totalRecords = $records['allcount'];

            if ($insert_log = $db->prepare("INSERT INTO Customer_Log (customer_id, customer_code, company_reg_no, name, address_line_1, address_line_2, address_line_3, address_line_4, phone_no, fax_no, action_id, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $insert_log->bind_param('ssssssssssss', $totalRecords, $Code, $RegNo, $Name, $Address1, $Address2, $Address3, $Address4, $Phone, $Fax, $action, $uid);
                $insert_log->execute();
                $insert_log->close();
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
