<?php
require_once __DIR__ . '/../requires/permissions.php';
require_once __DIR__ . '/../services/InventoryService.php';

/**
 * InventoryController
 *
 * Handles every inventory request coming through php/Inventory/index.php.
 * The request's "action" field picks the handler:
 *
 *   list               Inventory DataTable
 *   get                One inventory record (Edit modal)
 *   update             Save the Edit modal
 *   stock_in           Add stock          (raw_mat_id, plant_id, batch_drum, qty)
 *   stock_out          Remove stock       (raw_mat_id, plant_id, batch_drum, qty)
 *   adj_raw_materials  Raw materials with current qty for the adjustment modal
 *   adj_create         Save a stock adjustment
 *   adj_get            One stock adjustment with items (Edit modal)
 *   adj_update         Save an edited stock adjustment (stock is re-applied)
 *   adj_delete         Delete a stock adjustment (stock is taken back)
 *   adj_list           Stock adjustment DataTable
 */
class InventoryController
{
    const MODULE = 'Stock Management';
    const SUB_MODULE = 'Inventory';
    const ADJ_MODULE = 'Stock Adjustment';

    private $db;
    private $inventory;

    public function __construct($db)
    {
        $this->db = $db;
        $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
        $this->inventory = new InventoryService($db, $userId);
    }

    public function handleRequest()
    {
        if (!isset($_SESSION['id'])) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Unauthorized']);
        }

        $action = $_POST['action'] ?? ($_GET['action'] ?? '');

        try {
            switch ($action) {
                case 'list':
                    $this->getList();
                    break;
                case 'get':
                    $this->get();
                    break;
                case 'update':
                    $this->update();
                    break;
                case 'stock_in':
                case 'stock_out':
                    $this->moveStock($action);
                    break;
                case 'adj_raw_materials':
                    $this->getAdjustmentRawMaterials();
                    break;
                case 'adj_create':
                    $this->createAdjustment();
                    break;
                case 'adj_get':
                    $this->getAdjustment();
                    break;
                case 'adj_update':
                    $this->updateAdjustment();
                    break;
                case 'adj_delete':
                    $this->deleteAdjustment();
                    break;
                case 'adj_list':
                    $this->getAdjustmentList();
                    break;
                default:
                    $this->jsonResponse(['status' => 'failed', 'message' => 'Invalid action']);
            }
        } catch (Throwable $e) {
            $this->jsonResponse(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    /* ---------------- Inventory ---------------- */

    private function getList()
    {
        $this->requirePermission(['view', 'create', 'edit', 'cancelled']);

        $plantCode = trim($_POST['plant'] ?? '');
        $batchDrum = trim($_POST['batch_drum'] ?? '');

        $this->jsonResponse($this->inventory->getList(
            $_POST,
            $plantCode,
            $batchDrum,
            $this->can(['view_all_plants']),
            $_SESSION['plant'] ?? []
        ));
    }

    private function get()
    {
        $id = intval($_POST['id'] ?? 0);
        $record = $id ? $this->inventory->getById($id) : null;

        if (!$record) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Inventory record not found']);
        }
        $this->jsonResponse(['status' => 'success', 'message' => $record]);
    }

    private function update()
    {
        $this->requirePermission(['edit']);

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Inventory record is required']);
        }

        $this->inventory->updateRecord(
            $id,
            $this->numberOrZero($_POST['basicUom'] ?? ''),
            $this->numberOrZero($_POST['weight'] ?? ''),
            $this->numberOrZero($_POST['drum'] ?? '')
        );
        $this->jsonResponse(['status' => 'success', 'message' => 'Updated Successfully!!']);
    }

    private function moveStock($action)
    {
        $this->requirePermission(['create', 'edit']);

        $rawMatId = intval($_POST['raw_mat_id'] ?? 0);
        $plantId = intval($_POST['plant_id'] ?? 0);
        $batchDrum = trim($_POST['batch_drum'] ?? '');
        $qty = $_POST['qty'] ?? '';

        if (!$rawMatId || !$plantId) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Raw material and plant are required']);
        }

        $this->db->begin_transaction();
        try {
            $result = $action === 'stock_in'
                ? $this->inventory->stockIn($rawMatId, $plantId, $batchDrum, $qty)
                : $this->inventory->stockOut($rawMatId, $plantId, $batchDrum, $qty, !empty($_POST['allow_negative']));
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollback();
            throw $e;
        }

