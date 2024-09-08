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

$sql_all_users = "SELECT username, email FROM users";
$result_all_users = $conn->query($sql_all_users);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0; 
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff; 
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
        }
        header {
            text-align: center;
            padding: 10px 0;
        }
        h1, h2 {
            margin: 0;
            font-size: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ccc;
        }
        .btn {
            display: inline-block;
            padding: 5px 10px; 
            border: 1px solid #000;
            text-decoration: none;
            color: #000;
            background-color: #f0f0f0; 
            border-radius: 5px; 
            margin-right: 10px;
            font-size: 14px; 
        }
        .btn:hover {
            background-color: #ddd; 
        }
    </style>
</head>
<body>
    <header>
        <h1>Manage Users</h1>
    </header>
    <div class="container">
        <h2>All Users</h2>
        <table>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result_all_users->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>
                    <a href="update.php?username=<?php echo urlencode($row['username']); ?>" class="btn">Edit</a>
                    <a href="delete.php?username=<?php echo urlencode($row['username']); ?>" class="btn">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>

        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>

