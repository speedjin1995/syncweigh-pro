<?php
require_once __DIR__ . '/../requires/permissions.php';
require_once __DIR__ . '/../services/ProductService.php';

/**
 * ProductController
 *
 * Handles product requests coming through php/modules/Product/index.php.
 * The request's "action" field picks the handler:
 *
 *   stockTakeList      products in the Stock Take List for a plant and Batch/Drum
 *   stockTakePercentage 60/70 percentage of one product for a plant and Batch/Drum
 *   saveStockTakeSort  save that list (members and drag-and-drop order)
 */
class ProductController
{
    const MODULE = 'Master Data';
    const SUB_MODULE = 'Product';

    private $db;
    private $products;

    public function __construct($db)
    {
        $this->db = $db;
        $this->products = new ProductService($db);
    }

    public function handleRequest()
    {
        if (!isset($_SESSION['id'])) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Unauthorized']);
        }

        $action = $_POST['action'] ?? ($_GET['action'] ?? '');

        try {
            switch ($action) {
                case 'stockTakeList':
                    $this->stockTakeList();
                    break;
                case 'stockTakePercentage':
                    $this->stockTakePercentage();
                    break;
                case 'saveStockTakeSort':
                    $this->saveStockTakeSort();
                    break;
                default:
                    $this->jsonResponse(['status' => 'failed', 'message' => 'Invalid action']);
            }
        } catch (Throwable $e) {
            $this->jsonResponse(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    /* ---------------- Stock Take List ---------------- */

    private function stockTakeList()
    {
        $this->requirePermission(['edit']);
        $this->validateListKey($plantId, $batchDrum);

        $rows = $this->products->getStockTakeList($plantId, $batchDrum);
        $this->jsonResponse(['status' => 'success', 'data' => $rows]);
    }

    private function stockTakePercentage()
    {
        $this->requirePermission(['edit']);
        $this->validateListKey($plantId, $batchDrum);

        $productId = $_POST['productId'] ?? '';
        if (!ctype_digit((string) $productId)) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Invalid product']);
        }

        $percentage = $this->products->getProductPercentage((int) $productId, $plantId, $batchDrum);
        $this->jsonResponse(['status' => 'success', 'percentage' => $percentage]);
    }

    private function saveStockTakeSort()
    {
        $this->requirePermission(['edit']);
        $this->validateListKey($plantId, $batchDrum);

        // Empty is allowed: it clears the list
        $productIds = $_POST['productIds'] ?? [];
        if (!is_array($productIds)) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Invalid products']);
        }

        foreach ($productIds as $productId) {
            if (!ctype_digit((string) $productId)) {
                $this->jsonResponse(['status' => 'failed', 'message' => 'Invalid products']);
            }
        }

        $this->products->saveStockTakeList($plantId, $batchDrum, array_values(array_unique($productIds)));
        $this->jsonResponse(['status' => 'success', 'message' => 'Stock take list saved']);
    }

    /* ---------------- Helpers ---------------- */

    private function validateListKey(&$plantId, &$batchDrum)
    {
        $plantId = $_POST['plant'] ?? '';
        $batchDrum = $_POST['batchDrum'] ?? '';

        if (!ctype_digit((string) $plantId) || !in_array($batchDrum, ['Batch', 'Drum'], true)) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Plant and Batch/Drum are required']);
        }
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
