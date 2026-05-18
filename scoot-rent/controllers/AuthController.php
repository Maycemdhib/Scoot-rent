<?php

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/User.php";

class AuthController {

    private $db;
    private $user;

    public function __construct() {

        // Start session safely
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $database   = new Database();
        $this->db   = $database->getConnection();
        $this->user = new User($this->db);
    }

    //  REGISTER
    public function register() {

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ../views/register.php");
            exit;
        }

        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        // Keep current language
        $lang = $_SESSION['lang'] ?? 'fr';

        // Validation
        if (empty($name) || empty($email) || empty($pass)) {
            header("Location: ../views/register.php?error=missing_fields&lang=$lang");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../views/register.php?error=invalid_email&lang=$lang");
            exit;
        }

        if (strlen($pass) < 6) {
            header("Location: ../views/register.php?error=weak_password&lang=$lang");
            exit;
        }

        // User data
        $this->user->name     = $name;
        $this->user->email    = $email;
        $this->user->password = $pass;
        $this->user->role     = "client";

        // Register
        if ($this->user->register()) {

            header("Location: ../views/login.php?success=1&lang=$lang");
            exit;

        } else {

            header("Location: ../views/register.php?error=email_taken&lang=$lang");
            exit;

        }
    }


    //  LOGIN
    public function login() {

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ../views/login.php");
            exit;
        }

        $lang = $_SESSION['lang'] ?? 'fr';

        $this->user->email    = trim($_POST['email'] ?? '');
        $this->user->password = $_POST['password'] ?? '';

        $user = $this->user->login();

        if ($user) {

            // Regenerate session BEFORE storing user
            session_regenerate_id(true);

            $_SESSION['user'] = [
                "id"    => $user['id'],
                "name"  => $user['name'],
                "email" => $user['email'],
                "role"  => $user['role']
            ];

            // Optional remember login time
            $_SESSION['logged_in_at'] = time();

            // Redirect by role
            if ($user['role'] === "admin") {

                header("Location: ../views/admin/dashboard.php?lang=$lang");

            } else {

                header("Location: ../views/dashboard.php?lang=$lang");

            }

            exit;

        } else {

            header("Location: ../views/login.php?error=invalid_credentials&lang=$lang");
            exit;

        }
    }

    //  LOGOUT
    public function logout() {

        // Keep language before destroying session
        $lang = $_SESSION['lang'] ?? 'fr';

        // Empty session
        $_SESSION = [];

        // Delete session cookie
        if (ini_get("session.use_cookies")) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();

        // Redirect
        header("Location: ../views/login.php?logged_out=1&lang=$lang");
        exit;
    }
}


// Route Dispatcher


if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {

    $controller = new AuthController();

    $action = $_GET['action'] ?? '';

    match($action) {

        'register' => $controller->register(),
        'login'    => $controller->login(),
        'logout'   => $controller->logout(),

        default    => header("Location: ../views/login.php"),

    };
}