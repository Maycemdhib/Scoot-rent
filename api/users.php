<?php
require_once __DIR__ . "/../config/Database.php";

header("Content-Type: application/json");

$database = new Database();
$pdo = $database->getConnection();

$stmt = $pdo->query("SELECT id, nom, email, role FROM users");
$data = $stmt->fetchAll();

echo json_encode($data);
