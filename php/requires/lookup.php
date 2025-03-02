<?php
// Id by Name
function searchCustIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM customers WHERE customer_name=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchDealerIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM dealer WHERE customer_name=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchBrandIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM brand WHERE brand=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchModelIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM model WHERE model=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchMachineIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM machines WHERE machine_type=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCapacityIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM capacity WHERE capacity=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchJenisAlatNameByid($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM alat WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['alat'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchJenisAlatIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM alat WHERE alat=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchValidatorIdByName($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM validators WHERE validator=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['id'];
        }
        $select_stmt->close();
    }

    return $id;
}

// Name by Id
function searchCustNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM customers WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['customer_name'];
        }
        $select_stmt->close();
    }

    return $id;
}

// Customer Code by Id
function searchCustCodeById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM customers WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['customer_code'];
        }
        $select_stmt->close();
    }

    return $id;
}

// Reseller Name by Id
function searchResellerNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM dealer WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['customer_name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchBrandNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM brand WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['brand'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchModelNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM model WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['model'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchMachineNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM machines WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['machine_type'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCapacityNameById($value, $db) {
    $id = '';

    if ($select_stmt = $db->prepare("SELECT * FROM capacity WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['name'];
        }
        $select_stmt->close();
    } 

    return $id;
}

function searchCapacityById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM capacity WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $idname = $row['name'];
            $id1 = explode("X",$idname)[0];
            $id = explode("x",$id1)[0];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchAlatNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM alat WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['alat'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchValidatorNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM validators WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['validator'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchStaffNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM users WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchStaffICById($value, $db) {
    $id = '000000-00-0000';

    if ($select_stmt = $db->prepare("SELECT * FROM users WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['ic_number'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCountryById($value, $db) {
    $id = 'MALAYSIA';

    if ($select_stmt = $db->prepare("SELECT * FROM country WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchUnitNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM units WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['units'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchSizeNameById($value, $db) {
    $id = '';

    if ($select_stmt = $db->prepare("SELECT * FROM size WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['size'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCountryNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM country WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function convertDatetimeToDate($datetime){
    $date = new DateTime($datetime);
  
    return $date->format('d/m/Y'); 
}

function searchStateNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM state WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['state'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchPlantCodeById($value, $db) {
    $id = '0';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['plant_code'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchPlantNameById($value, $db) {
    $id = '0';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchFilePathById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM files WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['filepath'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchNamebyId($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM Users WHERE username=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['name'];
        }
        $select_stmt->close();
    }

    return $id;
}
?>