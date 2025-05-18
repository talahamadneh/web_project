<?php
include 'database.php';


$tripName = $_POST['tripName'];
$tripDesc = $_POST['tripDesc'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$price_per_person = $_POST['price_per_person'];
$available_seats = $_POST['available_seats'];
$continent = $_POST['continent'];


if (isset($_FILES['tripImg']) && $_FILES['tripImg']['error'] === 0) {
    $imgName = $_FILES['tripImg']['name'];
    $imgTmp = $_FILES['tripImg']['tmp_name'];
    $imgExt = pathinfo($imgName, PATHINFO_EXTENSION);

    $newImgName = uniqid('trip_', true) . '.' . $imgExt;
    $uploadPath = '../imges/' . $newImgName;

   
    if (move_uploaded_file($imgTmp, $uploadPath)) {
     
        $sql = "INSERT INTO trips (destination, description, img, start_date, end_date, price_per_person, available_seats,Continent)
                VALUES (?, ?, ?, ?, ?, ?, ?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssis", $tripName, $tripDesc, $newImgName, $start_date, $end_date, $price_per_person, $available_seats,$continent);
        $stmt->execute();
        $stmt->close();

        header("Location: ../page2.php");
        exit();
    } 
} 

$conn->close();
?>
