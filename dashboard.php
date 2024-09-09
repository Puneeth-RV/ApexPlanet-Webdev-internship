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
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #8D58BF; 
            color: #333;
        }
        
        header {
            background: #ffffff;
            padding: 15px 10%;
            border-bottom: 1px solid #ddd;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .top-right-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: 1px solid #8D58BF;
            text-decoration: none;
            color: #8D58BF;
            background-color: #fff;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn:hover {
            background-color: #8D58BF;
            color: #fff;
        }

        .container {
            width: 80%;
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-thought-container {
            display: flex;
            gap: 20px;
        }

        .profile-card, .thought-card {
            flex: 1;
        }

        .thought-card {
            margin-left: 20px;
        }

        .profile-card, .thought-card {
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-card h2, .thought-card h2 {
            margin-top: 0;
            font-size: 22px;
            color: #8D58BF;
        }

        .profile-card p, .thought-card p {
            margin: 10px 0;
            font-size: 16px;
            color: #555;
        }

        textarea {
            width: 100%;
            height: 100px;
            padding: 8px;
            font-size: 14px;
            border: 2px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            resize: none;
        }

        textarea:focus {
            border-color: #8D58BF;
            box-shadow: 0 0 10px rgba(141, 88, 191, 0.1);
        }

        button {
            padding: 8px;
            border: none;
            background-color: #8D58BF;
            color: #fff;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: auto;
            display: inline-block;
        }

        button:hover {
            background-color: #6D5BBA;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            font-size: 14px;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            font-size: 14px;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
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
        <div class="profile-thought-container">
            <div class="profile-card">
                <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
                <p>Email: <?php echo isset($user['email']) ? htmlspecialchars($user['email']) : 'N/A'; ?></p>
                <p>Phone: <?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : 'N/A'; ?></p>
                <p>Date of Birth: <?php echo isset($user['dob']) ? htmlspecialchars($user['dob']) : 'N/A'; ?></p>
                <p>Address: <?php echo isset($user['address']) ? htmlspecialchars($user['address']) : 'N/A'; ?></p>
                <p>Current Thought: <?php echo isset($user['current_thought']) ? htmlspecialchars($user['current_thought']) : 'N/A'; ?></p>
                <p>Posted at: <?php echo isset($user['thought_timestamp']) ? htmlspecialchars($user['thought_timestamp']) : 'N/A'; ?></p>
            </div>

            <div class="thought-card">
                <h2>Update Your Thought</h2>
                <form method="POST" action="dashboard.php">
                    <textarea name="current_thought" placeholder="Share your thoughts..."><?php echo isset($user['current_thought']) ? htmlspecialchars($user['current_thought']) : ''; ?></textarea>
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
    </div>
</body>
</html>
