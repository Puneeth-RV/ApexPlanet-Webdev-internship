<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (count($errors) === 0) {
        $sql = "SELECT id, username, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $errors[] = "Database prepare statement failed: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_id'] = $user['id']; 
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $errors[] = "Invalid email or password.";
                }
            } else {
                $errors[] = "No user found with this email.";
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
    <title>Login</title>
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
    </style>
    <script>
        function validateForm(event) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            let errors = [];
            const errorDiv = document.getElementById('error-message');
            errorDiv.innerHTML = '';

            if (!email || !password) {
                errors.push("All fields are required.");
            }

            if (!email.includes('@') || !email.includes('.')) {
                errors.push("Invalid email format.");
            }

            if (errors.length > 0) {
                errorDiv.innerHTML = errors.join('<br>');
                event.preventDefault();
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" onsubmit="validateForm(event)">
            <div id="error-message" class="error">
                <?php 
                if (count($errors) > 0) {
                    echo implode('<br>', $errors);
                }
                ?>
            </div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">

            <a href="register.php">Don't have an account? Register here</a>
        </form>
    </div>
</body>
</html>



