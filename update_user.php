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

$message = "";  // Initialize message
$user = null;   // Initialize user

$loggedInUsername = $_SESSION['username'];

if (isset($_GET['username'])) {
    $username_to_edit = $_GET['username'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = trim($_POST['email']);
        $phone_number = trim($_POST['phone_number']);
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
        $message = "<p>Error fetching user information.</p>";
    }

    $stmt_user->close();
} else {
    $message = "<p>No user specified for update.</p>";
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
            position: relative; /* Allows positioning of the button within the container */
        }
        .container .btn-back {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 6px 12px; /* Reduced padding */
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            font-size: 14px; /* Reduced font size */
        }
        .container .btn-back:hover {
            background: linear-gradient(135deg, #ff7eb3, #ff758c);
        }
        h2 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #333;
            font-weight: 600;
        }
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: center;
            margin-top: 20px;
            text-align: left; /* Aligns labels to the left */
        }
        label {
            margin-bottom: 5px;
            font-size: 16px;
            color: #555;
            text-align: left;
            display: block; /* Ensures the label occupies the full width */
        }
        input[type="text"], input[type="email"], input[type="tel"], input[type="date"], select {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            width: 100%;
            box-sizing: border-box;
        }
        input[type="submit"] {
            grid-column: span 2; /* Makes the submit button span across both columns */
            padding: 12px;
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background: linear-gradient(135deg, #ff7eb3, #ff758c);
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
            var phone_number = document.getElementById('phone_number').value.trim();
            var date_of_birth = document.getElementById('date_of_birth').value.trim();
            var gender = document.getElementById('gender').value.trim();
            var message = '';

            if (email === '' || phone_number === '' || date_of_birth === '' || gender === '') {
                message = 'Please fill in all fields.';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                message = 'Invalid email format.';
            } else if (!/^\d{10}$/.test(phone_number)) {
                message = 'Phone number must be 10 digits.';
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
        <a href="manage_users.php" class="btn-back">Back to Manage Users</a>
        <h2>Update User Profile</h2>
        <div id="form-message" class="message">
            <?php echo $message; ?>
        </div>
        <form action="update_user.php?username=<?php echo htmlspecialchars($username_to_edit); ?>" method="post" onsubmit="return validateForm(event)">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo isset($user['phone_number']) ? htmlspecialchars($user['phone_number']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo isset($user['date_of_birth']) ? htmlspecialchars($user['date_of_birth']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="Male" <?php echo (isset($user['gender']) && $user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo (isset($user['gender']) && $user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo (isset($user['gender']) && $user['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <input type="submit" value="Update">
        </form>
    </div>
</body>
</html>
