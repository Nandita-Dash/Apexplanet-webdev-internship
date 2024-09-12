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
    $username_to_edit = $_GET['username'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $date_of_birth = $_POST['date_of_birth'];
        $gender = $_POST['gender'];

        if (empty($email) || empty($phone_number) || empty($date_of_birth) || empty($gender)) {
            $message = "<p>Please fill in all fields.</p>";
        } else {
            $sql_update = "UPDATE users SET email=?, phone_number=?, date_of_birth=?, gender=? WHERE username=?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("sssss", $email, $phone_number, $date_of_birth, $gender, $username_to_edit);

            if ($stmt_update->execute()) {
                $message = "<p>Update successful!</p>";
            } else {
                $message = "<p>Error: " . $stmt_update->error . "</p>";
            }

            $stmt_update->close();
        }
    }

    $sql_user = "SELECT username, email, phone_number, date_of_birth, gender FROM users WHERE username=?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $username_to_edit);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();
    } else {
        echo "Error fetching user information.";
    }

    $stmt_user->close();
} else {
    echo "No user specified for update.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
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
            max-width: 500px;
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
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-size: 14px;
            color: #555;
        }
        input[type="text"], input[type="email"], input[type="tel"], input[type="date"], select {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            width: 100%;
            box-sizing: border-box;
        }
        input[type="submit"] {
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #555;
        }
        .message {
            margin-bottom: 20px;
            color: red;
            text-align: center;
        }
        .btn-back {
            display: inline-block;
            padding: 5px 10px;
            background-color: #eee;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            font-size: 14px;
            margin-top: 20px;
            width: auto;
        }
        .btn-back:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update User Profile</h2>
        <div class="message">
            <?php echo $message; ?>
        </div>
        <form action="update_user.php?username=<?php echo htmlspecialchars($username_to_edit); ?>" method="post">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="phone_number">Phone Number</label>
            <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>

            <label for="date_of_birth">Date of Birth</label>
            <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>

            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo ($user['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>

            <input type="submit" value="Update">
        </form>
        <a href="dashboard.php" class="btn-back">Return to Dashboard</a>
    </div>
</body>
</html>
