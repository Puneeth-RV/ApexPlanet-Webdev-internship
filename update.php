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

if (isset($_GET['username'])) {
    $selectedUsername = $_GET['username'];
    $sql = "SELECT username, email, phone, dob, address FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selectedUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } 
    else {
        echo "No user found.";
        exit();
    }

    if (isset($_POST['update'])) {
        // Get updated data from the form
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $dob = $_POST['dob'];
        $address = $_POST['address'];

        $sql_update = "UPDATE users SET email=?, phone=?, dob=?, address=? WHERE username=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssss", $email, $phone, $dob, $address, $selectedUsername);
        if ($stmt_update->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error updating record.";
        }
        $stmt_update->close();
    }

    $stmt->close();
} 
else {
    echo "No user selected.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #fff;
        }
        .container {
            width: 400px;
            padding: 20px;
            border: 1px solid black; 
            border-radius: 8px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label, input {
            margin-bottom: 10px;
            width: 100%;
            box-sizing: border-box;
        }
        input[type="submit"] {
            padding: 10px;
            cursor: pointer;
            border: 1px solid #000; 
            background-color: #f0f0f0;
        }
        input[type="submit"]:hover {
            background-color: #e0e0e0; 
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update User Information</h2>
        <form method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>

            <input type="submit" name="update" value="Update">
        </form>
    </div>
</body>
</html>
