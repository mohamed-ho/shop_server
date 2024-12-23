<?php
require_once '../config/Database.php';

// إعداد الاتصال بقاعدة البيانات
$database = new Database();
$db = $database->connect();

// استلام بيانات POST
$data = json_decode(file_get_contents("php://input"), true);

// التحقق من وجود معرّف المستخدم في البيانات المرسلة عبر POST
if (isset($data['user_id']) && is_numeric($data['user_id'])) {
    $user_id = $data['user_id'];  // استلام معرّف المستخدم من POST

    // جلب جميع العربات الخاصة بالمستخدم مع المنتجات الخاصة بكل عربة
    $query = "SELECT 
                c.id AS cart_id, 
                c.user_id, 
                c.date, 
                cp.product_id, 
                cp.quantity
              FROM carts c
              LEFT JOIN cart_products cp ON c.id = cp.cart_id
              WHERE c.user_id = :user_id";

    // تحضير الاستعلام
    $stmt = $db->prepare($query);

    // ربط المعامل
    $stmt->bindParam(':user_id', $user_id);

    // تنفيذ الاستعلام
    $stmt->execute();

    // التحقق إذا كانت هناك نتائج
    if ($stmt->rowCount() > 0) {
        // Fetch all cart data
        $carts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Transform the result into the desired format
        $cartData = [];
        
        foreach ($carts as $cart) {
            // Check if cart already exists in $cartData
            if (!isset($cartData[$cart['cart_id']])) {
                // Initialize the cart data
                $cartData[$cart['cart_id']] = [
                    'id' => $cart['cart_id'],
                    'user_id' => $cart['user_id'],
                    'date' => $cart['date'],
                    'products' => []
                ];
            }
            
            // Add product to the cart's products array
            $cartData[$cart['cart_id']]['products'][] = [
                'productId' => $cart['product_id'],
                'quantity' => $cart['quantity']
            ];
        }

        // Return the transformed data
        echo json_encode(array_values($cartData)); // Re-index the array to avoid associative keys
    } else {
        echo json_encode([]);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'User ID is required and must be numeric.']);
}
?>
