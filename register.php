<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone_number = trim($_POST['phone_number']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $gender = trim($_POST['gender']);
    $role = trim($_POST['role']);

    if (empty($username) || empty($email) || empty($password) || empty($phone_number) || empty($date_of_birth) || empty($gender) || empty($role)) {
        $message = "<p>Please fill in all fields.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<p>Invalid email format.</p>";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone_number)) {
        $message = "<p>Invalid phone number. It should be a 10-digit number.</p>";
    } elseif (strlen($password) < 6) {
        $message = "<p>Password must be at least 6 characters long.</p>";
    } else {
        $sql_check = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ss", $email, $username);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $message = "<p>This email or username is already registered. Please use another one.</p>";
        } else {
            $stmt_check->close();
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password, phone_number, date_of_birth, gender, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $username, $email, $hashed_password, $phone_number, $date_of_birth, $gender, $role);

            if ($stmt->execute()) {
                $message = "<p>Registration successful! <a href='login.php'>Go to Login</a></p>";
            } else {
                $message = "<p>Error: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
            max-width: 550px;
            padding: 40px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            font-size: 30px;
            margin-bottom: 25px;
            color: #333;
            font-weight: 600;
        }
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"], input[type="date"], select {
            padding: 14px;
            font-size: 15px;
            border: 2px solid transparent;
            border-radius: 8px;
            background-color: #f0f0f0;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, input[type="tel"]:focus, input[type="date"]:focus, select:focus {
            border-color: #ff9a9e;
            outline: none;
        }
        .full-width {
            grid-column: 1 / span 2;
        }
        input[type="submit"] {
            padding: 14px;
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 17px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        input[type="submit"]:hover {
            background: linear-gradient(135deg, #ff7eb3, #ff758c);
        }
        a {
            margin-top: 15px;
            color: #ff7eb3;
            font-size: 15px;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .message {
            margin-bottom: 20px;
            color: red;
            font-size: 13px;
            grid-column: 1 / span 2;
            text-align: center;
        }
        select.full-width {
            grid-column: 1 / span 2;
        }
    </style>
    <script>
        function validateForm(event) {
            var username = document.getElementById('username').value.trim();
            var email = document.getElementById('email').value.trim();
            var password = document.getElementById('password').value.trim();
            var phone_number = document.getElementById('phone_number').value.trim();
            var date_of_birth = document.getElementById('date_of_birth').value.trim();
            var gender = document.getElementById('gender').value.trim();
            var role = document.getElementById('role').value.trim();
            var message = '';

            if (username === '' || email === '' || password === '' || phone_number === '' || date_of_birth === '' || gender === '' || role === '') {
                message = 'Please fill in all fields.';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                message = 'Invalid email format.';
            } else if (!/^[0-9]{10}$/.test(phone_number)) {
                message = 'Phone number must be a 10-digit number.';
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
        <h2>Register</h2>
        <div id="form-message" class="message">
            <?php echo $message; ?>
        </div>
        <form action="register.php" method="post" onsubmit="return validateForm(event)">
            <input type="text" id="username" name="username" placeholder="Enter your username" required>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <input type="tel" id="phone_number" name="phone_number" placeholder="Enter your phone number" required>
            <input type="date" id="date_of_birth" name="date_of_birth" required>
            <select id="gender" name="gender" required>
                <option value="">Select your gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <select id="role" name="role" required class="full-width">
                <option value="">Select your role</option>
                <option value="Admin">Admin</option>
                <option value="User">User</option>
            </select>
            <input type="submit" value="Register" class="full-width">
            <a href="login.php" class="full-width">Already have an account? Login here</a>
        </form>
    </div>
</body>
</html>
