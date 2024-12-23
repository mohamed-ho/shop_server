<?php
header("Content-Type: application/json");
require_once '../config/Database.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate input data
if (isset($data['id'], $data['title'], $data['price'], $data['category'], $data['image']) && isset($data['rating']['rate'])&& isset($data['rating']['count'])){
    $id = $data['id'];
    $title = $data['title'];
    $price = $data['price'];
    $description = isset($data['description']) ? $data['description'] : null;
    $category = $data['category'];  // Category name (string)
    $image = $data['image'];
    $rating_rate = $data['rating']['rate'];
    $rating_count = $data['rating']['count'];

    // Establish DB connection
    $database = new Database();
    $db = $database->connect();

    // Check if the category exists in the categories table
    $query = "SELECT id FROM categories WHERE id = :category";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':category', $category);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Get the category_id
        $category_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $category_id = $category_data['id'];

        // Update the product
        $query = "UPDATE products SET title = :title, price = :price, description = :description, 
                  category_id = :category_id, image = :image, rating_rate = :rating_rate, 
                  rating_count = :rating_count WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':rating_rate', $rating_rate);
        $stmt->bindParam(':rating_count', $rating_count);
        $stmt->execute();

        echo json_encode(['message' => 'Product updated successfully']);
    } else {
        http_response_code(400); 
        echo json_encode(['message' => 'Invalid category']);
    }
} else {
    http_response_code(400); 
    echo json_encode(['message' => 'Missing required fields']);
}
?>
