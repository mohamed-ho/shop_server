<?php
require_once '../config/Database.php';

$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['email']) && !empty($data['password'])) {
        $query = "SELECT * FROM user WHERE email = :email AND password = :password";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']); // Ideally, hash passwords before storing

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Format the data like the provided structure
            $response = [
                "id" => $user['id'],
                "email" => $user['email'],
                "username" => $user['username'],
                "password" => $user['password'], // Expose password only if necessary
                "name" => [
                    "firstname" => $user['firstname'],
                    "lastname" => $user['lastname']
                ],
                "address" => [
                    "city" => $user['city'],
                    "street" => $user['street'],
                    "number" => $user['number'],
                    
                ],
                "phone" => $user['phone']
            ];

            echo json_encode($response);
        } else {
            http_response_code(400); 
            echo json_encode(["message" => "Invalid email or password."]);
            
        }
    } else {
        http_response_code(400); 
        echo json_encode(["message" => "Email and password are required."]);
    }
} else {
    http_response_code(400); 
    echo json_encode(["message" => "Invalid request method. Use POST."]);
}
?>
