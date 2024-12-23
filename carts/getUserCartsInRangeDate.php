<?php
require_once '../config/Database.php';

// إعداد الاتصال بقاعدة البيانات
$database = new Database();
$db = $database->connect();

// استلام بيانات POST
$data = json_decode(file_get_contents("php://input"), true);

// التحقق من وجود معرّف المستخدم وتواريخ البداية والنهاية في البيانات المرسلة عبر POST
if (isset($data['user_id']) && is_numeric($data['user_id']) && isset($data['start_date']) && isset($data['end_date'])) {
    $user_id = $data['user_id'];  // استلام معرّف المستخدم من POST
    $start_date = $data['start_date'];  // تاريخ البداية من POST
    $end_date = $data['end_date'];  // تاريخ النهاية من POST

    // التحقق من صحة التواريخ
    if (strtotime($start_date) && strtotime($end_date)) {
        // جلب جميع العربات الخاصة بالمستخدم في النطاق الزمني المحدد
        $query = "SELECT * FROM carts 
                  WHERE user_id = :user_id 
                  AND date BETWEEN :start_date AND :end_date";

        // تحضير الاستعلام
        $stmt = $db->prepare($query);

        // ربط المعاملات
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);

        // تنفيذ الاستعلام
        $stmt->execute();

        // التحقق إذا كانت هناك نتائج
        if ($stmt->rowCount() > 0) {
            $carts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($carts); // إرجاع البيانات بتنسيق JSON
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([]);
           
        }
    } else { http_response_code(400); // Bad Request
        echo json_encode([
     
            'message' =>'Invalid date format. Please provide dates in the correct format (YYYY-MM-DD).',
        ]);

    
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode([
      
        'message' =>'User ID, start_date, and end_date are required.',
    ]);
    
}
?>
