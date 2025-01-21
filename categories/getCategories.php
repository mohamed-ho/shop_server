<?php
require_once '../config/Database.php';

// Establish database connection
$database = new Database();
$db = $database->connect();

$query = "SELECT * FROM categories ORDER BY id ASC";
$stmt = $db->prepare($query);

try {
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($categories);
} catch (PDOException $e) {
    http_response_code(400); 
    echo json_encode(["message" => "Failed to fetch categories.", "error" => $e->getMessage()]);
}
?>
