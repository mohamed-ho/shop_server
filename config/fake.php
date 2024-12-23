<?php
require_once '../config/Database.php';

// Prepare database connection
$database = new Database();
$db = $database->connect();

// Fetch data from the fake store API
$api_url = 'https://fakestoreapi.com/carts'; // API endpoint for carts
$cart_data = json_decode(file_get_contents($api_url), true);

if ($cart_data) {
    foreach ($cart_data as $cart) {
        // Extract cart ID
        $cart_id = $cart['id'];

        // Iterate through products in the cart
        foreach ($cart['products'] as $product) {
            // Extract product details
            $product_id = $product['productId'];
            $quantity = $product['quantity'];

            // Insert data into the cart_products table
            $query = "INSERT INTO cart_products (cart_id, product_id, quantity) 
                      VALUES (:cart_id, :product_id, :quantity)";
            $stmt = $db->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':cart_id', $cart_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':quantity', $quantity);

            // Execute the query
            $stmt->execute();
        }
    }

    echo json_encode(['message' => 'Cart products populated successfully']);
} else {
    echo json_encode(['message' => 'Failed to retrieve data from the API']);
}
?>
