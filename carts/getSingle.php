<?php
header("Content-Type: application/json");
require_once '../config/Database.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if 'id' is provided in the POST data
$cartId = isset($data['id']) ? intval($data['id']) : 0;

if ($cartId > 0) {
    // Connect to the database
    $database = new Database();
    $db = $database->connect();

    // Query to fetch the cart and associated products
    $query = "SELECT 
                c.id AS cart_id, 
                c.user_id, 
                c.date, 
                GROUP_CONCAT(
                    CONCAT(
                        '{\"productId\":', cp.product_id, ',\"quantity\":', cp.quantity, '}'
                    )
                ) AS products
              FROM carts c
              LEFT JOIN cart_products cp ON c.id = cp.cart_id
              WHERE c.id = :cart_id
              GROUP BY c.id";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':cart_id', $cartId);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $products = [];
        if (!empty($row['products'])) {
            $products = json_decode("[" . $row['products'] . "]", true);
        }

        $cart = [
            'id' => $row['cart_id'],
            'user_id' => $row['user_id'],
            'date' => $row['date'],
            'products' => $products
        ];

        echo json_encode($cart);
    } else {
        http_response_code(400); 
        echo json_encode(['message' => 'Cart not found']);
    }
} else {
    http_response_code(400); 
    echo json_encode(['message' => 'Cart ID is required']);
}
?>
