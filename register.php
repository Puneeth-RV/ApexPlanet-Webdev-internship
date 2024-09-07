<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$phone = $_POST['phone'];
$dob = $_POST['dob'];
$address = $_POST['address'];
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if (empty($phone) || empty($dob) || empty($address) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    echo "<p>Please fill in all fields.</p>";
    echo '<a href="register.html">Go Back to Register</a>';
    $conn->close();
    exit();
}

if ($password !== $confirm_password) {
    echo "<p>Passwords do not match.</p>";
    echo '<a href="register.html">Go Back to Register</a>';
    $conn->close();
    exit();
}

$sql_check = "SELECT * FROM users WHERE email = ? OR username = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ss", $email, $username);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "<p>This email or username is already registered. Please use another one.</p>";
    echo '<a href="register.html">Go Back to Register</a>';
    $stmt_check->close();
    $conn->close();
    exit();
}

$stmt_check->close();

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (phone, dob, address, username, email, password) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $phone, $dob, $address, $username, $email, $hashed_password);

if ($stmt->execute()) {
    echo "<p>Registration successful!</p>";
    echo '<a href="login.html">Go to Login</a>';
} else {
    echo "<p>Error: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>
