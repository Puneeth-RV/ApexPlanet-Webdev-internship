<?php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $dob = $_POST['dob'];
    $address = trim($_POST['address']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Server-Side Validation
    if (empty($phone) || empty($dob) || empty($address) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "Please fill in all fields.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Invalid phone number. Must be 10 digits.";
    }

    if (count($errors) === 0) {
        $sql_check = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ss", $email, $username);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $errors[] = "This email or username is already registered. Please use another one.";
        }

        $stmt_check->close();

        if (count($errors) === 0) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (phone, dob, address, username, email, password) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $phone, $dob, $address, $username, $email, $hashed_password);

            try {
                if ($stmt->execute()) {
                    $successMessage = "Registration successful!";
                }
            } catch (mysqli_sql_exception $e) {
                $errors[] = "Error: " . $e->getMessage();
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .container {
            width: 300px;
            padding: 15px;
            border: 1px solid black;
            border-radius: 8px;
            text-align: left;
            background-color: #fff;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label, input {
            margin-bottom: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        input[type="submit"] {
            padding: 8px;
            cursor: pointer;
            border: 1px solid #000;
            background-color: #f0f0f0;
        }
        input[type="submit"]:hover {
            background-color: #e0e0e0;
        }
        a {
            display: block;
            margin-top: 8px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form id="registerForm" action="register.php" method="post">
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

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES); ?>" required>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($_POST['dob'] ?? '', ENT_QUOTES); ?>" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES); ?>" required>

            <input type="submit" value="Register">

            <a href="login.php">Already have an account? Login here</a>
        </form>
    </div>
</body>
</html>
