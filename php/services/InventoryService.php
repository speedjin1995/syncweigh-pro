<?php

/**
 * InventoryService
 *
 * Single place for moving stock in and out of the Inventory table.
 * Stock is tracked per raw material, plant and batch/drum.
 *
 * Quantities are in KG (raw_mat_weight). raw_mat_basic_uom is kept in sync
 * as weight x the raw material's KG rate from Raw_Mat_UOM (rate 1 if none set).
 *
 * These methods do not open or commit a transaction. Callers that make several
 * stock movements together should wrap them in begin_transaction()/commit()
 * so the row locks (SELECT ... FOR UPDATE) hold until everything is saved.
 *
 * Usage:
 *   $inventory = new InventoryService($db, $userId);
 *   $inventory->stockIn($rawMatId, $plantId, 'Batch', 1250.5);
 *   $inventory->stockOut($rawMatId, $plantId, 'Drum', 300);
 *   $inventory->setStock($rawMatId, $plantId, 'Batch', 900); // stock take
 *
 * Stock adjustments (Stock_Adjustment header + items) are also handled here:
 *   getAdjustmentRawMaterials(), createAdjustment(), getAdjustment(),
 *   updateAdjustment(), deleteAdjustment(), getAdjustmentList()
 *
 * Each movement returns:
 *   ['inventory_id' => int, 'qty_before' => float, 'qty_after' => float]
 */
class InventoryService
{
    const KG_UNIT_ID = 2;
    const BATCH_DRUM_VALUES = ['Batch', 'Drum'];

    private $db;
    private $userId;

    public function __construct($db, $userId)
    {
        $this->db = $db;
        $this->userId = $userId;
    }

    /* ================= Inventory ================= */

    /**
     * Inventory list for DataTables (server-side processing).
     * $plantCode / $batchDrum are optional filters. Without a plant filter,
     * users lacking "view all plants" only see their own plants.
     */
    public function getList($params, $plantCode = '', $batchDrum = '', $hasViewAllPlants = false, $userPlants = [])
    {
        $draw = intval($params['draw'] ?? 0);
        $start = intval($params['start'] ?? 0);
        $length = intval($params['length'] ?? 10);
        $searchValue = $params['search']['value'] ?? '';
        $columnIndex = $params['order'][0]['column'] ?? 1;
        $columnName = $params['columns'][$columnIndex]['data'] ?? '';
        $sortOrder = strtolower($params['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

        $columnMap = [
            'raw_mat_code' => 'rm.raw_mat_code',
            'name' => 'rm.name',
            'plant_name' => 'p.name',
            'batch_drum' => 'i.batch_drum',
            'raw_mat_weight' => 'CAST(i.raw_mat_weight AS DECIMAL(15,2))'
        ];
        $sortColumn = $columnMap[$columnName] ?? 'rm.raw_mat_code';

        $where = " WHERE i.status = '0'";
        if ($plantCode !== '' && $plantCode !== '-') {
            $where .= " AND i.plant_code = '" . $this->db->real_escape_string($plantCode) . "'";
        } else if (!$hasViewAllPlants) {
            $plants = implode("', '", array_map([$this->db, 'real_escape_string'], (array) $userPlants));
            $where .= " AND i.plant_code IN ('$plants')";
        }
        if ($batchDrum !== '') {
            $where .= " AND i.batch_drum = '" . $this->db->real_escape_string($batchDrum) . "'";
        }

        $searchWhere = '';
        if ($searchValue !== '') {
            $s = $this->db->real_escape_string($searchValue);
            $searchWhere = " AND (rm.raw_mat_code LIKE '%$s%' OR rm.name LIKE '%$s%' OR p.name LIKE '%$s%')";
        }

        $from = " FROM Inventory i JOIN Raw_Mat rm ON i.raw_mat_id = rm.id LEFT JOIN Plant p ON i.plant_id = p.id";
        $totalRecords = $this->db->query("SELECT COUNT(*) AS c" . $from . $where)->fetch_assoc()['c'];
        $totalFiltered = $this->db->query("SELECT COUNT(*) AS c" . $from . $where . $searchWhere)->fetch_assoc()['c'];

        $result = $this->db->query("SELECT i.*, rm.raw_mat_code, rm.name, p.name AS plant_name" . $from . $where . $searchWhere
            . " ORDER BY $sortColumn $sortOrder LIMIT $start, $length");

        $data = [];
        $no = $start + 1;
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                "id" => $row['id'],
                "no" => $no++,
                "raw_mat_code" => $row['raw_mat_code'],
                "name" => $row['name'],
                "plant_name" => $row['plant_name'],
                "batch_drum" => $row['batch_drum'],
                "raw_mat_weight" => $row['raw_mat_weight'],
                "raw_mat_count" => $row['raw_mat_count']
            ];
        }

        return [
            "draw" => $draw,
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ];
    }

