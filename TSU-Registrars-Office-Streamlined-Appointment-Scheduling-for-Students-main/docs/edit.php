<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "booking_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];
    $current_password = $_POST['current_password'];

    if (!empty($new_password)) {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();

        if (password_verify($current_password, $user_data['password'])) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password_sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_password_sql);
            $stmt->bind_param("si", $new_password_hash, $user_id);
            $stmt->execute();
            $password_message = "Password updated successfully!";
        } else {
            $password_message = "Current password is incorrect.";
        }
    }
    if (!empty($new_email) && $new_email !== $user['email']) {
        $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $new_email, $user_id);
        $stmt->execute();
        $email_message = "Email updated successfully!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    <script>
        function showAlert(message) {
            alert(message);
        }
    </script>
</head>
<body>

    <header class="header">
        <a href="#home" class="logo"> <img src="images/tsu-seal.png"> TSU <span>Registrar</span></a>
        <nav class="navbar">
            <a href="booking-page.html">Booking</a>
            <a href="user-dashboard.html" class="active">Profile</a>
            <a href="index.php">Log Out</a>
        </nav>
    </header>

    <section class="log-in">
        <h2 class="log-header">Hello, <?php echo htmlspecialchars($user['name']); ?>. Do you want to edit your profile?</h2>

        <?php if (isset($email_message)) { echo "<script>showAlert('$email_message');</script>"; } ?>
        <?php if (isset($password_message)) { echo "<script>showAlert('$password_message');</script>"; } ?>

        <form method="POST">
            <div class="input-box">
                
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Enter new email">
                <label class="log-header" for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" placeholder="Enter your current password">
                <label class="log-header" for="password">New Password</label>
                <input type="password" id="password" name="password" placeholder="Enter new password">
                <button type="submit" class="btn">Save Changes</button>
                <a href="user-dashboard.php" class="btn bypass-btn guest-log-in">BACK</a>
            </div>
        </form>
    </section>

    <footer class="footer">
        <div class="social">
            <a href="" target="_blank"><i class="ti ti-brand-facebook"></i></a>
            <a href="" target="_blank"><i class="ti ti-brand-x-filled"></i></a>
            <a href="" target="_blank"><i class="ti ti-brand-instagram-filled"></i></a>
        </div>
        <ul class="list">
            <li><a href="booking-page.html">Booking</a></li>
            <li><a href="user-dashboard.html">Profile</a></li>
        </ul>
        <p class="copyright">@ Tarlac State University | All Rights Reserved</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>
