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
            max-width: 900px;
            padding: 40px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 36px;
            margin: 0;
            color: #333;
            font-weight: 600;
        }
        .btn-manage-users, .btn-logout {
            padding: 10px 20px;
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            text-align: center;
        }
        .btn-manage-users:hover, .btn-logout:hover {
            background: linear-gradient(135deg, #ff7eb3, #ff758c);
        }
        .profile-section {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .profile-info, .cat-fact {
            flex: 1;
            padding: 25px;
            border-radius: 12px;
            background-color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .profile-info:hover, .cat-fact:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .profile-info img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 15px;
            object-fit: cover;
        }
        .profile-info h2 {
            margin: 0;
            font-size: 28px;
            color: #333;
        }
        .profile-info p {
            margin: 12px 0;
            color: #666;
        }
        .cat-fact h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #333;
        }
        .cat-fact p {
            font-size: 18px;
            line-height: 1.6;
            padding: 15px;
            background: rgba(255, 183, 196, 0.2);
            border-radius: 10px;
            color: #666;
            border: 1px solid rgba(255, 183, 196, 0.5);
        }
        .cat-fact p:before {
            content: "🐱 ";
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
        <div class="profile-section">
            <div class="profile-info">
                <?php
                $profilePicturePath = !empty($user['profile_picture']) ? 'uploads/' . htmlspecialchars($user['profile_picture']) : 'images/ud.png';
                ?>
                <img src="<?php echo $profilePicturePath; ?>" alt="Profile Picture">
                <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
                <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                <p>Phone Number: <?php echo htmlspecialchars($user['phone_number']); ?></p>
                <p>Date of Birth: <?php echo htmlspecialchars($user['date_of_birth']); ?></p>
                <p>Gender: <?php echo htmlspecialchars($user['gender']); ?></p>
            </div>
            <div class="cat-fact">
                <h3>Cat Fact of the Day</h3>
                <p id="catFact">Loading cat fact...</p>
            </div>
        </div>
    </div>

    <script>
        fetch('https://catfact.ninja/fact')
            .then(response => response.json())
            .then(data => {
                document.getElementById('catFact').innerText = data.fact;
            })
            .catch(error => {
                document.getElementById('catFact').innerText = "Unable to fetch cat fact.";
                console.error('Error fetching cat fact:', error);
            });
    </script>
</body>
</html>
