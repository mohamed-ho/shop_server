<?php

require_once '../config/Database.php';
header("Content-Type: application/json");

// Read JSON or Form Data
$input = json_decode(file_get_contents('php://input'), true);
$productId = $input['id'] ?? $_POST['id'] ?? 0;

$productId = intval($productId); // Convert to integer

if ($productId === 0) {
    http_response_code(400); 
    echo json_encode(["message" => "Product ID is required"]);
    exit;
}

$database = new Database();
$db = $database->connect();

try {
    // Enable error handling for PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the DELETE query
    $query = "DELETE FROM products WHERE id = :id";
    $stmt = $db->prepare($query);

    // Bind the parameter correctly
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo json_encode(["message" => "Product deleted successfully"]);
        } else {
            http_response_code(404); // No rows deleted
            echo json_encode(["message" => "Product not found"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Failed to delete the product"]);
    }
} catch (PDOException $e) {
    http_response_code(400); 
    echo json_encode(["error" => $e->getMessage()]);
}
?>
