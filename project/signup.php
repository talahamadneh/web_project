<?php
header('Content-Type: application/json'); // Important: specify JSON response type

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_tourism";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to connect to the database'
    ]);
    exit();
}

$fullname = isset($_POST['FullName']) ? trim($_POST['FullName']) : '';
$email    = isset($_POST['Email']) ? trim($_POST['Email']) : '';
$pass     = isset($_POST['Password']) ? $_POST['Password'] : '';
$phone    = isset($_POST['PhoneNum']) ? trim($_POST['PhoneNum']) : '';
$acctype  = isset($_POST['AccType']) ? trim($_POST['AccType']) : '';

$check_sql = "SELECT * FROM person WHERE Email = ?";
$stmt_check = $conn->prepare($check_sql);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'This email is already in use. Please use another email.'
    ]);
} else {
    $sql = "INSERT INTO person (FullName, Email, Password, PhoneNum, AccType)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $fullname, $email, $pass, $phone, $acctype);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Account created successfully!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred while creating the account: ' . $stmt->error
        ]);
    }
    $stmt->close();
}

$conn->close();
?>
