<?php
require_once '../config//Database.php';

$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($data['username']) && !empty($data['password'])) {
        $query = "INSERT INTO admin (username, password) VALUES (:username, :password)";
        $stmt = $db->prepare($query);

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $hashedPassword);

        try {
            $stmt->execute();
            echo json_encode(["message" => "Account added successfully."]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { // Unique constraint violation
                http_response_code(400);
                echo json_encode(["message" => "Username already exists."]);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Failed to add account.", "error" => $e->getMessage()]);
            }
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Username and password are required."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed."]);
}
?>
