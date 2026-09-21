<?php

class StockAdjustmentService
{
    private $db;
    private $userId;

    public function __construct($db, $userId)
    {
        $this->db = $db;
        $this->userId = $userId;
    }

    /**
     * Get raw materials with current inventory qty for a plant
     */
    public function getRawMaterialsWithQty($plantId)
    {
        $query = "SELECT rm.id, rm.raw_mat_code, rm.name, COALESCE(i.raw_mat_weight, 0) as current_qty 
                  FROM Raw_Mat rm 
                  LEFT JOIN Inventory i ON rm.id = i.raw_mat_id AND i.plant_id = ? AND i.status = '0'
                  WHERE rm.status = '0' 
                  ORDER BY rm.raw_mat_code ASC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $plantId);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                "id" => $row['id'],
                "raw_mat_code" => $row['raw_mat_code'],
                "name" => $row['name'],
                "current_qty" => floatval($row['current_qty'])
            ];
        }
        $stmt->close();

        return $data;
    }

    /**
     * Generate next adjustment number
     */
    private function generateAdjustmentNo()
    {
        $today = date('Ymd');
        $prefix = "SA-" . $today . "-";

        $result = $this->db->query("SELECT adjustment_no FROM Stock_Adjustment WHERE adjustment_no LIKE '$prefix%' ORDER BY id DESC LIMIT 1");
        if ($row = $result->fetch_assoc()) {
            $lastNum = intval(substr($row['adjustment_no'], -4));
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }

        return $prefix . $newNum;
    }

    /**
     * Get raw material code by ID
     */
    private function getRawMatCode($rawMatId)
    {
        $stmt = $this->db->prepare("SELECT raw_mat_code FROM Raw_Mat WHERE id = ?");
        $stmt->bind_param('i', $rawMatId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ? $row['raw_mat_code'] : '';
    }

    /**
     * Create stock adjustment with items
     */
    public function create($plantId, $batchDrum, $remark, $items)
    {
        $this->db->begin_transaction();

        try {
            $adjustmentNo = $this->generateAdjustmentNo();
            $adjustmentDate = date('Y-m-d');
            $totalItems = count($items);
            $totalQty = 0;
            $totalCost = 0;

            foreach ($items as $item) {
                $totalQty += abs(floatval($item['qty']));
                $totalCost += floatval($item['total_cost'] ?? 0);
            }

            // Insert header
            $headerStmt = $this->db->prepare("INSERT INTO Stock_Adjustment (adjustment_no, adjustment_date, plant_id, batch_drum, remark, total_items, total_qty, total_cost, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $headerStmt->bind_param('ssisssssi', $adjustmentNo, $adjustmentDate, $plantId, $batchDrum, $remark, $totalItems, $totalQty, $totalCost, $this->userId);
            $headerStmt->execute();
            $adjustmentId = $headerStmt->insert_id;
            $headerStmt->close();

            // Insert items and update inventory
            foreach ($items as $item) {
                $this->createItem($adjustmentId, $plantId, $item);
            }

            $this->db->commit();

            return ['success' => true, 'adjustment_no' => $adjustmentNo];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Create adjustment item and update inventory
     */
    private function createItem($adjustmentId, $plantId, $item)
    {
        $rawMatId = $item['raw_mat_id'];
        $adjustQty = floatval($item['qty']);
        $qtyBefore = floatval($item['qty_before']);
        $qtyAfter = floatval($item['qty_after']);
        $unitCost = floatval($item['unit_cost'] ?? 0);
        $totalCost = floatval($item['total_cost'] ?? 0);
        $reason = isset($item['reason']) ? $item['reason'] : '';

        $rawMatCode = $this->getRawMatCode($rawMatId);

        // Insert item
        $itemStmt = $this->db->prepare("INSERT INTO Stock_Adjustment_Items (adjustment_id, raw_mat_id, raw_mat_code, quantity_before, adjustment_qty, quantity_after, unit_cost, total_cost, reason, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $itemStmt->bind_param('iissssssi', $adjustmentId, $rawMatId, $rawMatCode, $qtyBefore, $adjustQty, $qtyAfter, $unitCost, $totalCost, $reason, $this->userId);
        $itemStmt->execute();
        $itemStmt->close();

        // Update inventory
        $this->updateInventory($rawMatId, $rawMatCode, $plantId, $qtyAfter);
    }

    /**
     * Update or create inventory record
     */
    private function updateInventory($rawMatId, $rawMatCode, $plantId, $newQty)
    {
        $checkStmt = $this->db->prepare("SELECT id FROM Inventory WHERE raw_mat_id = ? AND plant_id = ? AND status = '0'");
        $checkStmt->bind_param('ii', $rawMatId, $plantId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $invRow = $result->fetch_assoc();
        $checkStmt->close();

        if ($invRow) {
            $updateStmt = $this->db->prepare("UPDATE Inventory SET raw_mat_weight = ?, modified_by = ? WHERE id = ?");
            $updateStmt->bind_param('dii', $newQty, $this->userId, $invRow['id']);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            $insertStmt = $this->db->prepare("INSERT INTO Inventory (raw_mat_id, raw_mat_code, plant_id, raw_mat_weight, created_by) VALUES (?, ?, ?, ?, ?)");
            $insertStmt->bind_param('isidi', $rawMatId, $rawMatCode, $plantId, $newQty, $this->userId);
            $insertStmt->execute();
            $insertStmt->close();
        }
    }

    /**
     * Get adjustments list for DataTable
     */
    public function getList($params, $plantFilter = '', $hasViewAllPlants = false, $userPlants = [])
    {
        $draw = $params['draw'];
        $start = $params['start'];
        $length = $params['length'];
        $searchValue = $params['search']['value'];
        $columnIndex = $params['order'][0]['column'];
        $columnName = $params['columns'][$columnIndex]['data'];
        $sortOrder = $params['order'][0]['dir'];

        // Map column names
        $sortColumn = 'sa.id';
        $columnMap = [
            'adjustment_no' => 'sa.adjustment_no',
            'adjustment_date' => 'sa.adjustment_date',
            'total_items' => 'sa.total_items',
            'total_qty' => 'sa.total_qty'
        ];
        if (isset($columnMap[$columnName])) {
            $sortColumn = $columnMap[$columnName];
        }

        $searchQuery = "";
        if ($searchValue != '') {
            $searchValue = $this->db->real_escape_string($searchValue);
            $searchQuery = " AND (sa.adjustment_no LIKE '%$searchValue%' OR sa.remark LIKE '%$searchValue%' OR p.name LIKE '%$searchValue%')";
        }

        $plantQuery = "";
        if ($plantFilter != '') {
            $plantFilter = $this->db->real_escape_string($plantFilter);
            $plantQuery = " AND sa.plant_id = '$plantFilter'";
        } else if (!$hasViewAllPlants && !empty($userPlants)) {
            $plants = implode("', '", $userPlants);
            $plantQuery = " AND p.plant_code IN ('$plants')";
        }

        // Total records
        $totalRecords = $this->db->query("SELECT COUNT(*) as total FROM Stock_Adjustment sa WHERE sa.deleted = 0")->fetch_assoc()['total'];

        // Filtered records
        $totalFiltered = $this->db->query("SELECT COUNT(*) as total FROM Stock_Adjustment sa LEFT JOIN Plant p ON sa.plant_id = p.id WHERE sa.deleted = 0 $plantQuery $searchQuery")->fetch_assoc()['total'];

        // Fetch records
        $query = "SELECT sa.*, p.name as plant_name 
                  FROM Stock_Adjustment sa 
                  LEFT JOIN Plant p ON sa.plant_id = p.id 
                  WHERE sa.deleted = 0 $plantQuery $searchQuery 
                  ORDER BY $sortColumn $sortOrder 
                  LIMIT $start, $length";

        $result = $this->db->query($query);
        $data = [];
        $no = $start + 1;

        while ($row = $result->fetch_assoc()) {
            $data[] = [
                "no" => $no++,
                "id" => $row['id'],
                "adjustment_no" => $row['adjustment_no'],
                "adjustment_date" => date('d/m/Y', strtotime($row['adjustment_date'])),
                "plant_name" => $row['plant_name'],
                "batch_drum" => $row['batch_drum'],
                "total_items" => $row['total_items'],
                "total_qty" => number_format($row['total_qty'], 2),
                "total_cost" => number_format($row['total_cost'], 2),
                "remark" => $row['remark'],
                "created_at" => date('d/m/Y H:i', strtotime($row['created_at']))
            ];
        }

        return [
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ];
    }
}
