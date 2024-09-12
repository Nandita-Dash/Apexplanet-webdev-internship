<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "<p>Please fill in all fields.</p>";
    } else {
        $sql = "SELECT * FROM users WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['username'] = $user['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $message = "<p>Invalid email or password.</p>";
            }
        } else {
            $message = "<p>No user found with this email.</p>";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #fff;
        }
        .container {
            width: 100%;
            max-width: 360px;
            padding: 20px;
            border-radius: 4px;
            border: 1px solid #ddd;
            background-color: #fff;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-size: 14px;
            color: #555;
            text-align: left;
        }
        input[type="email"], input[type="password"] {
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
            background-color: #000;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        input[type="submit"]:hover {
            background-color: #333;
        }
        a {
            margin-top: 10px;
            display: block;
            color: #007bff;
            text-decoration: none;
        }
        .message {
            margin-bottom: 20px;
            color: red;
            text-align: center;
        }
    </style>
    <script>
        function validateForm(event) {
            var email = document.getElementById('email').value.trim();
            var password = document.getElementById('password').value.trim();
            var message = '';

            if (email === '' || password === '') {
                message = 'Please fill in all fields.';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                message = 'Invalid email format.';
            } else if (password.length < 6) {
                message = 'Password must be at least 6 characters long.';
            }

            if (message) {
                document.getElementById('form-message').textContent = message;
                event.preventDefault();
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <div id="form-message" class="message">
            <?php echo $message ?? ''; ?>
        </div>
        <form action="login.php" method="post" onsubmit="return validateForm(event)">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
            <a href="register.php">Don't have an account? Register here</a>
        </form>
    </div>
</body>
</html>
