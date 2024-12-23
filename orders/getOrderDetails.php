<?php
require_once '../config/Database.php';

header('Content-Type: application/json');

// استلام cart_id من JSON أو POST Form Data
$input = json_decode(file_get_contents('php://input'), true); // قراءة بيانات JSON
$cart_id = $input['cart_id'] ?? $_POST['cart_id'] ?? 0; // JSON أو Form Data

$cart_id = intval($cart_id); // تحويل إلى عدد صحيح

if ($cart_id === 0) {
    http_response_code(400); 
    echo json_encode(["error" => "Invalid or missing cart ID"]);
    exit;
}

$database = new Database();
$db = $database->connect();

try {
    // جلب بيانات الكارت والمستخدم
    $cartQuery = "
        SELECT 
            carts.id AS cart_id,
            carts.date,
            carts.shipped,
            CONCAT(user.firstname, ' ', user.lastname) AS user_name,
            user.email,
            CONCAT(user.city, ' /', user.street, '/ building number ', user.number) AS address,
            user.phone
        FROM carts
        INNER JOIN user ON carts.user_id = user.id
        WHERE carts.id = :cart_id
    ";
    $cartStmt = $db->prepare($cartQuery);
    $cartStmt->bindParam(':cart_id', $cart_id);
    $cartStmt->execute();

    $cart = $cartStmt->fetch(PDO::FETCH_ASSOC);
    if (!$cart) {
        echo json_encode(["error" => "Cart not found"]);
        exit;
    }

    // جلب بيانات المنتجات المرتبطة بالكارت
    $productsQuery = "
        SELECT 
            products.id, 
            products.title, 
            products.price, 
            products.description, 
            products.image, 
            cart_products.quantity,
            (products.price * cart_products.quantity) AS total_price
        FROM cart_products
        INNER JOIN products ON cart_products.product_id = products.id
        WHERE cart_products.cart_id = :cart_id
    ";
    $productsStmt = $db->prepare($productsQuery);
    $productsStmt->bindParam(':cart_id', $cart_id);
    $productsStmt->execute();

    $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);

    // حساب إجمالي السعر
    $all_total_price = 0;
    foreach ($products as $product) {
        $all_total_price += $product['total_price'];
    }

    // إنشاء الاستجابة
    $response = [
        "cart_id" => $cart["cart_id"],
        "date" => $cart["date"],
        "shipped" => $cart["shipped"],
        "User" => [
            "name" => $cart["user_name"],
            "email" => $cart["email"],
            "address" => $cart["address"],
            "phone" => $cart["phone"],
        ],
        "products" => $products,
        "all_total_price" => $all_total_price,
    ];

    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(400); 
    echo json_encode(["error" => $e->getMessage()]);
}

$db = null;
?>
