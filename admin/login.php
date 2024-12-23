<?php
require_once '../config/Database.php';

$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    if (!empty($data['username']) && !empty($data['password'])) {
        $query = "SELECT * FROM admin WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($data['password'], $admin['password'])) {
            echo json_encode( $admin);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Invalid username or password."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Username and password are required."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Invalid action or method not allowed."]);
}
?>
