<?php
require_once '../config/Database.php';

$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $query = "INSERT INTO user (email, username, password, firstname, lastname, city, street, number, phone) 
              VALUES (:email, :username, :password, :firstname, :lastname, :city, :street, :number, :phone)";

    $stmt = $db->prepare($query);

    try {
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $data['password']); // Hash the password in production
        $stmt->bindParam(':firstname', $data['name']['firstname']);
        $stmt->bindParam(':lastname', $data['name']['lastname']);
        $stmt->bindParam(':city', $data['address']['city']);
        $stmt->bindParam(':street', $data['address']['street']);
        $stmt->bindParam(':number', $data['address']['number']);
        $stmt->bindParam(':phone', $data['phone']);

        $stmt->execute();
        echo json_encode(["message" => "User created successfully."]);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') { 
            http_response_code(400); // Integrity constraint violation
            echo json_encode(["message" => "Email already exists."]);
        } else {
            http_response_code(400); 
            echo json_encode(["message" => "Failed to create user.", "error" => $e->getMessage()]);
        }
    }
} else {
    http_response_code(400); 
    echo json_encode(["message" => "Invalid request method. Use POST."]);
}
?>
