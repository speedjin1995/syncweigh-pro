<?php
require_once __DIR__ . '/../requires/permissions.php';
require_once __DIR__ . '/../services/RoleService.php';

/**
 * RoleController
 *
 * Handles every role request coming through php/modules/Role/index.php.
 * The request's "action" field picks the handler:
 *
 *   export   Excel (.xls) of every role and the permissions granted in it
 */
class RoleController
{
    const MODULE = 'User Management';
    const SUB_MODULE = 'Role';

    private $db;
    private $roles;

    public function __construct($db)
    {
        $this->db = $db;
        $this->roles = new RoleService($db);
    }

    public function handleRequest()
    {
        if (!isset($_SESSION['id'])) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Unauthorized']);
        }

        $action = $_POST['action'] ?? ($_GET['action'] ?? '');

        try {
            switch ($action) {
                case 'export':
                    $this->export();
                    break;
                default:
                    $this->jsonResponse(['status' => 'failed', 'message' => 'Invalid action']);
            }
        } catch (Throwable $e) {
            $this->jsonResponse(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    /* ---------------- Export ---------------- */

    private function export()
    {
        $this->requirePermission(['export_excel']);

        $includeSadmin = ($_SESSION['roles'] ?? '') === 'SADMIN';
        $rows = $this->roles->getRolePermissionsForExport($includeSadmin);

        $lines = [implode("\t", ['Role Code', 'Role Name', 'Category', 'Module', 'Permissions'])];
        foreach ($rows as $row) {
            $lines[] = implode("\t", array_map([$this, 'excelCell'], $row));
        }

        $fileName = 'Role-Permissions_' . date('Y-m-d') . '.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        echo implode("\n", $lines) . "\n";
        exit;
    }

    /* ---------------- Helpers ---------------- */

    // Same escaping as php/exportMasterData.php: tabs and line breaks would split the row
    private function excelCell($value)
    {
        $value = str_replace(["\t", "\r", "\n"], ' ', (string) $value);
        if (strpos($value, '"') !== false) {
            $value = '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }

    private function can(array $permissions)
    {
        return hasModulePermission(self::MODULE, self::SUB_MODULE, $permissions);
    }

    private function requirePermission(array $permissions)
    {
        if (!$this->can($permissions)) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'You do not have permission to do this']);
        }
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
