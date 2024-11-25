<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to manage appointments.";
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

    $getScheduleSql = "SELECT schedule_date, schedule_time FROM booked_schedules WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($getScheduleSql);

    if ($stmt) {
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $schedule = $result->fetch_assoc();
            $schedule_date = $schedule['schedule_date'];
            $schedule_time = $schedule['schedule_time'];

            $deleteBookingSql = "DELETE FROM booked_schedules WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteBookingSql);
            $deleteStmt->bind_param("i", $id);

            if ($deleteStmt->execute()) {

                $updateStatusSql = "UPDATE appointments 
                                    SET status = 'available' 
                                    WHERE appointment_date = ? AND appointment_time = ?";
                $updateStmt = $conn->prepare($updateStatusSql);
                $updateStmt->bind_param("ss", $schedule_date, $schedule_time);

                if ($updateStmt->execute()) {
                    header("Location: /WebProg/TSU-Registrars-Office-Streamlined-Appointment-Scheduling-for-Students-main/docs/booking-page.html?status=canceled");
                    exit;
                } else {
                    echo "Error updating appointment status: " . $conn->error;
                }
            } else {
                echo "Error deleting the booking: " . $conn->error;
            }
        } else {
            echo "No booking found to cancel.";
        }

        $stmt->close();
    } else {
        echo "Failed to prepare the statement: " . $conn->error;
    }

    $conn->close();
} else {
    echo "No appointment ID provided!";
}
?>
