<?php
require_once 'db_connect.php';
require_once 'requires/lookup.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['username'];
$type = '';

if($_POST['type'] != null && $_POST['type'] != ''){
    $type = $_POST['type'];
}

## Search 
$searchQuery = "";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_POST['fromDate']);
  $fromDateTime = $dateTime->format('Y-m-d H:i:00');
  $searchQuery = " and tare_weight1_date >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $dateTime = DateTime::createFromFormat('d-m-Y H:i', $_POST['toDate']);
  $toDateTime = $dateTime->format('Y-m-d H:i:59');
	$searchQuery .= " and tare_weight1_date <= '".$toDateTime."'";
}

if($_POST['status'] != null && $_POST['status'] != '' && $_POST['status'] != '-'){
	$searchQuery .= " and transaction_status = '".$_POST['status']."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and customer_code = '".$_POST['customer']."'";
}

if($_POST['product'] != null && $_POST['product'] != '' && $_POST['product'] != '-'){
	$searchQuery .= " and product_code = '".$_POST['product']."'";
}

if($_POST['plant'] != null && $_POST['plant'] != '' && $_POST['plant'] != '-'){
	$searchQuery .= " and plant_code = '".$_POST['plant']."'";
}

if($_POST['purchaseOrder'] != null && $_POST['purchaseOrder'] != '' && $_POST['purchaseOrder'] != '-'){
	$searchQuery .= " and purchase_order = '".$_POST['purchaseOrder']."'";
}

if ($type == "MULTI"){
    if(is_array($_POST['userID'])){
        $ids = implode(",", $_POST['userID']);
    }else{
        $ids = $_POST['userID'];
    }

    if ($stmt2 = $db->prepare("SELECT * FROM Weight WHERE id IN ($ids)")) {
        
        if($stmt2->execute()){
        

            $stmt2->close();
            $db->close();
            
            echo json_encode(
                array(
                    "status"=> "success", 
                    "message"=> "Deleted"
                )
            );
        } else{
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $stmt2->error
                )
            );
        }
    } 
    else{
        echo json_encode(
            array(
                "status"=> "failed", 
                "message"=> "Somthings wrong"
            )
        );
    }
}else{
    $empQuery = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y'".$searchQuery." group by purchase_order";
    if($_SESSION["roles"] != 'ADMIN' && $_SESSION["roles"] != 'SADMIN'){
        $username = implode("', '", $_SESSION["plant"]);
        $empQuery = "select * from Weight where is_complete = 'Y' AND  is_cancel <> 'Y' and plant_code IN ('$username')".$searchQuery." group by purchase_order";
    }

}


?>