    /**
     * One inventory record with its raw material and basic unit details, or null.
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT i.*, rm.raw_mat_code, rm.name, rm.basic_uom AS basic_uom_id, u.unit AS basic_uom
                                    FROM Inventory i
                                    JOIN Raw_Mat rm ON i.raw_mat_id = rm.id
                                    LEFT JOIN Unit u ON rm.basic_uom = u.id
                                    WHERE i.id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            return null;
        }

        return [
            'id' => $row['id'],
            'raw_mat_id' => $row['raw_mat_id'],
            'raw_mat_code' => $row['raw_mat_code'],
            'name' => $row['name'],
            'raw_mat_basic_uom' => $row['raw_mat_basic_uom'],
            'raw_mat_weight' => $row['raw_mat_weight'],
            'raw_mat_count' => $row['raw_mat_count'],
            'basic_uom' => $row['basic_uom'] ?? '',
            'basic_uom_id' => $row['basic_uom_id']
        ];
    }

    /**
     * Direct edit of an inventory record (the Edit Inventory modal).
     */
    public function updateRecord($id, $basicUom, $weight, $count)
    {
        foreach (['Basic UOM' => $basicUom, 'Weight' => $weight, 'Drum' => $count] as $label => $value) {
            if (!is_numeric($value)) {
                throw new Exception("$label must be a number");
            }
        }

        $stmt = $this->db->prepare("UPDATE Inventory SET raw_mat_basic_uom = ?, raw_mat_weight = ?, raw_mat_count = ?, modified_by = ? WHERE id = ? AND status = '0'");
        $stmt->bind_param('ssssi', $basicUom, $weight, $count, $this->userId, $id);
        $this->execute($stmt, 'update inventory');
    }

    /**
     * Current stock row for a raw material, plant and batch/drum, or null if none.
     */
    public function getStock($rawMatId, $plantId, $batchDrum)
    {
        $this->validateBatchDrum($batchDrum);
        return $this->findRow($rawMatId, $plantId, $batchDrum, false);
    }

    /**
     * Current stock quantity in KG (0 if there is no inventory row yet).
     */
    public function getQty($rawMatId, $plantId, $batchDrum)
    {
        $row = $this->getStock($rawMatId, $plantId, $batchDrum);
        return $row ? floatval($row['raw_mat_weight']) : 0.0;
    }

    /**
     * Add stock (e.g. purchase received). Creates the inventory row if needed.
     */
    public function stockIn($rawMatId, $plantId, $batchDrum, $qty)
    {
        $qty = $this->validateQty($qty);
        return $this->move($rawMatId, $plantId, $batchDrum, function ($before) use ($qty) {
            return $before + $qty;
        });
    }

    /**
     * Remove stock (e.g. sales or production usage).
     * By default stock may go negative, matching how weighing already behaves.
     * Pass $allowNegative = false to throw instead when there is not enough stock.
     */
    public function stockOut($rawMatId, $plantId, $batchDrum, $qty, $allowNegative = true)
    {
        $qty = $this->validateQty($qty);
        return $this->move($rawMatId, $plantId, $batchDrum, function ($before) use ($qty, $allowNegative) {
            $after = $before - $qty;
            if (!$allowNegative && $after < 0) {
                throw new Exception("Insufficient stock: available " . number_format($before, 2) . " KG, requested " . number_format($qty, 2) . " KG");
            }
            return $after;
        });
    }

    /**
     * Add or remove stock by a signed amount (+ in, - out).
     * With $floorAtZero the result is clamped to 0 instead of going negative.
     */
    public function adjustBy($rawMatId, $plantId, $batchDrum, $delta, $floorAtZero = false)
    {
        if (!is_numeric($delta) || floatval($delta) == 0) {
            throw new Exception("Adjustment quantity must not be zero");
        }
        $delta = floatval($delta);
        return $this->move($rawMatId, $plantId, $batchDrum, function ($before) use ($delta, $floorAtZero) {
            $after = $before + $delta;
            return $floorAtZero ? max(0, $after) : $after;
        });
    }

