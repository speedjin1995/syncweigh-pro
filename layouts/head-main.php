<?php
// Reload the role's permissions on every page (same query as login.php) so role changes apply without logging in again.
// login.php / register.php also include this file, so only do it for a logged-in session.
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && !empty($_SESSION['company']) && isset($_SESSION['roles'])) {
    require_once __DIR__ . '/config.php';

    $headPermSql = "SELECT m.category, m.name AS module_name, p.name AS permission_name
        FROM role_permissions rp
        JOIN roles r ON r.id = rp.role_id
        JOIN modules m ON m.id = rp.module_id
        JOIN permissions p ON p.id = rp.permission_id
        WHERE r.role_code = ?";
    if ($headPermStmt = mysqli_prepare($link, $headPermSql)) {
        mysqli_stmt_bind_param($headPermStmt, "s", $_SESSION['roles']);
        if (mysqli_stmt_execute($headPermStmt)) {
            $headPermResult = mysqli_stmt_get_result($headPermStmt);
            $headPermissions = array();
            while ($headPermRow = mysqli_fetch_assoc($headPermResult)) {
                $headPermissions[$headPermRow['category']][$headPermRow['module_name']][] = $headPermRow['permission_name'];
            }
            $_SESSION['permissions'] = $headPermissions;
        }
        mysqli_stmt_close($headPermStmt);
    }
    unset($headPermSql, $headPermStmt, $headPermResult, $headPermRow, $headPermissions);
}

require_once __DIR__ . '/../php/requires/permissions.php';

require_once ("lang.php");

$isScssconverted = false;

require_once ("scssphp/scss.inc.php");

use ScssPhp\ScssPhp\Compiler;

if($isScssconverted){

    global $compiler;
    $compiler = new Compiler();

    $compine_css = "assets/css/app.min.css";

    $source_scss = "assets/scss/config/default/app.scss";

    $scssContents = file_get_contents($source_scss);

    $import_path = "assets/scss/config/default";
    $compiler->addImportPath($import_path);
    $target_css = $compine_css;

    $css = $compiler->compileString($scssContents);

    if (!empty($css) && is_string($css)) {
        file_put_contents($target_css, $css);
    }
}
?>
<!DOCTYPE html>
<html lang="<?=$lng?>" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">