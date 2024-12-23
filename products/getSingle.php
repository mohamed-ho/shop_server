<?php
header("Content-Type: application/json");
require_once '../config/Database.php';

// Read POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the ID is provided in the POST request
if (isset($data['id'])) {
    $database = new Database();
    $db = $database->connect();

    $query = "SELECT * FROM products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data['id']);
    $stmt->execute();

    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $response = [
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
        echo json_encode($response);
    } else {
        http_response_code(400); 
        echo json_encode(["message" => "Product not found"]);
    }
} else {
    http_response_code(400); 
    echo json_encode(["message" => "Product ID is required"]);
}
?>
