<?php

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "booking_system";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "UPDATE appointments SET status = 'booked' WHERE id = ?";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
        
            header("Location: /WebProg/TSU-Registrars-Office-Streamlined-Appointment-Scheduling-for-Students-main/docs/booking-page.html?status=success");
            exit;
        } else {
            echo "Error updating record: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Failed to prepare the statement: " . $conn->error;
    }

    $conn->close();
}
?>
