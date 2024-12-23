<?php
header("Content-Type: application/json");
require_once '../config/Database.php';

// Read POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if Category_id is provided in the POST request
if (isset($data['category_id'])) {
    $database = new Database();
    $db = $database->connect();

    $query = "SELECT * FROM products WHERE category_id = :category_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':category_id', $data['category_id']);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
} else {
    http_response_code(400); 
    echo json_encode(["message" => "category_id is required"]);
}
?>
