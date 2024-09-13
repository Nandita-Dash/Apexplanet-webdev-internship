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
            max-width: 400px;
            padding: 40px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            font-size: 32px;
            margin-bottom: 30px;
            color: #333;
            font-weight: 600;
        }
        form {
            display: grid;
            gap: 20px;
        }
        input[type="email"], input[type="password"] {
            padding: 14px;
            font-size: 16px;
            border: 2px solid transparent;
            border-radius: 8px;
            background-color: #f0f0f0;
            transition: border-color 0.3s ease;
        }
        input[type="email"]::placeholder, input[type="password"]::placeholder {
            color: #aaa;
        }
        input[type="email"]:focus, input[type="password"]:focus {
            border-color: #ff9a9e;
            outline: none;
        }
        input[type="submit"] {
            padding: 14px;
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        input[type="submit"]:hover {
            background: linear-gradient(135deg, #ff7eb3, #ff758c);
        }
        a {
            margin-top: 20px;
            color: #ff7eb3;
            font-size: 16px;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .message {
            margin-bottom: 20px;
            color: red;
            font-size: 14px;
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
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <input type="submit" value="Login">
            <a href="register.php">Don't have an account? Register here</a>
        </form>
    </div>
</body>
</html>
