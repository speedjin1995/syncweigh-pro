<?php
function hasPermission($category, $permission = 'view') {
    if (!isset($_SESSION['permissions'][$category])) return false;
    foreach ($_SESSION['permissions'][$category] as $module => $perms) {
        if (in_array($permission, $perms)) return true;
    }
    return false;
}

function hasModulePermission($category, $module, $permission = 'view') {
    return isset($_SESSION['permissions'][$category][$module]) && in_array($permission, $_SESSION['permissions'][$category][$module]);
}
