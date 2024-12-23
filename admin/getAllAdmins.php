<?php
// Include database configuration
require_once '../config/Database.php';

// Create database connection
$database = new Database();
$db = $database->connect();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Prepare the SQL query to get all admin users
        $query = "SELECT id, username FROM admin";
        $stmt = $db->prepare($query);

        // Execute the query
        $stmt->execute();

        // Fetch all results
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Respond with the admin data
        echo json_encode(
             $admins
        );
    } catch (PDOException $e) {
        // Handle database errors
        http_response_code(500);
        echo json_encode([
            
            "message" => "Error fetching admins",
            
        ]);
    }
} else {
    // Handle invalid request method
    http_response_code(405);
    echo json_encode([
        "message" => "Invalid request method. Use POST."
    ]);
}
?>
