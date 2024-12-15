<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "booking_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if the username is already taken
    $result = $conn->query("SELECT id FROM users WHERE username = '$username'");
    if ($result->num_rows > 0) {
        $alertMessage = "Username is already taken.";
    } else {
        // Insert the new user into the users table
        $conn->query("INSERT INTO users (name, username, password) VALUES ('$name', '$username', '$password')");
        $alertMessage = "Signup successful. You can now login your account.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="icon" type="image/x-icon" href="images/tsu-seal.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
</head>
<body>

    <header class="header">
        <a href="#home" class="logo">
            <img src="images/tsu-seal.png">TSU <span>Registrar</span>
        </a>
    </header>

    <section class="log-in">
        <form method="POST">
            <div class="img-container">
                <div class="login-wrapper">
                    <div class="input-box">
                        <h1 class="log-header">Sign Up</h1>
                        <input type="text" name="name" placeholder="Full Name" required>
                        <input type="text" name="username" placeholder="Username" required> 
                        <input type="password" name="password" placeholder="Password" required>
                        <button type="submit" class="btn">Sign Up</button>
                        <a href="index.php" class="btn bypass-btn guest-log-in">Back</a>
                    </div>
                </div>
            </div>
        </form>

        <?php if (isset($alertMessage)) { ?>
            <script>
                alert("<?php echo $alertMessage; ?>");
            </script>
        <?php } ?>
    </section>

    <footer class="footer">
        <div class="social">
            <a href="" target="_blank"><i class="ti ti-brand-facebook"></i></a>
            <a href="" target="_blank"><i class="ti ti-brand-x-filled"></i></a>
            <a href="" target="_blank"><i class="ti ti-brand-instagram-filled"></i></a>
        </div>
        <p class="copyright"> 
            @ Tarlac State University | All Rights Reserved
        </p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
