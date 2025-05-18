<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "db_tourism");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

$sql = "SELECT TripID AS trip_id, start_date, end_date, price_per_person FROM trips WHERE destination = 'Istanbul'";
$result = $conn->query($sql);

$trips = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $trips[] = $row;
    }
}

echo json_encode($trips);
$conn->close();
