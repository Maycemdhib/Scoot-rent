<?php
require_once __DIR__ . "/../config/Database.php";

header("Content-Type: application/json");

$database = new Database();
$pdo = $database->getConnection();

$sql = "
SELECT r.*, u.nom AS user_name, t.marque AS trottinette_name
FROM reservations r
JOIN users u ON r.user_id = u.id
JOIN trottinettes t ON r.trottinette_id = t.id
";

$stmt = $pdo->query($sql);
$data = $stmt->fetchAll();

echo json_encode($data);