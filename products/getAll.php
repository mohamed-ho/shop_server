<?php
header("Content-Type: application/json");
require_once '../config/Database.php';

$database = new Database();
$db = $database->connect();

$query = "SELECT * FROM products";
$stmt = $db->prepare($query);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format products with nested rating
$structuredProducts = [];
foreach ($products as $product) {
    $structuredProducts[] = [
        "id" => $product['id'],
        "title" => $product['title'],
        "price" => $product['price'],
        "description" => $product['description'],
        "category_id" => $product['category_id'],
        "image" => $product['image'],
        "rating" => [
            "rate" => $product['rating_rate'],
            "count" => $product['rating_count']
        ]
    ];
}
echo json_encode($structuredProducts);
?>
                            