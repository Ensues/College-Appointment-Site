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

    // Fetch schedule date and time for the current booking from booked_schedules
    $getScheduleSql = "SELECT schedule_date, schedule_time FROM booked_schedules WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($getScheduleSql);

    if ($stmt) {
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the schedule details
            $schedule = $result->fetch_assoc();
            $schedule_date = $schedule['schedule_date'];
            $schedule_time = $schedule['schedule_time'];

            // Start transaction to delete from both tables
            $conn->begin_transaction();

            try {
                // Delete the booking from the booked_schedules table
                $deleteBookingSql = "DELETE FROM booked_schedules WHERE id = ? AND user_id = ?";
                $deleteStmt = $conn->prepare($deleteBookingSql);
                $deleteStmt->bind_param("ii", $id, $user_id);
                if (!$deleteStmt->execute()) {
                    throw new Exception("Error deleting the booking from booked_schedules: " . $conn->error);
                }

                // Delete the specific booking from the bookings table using the exact schedule date and time
                $deleteBookingsSql = "DELETE FROM bookings WHERE user_id = ? AND date = ? AND timeslot = ?";
                $deleteStmt2 = $conn->prepare($deleteBookingsSql);
                $deleteStmt2->bind_param("iss", $user_id, $schedule_date, $schedule_time);
                if (!$deleteStmt2->execute()) {
                    throw new Exception("Error deleting the booking from bookings: " . $conn->error);
                }

                // Commit transaction if deletions were successful
                $conn->commit();

                // Redirect back to the booking page after successful deletion
                header("Location: booking-page.html?status=canceled");
                exit;

            } catch (Exception $e) {
                // Rollback transaction if any error occurs
                $conn->rollback();
                echo "Error: " . $e->getMessage();
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
