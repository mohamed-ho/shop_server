<?php
require_once '../config/Database.php';

$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    if (!empty($data['id'])) {
        $query = "DELETE FROM admin WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $data['id']);

        try {
            $stmt->execute();
            echo json_encode(["message" => "Account deleted successfully."]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["message" => "Failed to delete account.", "error" => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "ID is required."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Invalid action or method not allowed."]);
}
?>
