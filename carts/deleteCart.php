<?php
header("Content-Type: application/json");
require_once '../config/Database.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the 'cart_id' is provided
if (isset($data['cart_id'])) {
    $cartId = intval($data['cart_id']); // Get cart ID from POST data

    // Connect to the database
    $database = new Database();
    $db = $database->connect();

    // Delete the products associated with the cart
    $query = "DELETE FROM cart_products WHERE cart_id = :cart_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':cart_id', $cartId);
    $stmt->execute();

    // Delete the cart itself
    $query = "DELETE FROM carts WHERE id = :cart_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':cart_id', $cartId);
    $stmt->execute();

    echo json_encode(['message' => 'Cart deleted successfully']);
} else {
    http_response_code(400); 
    echo json_encode(['message' => 'Cart ID is required']);
}
?>
