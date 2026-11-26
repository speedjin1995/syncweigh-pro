<?php

/**
 * TranslationService
 *
 * CRUD queries on message_resource. Permission and session rules are
 * decided by the caller (TranslationController).
 */
class TranslationService
{
    const SORTABLE = ['message_key_code', 'en', 'zh', 'my', 'ne'];

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * One DataTables page of active rows.
     * Returns ['total' => int, 'filtered' => int, 'rows' => array].
     */
    public function getPage($search, $sortColumn, $sortDir, $start, $length)
    {
        if (!in_array($sortColumn, self::SORTABLE, true)) {
            $sortColumn = 'message_key_code';
        }
        if (strtolower($sortDir) === 'desc') {
            $sortDir = 'DESC';
        } else {
            $sortDir = 'ASC';
        }
        $start = max(0, (int) $start);
        $length = (int) $length;
        if ($length < 1) {
            $length = 10;
        }

        $total = $this->count('', []);

        $where = '';
        $params = [];
        if ($search !== '') {
            $where = " AND (message_key_code LIKE ? OR en LIKE ? OR zh LIKE ? OR my LIKE ? OR ne LIKE ?)";
            $like = '%' . $search . '%';
            $params = [$like, $like, $like, $like, $like];
        }

        if ($search !== '') {
            $filtered = $this->count($where, $params);
        } else {
            $filtered = $total;
        }

        // Sort column and direction are whitelisted above.
        $sql = "SELECT id, message_key_code, en, zh, my, ne FROM message_resource WHERE status = '0'"
            . $where . " ORDER BY " . $sortColumn . " " . $sortDir . " LIMIT ?, ?";

        $types = str_repeat('s', count($params)) . 'ii';
        $params[] = $start;
        $params[] = $length;

        $stmt = $this->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $this->execute($stmt);
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return ['total' => $total, 'filtered' => $filtered, 'rows' => $rows];
    }

    public function getById($id)
    {
        $stmt = $this->prepare("SELECT id, message_key_code, en, zh, my, ne FROM message_resource WHERE id = ? AND status = '0'");
        $stmt->bind_param('i', $id);
        $this->execute($stmt);
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row;
    }

    public function keyExists($keyCode)
    {
        $stmt = $this->prepare("SELECT id FROM message_resource WHERE message_key_code = ? AND status = '0' LIMIT 1");
        $stmt->bind_param('s', $keyCode);
        $this->execute($stmt);
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    public function create($keyCode, array $values, $username)
    {
        $stmt = $this->prepare("INSERT INTO message_resource (message_key_code, en, zh, my, ne, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $keyCode, $values['en'], $values['zh'], $values['my'], $values['ne'], $username);
        $this->execute($stmt);
        $stmt->close();
    }

    public function update($id, array $values, $username)
    {
        $stmt = $this->prepare("UPDATE message_resource SET en = ?, zh = ?, my = ?, ne = ?, modified_by = ?, modified_date = NOW() WHERE id = ? AND status = '0'");
        $stmt->bind_param('sssssi', $values['en'], $values['zh'], $values['my'], $values['ne'], $username, $id);
        $this->execute($stmt);
        $stmt->close();
    }

    public function softDelete($id, $username)
    {
        $stmt = $this->prepare("UPDATE message_resource SET status = '1', modified_by = ?, modified_date = NOW() WHERE id = ?");
        $stmt->bind_param('si', $username, $id);
        $this->execute($stmt);
        $stmt->close();
    }

    /* ---------------- Helpers ---------------- */

    private function count($where, array $params)
    {
        $stmt = $this->prepare("SELECT COUNT(*) AS total FROM message_resource WHERE status = '0'" . $where);
        if (count($params) > 0) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }
        $this->execute($stmt);
        $total = (int) $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();

        return $total;
    }

    private function prepare($sql)
    {
        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            throw new RuntimeException('Failed to prepare statement: ' . $this->db->error);
        }
        return $stmt;
    }

    private function execute($stmt)
    {
        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new RuntimeException($error);
        }
    }
}
