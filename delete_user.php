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

    // Check if the user is trying to delete their own account
    if ($username_to_delete === $loggedInUsername) {
        // Handle self-deletion
        $sql_delete = "DELETE FROM users WHERE username=?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("s", $username_to_delete);

        if ($stmt_delete->execute()) {
            // Destroy the session and redirect to login.php
            session_destroy();
            header("Location: login.php");
            exit();
        } else {
            $message = "<p>Error: " . $stmt_delete->error . "</p>";
        }

        $stmt_delete->close();
    } else {
        // Allow deleting other users' accounts
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
    <title>Delete User</title>
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
            width: 90%;
            max-width: 600px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .message {
            margin-bottom: 20px;
            color: red;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Delete User</h2>
        <div class="message">
            <?php echo $message; ?>
        </div>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
