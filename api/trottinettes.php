<?php
require_once __DIR__ . "/../config/Database.php";

header("Content-Type: application/json");

$database = new Database();
$pdo = $database->getConnection();

$stmt = $pdo->query("SELECT * FROM trottinettes");
$data = $stmt->fetchAll();

echo json_encode($data);