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
            width: 100%;
            max-width: 800px; /* Increased width */
            padding: 60px; /* Increased padding */
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px; /* Increased margin for spacing */
        }
        h1 {
            font-size: 36px; /* Increased font size */
            margin: 0;
            color: #333;
            font-weight: 600;
        }
        .btn-manage-users, .btn-logout {
            padding: 10px 20px; /* Increased padding */
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px; /* Increased font size */
            text-align: center;
        }
        .btn-manage-users {
            margin-right: 20px;
        }
        .btn-manage-users:hover {
            background: linear-gradient(135deg, #ff7eb3, #ff758c);
        }
        .btn-logout {
            margin-left: auto;
        }
        .btn-logout:hover {
            background: linear-gradient(135deg, #ff7eb3, #ff758c);
        }
        .profile-info {
            margin-bottom: 30px; /* Increased margin */
            padding: 25px; /* Increased padding */
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
        }
        .profile-info h2 {
            margin: 0;
            font-size: 28px; /* Increased font size */
            color: #333;
        }
        .profile-info p {
            margin: 12px 0; /* Increased margin */
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Dashboard</h1>
            <div>
                <a href="manage_users.php" class="btn-manage-users">Manage Users</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </header>
        <div class="profile-info">
            <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Phone Number: <?php echo htmlspecialchars($user['phone_number']); ?></p>
            <p>Date of Birth: <?php echo htmlspecialchars($user['date_of_birth']); ?></p>
            <p>Gender: <?php echo htmlspecialchars($user['gender']); ?></p>
        </div>
    </div>
</body>
</html>
