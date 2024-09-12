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

$sql_user = "SELECT username, email, phone_number, date_of_birth, gender FROM users WHERE username=?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $loggedInUsername);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
} else {
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .container {
            width: 80%;
            max-width: 700px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .profile-info {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fafafa;
        }
        .profile-info h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .profile-info p {
            margin: 8px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
        }
        .btn:hover {
            background-color: #555;
        }
        .action-btns a {
            display: inline-block;
            padding: 5px 8px;
            margin: 0 4px;
            color: #ffffff;
            text-decoration: none;
            border-radius: 3px;
            font-size: 12px;
            text-align: center;
        }
        .edit {
            background-color: #333;
        }
        .edit:hover {
            background-color: #222;
        }
        .delete {
            background-color: #d9534f;
        }
        .delete:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Dashboard</h1>
        </header>
        <div class="profile-info">
            <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Phone Number: <?php echo htmlspecialchars($user['phone_number']); ?></p>
            <p>Date of Birth: <?php echo htmlspecialchars($user['date_of_birth']); ?></p>
            <p>Gender: <?php echo htmlspecialchars($user['gender']); ?></p>
        </div>
        <h2>All Users</h2>
        <table>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result_all_users->num_rows > 0) {
                while ($row = $result_all_users->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td class='action-btns'>";
                    echo "<a href='update_user.php?username=" . urlencode($row['username']) . "' class='edit'>Edit</a>";
                    echo "<a href='delete_user.php?username=" . urlencode($row['username']) . "' class='delete'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No users found.</td></tr>";
            }
            ?>
        </table>
        <a href="logout.php" class="btn">Logout</a>
    </div>
</body>
</html>
