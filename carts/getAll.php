<?php
header("Content-Type: application/json");
require_once '../config/Database.php';

$database = new Database();
$db = $database->connect();

// استعلام لجلب العربات والمنتجات مع GROUP_CONCAT
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
          GROUP BY c.id";

$stmt = $db->prepare($query);
$stmt->execute();

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// تحويل البيانات من نص إلى JSON
$carts = [];
foreach ($result as $row) {
    $products = [];
    if (!empty($row['products'])) {
        $products = json_decode("[" . $row['products'] . "]", true);
    }

    $carts[] = [
        'id' => $row['cart_id'],
        'user_id' => $row['user_id'],
        'date' => $row['date'],
        'products' => $products
    ];
}

echo json_encode($carts);
?>
