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
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #8D58BF; 
            color: #333;
        }
        header {
            background: #ffffff;
            padding: 20px 10%;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        h1 {
            margin: 0;
            font-size: 32px; 
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1400px;
            margin: 30px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        .thought {
            padding: 20px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
            background: #f9f9f9;
            border-radius: 10px;
        }
        .thought p {
            margin: 0;
            font-size: 20px; 
            color: #555;
        }
        .thought p strong {
            color: #8D58BF;
            font-size: 20px; 
        }
        .thought p small {
            font-size: 14px; 
            color: #777;
        }
        .btn-back {
            display: inline-block;
            padding: 12px 24px;
            border: 1px solid #8D58BF;
            background-color: #fff;
            border-radius: 8px;
            font-size: 20px; 
            text-align: center;
            text-decoration: none;
            color: #8D58BF;
            margin-top: 25px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #8D58BF;
            color: #fff;
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
