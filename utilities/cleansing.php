<?php
$db = mysqli_connect("localhost", "u664110560_blacktop", "@Sync5500", "u664110560_blacktop");

if(mysqli_connect_errno()){
    echo 'Database connection failed with following errors: ' . mysqli_connect_error();
    die();
}

try {

    // 1️⃣ Find duplicated codes
    $dupSql = "
        SELECT destination_code
        FROM Destination
        GROUP BY destination_code
        HAVING COUNT(*) > 1
    ";
    $dupRes = $db->query($dupSql);

    while ($dupRow = $dupRes->fetch_assoc()) {

        $code = $dupRow['destination_code'];

        // 2️⃣ Get all rows using this code
        $rows = $db->query("
            SELECT id, name, destination_code
            FROM Destination
            WHERE destination_code = '$code'
            ORDER BY id ASC
        ");

        $first = true;

        while ($r = $rows->fetch_assoc()) {

            $destName = $r['name'];
            $oldCode  = $r['destination_code'];
            $id       = $r['id'];

            // keep first
            if ($first) {
                $first = false;
                continue;
            }

            // 3️⃣ Generate new code
            $prefix = strtoupper(substr($destName,0,1));

            // Get misc counter
            $stmt = $db->prepare("SELECT value FROM miscellaneous WHERE code='destination' AND name=? FOR UPDATE");
            $stmt->bind_param('s',$prefix);
            $stmt->execute();
            $res = $stmt->get_result();
            $mis = $res->fetch_assoc();
            $stmt->close();

            $val = intval($mis['value']);
            $newCode = $prefix.'-'.str_pad($val,5,'0',STR_PAD_LEFT);

            // 4️⃣ Update Destination
            $stmt = $db->prepare("UPDATE Destination SET destination_code=? WHERE id=?");
            $stmt->bind_param('si',$newCode,$id);
            $stmt->execute();
            $stmt->close();

            // 5️⃣ Update Weight tables based on destination_name
            $tablesA = ["Weight", "Weight_Log"];
            foreach ($tablesA as $tbl) {
                $sql = "UPDATE $tbl SET destination_code=? WHERE destination=?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('ss',$newCode,$destName);
                $stmt->execute();
                $stmt->close();
            }

            // 6️⃣ Other tables use code only
            $tablesB = [
                "Sales_Order",
                "Sales_Order_Log",
                "Purchase_Order",
                "Purchase_Order_Log"
            ];

            foreach ($tablesB as $tbl) {
                $sql = "UPDATE $tbl SET destination_code=? WHERE destination_code=?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('ss',$newCode,$oldCode);
                $stmt->execute();
                $stmt->close();
            }

            // 7️⃣ Increment misc
            $val++;
            $stmt = $db->prepare("UPDATE miscellaneous SET value=? WHERE code='destination' AND name=?");
            $stmt->bind_param('is',$val,$prefix);
            $stmt->execute();
            $stmt->close();
        }
    }

    $db->commit();
    echo "SUCCESS — Duplicate destination codes recalculated";

} catch(Exception $e){
    $db->rollback();
    echo "FAILED — ".$e->getMessage();
}
