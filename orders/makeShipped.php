<?php
require_once '../config/Database.php';
header('Content-Type: application/json');
// استلام cart_id من JSON أو POST Form Data
$input = json_decode(file_get_contents('php://input'), true); // قراءة بيانات JSON
$cart_id = $input['id'] ?? $_POST['id'] ?? 0; // JSON أو Form Data

$cart_id = intval($cart_id); // تحويل إلى عدد صحيح

if ($cart_id === 0) {
    http_response_code(400); 
    echo json_encode(["error" => "Invalid or missing cart ID"]);
    exit;
}

$database = new Database();
$db = $database->connect();


try {
    $stmt = $db->prepare("UPDATE `carts` SET `shipped`= 1 WHERE id = :id AND shipped = 0");
    $stmt->bindParam(':id', $cart_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["message" => "Shipped value updated successfully"]);
    } else {
        echo json_encode(["message" => "No rows updated, check the ID"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$db = null;
?>
