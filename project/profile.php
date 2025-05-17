<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['PersonID'])) {
    echo json_encode(["status" => "fail", "message" => "User not logged in"]);
    exit;
}

$host = "localhost";
$db = "db_tourism";  // ✴️ غيّر اسم قاعدة البيانات حسب اسمك
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["status" => "fail", "message" => "Database connection failed"]);
    exit;
}

$personID = $_SESSION['PersonID'];

// جلب بيانات المستخدم
$user = [
    "PersonID" => $_SESSION['PersonID'],
    "FullName" => $_SESSION['FullName'],
    "Email" => $_SESSION['Email'],
    "PhoneNum" => $_SESSION['PhoneNum']
];

// جلب بيانات الحجز
$sql = "SELECT b.BookingID, b.CreatedAt, t.destination, t.start_date 
        FROM bookings b
        JOIN trips t ON b.TripID = t.TripID
        WHERE b.PersonID = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $personID);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = [
        "BookingID" => $row["BookingID"],
        "CreatedAt" => $row["CreatedAt"],
        "destination" => $row["destination"],
        "start_date" => $row["start_date"]
    ];
}

echo json_encode([
    "status" => "success",
    "user" => $user,
    "bookings" => $bookings
]);

$conn->close();
?>