    /**
     * Set stock to an exact quantity (stock take).
     */
    public function setStock($rawMatId, $plantId, $batchDrum, $qty)
    {
        if (!is_numeric($qty) || floatval($qty) < 0) {
            throw new Exception("Stock quantity must be zero or more");
        }
        $qty = floatval($qty);
        return $this->move($rawMatId, $plantId, $batchDrum, function ($before) use ($qty) {
            return $qty;
        });
    }

    /**
     * Lock the row, compute the new quantity and save it.
     */
    private function move($rawMatId, $plantId, $batchDrum, callable $calculate)
    {
        $this->validateBatchDrum($batchDrum);

        $row = $this->findRow($rawMatId, $plantId, $batchDrum, true);
        $before = $row ? floatval($row['raw_mat_weight']) : 0.0;
        $after = round($calculate($before), 2);
        $basicUom = round($after * $this->getKgRate($rawMatId), 2);

        if ($row) {
            $inventoryId = $row['id'];
            $stmt = $this->db->prepare("UPDATE Inventory SET raw_mat_weight = ?, raw_mat_basic_uom = ?, modified_by = ? WHERE id = ?");
            $stmt->bind_param('ddsi', $after, $basicUom, $this->userId, $inventoryId);
            $this->execute($stmt, 'update inventory');
        } else {
            $this->assertRawMatExists($rawMatId);
            $plantCode = $this->getPlantCode($plantId);

            $stmt = $this->db->prepare("INSERT INTO Inventory (raw_mat_id, raw_mat_basic_uom, raw_mat_weight, plant_id, plant_code, batch_drum, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('iddissss', $rawMatId, $basicUom, $after, $plantId, $plantCode, $batchDrum, $this->userId, $this->userId);
            $this->execute($stmt, 'create inventory');
            $inventoryId = $this->db->insert_id;
        }

        return [
            'inventory_id' => (int) $inventoryId,
            'qty_before' => $before,
            'qty_after' => $after
        ];
    }

    private function findRow($rawMatId, $plantId, $batchDrum, $forUpdate)
    {
        $sql = "SELECT * FROM Inventory WHERE raw_mat_id = ? AND plant_id = ? AND batch_drum = ? AND status = '0' ORDER BY id ASC LIMIT 1";
        if ($forUpdate) {
            $sql .= " FOR UPDATE";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iis', $rawMatId, $plantId, $batchDrum);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    private function getKgRate($rawMatId)
    {
        $unitId = self::KG_UNIT_ID;
        $stmt = $this->db->prepare("SELECT rate FROM Raw_Mat_UOM WHERE raw_mat_id = ? AND unit_id = ? AND status = '0' LIMIT 1");
        $stmt->bind_param('ii', $rawMatId, $unitId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return ($row && is_numeric($row['rate'])) ? floatval($row['rate']) : 1.0;
    }

    private function assertRawMatExists($rawMatId)
    {
        $stmt = $this->db->prepare("SELECT id FROM Raw_Mat WHERE id = ?");
        $stmt->bind_param('i', $rawMatId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            throw new Exception("Raw material not found");
        }
    }

    private function getPlantCode($plantId)
    {
        $stmt = $this->db->prepare("SELECT plant_code FROM Plant WHERE id = ?");
        $stmt->bind_param('i', $plantId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            throw new Exception("Plant not found");
        }
        return $row['plant_code'];
    }

    private function validateBatchDrum($batchDrum)
    {
        if (!in_array($batchDrum, self::BATCH_DRUM_VALUES, true)) {
            throw new Exception("Batch/Drum must be 'Batch' or 'Drum'");
        }
    }

    private function validateQty($qty)
    {
        if (!is_numeric($qty) || floatval($qty) <= 0) {
            throw new Exception("Quantity must be greater than zero");
        }
        return floatval($qty);
    }

    private function execute($stmt, $action)
    {
        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Failed to $action: $error");
        }
        $stmt->close();
    }

    /* ================= Stock adjustment ================= */

    /**
     * Get raw materials with current inventory qty for a plant
     */
    public function getAdjustmentRawMaterials($plantId, $batchDrum)
    {
        $query = "SELECT rm.id, rm.raw_mat_code, rm.name, COALESCE(i.raw_mat_weight, 0) as current_qty
                  FROM Raw_Mat rm
                  LEFT JOIN Inventory i ON rm.id = i.raw_mat_id AND i.plant_id = ? AND i.batch_drum = ? AND i.status = '0'
                  WHERE rm.status = '0'
                  ORDER BY rm.raw_mat_code ASC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('is', $plantId, $batchDrum);
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
     * Create stock adjustment with items.
     * Each item moves stock by its signed qty (stock never goes below 0).
     * Stock is moved first so the header can be inserted once with its final totals,
     * based on what actually moved rather than the browser's figures. (Updating the
     * header afterwards would fire TRG_UPD_STK_ADJ and log a spurious edit.)
     */
    public function createAdjustment($plantId, $batchDrum, $remark, $items)
    {
        $plantId = intval($plantId);
        if (!is_array($items) || empty($items)) {
            return ['success' => false, 'message' => 'Please add at least one item'];
        }

        $this->db->begin_transaction();

        try {
            $adjustmentNo = $this->generateAdjustmentNo();
            $adjustmentDate = date('Y-m-d');

            // 1) Validate each line and move its stock
            $lines = [];
            $totalQty = 0;
            $totalCost = 0;
            foreach (array_values($items) as $index => $item) {
                $line = $this->moveAdjustmentLine($plantId, $batchDrum, $item, $index + 1);
                $totalQty += abs($line['moved']);
                $totalCost += $line['total_cost'];
                $lines[] = $line;
            }

            // 2) Header with final totals
            $totalItems = count($lines);
            $totalQtyStr = (string) round($totalQty, 2);
            $totalCostStr = (string) round($totalCost, 2);
            $headerStmt = $this->db->prepare("INSERT INTO Stock_Adjustment (adjustment_no, adjustment_date, plant_id, batch_drum, remark, total_items, total_qty, total_cost, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $headerStmt->bind_param('ssississi', $adjustmentNo, $adjustmentDate, $plantId, $batchDrum, $remark, $totalItems, $totalQtyStr, $totalCostStr, $this->userId);
            $this->execute($headerStmt, 'save stock adjustment');
            $adjustmentId = $this->db->insert_id;

            // 3) Items
            foreach ($lines as $line) {
                $itemStmt = $this->db->prepare("INSERT INTO Stock_Adjustment_Items (adjustment_id, raw_mat_id, raw_mat_code, quantity_before, adjustment_qty, quantity_after, unit_cost, total_cost, reason, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $itemStmt->bind_param('iisdddddsi', $adjustmentId, $line['raw_mat_id'], $line['raw_mat_code'], $line['qty_before'], $line['moved'], $line['qty_after'], $line['unit_cost'], $line['total_cost'], $line['reason'], $this->userId);
                $this->execute($itemStmt, "save item {$line['line_no']}");
            }

            $this->db->commit();

            return ['success' => true, 'adjustment_no' => $adjustmentNo];
        } catch (Throwable $e) {
            // Throwable also catches PHP errors (e.g. bad bind_param), so nothing is half-saved
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * One adjustment (header + active items) for the edit modal.
     * Each raw material's current_qty is the stock as it would be with this
     * adjustment taken back out, so the modal can recalculate NEW QTY from it.
     * Returns null if the adjustment does not exist or was deleted.
     */
    public function getAdjustment($id)
    {
        $id = intval($id);
        $stmt = $this->db->prepare("SELECT sa.*, p.plant_code FROM Stock_Adjustment sa LEFT JOIN Plant p ON sa.plant_id = p.id WHERE sa.id = ? AND sa.deleted = 0");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $header = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$header) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT raw_mat_id, adjustment_qty, unit_cost, reason FROM Stock_Adjustment_Items WHERE adjustment_id = ? AND deleted = 0 ORDER BY id ASC");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        $reversed = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = [
                'raw_mat_id' => $row['raw_mat_id'],
                'qty' => floatval($row['adjustment_qty']),
                'unit_cost' => floatval($row['unit_cost']),
                'reason' => $row['reason']
            ];
            $reversed[$row['raw_mat_id']] = ($reversed[$row['raw_mat_id']] ?? 0) + floatval($row['adjustment_qty']);
        }
        $stmt->close();

        $rawMaterials = $this->getAdjustmentRawMaterials($header['plant_id'], $header['batch_drum']);
        foreach ($rawMaterials as &$rawMat) {
            if (isset($reversed[$rawMat['id']])) {
                $rawMat['current_qty'] = max(0, round($rawMat['current_qty'] - $reversed[$rawMat['id']], 2));
            }
        }
        unset($rawMat);

        return [
            'id' => (int) $header['id'],
            'adjustment_no' => $header['adjustment_no'],
            'adjustment_date' => date('d/m/Y', strtotime($header['adjustment_date'])),
            'plant_id' => $header['plant_id'],
            'plant_code' => $header['plant_code'],
            'batch_drum' => $header['batch_drum'],
            'remark' => $header['remark'],
            'items' => $items,
            'raw_materials' => $rawMaterials
        ];
    }

    /**
     * Edit an adjustment. Plant and Batch/Drum stay as saved.
     * The old items' stock movement is taken back first, then the new items are
     * applied, so inventory ends up as if only the new items had ever been made.
     */
    public function updateAdjustment($id, $remark, $items)
    {
        $id = intval($id);
        if (!is_array($items) || empty($items)) {
            return ['success' => false, 'message' => 'Please add at least one item'];
        }

        $this->db->begin_transaction();

        try {
            $header = $this->lockAdjustment($id);
            $plantId = intval($header['plant_id']);
            $batchDrum = $header['batch_drum'];

            $this->reverseAdjustmentItems($id, $plantId, $batchDrum);

            $lines = [];
            $totalQty = 0;
            $totalCost = 0;
            foreach (array_values($items) as $index => $item) {
                $line = $this->moveAdjustmentLine($plantId, $batchDrum, $item, $index + 1);
                $totalQty += abs($line['moved']);
                $totalCost += $line['total_cost'];
                $lines[] = $line;
            }

            foreach ($lines as $line) {
                $itemStmt = $this->db->prepare("INSERT INTO Stock_Adjustment_Items (adjustment_id, raw_mat_id, raw_mat_code, quantity_before, adjustment_qty, quantity_after, unit_cost, total_cost, reason, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $itemStmt->bind_param('iisdddddsi', $id, $line['raw_mat_id'], $line['raw_mat_code'], $line['qty_before'], $line['moved'], $line['qty_after'], $line['unit_cost'], $line['total_cost'], $line['reason'], $this->userId);
                $this->execute($itemStmt, "save item {$line['line_no']}");
            }

            $totalItems = count($lines);
            $totalQtyStr = (string) round($totalQty, 2);
            $totalCostStr = (string) round($totalCost, 2);
            $headerStmt = $this->db->prepare("UPDATE Stock_Adjustment SET remark = ?, total_items = ?, total_qty = ?, total_cost = ?, modified_by = ? WHERE id = ?");
            $headerStmt->bind_param('sissii', $remark, $totalItems, $totalQtyStr, $totalCostStr, $this->userId, $id);
            $this->execute($headerStmt, 'update stock adjustment');

            $this->db->commit();

            return ['success' => true, 'adjustment_no' => $header['adjustment_no']];
        } catch (Throwable $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Soft delete an adjustment and take its stock movement back out of inventory.
     */
    public function deleteAdjustment($id)
    {
        $id = intval($id);

        $this->db->begin_transaction();

        try {
            $header = $this->lockAdjustment($id);

            $this->reverseAdjustmentItems($id, intval($header['plant_id']), $header['batch_drum']);

            $stmt = $this->db->prepare("UPDATE Stock_Adjustment SET deleted = 1, modified_by = ? WHERE id = ?");
            $stmt->bind_param('ii', $this->userId, $id);
            $this->execute($stmt, 'delete stock adjustment');

            $this->db->commit();

            return ['success' => true, 'adjustment_no' => $header['adjustment_no']];
        } catch (Throwable $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get adjustments list for DataTable
     */
    public function getAdjustmentList($params, $plantFilter = '', $hasViewAllPlants = false, $userPlants = [])
    {
        $draw = $params['draw'];
        $start = intval($params['start']);
        $length = intval($params['length']);
        $searchValue = $params['search']['value'];
        $columnIndex = $params['order'][0]['column'];
        $columnName = $params['columns'][$columnIndex]['data'];
        $sortOrder = strtolower($params['order'][0]['dir']) === 'asc' ? 'ASC' : 'DESC';

        // Map column names
        $sortColumn = 'sa.id';
        $columnMap = [
            'adjustment_no' => 'sa.adjustment_no',
            'adjustment_date' => 'sa.adjustment_date',
            'plant_name' => 'p.name',
            'batch_drum' => 'sa.batch_drum',
            'total_items' => 'sa.total_items',
            'total_qty' => 'sa.total_qty',
            'total_cost' => 'sa.total_cost',
            'remark' => 'sa.remark'
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
            $plantQuery = " AND p.plant_code = '$plantFilter'";
        } else if (!$hasViewAllPlants && !empty($userPlants)) {
            $plants = implode("', '", array_map([$this->db, 'real_escape_string'], $userPlants));
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

    /**
     * Generate next adjustment number (SA-YYYYMMDD-0001).
     * Called inside the save transaction; FOR UPDATE makes concurrent saves wait
     * for each other instead of both taking the same number.
     */
    private function generateAdjustmentNo()
    {
        $prefix = "SA-" . date('Ymd') . "-";

        $result = $this->db->query("SELECT adjustment_no FROM Stock_Adjustment WHERE adjustment_no LIKE '$prefix%' ORDER BY id DESC LIMIT 1 FOR UPDATE");
        if ($row = $result->fetch_assoc()) {
            $newNum = str_pad(intval(substr($row['adjustment_no'], -4)) + 1, 4, '0', STR_PAD_LEFT);
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
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            throw new Exception("Raw material not found");
        }
        return $row['raw_mat_code'];
    }

    /**
     * Validate one adjustment line and move its stock.
     * Returns the line's data, including the quantity actually moved
     * (less than requested if stock would have gone below 0).
     */
    private function moveAdjustmentLine($plantId, $batchDrum, $item, $lineNo)
    {
        $rawMatId = intval($item['raw_mat_id'] ?? 0);
        $requestedQty = $item['qty'] ?? '';
        $unitCost = floatval($item['unit_cost'] ?? 0);
        $reason = trim((string) ($item['reason'] ?? ''));

        if (!$rawMatId) {
            throw new Exception("Item $lineNo: please select a raw material");
        }
        if (!is_numeric($requestedQty) || floatval($requestedQty) == 0) {
            throw new Exception("Item $lineNo: adjustment quantity must be a non-zero number");
        }
        if ($unitCost < 0) {
            throw new Exception("Item $lineNo: unit cost cannot be negative");
        }

        $rawMatCode = $this->getRawMatCode($rawMatId);

        // before/after come from the locked inventory row, not the browser
        $movement = $this->adjustBy($rawMatId, $plantId, $batchDrum, floatval($requestedQty), true);
        $moved = round($movement['qty_after'] - $movement['qty_before'], 2);

        return [
            'line_no' => $lineNo,
            'raw_mat_id' => $rawMatId,
            'raw_mat_code' => $rawMatCode,
            'qty_before' => $movement['qty_before'],
            'qty_after' => $movement['qty_after'],
            'moved' => $moved,
            'unit_cost' => $unitCost,
            'total_cost' => round(abs($moved) * $unitCost, 2),
            'reason' => $reason
        ];
    }

    /**
     * Take back the stock movement of every active item of an adjustment and
     * soft delete those items. Stock never goes below 0. Caller owns the transaction.
     */
    private function reverseAdjustmentItems($adjustmentId, $plantId, $batchDrum)
    {
        $stmt = $this->db->prepare("SELECT id, raw_mat_id, adjustment_qty FROM Stock_Adjustment_Items WHERE adjustment_id = ? AND deleted = 0 ORDER BY id ASC FOR UPDATE");
        $stmt->bind_param('i', $adjustmentId);
        $stmt->execute();
        $oldItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        foreach ($oldItems as $old) {
            $qty = floatval($old['adjustment_qty']);
            if ($qty != 0) {
                $this->adjustBy($old['raw_mat_id'], $plantId, $batchDrum, -$qty, true);
            }

            $itemId = intval($old['id']);
            $delStmt = $this->db->prepare("UPDATE Stock_Adjustment_Items SET deleted = 1, modified_by = ? WHERE id = ?");
            $delStmt->bind_param('ii', $this->userId, $itemId);
            $this->execute($delStmt, 'remove old adjustment item');
        }
    }

    /**
     * Lock and return an active adjustment header, or throw.
     */
    private function lockAdjustment($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM Stock_Adjustment WHERE id = ? AND deleted = 0 FOR UPDATE");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $header = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$header) {
            throw new Exception("Stock adjustment not found");
        }
        return $header;
    }
}
