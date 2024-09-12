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
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];

    if (empty($username) || empty($email) || empty($password) || empty($phone_number) || empty($date_of_birth) || empty($gender)) {
        $message = "<p>Please fill in all fields.</p>";
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

            $sql = "INSERT INTO users (username, email, password, phone_number, date_of_birth, gender) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $username, $email, $hashed_password, $phone_number, $date_of_birth, $gender);

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
            max-width: 400px;
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
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"], input[type="date"] {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            width: 100%;
            box-sizing: border-box;
        }
        select {
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
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <div class="message">
            <?php echo $message; ?>
        </div>
        <form action="register.php" method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="phone_number">Phone Number</label>
            <input type="tel" id="phone_number" name="phone_number" required>

            <label for="date_of_birth">Date of Birth</label>
            <input type="date" id="date_of_birth" name="date_of_birth" required>

            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="">Select...</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <input type="submit" value="Register">
            <a href="login.php">Already have an account? Login here</a>
        </form>
    </div>
</body>
</html>
