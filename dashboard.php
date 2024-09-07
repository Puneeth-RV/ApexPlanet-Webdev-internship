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

$loggedInUsername = $_SESSION['username'];

$sql_user = "SELECT username, email, phone, dob, address FROM users WHERE username=?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $loggedInUsername);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
} 
else {
    echo "Error fetching user information.";
}

$sql_all_users = "SELECT username, email FROM users";
$result_all_users = $conn->query($sql_all_users);

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
        }
        .container {
            width: 80%;
            margin: 20px auto;
        }
        header {
            text-align: center;
            padding: 10px 0;
        }
        h1, h2 {
            margin: 0;
            font-size: 24px;
        }
        .profile-info {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
        }
        .profile-info h2 {
            margin: 0;
            font-size: 20px;
        }
        .profile-info p {
            margin: 10px 0;
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
            padding: 5px;
            border: 1px solid #000;
            text-decoration: none;
            color: #000;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Dashboard</h1>
    </header>
    <div class="container">
        <div class="profile-info">
            <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Phone: <?php echo htmlspecialchars($user['phone']); ?></p>
            <p>Date of Birth: <?php echo htmlspecialchars($user['dob']); ?></p>
            <p>Address: <?php echo htmlspecialchars($user['address']); ?></p>
        </div>

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

        <a href="logout.php" class="btn">Logout</a>
    </div>
</body>
</html>


