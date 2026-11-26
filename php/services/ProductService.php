<?php

/**
 * ProductService
 *
 * Stock take list (Stock_Take_List) queries. This table drives which products
 * the Stock Analysis report shows and in what order.
 */
class ProductService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Products in the list for a plant and Batch/Drum, in sort order, with the
     * 60/70 percentage. Returns rows of [product_id, product_code, product_name, percentage].
     */
    public function getStockTakeList($plantId, $batchDrum)
    {
        // Same 60/70 raw mat lookup as printStockDeclaration.php
        $rawMatId = 0;
        $result = $this->db->query("SELECT id FROM Raw_Mat WHERE raw_mat_code IN ('BTBI001', '3001/001') AND status = 0 LIMIT 1");
        if ($result && ($row = $result->fetch_assoc())) {
            $rawMatId = (int) $row['id'];
        }

        $sql = "SELECT STL.product_id, P.product_code, P.name AS product_name, PRW.raw_mat_basic_uom AS percentage
                FROM Stock_Take_List STL
                JOIN Product P ON P.id = STL.product_id AND P.status = '0'
                LEFT JOIN Product_RawMat PRW ON PRW.product_id = P.id AND PRW.plant_id = STL.plant_id
                    AND PRW.batch_drum = STL.batch_drum AND PRW.status = 0 AND PRW.raw_mat_id = ?
                WHERE STL.plant_id = ? AND STL.batch_drum = ?
                ORDER BY STL.sort ASC, P.name ASC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException($this->db->error);
        }

        $stmt->bind_param('iis', $rawMatId, $plantId, $batchDrum);
        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new RuntimeException($error);
        }

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Saves the list order: sort = position in $productIds, starting at 1.
     * All-or-nothing; the caller has already validated the input.
     */
    public function saveStockTakeListSort($plantId, $batchDrum, array $productIds)
    {
        $stmt = $this->db->prepare("UPDATE Stock_Take_List SET sort = ? WHERE plant_id = ? AND product_id = ? AND batch_drum = ?");
        if (!$stmt) {
            throw new RuntimeException($this->db->error);
        }

        $this->db->begin_transaction();
        try {
            foreach (array_values($productIds) as $index => $productId) {
                $sort = $index + 1;
                $stmt->bind_param('iiis', $sort, $plantId, $productId, $batchDrum);
                if (!$stmt->execute()) {
                    throw new RuntimeException($stmt->error);
                }
            }
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollback();
            throw $e;
        } finally {
            $stmt->close();
        }
    }
}
