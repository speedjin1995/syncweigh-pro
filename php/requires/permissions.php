<?php
$role = $_SESSION['roles'];

function hasPermission($category, $permission = 'view') {
    global $role;
    if ($role === 'SADMIN') return true;
    if (!isset($_SESSION['permissions'][$category])) return false;
    $permissions = is_array($permission) ? $permission : [$permission];
    foreach ($_SESSION['permissions'][$category] as $module => $perms) {
        if (array_intersect($permissions, $perms)) return true;
    }
    return false;
}

function hasModulePermission($category, $module, $permission = 'view') {
    global $role;
    if ($role === 'SADMIN') return true;
    if (!isset($_SESSION['permissions'][$category][$module])) return false;
    $permissions = is_array($permission) ? $permission : [$permission];
    return !empty(array_intersect($permissions, $_SESSION['permissions'][$category][$module]));
}

function hasModulePermissionDb($db, $category, $module, $permission = 'view') {
    if (!isset($_SESSION['roles'])) return false;

    $permissions = is_array($permission) ? $permission : [$permission];
    foreach ($permissions as $perm) {
        $sql = "SELECT COUNT(*) AS total
                FROM role_permissions rp
                JOIN roles r ON r.id = rp.role_id
                JOIN modules m ON m.id = rp.module_id
                JOIN permissions p ON p.id = rp.permission_id
                WHERE r.role_code = ? AND m.category = ? AND m.name = ? AND p.name = ?";

        if ($stmt = $db->prepare($sql)) {
            $stmt->bind_param('ssss', $_SESSION['roles'], $category, $module, $perm);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            if ((int)$row['total'] > 0) {
                return true;
            }
        }
    }

    return false;
}
