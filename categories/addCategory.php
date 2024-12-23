<?php
require_once '../config/Database.php';

// Establish database connection
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['name']) && !empty($data['image'])) {
        $query = "INSERT INTO categories (name, image) VALUES (:name, :image)";
        $stmt = $db->prepare($query);

        try {
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':image', $data['image']);
            $stmt->execute();

            echo json_encode(["message" => "Category added successfully."]);
        } catch (PDOException $e) {
            http_response_code(400); 
            echo json_encode(["message" => "Failed to add category.", ]);
        }
    } else {
        http_response_code(400); 
        echo json_encode(["message" => "Name and Image are required."]);
    }
} else {
    http_response_code(400); 
    echo json_encode(["message" => "Invalid request method. Use POST."]);
}
?>
