<?php

/**
 * RoleService
 *
 * Read-only role and permission queries. Session-dependent rules (such as
 * hiding SADMIN) are decided by the caller and passed in.
 */
class RoleService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * One row per role and module, with the granted permissions comma-joined.
     * A role with no permissions still gets one row with blank module/permissions.
     * Returns rows of [role_code, role_name, category, module, permissions].
     */
    public function getRolePermissionsForExport($includeSadmin)
    {
        $sql = "SELECT r.id AS role_id, r.role_code, r.role_name, m.id AS module_id, m.category, m.name AS module, p.name AS permission
                FROM roles r
                LEFT JOIN role_permissions rp ON rp.role_id = r.id
                LEFT JOIN modules m ON m.id = rp.module_id
                LEFT JOIN permissions p ON p.id = rp.permission_id
                WHERE r.deleted = 0";

        if (!$includeSadmin) {
            $sql .= " AND r.role_code <> 'SADMIN'";
        }

        $sql .= " ORDER BY r.role_code, m.category, m.name, p.id";

        $result = $this->db->query($sql);
        if ($result === false) {
            throw new RuntimeException('Unable to load role permissions');
        }

        $grouped = [];
        while ($row = $result->fetch_assoc()) {
            $key = $row['role_id'] . '|' . $row['module_id'];

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'role_code' => $row['role_code'],
                    'role_name' => $row['role_name'],
                    'category' => $row['category'] ?? '',
                    'module' => $row['module'] ?? '',
                    'permissions' => [],
                ];
            }

            if ($row['permission'] !== null) {
                $grouped[$key]['permissions'][] = ucwords(str_replace('_', ' ', $row['permission']));
            }
        }
        $result->free();

        $rows = [];
        foreach ($grouped as $group) {
            $rows[] = [
                $group['role_code'],
                $group['role_name'],
                $group['category'],
                $group['module'],
                implode(', ', $group['permissions']),
            ];
        }

        return $rows;
    }
}
