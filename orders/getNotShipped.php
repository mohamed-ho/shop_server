<?php
require_once '../config/Database.php';

$database = new Database();
$db = $database->connect(); // Connect to the database

try {
    $stmt = $db->prepare("SELECT * FROM carts WHERE shipped = 0");
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(["message" => $e->getMessage()]);
}

$db = null;
?>
