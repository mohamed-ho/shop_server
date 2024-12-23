<?php
header("Content-Type: application/json");
require_once '../config/Database.php';

$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['user_id']) && !empty($data['date']) && !empty($data['products'])) {
    // إدخال العربة
    $query = "INSERT INTO carts (user_id, date) VALUES (:user_id, :date)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $data['user_id']);
    $stmt->bindParam(':date', $data['date']);
    $stmt->execute();

    $cartId = $db->lastInsertId();

    // إدخال المنتجات المرتبطة
    foreach ($data['products'] as $product) {
        $query = "INSERT INTO cart_products (cart_id, product_id, quantity) VALUES (:cart_id, :product_id, :quantity)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':cart_id', $cartId);
        $stmt->bindParam(':product_id', $product['productId']);
        $stmt->bindParam(':quantity', $product['quantity']);
        $stmt->execute();
    }

    echo json_encode(['message' => 'Cart added successfully']);
} else {
    http_response_code(400); 
    echo json_encode(['message' => 'Invalid input']);
}
?>
