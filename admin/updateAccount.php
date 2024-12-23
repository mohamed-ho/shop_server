<?php
require_once '../config/Database.php';

$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($data['id']) && (!empty($data['username']) || !empty($data['password']))) {
        $query = "UPDATE admin SET 
                    username = COALESCE(:username, username), 
                    password = COALESCE(:password, password) 
                  WHERE id = :id";
        $stmt = $db->prepare($query);

        $hashedPassword = !empty($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null;

        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $hashedPassword);

        try {
            $stmt->execute();
            echo json_encode(["message" => "Account updated successfully."]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["message" => "Failed to update account.", "error" => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "ID and at least one of username or password are required."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Invalid action or method not allowed."]);
}
?>
