<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management";

$conn = new mysqli($servername, $username, $password, $dbname);


if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}


$email = $_POST['email'];
$password = $_POST['password'];


if(empty($email)||empty($password)){
    echo "<p>Please fill in all fields.</p>";
    echo '<a href="login.html">Go Back to Login</a>';
    $conn->close();
    exit();
}


$sql = "SELECT * FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();


    if (password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php"); // Redirect to dashboard
        exit();
    } 
    else {
        echo "<p>Invalid email or password.</p>";
    }
} 
else {
    echo "<p>No user found with this email.</p>";
}

$stmt->close();
$conn->close();
?>

