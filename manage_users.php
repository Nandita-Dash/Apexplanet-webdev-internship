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

$sql_all_users = "SELECT username, email, phone_number, date_of_birth, gender FROM users";
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
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            width: 80%;
            max-width: 1000px; /* Increased width for more space */
            padding: 40px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h1 {
            margin: 0;
            font-size: 32px;
            color: #333;
            font-weight: 600;
        }
        .btn-back {
            padding: 8px 16px;
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            text-align: center;
        }
        .btn-back:hover {
            background: linear-gradient(135deg, #ff7eb3, #ff758c);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px; /* Increased padding for more space */
            text-align: left;
            border: 1px solid #ddd;
            word-break: break-word; /* Ensure long words break properly */
        }
        th {
            background-color: #f5f5f5;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .action-btns a {
            display: inline-block;
            padding: 6px 12px; /* Increased padding for action buttons */
            margin: 0 4px;
            color: #ffffff;
            text-decoration: none;
            border-radius: 3px;
            font-size: 14px;
            text-align: center;
        }
        .edit {
            background: linear-gradient(135deg, #333, #555);
        }
        .edit:hover {
            background: linear-gradient(135deg, #555, #333);
        }
        .delete {
            background: linear-gradient(135deg, #d9534f, #c9302c);
        }
        .delete:hover {
            background: linear-gradient(135deg, #c9302c, #d9534f);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Manage Users</h1>
            <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
        </header>
        <table>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result_all_users->num_rows > 0) {
                while ($row = $result_all_users->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_of_birth']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                    echo "<td class='action-btns'>";
                    echo "<a href='update_user.php?username=" . urlencode($row['username']) . "' class='edit'>Edit</a>";
                    echo "<a href='delete_user.php?username=" . urlencode($row['username']) . "' class='delete'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No users found.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
