<?php
include 'handel/database.php';

$continent = $_GET['continent'] ?? null;

$sql = "SELECT * FROM trips";
if ($continent) {
    $sql .= " WHERE continent = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $continent);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$trips = [];
while ($row = $result->fetch_assoc()) {
    $trips[] = $row;
}
?>
