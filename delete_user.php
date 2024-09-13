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

$message = "";
$loggedInUsername = $_SESSION['username'];

if (isset($_GET['username'])) {
    $username_to_delete = $_GET['username'];

    if ($username_to_delete === $loggedInUsername) {
        $sql_delete = "DELETE FROM users WHERE username=?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("s", $username_to_delete);

        if ($stmt_delete->execute()) {
            session_destroy();
            header("Location: login.php");
            exit();
        } else {
            $message = "<p>Error: " . $stmt_delete->error . "</p>";
        }

        $stmt_delete->close();
    } else {
        $sql_delete = "DELETE FROM users WHERE username=?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("s", $username_to_delete);

        if ($stmt_delete->execute()) {
            $message = "<p>User account deleted successfully.</p>";
        } else {
            $message = "<p>Error: " . $stmt_delete->error . "</p>";
        }

        $stmt_delete->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operation Success</title>
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
            max-width: 600px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #333;
            font-weight: 600;
        }
        .message {
            margin-bottom: 20px;
            color: red;
            text-align: center;
        }
        .btn {
            padding: 12px 20px;
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            font-size: 16px;
        }
        .btn:hover {
            background: linear-gradient(135deg, #ff7eb3, #ff758c);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Delete User</h2>
        <div class="message">
            <?php echo $message; ?>
        </div>
        <a href="manage_users.php" class="btn">Back to Manage Users</a>
    </div>
</body>
</html>
