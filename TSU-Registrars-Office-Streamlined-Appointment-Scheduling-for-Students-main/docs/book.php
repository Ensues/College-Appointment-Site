<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to book an appointment.";
    exit;
}

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $user_id = $_SESSION['user_id'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "booking_system";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT appointment_date, appointment_time, office_window FROM appointments WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $appointment = $result->fetch_assoc();

            $insertBooking = $conn->prepare("INSERT INTO booked_schedules (user_id, schedule_date, schedule_time, schedule_details, created_at) VALUES (?, ?, ?, ?, NOW())");
            $insertBooking->bind_param("isss", $user_id, $appointment['appointment_date'], $appointment['appointment_time'], $appointment['office_window']);
            
            if ($insertBooking->execute()) {
                $updateStatus = $conn->prepare("UPDATE appointments SET status = 'booked' WHERE id = ?");
                $updateStatus->bind_param("i", $id);
                $updateStatus->execute();

                header("Location: booking-page.html?status=success");
                exit;
            } else {
                echo "Error booking appointment: " . $conn->error;
            }
        } else {
            echo "Appointment not found!";
        }

        $stmt->close();
    } else {
        echo "Failed to prepare the statement: " . $conn->error;
    }

    $conn->close();
}
?>