        $this->jsonResponse(['status' => 'success', 'message' => 'Stock updated', 'data' => $result]);
    }

    /* ---------------- Stock adjustment ---------------- */

    private function getAdjustmentRawMaterials()
    {
        $this->requireAdjPermission(['create', 'edit']);

        $plantId = trim($_POST['plant'] ?? '');
        $batchDrum = trim($_POST['batch_drum'] ?? '');

        if ($plantId === '' || $batchDrum === '') {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Plant and Batch/Drum are required']);
        }

        $this->jsonResponse(['status' => 'success', 'data' => $this->inventory->getAdjustmentRawMaterials($plantId, $batchDrum)]);
    }

    private function createAdjustment()
    {
        $this->requireAdjPermission(['create']);

        $plantId = trim($_POST['plant'] ?? '');
        $batchDrum = trim($_POST['batch_drum'] ?? '');
        $remark = trim($_POST['remark'] ?? '');
        $items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];

        if ($plantId === '' || $batchDrum === '' || empty($items)) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Please fill in all required fields']);
        }

        $result = $this->inventory->createAdjustment($plantId, $batchDrum, $remark, $items);

        if ($result['success']) {
            $this->jsonResponse(['status' => 'success', 'message' => "Stock adjustment {$result['adjustment_no']} saved successfully!!"]);
        }
        $this->jsonResponse(['status' => 'failed', 'message' => $result['message']]);
    }

    private function getAdjustment()
    {
        $this->requireAdjPermission(['view', 'edit']);
        $adjustment = $this->loadOwnAdjustment();
        $this->jsonResponse(['status' => 'success', 'message' => $adjustment]);
    }

    private function updateAdjustment()
    {
        $this->requireAdjPermission(['edit']);
        $adjustment = $this->loadOwnAdjustment();

        $remark = trim($_POST['remark'] ?? '');
        $items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];

        if (empty($items)) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Please fill in all required fields']);
        }

        $result = $this->inventory->updateAdjustment($adjustment['id'], $remark, $items);

        if ($result['success']) {
            $this->jsonResponse(['status' => 'success', 'message' => "Stock adjustment {$result['adjustment_no']} updated successfully!!"]);
        }
        $this->jsonResponse(['status' => 'failed', 'message' => $result['message']]);
    }

    private function deleteAdjustment()
    {
        $this->requireAdjPermission(['cancelled']);
        $adjustment = $this->loadOwnAdjustment();

        $result = $this->inventory->deleteAdjustment($adjustment['id']);

        if ($result['success']) {
            $this->jsonResponse(['status' => 'success', 'message' => "Stock adjustment {$result['adjustment_no']} deleted successfully!!"]);
        }
        $this->jsonResponse(['status' => 'failed', 'message' => $result['message']]);
    }

    /**
     * Load the adjustment named by $_POST['id'], making sure the user may see its plant.
     */
    private function loadOwnAdjustment()
    {
        $id = intval($_POST['id'] ?? 0);
        $adjustment = $id ? $this->inventory->getAdjustment($id) : null;

        if (!$adjustment) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Stock adjustment not found']);
        }
        if (!$this->canAdj(['view_all_plants']) && !in_array($adjustment['plant_code'], $_SESSION['plant'] ?? [])) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'You do not have permission to do this']);
        }
        return $adjustment;
    }

    private function getAdjustmentList()
    {
        $this->requireAdjPermission(['view', 'create', 'edit', 'cancelled']);

        $this->jsonResponse($this->inventory->getAdjustmentList(
            $_POST,
            trim($_POST['plant'] ?? ''),
            $this->canAdj(['view_all_plants']),
            $_SESSION['plant'] ?? [],
            trim($_POST['batch_drum'] ?? ''),
            $this->toDbDate($_POST['from_date'] ?? ''),
            $this->toDbDate($_POST['to_date'] ?? '')
        ));
    }

    /* ---------------- Helpers ---------------- */

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

    // Stock adjustment has its own module; delete is the 'cancelled' permission
    private function canAdj(array $permissions)
    {
        return hasModulePermission(self::MODULE, self::ADJ_MODULE, $permissions);
    }

    private function requireAdjPermission(array $permissions)
    {
        if (!$this->canAdj($permissions)) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'You do not have permission to do this']);
        }
    }

    // d-m-Y from the search bar -> Y-m-d, or '' when empty / invalid
    private function toDbDate($value)
    {
        $date = DateTime::createFromFormat('!d-m-Y', trim((string) $value));
        return $date ? $date->format('Y-m-d') : '';
    }

    private function numberOrZero($value)
    {
        $value = trim((string) $value);
        return $value === '' ? '0' : $value;
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
