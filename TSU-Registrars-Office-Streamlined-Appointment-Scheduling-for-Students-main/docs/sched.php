<?php
session_start(); 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "booking_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$window = $_GET['window'] ?? '';

$sql = "SELECT appointment_date, appointment_time, status FROM appointments WHERE office_window = ? AND status = 'available' ORDER BY appointment_date, appointment_time";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $window);

$stmt->execute();
$result = $stmt->get_result();

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width,initial-scale=1.0">
    <title> Available Schedules </title>
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
                <a href="user-dashboard.php">Profile</a>   
                <a href="index.php">LogOut</a>
            </nav>
    </header>
    
    
    <section class="container">
    <br><br><br><br>
        <div class="office-window-title"> 
            <img style="width: 60px; padding-top: 50px;" src="images/tsu-seal.png">
        </div>
        <h1 class="office-window-title">List of Available Schedules</h1>
        <div class="office-window-title">
            <a href="booking-page.html" class="custom-btn">BACK</a>
        </div>
        
        <div class="">
            <table>
                <thead>
                    <tr>
                        <th scope="col"><h1>Window</h1></th>
                        <th scope="col"><h1>Dates</h1></th>
                        <th scope="col"><h1>Time</h1></th>
                        <th scope="col"><h1>Status</h1></th>
                        <th scope="col"><h1>Book?</h1></th>
                    </tr>
                </thead>
                <tbody>
                        <?php
                        $servername = "localhost";
                        $username = "root";
                        $password = ""; 
                        $dbname = "booking_system";

                        $conn = new mysqli($servername, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        
                        
                        $sql = "SELECT appointment_date, id, appointment_time, status FROM appointments WHERE office_window = ? AND status = 'available' ORDER BY appointment_date, appointment_time";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $window);

                        $stmt->execute();
                        $result = $stmt->get_result();
                        

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                <td>".$window."</td>
                                <td>".$row["appointment_date"]."</td>
                                <td>".$row["appointment_time"]."</td>
                                <td>".$row["status"]."</td>
                                <td><a href='book.php?id=$row[id]' class='custom-btn'>BOOK</a></td>
                            </tr>";
                        }
                        } else {
                            echo "<h1>No available appointments for $window</h1>";
                            
                        }                  
                        ?>
                    </tbody>
            </table>
        </div>
        <div class="office-window-title">
            <a href="user-dashboard.php" class="custom-btn">View Your Booked Schedules</a>
        </div>
    </section>

    <script src="script.js">
    </script>
    <script >
        
    </script>
    
</body>
</html>
