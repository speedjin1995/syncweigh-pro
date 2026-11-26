<?php
require_once __DIR__ . '/../requires/permissions.php';
require_once __DIR__ . '/../services/TranslationService.php';
require_once __DIR__ . '/../requires/translation.php';

/**
 * TranslationController
 *
 * Handles every translation request coming through php/modules/Translation/index.php.
 * The request's "action" field picks the handler:
 *
 *   list     DataTables server-side rows of message_resource
 *   get      one translation by id
 *   save     insert (no id) or update (with id)
 *   delete   soft delete (status = 1)
 */
class TranslationController
{
    const MODULE = 'Setting';
    const SUB_MODULE = 'Translation';

    private $db;
    private $translations;
    private $messages;

    public function __construct($db)
    {
        $this->db = $db;
        $this->translations = new TranslationService($db);
        $this->messages = loadTranslations($db, $_SESSION['language'] ?? 'en');
    }

    public function handleRequest()
    {
        if (!isset($_SESSION['id'])) {
            $this->jsonResponse(['status' => 'failed', 'message' => 'Unauthorized']);
        }

        $action = $_POST['action'] ?? '';

        try {
            switch ($action) {
                case 'list':
                    $this->listTranslations();
                    break;
                case 'get':
                    $this->get();
                    break;
                case 'save':
                    $this->save();
                    break;
                case 'delete':
                    $this->delete();
                    break;
                default:
                    $this->jsonResponse(['status' => 'failed', 'message' => 'Invalid action']);
            }
        } catch (Throwable $e) {
            $this->jsonResponse(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    /* ---------------- Actions ---------------- */

    private function listTranslations()
    {
        $draw = intval($_POST['draw'] ?? 0);

        if (!$this->can(['view', 'create', 'edit'])) {
            $this->jsonResponse(['draw' => $draw, 'iTotalRecords' => 0, 'iTotalDisplayRecords' => 0, 'aaData' => []]);
        }

        $columnIndex = intval($_POST['order'][0]['column'] ?? 0);
        $sortColumn = $_POST['columns'][$columnIndex]['data'] ?? 'message_key_code';
        $sortDir = $_POST['order'][0]['dir'] ?? 'asc';

        $result = $this->translations->getPage(
            trim($_POST['search']['value'] ?? ''),
            $sortColumn,
            $sortDir,
            intval($_POST['start'] ?? 0),
            intval($_POST['length'] ?? 10)
        );

        $this->jsonResponse([
            'draw' => $draw,
            'iTotalRecords' => $result['total'],
            'iTotalDisplayRecords' => $result['filtered'],
            'aaData' => $result['rows'],
        ]);
    }

    private function get()
    {
        $this->requirePermission(['edit']);

        $row = $this->translations->getById(intval($_POST['id'] ?? 0));
        if (!$row) {
            $this->jsonResponse(['status' => 'failed', 'message' => $this->msg('record_not_found_code', 'Record not found')]);
        }

        $this->jsonResponse(['status' => 'success', 'message' => $row]);
    }

    private function save()
    {
        $id = intval($_POST['id'] ?? 0);
        $values = [
            'en' => trim($_POST['en'] ?? ''),
            'zh' => trim($_POST['zh'] ?? ''),
            'my' => trim($_POST['my'] ?? ''),
            'ne' => trim($_POST['ne'] ?? ''),
        ];
        $username = $_SESSION['username'] ?? '';

        if ($values['en'] === '') {
            $this->jsonResponse(['status' => 'failed', 'message' => $this->msg('english_required_code', 'English text is required')]);
        }

        if ($id > 0) {
            $this->requirePermission(['edit']);

            // Key code is fixed on edit: pages reference it in code.
            $this->translations->update($id, $values, $username);
            $this->jsonResponse(['status' => 'success', 'message' => $this->msg('updated_successfully_code', 'Updated Successfully!!')]);
        }

        $this->requirePermission(['create']);

        $keyCode = trim($_POST['messageKeyCode'] ?? '');
        if (!preg_match('/^[A-Za-z0-9_]+$/', $keyCode)) {
            $this->jsonResponse(['status' => 'failed', 'message' => $this->msg('invalid_key_code_code', 'Message key code may only contain letters, numbers and underscores')]);
        }

        if ($this->translations->keyExists($keyCode)) {
            $this->jsonResponse(['status' => 'failed', 'message' => $this->msg('key_code_exists_code', 'Message key code already exists')]);
        }

        $this->translations->create($keyCode, $values, $username);
        $this->jsonResponse(['status' => 'success', 'message' => $this->msg('added_successfully_code', 'Added Successfully!!')]);
    }

    private function delete()
    {
        $this->requirePermission(['cancelled']);

        $this->translations->softDelete(intval($_POST['id'] ?? 0), $_SESSION['username'] ?? '');
        $this->jsonResponse(['status' => 'success', 'message' => $this->msg('deleted_code', 'Deleted')]);
    }

    /* ---------------- Helpers ---------------- */

    // Raw text for JSON responses, falling back to English when the key is missing.
    private function msg($key, $fallback)
    {
        if (isset($this->messages[$key])) {
            return $this->messages[$key];
        }
        return $fallback;
    }

    private function can(array $permissions)
    {
        return hasModulePermission(self::MODULE, self::SUB_MODULE, $permissions);
    }

    private function requirePermission(array $permissions)
    {
        if (!$this->can($permissions)) {
            $this->jsonResponse(['status' => 'failed', 'message' => $this->msg('no_permission_code', 'You do not have permission to do this')]);
        }
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
