<?php
require_once '../config/Database.php';

// Establish database connection
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['id'])) {
        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $db->prepare($query);

        try {
            $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo json_encode(["message" => "Category deleted successfully."]);
            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Category not found."]);
            }
        } catch (PDOException $e) {
            http_response_code(400); 
            echo json_encode(["message" => "Failed to delete category.", "error" => $e->getMessage()]);
        }
    } else {
        http_response_code(400); 
        echo json_encode(["message" => "Category ID is required."]);
    }
} else {
    http_response_code(400); 
    echo json_encode(["message" => "Invalid request method. Use POST."]);
}
?>
