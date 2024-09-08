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

// Fetch all thoughts
$sql_thoughts = "SELECT username, current_thought, created_at FROM users WHERE current_thought IS NOT NULL";
$result_thoughts = $conn->query($sql_thoughts);

if (!$result_thoughts) {
    die("Error fetching thoughts: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Thoughts</title>
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
            background-color: #fff; 
            border-bottom: 1px solid #ccc;
        }
        h1 {
            margin: 0;
            font-size: 28px; 
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff; 
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .thought {
            padding: 15px;
            border-bottom: 1px solid #ccc;
            word-wrap: break-word; 
        }
        .thought p {
            margin: 0;
            font-size: 20px; 
        }
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            border: 1px solid #000;
            background-color: #f0f0f0;
            border-radius: 5px;
            font-size: 18px; 
            text-align: center;
            text-decoration: none;
            color: #000;
            margin-top: 20px;
        }
        .btn-back:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <header>
        <h1>View Thoughts</h1>
    </header>
    <div class="container">
        <div class="thoughts-list">
            <?php if ($result_thoughts->num_rows > 0): ?>
                <?php while ($row = $result_thoughts->fetch_assoc()): ?>
                    <div class="thought">
                        <p><strong><?php echo htmlspecialchars($row['username']); ?></strong> said:</p>
                        <p><?php echo htmlspecialchars($row['current_thought']); ?></p>
                        <p><small>Posted at: <?php echo htmlspecialchars($row['created_at']); ?></small></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No thoughts to display.</p>
            <?php endif; ?>
        </div>

        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
</body>
</html>

