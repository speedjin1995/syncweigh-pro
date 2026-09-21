<?php
session_start();
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../requires/permissions.php';
require_once __DIR__ . '/../services/StockAdjustmentService.php';

class StockAdjustmentController
{
    private $service;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
        $this->service = new StockAdjustmentService($db, $userId);
    }

    /**
     * Handle incoming request
     */
    public function handleRequest()
    {
        if (!isset($_SESSION['id'])) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Unauthorized']);
            return;
        }

        $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

        switch ($action) {
            case 'getRawMaterials':
                $this->getRawMaterials();
                break;
            case 'create':
                $this->create();
                break;
            case 'list':
                $this->getList();
                break;
            default:
                $this->jsonResponse(['status' => 'failed', 'message' => 'Invalid action']);
        }
    }

    /**
     * Get raw materials with current qty
     */
    private function getRawMaterials()
    {
        $plantId = isset($_POST['plant']) ? trim($_POST['plant']) : '';

        if (empty($plantId)) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Plant is required']);
            return;
        }

        $data = $this->service->getRawMaterialsWithQty($plantId);
        $this->jsonResponse(['status' => 'success', 'data' => $data]);
    }

    /**
     * Create new stock adjustment
     */
    private function create()
    {
        $plantId = isset($_POST['plant']) ? trim($_POST['plant']) : '';
        $batchDrum = isset($_POST['batch_drum']) ? trim($_POST['batch_drum']) : '';
        $remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';
        $items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];

        if (empty($plantId) || empty($batchDrum) || empty($items)) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Please fill in all required fields']);
            return;
        }

        $result = $this->service->create($plantId, $batchDrum, $remark, $items);

        if ($result['success']) {
            $this->jsonResponse(['status' => 'success', 'message' => "Stock adjustment {$result['adjustment_no']} saved successfully!!"]);
        } else {
            $this->jsonResponse(['status' => 'failed', 'message' => $result['message']]);
        }
    }

    /**
     * Get list for DataTable
     */
    private function getList()
    {
        $plant = isset($_POST['plant']) ? $_POST['plant'] : '';
        $hasViewAllPlants = hasModulePermission('Stock Management', 'Inventory', ['view_all_plants']);
        $userPlants = isset($_SESSION['plant']) ? $_SESSION['plant'] : [];

        $result = $this->service->getList($_POST, $plant, $hasViewAllPlants, $userPlants);
        $this->jsonResponse($result);
    }

    /**
     * Send JSON response
     */
    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

// Initialize and handle request
$controller = new StockAdjustmentController($db);
$controller->handleRequest();
$db->close();
