<?php
require_once '../config/Database.php';

// Establish database connection
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['id'])) {
        try {
            // Start the transaction
            $db->beginTransaction();

            // Delete related products
            $deleteProductsQuery = "DELETE FROM products WHERE category_id = :id";
            $deleteProductsStmt = $db->prepare($deleteProductsQuery);
            $deleteProductsStmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
            $deleteProductsStmt->execute();
    
            // Delete the category
            $deleteCategoryQuery = "DELETE FROM categories WHERE id = :id";
            $deleteCategoryStmt = $db->prepare($deleteCategoryQuery);
            $deleteCategoryStmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
            $deleteCategoryStmt->execute();
    
            if ($deleteCategoryStmt->rowCount() > 0) {
                $db->commit(); // Commit the transaction
                echo json_encode(["message" => "Category deleted successfully."]);
            } else {
                $db->rollBack(); // Roll back the transaction
                http_response_code(400);
                echo json_encode(["message" => "Category not found."]);
            }
        } catch (PDOException $e) {
            $db->rollBack(); // Roll back the transaction
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
