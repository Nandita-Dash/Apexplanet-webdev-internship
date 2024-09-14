<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get logged-in user's username from the session
$logged_in_user = $_SESSION['username'];

// Query to fetch the logged-in user's role from the database
$sql_role = "SELECT role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql_role);
$stmt->bind_param("s", $logged_in_user);
$stmt->execute();
$stmt->bind_result($logged_in_role);
$stmt->fetch();
$stmt->close();

// Initialize result variables
$result_admins = null;
$result_users = null;

// Separate queries for Admins and regular users
$sql_admins = "SELECT username, email, phone_number, date_of_birth, gender FROM users WHERE role = 'Admin'";
$sql_users = "SELECT username, email, phone_number, date_of_birth, gender FROM users WHERE role = 'User'";

// Execute query for Admins
if ($result_admins = $conn->query($sql_admins)) {
    // Check if query failed
    if ($result_admins === false) {
        echo "Error executing query: " . $conn->error;
        exit();
    }
} else {
    echo "Error preparing query: " . $conn->error;
    exit();
}

// Execute query for regular users
if ($result_users = $conn->query($sql_users)) {
    // Check if query failed
    if ($result_users === false) {
        echo "Error executing query: " . $conn->error;
        exit();
    }
} else {
    echo "Error preparing query: " . $conn->error;
    exit();
}

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
            width: 90%; 
            max-width: 1200px;
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
            background-color: #ff758c;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            text-align: center;
        }
        .btn-back:hover {
            background-color: #ff7eb3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        colgroup col {
            width: 14%; /* Adjusted to balance columns */
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            word-break: break-word;
        }
        th {
            background-color: #f5f5f5;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .action-btns {
            display: flex;
            gap: 4px; /* Space between buttons */
            justify-content: center;
        }
        .action-btns a {
            display: inline-block;
            padding: 8px 16px; /* Increased padding for better button visibility */
            background-color: #ff758c;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            text-align: center;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .action-btns a:hover {
            background-color: #ff7eb3;
            transform: scale(1.05);
        }
        td.action-btns {
            text-align: center;
        }
        h2 {
            text-align: left;
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Manage Users</h1>
            <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
        </header>

        <!-- Admin Users Table -->
        <h2>Admin Users</h2>
        <table>
            <colgroup>
                <col>
                <col style="width: 30%;"> <!-- Wider column for email -->
                <col>
                <col>
                <col>
                <col style="width: 20%;"> <!-- Adjusted width for Actions column -->
            </colgroup>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result_admins && $result_admins->num_rows > 0) {
                while ($row = $result_admins->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_of_birth']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                    echo "<td class='action-btns'>";
                    if ($logged_in_role === 'Admin') {
                        echo "<a href='update_user.php?username=" . urlencode($row['username']) . "'>Edit</a>";
                        echo "<a href='delete_user.php?username=" . urlencode($row['username']) . "'>Delete</a>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No Admins found.</td></tr>";
            }
            ?>
        </table>

        <!-- Regular Users Table -->
        <h2>Regular Users</h2>
        <table>
            <colgroup>
                <col>
                <col style="width: 30%;"> <!-- Wider column for email -->
                <col>
                <col>
                <col>
                <col style="width: 20%;"> <!-- Adjusted width for Actions column -->
            </colgroup>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result_users && $result_users->num_rows > 0) {
                while ($row = $result_users->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_of_birth']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                    echo "<td class='action-btns'>";
                    if ($logged_in_role === 'Admin' || $row['username'] === $logged_in_user) {
                        echo "<a href='update_user.php?username=" . urlencode($row['username']) . "'>Edit</a>";
                        echo "<a href='delete_user.php?username=" . urlencode($row['username']) . "'>Delete</a>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No Regular Users found.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
