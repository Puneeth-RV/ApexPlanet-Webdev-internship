<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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

$sql_user = "SELECT username, email, phone, dob, address, current_thought, thought_timestamp FROM users WHERE username=?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $loggedInUsername);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
} else {
    $error = "Error fetching user information.";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_thought = trim($_POST['current_thought']);

    if (empty($new_thought)) {
        $error = "Thought cannot be empty.";
    } else {
        $sql_update_thought = "UPDATE users SET current_thought=?, thought_timestamp=NOW() WHERE username=?";
        $stmt_update_thought = $conn->prepare($sql_update_thought);
        $stmt_update_thought->bind_param("ss", $new_thought, $loggedInUsername);

        if ($stmt_update_thought->execute()) {
            $success = "Thought updated successfully!";
            header("Refresh: 1"); 
        } else {
            $error = "Error updating thought: " . $conn->error;
        }

        $stmt_update_thought->close();
    }
}

$stmt_user->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5; 
        }
        header {
            text-align: center;
            padding: 10px 0;
            position: relative;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        .container {
            width: 80%;
            margin: 20px auto;
        }
        .profile-info {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            background-color: #fff; 
            word-wrap: break-word;
        }
        .profile-info h2 {
            margin: 0;
            font-size: 20px;
        }
        .profile-info p {
            margin: 10px 0;
        }
        textarea {
            width: 100%;
            height: 150px; 
            padding: 10px;
            font-size: 16px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; 
            resize: none; 
        }
        button {
            padding: 8px 12px;
            border: 1px solid #000;
            background-color: #f0f0f0;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #ddd;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            border: 1px solid #000;
            text-decoration: none;
            color: #000;
            background-color: #f0f0f0;
            border-radius: 5px;
            margin-right: 10px;
            font-size: 16px;
            text-align: center;
        }
        .top-right-buttons {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 10px; 
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .success {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>Dashboard</h1>
        <div class="top-right-buttons">
            <a href="thought.php" class="btn">View Thoughts</a>
            <a href="usermanager.php" class="btn">View Users</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </header>
    
    <div class="container">
        <div class="profile-info">
            <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Phone: <?php echo htmlspecialchars($user['phone']); ?></p>
            <p>Date of Birth: <?php echo htmlspecialchars($user['dob']); ?></p>
            <p>Address: <?php echo htmlspecialchars($user['address']); ?></p>
            <p>Current Thought: <?php echo htmlspecialchars($user['current_thought']); ?></p>
            <p>Posted at: <?php echo htmlspecialchars($user['thought_timestamp']); ?></p>
        </div>

        <div class="thought-form">
            <form method="POST" action="dashboard.php">
                <textarea name="current_thought" placeholder="Share your thoughts..."><?php echo htmlspecialchars($user['current_thought']); ?></textarea>
                <button type="submit">Update Thought</button>
            </form>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>



