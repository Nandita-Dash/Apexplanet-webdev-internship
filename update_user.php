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

// Initialize the message variable to avoid undefined warnings
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];

    // Handle file upload
    $profilePicture = null; // Default to null if no file is uploaded

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/'; // Directory where files should be uploaded
        $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);

        // Ensure the directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            $profilePicture = basename($_FILES['profile_picture']['name']);
        } else {
            $message = "<p>Failed to upload the file.</p>";
        }
    }

    if (empty($email) || empty($phone_number) || empty($date_of_birth) || empty($gender)) {
        $message = "<p>Please fill in all fields.</p>";
    } else {
        // Update user information in the database
        $sql_update = "UPDATE users SET email=?, phone_number=?, date_of_birth=?, gender=?, profile_picture=? WHERE username=?";
        
        if ($stmt = $conn->prepare($sql_update)) {
            $stmt->bind_param("ssssss", $email, $phone_number, $date_of_birth, $gender, $profilePicture, $loggedInUsername);
            
            if ($stmt->execute()) {
                $message = "<p>Update successful!</p>";
            } else {
                $message = "<p>Error: " . $conn->error . "</p>";
            }

            $stmt->close();
        } else {
            $message = "<p>Failed to prepare SQL statement.</p>";
        }
    }
}

// Fetch user data
$sql_user = "SELECT username, email, phone_number, date_of_birth, gender, profile_picture FROM users WHERE username=?";
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
            position: relative;
        }
        .container .btn-back {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 6px 12px;
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            font-size: 14px;
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
            text-align: left;
        }
        label {
            margin-bottom: 5px;
            font-size: 16px;
            color: #555;
            text-align: left;
            display: block;
        }
        input[type="text"], input[type="email"], input[type="tel"], input[type="date"], select, input[type="file"] {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            width: 100%;
            box-sizing: border-box;
        }
        input[type="submit"] {
            grid-column: span 2;
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
</head>
<body>
    <div class="container">
        <a href="manage_users.php" class="btn-back">Back to Manage Users</a>
        <h2>Update User Profile</h2>
        <div id="form-message" class="message">
            <?php echo $message; ?>
        </div>
        <form action="update_user.php" method="post" enctype="multipart/form-data">
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
                    <option value="">Select Gender</option>
                    <option value="male" <?php echo (isset($user['gender']) && $user['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo (isset($user['gender']) && $user['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                    <option value="other" <?php echo (isset($user['gender']) && $user['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <input type="file" id="profile_picture" name="profile_picture">
            </div>

            <input type="submit" value="Update">
        </form>
    </div>
</body>
</html>
