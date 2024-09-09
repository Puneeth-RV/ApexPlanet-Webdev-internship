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
                    $_POST = [];
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
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #6D5BBA, #8D58BF);
            color: #fff;
        }

        .container {
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            animation: fadeInUp 1s ease-in-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            color: #8D58BF;
            margin-bottom: 20px;
            font-size: 26px;
            font-weight: 600;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .form-group {
            flex: 1;
            min-width: 250px;
            box-sizing: border-box;
        }

        .form-group input {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 30px;
            box-sizing: border-box;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
            width: 100%;
        }

        .form-group input:focus {
            border-color: #8D58BF;
            box-shadow: 0 0 10px rgba(141, 88, 191, 0.1);
        }

        input[type="submit"] {
            cursor: pointer;
            background-color: #8D58BF;
            color: white;
            border: none;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: 100%;
            padding: 12px;
            border-radius: 30px;
        }

        input[type="submit"]:hover {
            background-color: #6D5BBA;
        }

        a {
            text-align: center;
            color: #8D58BF;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }

        a:hover {
            text-decoration: underline;
        }

        .error, .success {
            font-size: 14px;
            padding: 10px;
            margin-bottom: 20px; 
            border-radius: 5px;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form id="registerForm" action="register.php" method="post">
            <?php if (count($errors) > 0): ?>
                <div class="error">
                    <?php echo implode('<br>', $errors); ?>
                </div>
            <?php endif; ?>

            <?php if ($successMessage): ?>
                <div class="success">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <input type="text" id="username" name="username" placeholder="Username" value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES); ?>" required>
            </div>

            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" required>
            </div>

            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <div class="form-group">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <div class="form-group">
                <input type="tel" id="phone" name="phone" placeholder="Phone Number (10 digits)" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES); ?>" required>
            </div>

            <div class="form-group">
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($_POST['dob'] ?? '', ENT_QUOTES); ?>" required>
            </div>

            <div class="form-group">
                <input type="text" id="address" name="address" placeholder="Address" value="<?php echo htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES); ?>" required>
            </div>

            <input type="submit" value="Register">

            <a href="login.php">Already have an account? Login here</a>
        </form>
    </div>
</body>
</html>
