<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$mysqli = new mysqli('localhost', 'root', '', 'booking_system');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch the logged-in user's name
$stmt = $mysqli->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

// Initialize variables
$date = $_GET['date'] ?? null;
$window = $_GET['window'] ?? '';
$bookings = [];

if ($date) {
    $stmt = $mysqli->prepare('SELECT timeslot FROM bookings WHERE date = ?');
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row['timeslot'];
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionType = $_POST['transactionType'] ?? '';
    $timeslot = $_POST['timeslot'] ?? '';

    $stmt = $mysqli->prepare('SELECT * FROM bookings WHERE date = ? AND timeslot = ?');
    $stmt->bind_param('ss', $date, $timeslot);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

    } else {
        $stmt = $mysqli->prepare("INSERT INTO bookings (timeslot, date, transaction_type, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $timeslot, $date, $transactionType, $user_id);
        $stmt->execute();

        $stmt = $mysqli->prepare("INSERT INTO booked_schedules (user_id, schedule_date, schedule_time, schedule_details, window, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("issss", $user_id, $date, $timeslot, $transactionType, $window);
        $stmt->execute();

        $bookings[] = $timeslot;
    }
    $stmt->close();
}

// Generate timeslots
function generateTimeslots($duration, $cleanup, $start, $end) {
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT{$duration}M");
    $cleanupInterval = new DateInterval("PT{$cleanup}M");
    $breakStart = new DateTime("12:00"); // Break start time
    $breakEnd = new DateTime("13:00");   // Break end time
    $slots = [];

    for ($intStart = $start; $intStart < $end; $intStart->add($interval)->add($cleanupInterval)) {
        $endPeriod = clone $intStart;
        $endPeriod->add($interval);
        if ($endPeriod > $end) {
            break;
        }
        // Skip slots that fall within the break time
        if (($intStart >= $breakStart && $intStart < $breakEnd) || ($endPeriod > $breakStart && $endPeriod <= $breakEnd)) {
            continue;
        }
        $slots[] = $intStart->format("H:iA") . "-" . $endPeriod->format("H:iA");
    }
    return $slots;
}

define("DURATION", 10);
define("CLEANUP", 0);
define("START", "08:00");
define("END", "17:00");
$timeslots = generateTimeslots(DURATION, CLEANUP, START, END);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Time Picker</title>
        
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
            
        <link rel='stylesheet' href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css" integrity="sha348-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src='main.js'></script>
        <link rel="stylesheet" href="sub-style.css">
        <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    
    </head>
    <body>
        <header class="header">
            <a class="logo"> <img src="images/tsu-seal.png"> TSU <span>Registrar</span></a>
        </header>
        <div class="container" style="padding-top: 50px">
            <h1 class="text-center"> Book for Date: <?= htmlspecialchars(date('m/d/Y', strtotime($date))); ?> </h1><hr>
            <?= $msg ?? ''; ?>

            <div class="row">
                <?php foreach ($timeslots as $ts): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="form-group">
                            <?php if (in_array($ts, $bookings)): ?>
                                <button class="btn btn-danger btn-block" disabled><?= $ts; ?></button>
                            <?php else: ?>
                                <button class="btn btn-success btn-block book" data-timeslot="<?= $ts; ?>"><?= $ts; ?></button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <form action="booking-page.html" method="get" class="text-center">
                <button class="btn btn-primary" type="submit">Back</button>
            </form>
        </div>
            <!-- Modal -->
            <div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Confirmation</span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action=""id="bookingForm" method="post">
                                    <div class="form-group">
                                        <label for="">Timeslot</label>
                                        <input type="text" readonly name="timeslot" id="timeslot" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Date</label>
                                        <input type="text" readonly name="date" id="date" class="form-control" value="<?php echo date('m/d/Y', strtotime($date)); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" readonly name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="transactionType">Transaction Type</label>
                                        <select name="transactionType" id="transactionType" class="form-control" required>
                                            <?php if ($window ==="Directors Office"): ?>
                                                <!-- Directors Office -->
                                                <option value="Request for Meeting with the Director">Request for Meeting with the Director</option>
                                                <option value="Consultation with the Director">Consultation with the Director</option>
                                                <option value="Document SigningID">Document Signing</option>
                                                
                                            <?php elseif ($window === "Window 1"): ?>
                                                <!-- Window 1 (Request for records documents) -->
                                                <option value="Request OR/CR">Request OR/CR</option>
                                                <option value="Request TOR">Request TOR</option>
                                            <?php elseif ($window === "Window 6"): ?>
                                                <!-- Window 6 (Data Processing Verifications) -->
                                                <option value="Submitting Documents">Submitting Documents</option>
                                            <?php elseif ($window === "Window 10"): ?>
                                                <!-- Window 10 (Claiming of requested document records) -->
                                                <option value="Claim documents">Claim documents</option>
                                            <?php elseif ($window === "Admission Unit"): ?>
                                                <!-- Window 16 (Admission unit) -->
                                                <option value="Submit Documents">Submit Documents</option>
                                                <option value="Scholarship Admission">Scholarship Admission</option>
                                                <option value="Admission Consultation">Admission Consultation</option>
                                            <?php else: ?>
                                                <!-- Default options if no specific window matches -->
                                                <option value="Signing ID">Signing ID</option>
                                                <option value="Request OR/CR">Request OR/CR</option>
                                                <option value="Request TOR">Request TOR</option>
                                                <option value="Request Diploma">Request Diploma</option>
                                                <option value="Request Transferee Form">Request Transferee Form</option>
                                                <option value="Request Other Document">Request Other Document</option>
                                                
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div class="form-group pull-right">
                                        <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                                    </div>
                                    
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384- Tc5lQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA712mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"> </script>
        <script>
            $(".book").click(function(){
                var timeslot = $(this).attr('data-timeslot');
                $("#slot").html(timeslot);
                $("#timeslot").val(timeslot);
                $("#myModal").modal("show");
            })
            $("#bookingForm").on("submit", function(event) {
                var timeslot = $("#timeslot").val();
                if (!timeslot) {
                    event.preventDefault(); 
                    alert("This timeslot is already booked."); 
                }
            });

        </script>

    </body>
</html>
