<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loggedInUsername = $_SESSION['username'];

if (isset($_GET['username'])) {
    $selectedUsername = $_GET['username'];

    $sql = "DELETE FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selectedUsername);

    if ($stmt->execute()) {
        if ($selectedUsername == $loggedInUsername) {
            session_destroy();
            header("Location: login.html");
            exit();
        } 
        else {
            header("Location: dashboard.php");
            exit();
        }
    } 
    else {
        echo "Error deleting user.";
    }

    $stmt->close();
} 
else {
    echo "No user selected.";
}

$conn->close();
?>
