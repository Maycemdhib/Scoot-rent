<?php

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/Trottinette.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class TrottinetteController {

    private $db;
    private $trottinette;

    private $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif'
    ];

    private $maxFileSize = 2 * 1024 * 1024; // 2MB

    public function __construct() {

        $database = new Database();

        $this->db = $database->getConnection();

        $this->trottinette = new Trottinette($this->db);
    }

    //  language helper
    private function lang() {
        return $_SESSION['lang'] ?? 'fr';
    }

    // IMAGE UPLOAD
    private function handleImageUpload() {

        if (empty($_FILES['image']['name'])) {
            return null;
        }

        $file = $_FILES['image'];

        $tmpPath = $file['tmp_name'];

        $mimeType = mime_content_type($tmpPath);

        if (!in_array($mimeType, $this->allowedTypes)) {
            return ['error' => 'invalid_image_type'];
        }

        if ($file['size'] > $this->maxFileSize) {
            return ['error' => 'file_too_large'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $safeName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        $targetDir = __DIR__ . "/../public/uploads/";

        $targetFile = $targetDir . $safeName;

        if (!move_uploaded_file($tmpPath, $targetFile)) {
            return ['error' => 'upload_failed'];
        }

        return ['name' => $safeName];
    }

    //  CREATE
    public function create() {

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ../views/admin/trottinettes.php");
            exit;
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            http_response_code(403);
            exit("Forbidden");
        }

        $upload = $this->handleImageUpload();

        if (isset($upload['error'])) {
            header("Location: ../views/admin/trottinettes.php?error=" . $upload['error']);
            exit;
        }

        $this->trottinette->name =
            trim($_POST['name']);

        $this->trottinette->brand =
            trim($_POST['brand']);

        $this->trottinette->autonomy =
            (int) $_POST['autonomy'];

        $this->trottinette->price_per_hour =
            (float) $_POST['price_per_hour'];

        $this->trottinette->description =
            trim($_POST['description']);

        $this->trottinette->image =
            $upload['name'] ?? 'default.jpg';

        if ($this->trottinette->create()) {

            header("Location: ../views/admin/trottinettes.php?success=1");

        } else {

            header("Location: ../views/admin/trottinettes.php?error=create_failed");
        }

        exit;
    }

    //  READ ALL
    public function readAll() {
        return $this->trottinette->readAll();
    }

    //  READ ONE
    public function readOne($id) {

        $this->trottinette->id = (int) $id;

        return $this->trottinette->readOne();
    }

    //  UPDATE
    public function update() {

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ../views/admin/trottinettes.php");
            exit;
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            http_response_code(403);
            exit("Forbidden");
        }

        $id = (int) $_POST['id'];

        $this->trottinette->id = $id;

        $this->trottinette->name = trim($_POST['name']);

        $this->trottinette->brand = trim($_POST['brand']);

        $this->trottinette->autonomy = (int) $_POST['autonomy'];

        $this->trottinette->price_per_hour = (float) $_POST['price_per_hour'];

        $this->trottinette->description = trim($_POST['description']);

        // keep old image
        $existing = $this->readOne($id);

        $this->trottinette->image = $existing['image'] ?? 'default.jpg';

        // new image
        if (!empty($_FILES['image']['name'])) {

            $upload = $this->handleImageUpload();

            if (isset($upload['error'])) {

                header("Location: ../views/admin/trottinettes.php?error=" . $upload['error']);

                exit;
            }

            $this->trottinette->image = $upload['name'];
        }

        if ($this->trottinette->update()) {

            header("Location: ../views/admin/trottinettes.php?updated=1");

        } else {

            header("Location: ../views/admin/trottinettes.php?error=update_failed");
        }

        exit;
    }

    //  DELETE
    public function delete($id) {

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            http_response_code(403);
            exit("Forbidden");
        }

        $this->trottinette->id = (int) $id;

        // (optional improvement: delete image file here)

        if ($this->trottinette->delete()) {

            header("Location: ../views/admin/trottinettes.php?deleted=1");

        } else {

            header("Location: ../views/admin/trottinettes.php?error=delete_failed");
        }

        exit;
    }
}

// ROUTER 
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $controller = new TrottinetteController();

    $action = $_GET['action'] ?? '';

    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

    match($action) {

        'create' => $controller->create(),

        'update' => $controller->update(),

        'delete' => $id ? $controller->delete($id) : header("Location: ../views/admin/trottinettes.php"),

        default => header("Location: ../views/admin/trottinettes.php"),
    };
}