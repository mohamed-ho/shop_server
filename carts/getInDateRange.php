<?php
header("Content-Type: application/json");
require_once '../config/Database.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Set default dates if not provided in the POST data
$startDate = isset($data['start']) ? $data['start'] : '0000-00-00';
$endDate = isset($data['end']) ? $data['end'] : '9999-12-31';

// Connect to the database
$database = new Database();
$db = $database->connect();

// Get carts within the specified date range
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
          WHERE c.date BETWEEN :start_date AND :end_date
          GROUP BY c.id";

$stmt = $db->prepare($query);
$stmt->bindParam(':start_date', $startDate);
$stmt->bindParam(':end_date', $endDate);
$stmt->execute();

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Transform the data
$carts = [];
foreach ($result as $row) {
    $products = json_decode("[" . $row['products'] . "]", true) ?? [];
    $carts[] = [
        'id' => $row['cart_id'],
        'user_id' => $row['user_id'],
        'date' => $row['date'],
        'products' => $products
    ];
}

// Return the response as JSON
echo json_encode($carts);
?>
