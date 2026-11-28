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
        $rawMatId = $this->getBitumenRawMatId();

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
     * 60/70 percentage of one product for a plant and Batch/Drum, or null when its
     * BOM has no 60/70 row.
     */
    public function getProductPercentage($productId, $plantId, $batchDrum)
    {
        $rawMatId = $this->getBitumenRawMatId();

        $stmt = $this->db->prepare("SELECT raw_mat_basic_uom FROM Product_RawMat
                WHERE product_id = ? AND plant_id = ? AND batch_drum = ? AND status = 0 AND raw_mat_id = ? LIMIT 1");
        if (!$stmt) {
            throw new RuntimeException($this->db->error);
        }

        $stmt->bind_param('iisi', $productId, $plantId, $batchDrum, $rawMatId);
        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new RuntimeException($error);
        }

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ? $row['raw_mat_basic_uom'] : null;
    }

    // Same 60/70 raw mat lookup as printStockDeclaration.php
    private function getBitumenRawMatId()
    {
        $result = $this->db->query("SELECT id FROM Raw_Mat WHERE raw_mat_code IN ('BTBI001', '3001/001') AND status = 0 LIMIT 1");
        if ($result && ($row = $result->fetch_assoc())) {
            return (int) $row['id'];
        }
        return 0;
    }

    /**
     * Replaces the list for a plant and Batch/Drum: sort = position in $productIds,
     * starting at 1. All-or-nothing; the caller has already validated the input.
     * Hard delete is fine here: the table has no status column and uploadBomList.php
     * rebuilds it the same way.
     */
    public function saveStockTakeList($plantId, $batchDrum, array $productIds)
    {
        $deleteStmt = $this->db->prepare("DELETE FROM Stock_Take_List WHERE plant_id = ? AND batch_drum = ?");
        if (!$deleteStmt) {
            throw new RuntimeException($this->db->error);
        }

        $insertStmt = $this->db->prepare("INSERT INTO Stock_Take_List (plant_id, product_id, batch_drum, sort) VALUES (?, ?, ?, ?)");
        if (!$insertStmt) {
            $error = $this->db->error;
            $deleteStmt->close();
            throw new RuntimeException($error);
        }

        $this->db->begin_transaction();
        try {
            $deleteStmt->bind_param('is', $plantId, $batchDrum);
            if (!$deleteStmt->execute()) {
                throw new RuntimeException($deleteStmt->error);
            }

            foreach (array_values($productIds) as $index => $productId) {
                $sort = $index + 1;
                $insertStmt->bind_param('iisi', $plantId, $productId, $batchDrum, $sort);
                if (!$insertStmt->execute()) {
                    throw new RuntimeException($insertStmt->error);
                }
            }
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollback();
            throw $e;
        } finally {
            $deleteStmt->close();
            $insertStmt->close();
        }
    }
}
