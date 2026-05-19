<?php

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/Reservation.php";

class ReservationController {

    private $db;
    private $reservation;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->reservation = new Reservation($this->db);
    }

    //  Helpers 

    private function lang(): string {
        return $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'fr';
    }

    private function redirect(string $path, array $params = []): void {
        $base   = '/scoot-rent-fixed/scoot-rent/';
        $query  = $params ? '?' . http_build_query($params) : '';
        header("Location: {$base}{$path}{$query}");
        exit;
    }

    // Create reservation 

    public function create(): void {

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->redirect('views/trottinettes.php');
        }

        $lang = $this->lang();

        if (!isset($_SESSION['user']['id'])) {
            $this->redirect('views/login.php', ['error' => 'not_logged_in', 'lang' => $lang]);
        }

        $id    = (int)($_POST['trottinette_id'] ?? 0);
        $start = trim($_POST['start_date'] ?? '');
        $end   = trim($_POST['end_date']   ?? '');

        if ($id <= 0 || empty($start) || empty($end)) {
            $this->redirect('views/trottinettes.php', ['error' => 'missing_fields', 'lang' => $lang]);
        }

        try {
            $startDT = new DateTime($start);
            $endDT   = new DateTime($end);
            $now     = new DateTime();
        } catch (Exception $e) {
            $this->redirect('views/trottinettes.php', ['error' => 'invalid_dates', 'lang' => $lang]);
        }

        if ($startDT < $now) {
            $this->redirect('views/trottinettes.php', ['error' => 'past_date', 'lang' => $lang]);
        }

        if ($endDT <= $startDT) {
            $this->redirect('views/trottinettes.php', ['error' => 'invalid_dates', 'lang' => $lang]);
        }

        // Get scooter price
        $stmt = $this->db->prepare("
            SELECT price_per_hour
            FROM trottinettes
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $trott = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$trott || !isset($trott['price_per_hour'])) {
            $this->redirect('views/trottinettes.php', ['error' => 'not_found', 'lang' => $lang]);
        }

        $pricePerHour = (float)$trott['price_per_hour'];

        // Calculate duration to next hour
        $diff  = $startDT->diff($endDT);
        $hours = ($diff->days * 24) + $diff->h + ($diff->i > 0 ? 1 : 0);
        if ($hours <= 0) $hours = 1;

        $totalPrice = $hours * $pricePerHour;

        // Save reservation
        $this->reservation->user_id        = $_SESSION['user']['id'];
        $this->reservation->trottinette_id = $id;
        $this->reservation->start_date     = $startDT->format('Y-m-d H:i:s');
        $this->reservation->end_date       = $endDT->format('Y-m-d H:i:s');
        $this->reservation->total_price    = $totalPrice;
// overlap check
if ($this->reservation->hasOverlap()) {

    header("Location: ../views/trottinettes.php?error=already_booked");

    exit;
}
        if ($this->reservation->create()) {
            $this->redirect('views/user/my_reservations.php', ['success' => 1, 'lang' => $lang]);
        }

        $this->redirect('views/trottinettes.php', ['error' => 'reservation_failed', 'lang' => $lang]);
    }

    //  Admin all reservations
    public function allReservations(): array {
        return $this->reservation->getAll();
    }

    //  User own reservations 

    public function myReservations(): array {
        if (!isset($_SESSION['user']['id'])) return [];

        $this->reservation->user_id = $_SESSION['user']['id'];
        return $this->reservation->getByUser() ?: [];
    }

    //  Admin update status 

    public function updateStatus(): void {

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->redirect('views/admin/reservations.php');
        }

        $lang = $this->lang();

        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            $this->redirect('views/login.php', ['error' => 'not_admin', 'lang' => $lang]);
        }

        $id     = (int)($_POST['id']     ?? 0);
        $status = trim($_POST['status']  ?? '');
        $allowed = ['pending', 'confirmed', 'cancelled'];

        if ($id <= 0 || !in_array($status, $allowed)) {
            $this->redirect('views/admin/reservations.php', ['error' => 'invalid', 'lang' => $lang]);
        }

        $stmt = $this->db->prepare("
            UPDATE reservations
            SET status = ?
            WHERE id = ?
        ");
        $stmt->execute([$status, $id]);

        $this->redirect('views/admin/reservations.php', ['updated' => 1, 'lang' => $lang]);
    }
}

// Route handler 
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $controller = new ReservationController();
    $action = $_GET['action'] ?? '';

    match($action) {
        'create'       => $controller->create(),
        'updateStatus' => $controller->updateStatus(),
        default        => header("Location: /scoot-rent-fixed/scoot-rent/views/trottinettes.php"),
    };
}