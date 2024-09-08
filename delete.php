<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username_db = "root";
$password = "";
$dbname = "user_management"; 

$conn = new mysqli($servername, $username_db, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_GET['username'];

$is_self_deletion = $username === $_SESSION['username'];

$sql = "DELETE FROM users WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    
    if ($is_self_deletion) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    } else {
        header("Location: usermanager.php");
        exit();
    }
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

