<?php
session_start(); // ✅ هذا يكفي فقط مرة واحدة في أول الملف
header("Content-Type: application/json");

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "db_tourism");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed"]);
    exit;
}

$email = isset($_POST['email']) ? $_POST['email'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "fail", "message" => "Please fill in all fields"]);
    exit;
}

$sql = "SELECT * FROM person WHERE Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    // ملاحظة: تأكد من أن أسماء الأعمدة صحيحة
    if ($password === $row['Password']) {
        // خزّن بيانات المستخدم في الجلسة
        $_SESSION['PersonID'] = $row['PersonID'];
        $_SESSION['FullName'] = $row['FullName'];
        $_SESSION['Email'] = $row['Email'];
        $_SESSION['PhoneNum'] = $row['PhoneNum'];
        $_SESSION['Password'] = $row['Password'];
        $_SESSION['AccType'] = $row['AccType'];


        echo json_encode(["status" => "success", "message" => "Login successful"]);
    } else {
        echo json_encode(["status" => "fail", "message" => "Incorrect password"]);
    }
} else {
    echo json_encode(["status" => "fail", "message" => "Account not found"]);
}

$stmt->close();
$conn->close();
?>
