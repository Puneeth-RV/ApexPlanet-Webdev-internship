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

$errors = [];
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $dob = $_POST['dob'];
    $address = trim($_POST['address']);

    // Server-Side Validation
    if (empty($email) || empty($phone) || empty($dob) || empty($address)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number must be 10 digits.";
    }

    if (count($errors) === 0) {
        $sql = "UPDATE users SET email=?, phone=?, dob=?, address=? WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $email, $phone, $dob, $address, $username);

        if ($stmt->execute()) {
            $successMessage = "User updated successfully!";
        } else {
            $errors[] = "Error updating user: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
} else {
    if (isset($_GET['username'])) {
        $username = $_GET['username'];

        $sql = "SELECT * FROM users WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();
    } else {
        $errors[] = "Username parameter is missing.";
        $user = null; // Explicitly set to null to avoid further errors
    }
}
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
            background-color: #f5f5f5;
        }
        .container {
            width: 400px;
            padding: 20px;
            border: 1px solid black;
            border-radius: 8px;
            text-align: left;
            background-color: #fff;
            position: relative;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label, input, textarea {
            margin-bottom: 10px;
            width: 100%;
            box-sizing: border-box;
        }
        input, textarea {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            padding: 10px;
            cursor: pointer;
            border: 1px solid #000;
            background-color: #f0f0f0;
            margin-bottom: 20px;
        }
        input[type="submit"]:hover {
            background-color: #e0e0e0;
        }
        .back-button {
            padding: 5px 10px;
            border: 1px solid #000;
            background-color: #f0f0f0;
            color: #000;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #e0e0e0;
        }
        textarea {
            resize: none;
            height: 80px;
        }
        .error, .success {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update User</h2>
        <form action="update.php" method="post">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
            
            <div class="error">
                <?php 
                if (count($errors) > 0) {
                    echo implode('<br>', $errors);
                }
                ?>
            </div>

            <div class="success">
                <?php 
                if ($successMessage) {
                    echo $successMessage;
                }
                ?>
            </div>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>" required>

            <label for="address">Address:</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>

            <input type="submit" value="Update User">
        </form>
        
        <a href="usermanager.php" class="back-button">Back to User Manager</a>
    </div>
</body>
</html>







