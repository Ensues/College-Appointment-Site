<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view your bookings.";
    exit;
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

$sql = "SELECT b.id, b.schedule_date, b.schedule_time, b.schedule_details, a.office_window, a.status 
        FROM booked_schedules b
        JOIN appointments a ON b.schedule_date = a.appointment_date AND b.schedule_time = a.appointment_time
        WHERE b.user_id = ? AND a.status = 'booked'
        ORDER BY b.schedule_date, b.schedule_time";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width,initial-scale=1.0">
    <title> Dashboard </title>
    <link rel="icon" type="image/x-icon" href="images/tsu-seal.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
</head>
<body>
    <header class="header">
        <a href="#home" class="logo"> <img src="images/tsu-seal.png"> TSU <span>Registrar</span></a>
        <nav class="navbar">
            <a href="booking-page.html">Booking</a>
            <a href="user-dashboard.php" class="active">Profile</a>
            <a href="index.php">Log Out</a>
        </nav>
    </header>
    <section class="error">
        <br>
        <br><br>
        <img style="width: 60px;" src="images/tsu-seal.png"><br><br>
        <a href="edit.php" class="custom-btn">Edit Profile</a>
        <br>
        <br>
        <h1 class="office-window-title">List of Your Schedules</h1>
        
        <a href="booking-page.html" class="custom-btn">BACK</a>
        <br>
    <div class="">
        <table>
            <thead>
                <tr>
                    <th scope="col"><h1>Window</h1></th>
                    <th scope="col"><h1>Dates</h1></th>
                    <th scope="col"><h1>Time</h1></th>
                    <th scope="col"><h1>Status</h1></th>
                    <th scope="col"><h1>Cancel?</h1></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row["office_window"]) . "</td>
                            <td>" . htmlspecialchars($row["schedule_date"]) . "</td>
                            <td>" . htmlspecialchars($row["schedule_time"]) . "</td>
                            <td>" . htmlspecialchars($row["status"]) . "</td>
                            <td>
                                <a href='/WebProg/TSU-Registrars-Office-Streamlined-Appointment-Scheduling-for-Students-main/docs/cancel.php?id=" . htmlspecialchars($row["id"]) . "' 
                                   class='custom-btn'
                                   onclick=\"return confirm('Are you sure you want to cancel this booking?')\">Cancel</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>You have no booked schedules.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php
    $stmt->close();
    $conn->close();
    ?>
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
        <p class="copyright"> 
            @ Tarlac State University | All Rights Reserved
        </p>
    </footer>
</body>
</html>
