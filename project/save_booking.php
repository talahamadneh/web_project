<?php
header('Content-Type: application/json');

// الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = '';
$dbname = "db_tourism";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // الحصول على البيانات المرسلة
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // التحقق من وجود اسم المستخدم
    $stmt = $conn->prepare("SELECT PersonID, Email FROM person WHERE FullName = :fullname");
    $stmt->bindParam(':fullname', $data['fullname']);
    $stmt->execute();

    $person = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$person) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: Full name not found.'
        ]);
        exit;
    }

    // التحقق من تطابق الإيميل مع الاسم
    if ($person['Email'] !== $data['email']) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: Email does not match the given full name.'
        ]);
        exit;
    }

    $person_id = $person['PersonID'];

    // حساب عدد الأشخاص الكلي
    $totalPersons = $data['adults'] + $data['children'];

    // إنشاء الحجز
    $stmt = $conn->prepare("INSERT INTO bookings (
        TripID, PersonID, Adults, Children, TotalPersons, TotalPrice
    ) VALUES (
        :trip_id, :person_id, :adults, :children, :total_persons, :total_price
    )");

    // ربط القيم
    $stmt->bindParam(':trip_id', $data['trip_id']);
    $stmt->bindParam(':person_id', $person_id);
    $stmt->bindParam(':adults', $data['adults'], PDO::PARAM_INT);
    $stmt->bindParam(':children', $data['children'], PDO::PARAM_INT);
    $stmt->bindParam(':total_persons', $totalPersons, PDO::PARAM_INT);
    $stmt->bindParam(':total_price', $data['total_price']);

    $stmt->execute();
    $booking_id = $conn->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Booking saved successfully',
        'booking_id' => $booking_id
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn = null;
?>
