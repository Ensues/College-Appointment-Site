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
    <title>Booking System</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="sub-style.css">
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

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Booking</h4>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <div class="form-group">
                        <label for="timeslot">Timeslot</label>
                        <input type="text" readonly name="timeslot" id="timeslot" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="text" readonly name="date" id="date" class="form-control" value="<?= htmlspecialchars(date('m/d/Y', strtotime($date))); ?>">
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" readonly name="name" class="form-control" value="<?= htmlspecialchars($user['name']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="transactionType">Transaction Type</label>
                        <select name="transactionType" class="form-control" required>
                            <!-- Dynamic transaction options based on window -->
                            <?= getTransactionOptions($window); ?>
                        </select>
                    </div>
                    <div class="form-group text-right">
                        <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
    $(document).on('click', '.book', function () {
        const timeslot = $(this).data('timeslot');
        $('#timeslot').val(timeslot);
        $('#myModal').modal('show');
    });
</script>
</body>
</html>

<?php
function getTransactionOptions($window) {
    $options = [
        "Directors Office" => [
            "Request for Meeting with the Director",
            "Consultation with the Director",
            "Document Signing",
        ],
        "Window 1" => [
            "Request OR/CR",
            "Request TOR",
        ],
        "Window 6" => [
            "Submitting Documents",
        ],
        "Window 10" => [
            "Claim documents",
        ],
        "Admission Unit" => [
            "Submit Documents",
            "Scholarship Admission",
            "Admission Consultation",
        ],
    ];

    $html = "";
    foreach ($options[$window] ?? ["Default Option"] as $option) {
        $html .= "<option value='" . htmlspecialchars($option) . "'>" . htmlspecialchars($option) . "</option>";
    }

    return $html;
}
?>
