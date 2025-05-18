<?php

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['AccType']) || $_SESSION['AccType'] !== 'Employee') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Only employees can add trips.']);
    exit;
}

// اتصال بقاعدة البيانات
$host = "localhost";
$dbname = "db_tourism";
$username = "root";
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // قراءة البيانات المرسلة JSON
    $json = file_get_contents('php://input');
    $trips = json_decode($json, true);

    if (!$trips || !is_array($trips)) {
        throw new Exception("Invalid data received");
    }

    $stmt = $pdo->prepare("INSERT INTO trips (destination, start_date, end_date, price_per_person, available_seats) VALUES (?, ?, ?, ?, ?)");

    $insertedCount = 0;
    foreach ($trips as $index => $trip) {
        if (
            empty($trip['destination']) ||
            empty($trip['start_date']) ||
            empty($trip['end_date']) ||
            !isset($trip['price_per_person']) ||
            !isset($trip['available_seats'])
        ) {
            throw new Exception("Missing fields in trip data at index $index");
        }

        // تحقق بسيط من تاريخ (YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $trip['start_date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $trip['end_date'])) {
            throw new Exception("Invalid date format in trip data at index $index");
        }

        $stmt->execute([
            $trip['destination'],
            $trip['start_date'],
            $trip['end_date'],
            $trip['price_per_person'],
            $trip['available_seats']
        ]);

        $insertedCount++;
    }

    echo json_encode(['success' => true, 'message' => "$insertedCount trips added successfully."]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
