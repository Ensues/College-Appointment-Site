f<?php
session_start(); 

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
function build_calendar($month, $year) {

    // Database connection
    $mysqli = new mysqli('localhost', 'root', '', 'booking_system');

    $stmt = $mysqli -> prepare('select * from windows');
    $windows = "";
    $first_window = 0;
    $i = 0;
    if($stmt->execute()){
        $result = $stmt -> get_result();
        if($result->num_rows > 0){
            while($row = $result -> fetch_assoc()){
                if($i==0){
                    $first_room = $row['id'];
                }
                $windows.="<option value='".$row['id']."'>".$row['window_name']."</option>";
                $i++;
            }
            $stmt->close();
        }
    }

    $stmt = $mysqli -> prepare('select * from bookings where MONTH(date) = ? AND YEAR(date) = ? AND window_id = ?');
    $stmt -> bind_param('ssi', $month, $year, $first_room);
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt -> get_result();
        if($result->num_rows > 0){
            while($row = $result -> fetch_assoc()){
                $bookings[] = $row['date'];
            }
            $stmt->close();
        }
    }

    /*
    $stmt = $mysqli -> prepare('select * from bookings where MONTH(date) = ? AND YEAR(date) = ?');
    $stmt -> bind_param('ss', $month, $year);
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt -> get_result();
        if($result->num_rows > 0){
            while($row = $result -> fetch_assoc()){
                $bookings[] = $row['date'];
            }
            $stmt->close();
        }
    }
    */
    $sql = "SELECT appointment_date, appointment_time, status FROM appointments WHERE office_window = ? AND status = 'available' ORDER BY appointment_date, appointment_time";
    $bookings = array();
    $window = $_GET['window'] ?? '';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $window);
    $bookings = array();


    // Initializing days of the week
    $daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

    // Getting first day of the month
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

    //Getting number of days in the month
    $numberDays = date('t', $firstDayOfMonth);

    //Getting info about the first day of the month
    $dateComponents = getdate($firstDayOfMonth);

    //Getting the name of this month
    $monthName = $dateComponents['month'];

    //Getting the index value 0-6 of the first day of this month
    $dayOfWeek = $dateComponents['wday'];

    //Getting current date
    $dateToday = date('Y-m-d');

    //Time Navigations
    $prev_month = date("m", mktime(0,0,0,$month - 1, 1, $year));
    $prev_year = date("Y", mktime(0,0,0,$month - 1, 1, $year));
    $next_month = date("m", mktime(0,0,0,$month + 1, 1, $year));
    $next_year = date("Y", mktime(0,0,0,$month + 1, 1, $year));

    
    //Creating the HTML table
    $calendar = "<center><h2>$monthName $year</h2></center>";
    $calendar .= "<div class='button-container' style='text-align: center; margin-top: 10px;'>";
    $calendar .= "<a class='btn btn-primary' href='?month=".$prev_month."&year=".$prev_year."' style='margin-right: 10px;margin-bottom: 10px;'>Prev Month</a>";
    $calendar .= "<a class='btn btn-primary' href='?month=".date('m')."&year=".date('Y')."' style='margin-right: 10px;margin-bottom: 10px;'>Current Month</a>";
    $calendar .= "<a class='btn btn-primary' href='?month=".$next_month."&year=".$next_year."' style='margin-bottom:10px;'>Next Month</a>";
    $calendar .= "</div>";

    $calendar .= "<br>
    <form id='room_select_form'>
    <div class='row'>
        <div class='col-md-6 col-md-offset-3 form-group'>
            <label>Select Window</label>
            <select class='form-control' id='room_select'>
                ".$windows."
            </select>
        </div>
    </div>
    
    <table class='table table-bordered'>";
    $calendar .= "<tr>";

    //Creating the calendar headers
    foreach($daysOfWeek as $day){
        $calendar .= "<th>$day</th>";
    }

    $calendar .= "</tr><tr>";

    //The variable $dayOfWeek will make sure that there must be only 7 columns on our table
    if($dayOfWeek > 0){
        for($k=0;$k<$dayOfWeek;$k++){
            $calendar .= "<td class='empty'></td>";
        }
    }

    //Initiating day counter
    $currentDay = 1;

    //Getting month number

    $month = str_pad($month,2,"0", STR_PAD_LEFT);

    while($currentDay <= $numberDays){

        //if seventh column(saturday) reached, start a new row
        if($dayOfWeek == 7){
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        // Initialization of variables relating to the calendar        
        $currentDayRel = str_pad($currentDay,2,"0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $dayName = strtolower(date("l", strtotime($date)));
        $eventNum = 0;
        $today = $date==date('Y-m-d')?"today":"";

        // If... else loop that determines the availability of the day (Workdays Only > Present and Future Days > Remaining Available)
        if($dayName == 'saturday' || $dayName == 'sunday' || $dayName == 'monday'){
            $calendar.="<td class='$today'><h4>$currentDayRel</h4> <a class='btn btn-danger btn-xs'>Closed Office</a>";
        }elseif($date<date('Y-m-d')){
            $calendar.="<td class='$today'><h4>$currentDayRel</h4> <a class='btn btn-danger btn-xs'>N/A</a>";
        }else{
            $totalbookings = checkSlots($mysqli, $date);
            // We supposed to use this to limit how much people can book a day to fill it all up but tbh doubt it will ever realistically fill up
            // Have tested it already and it works, you can change 54 to 1 and book a timeslot to check yourself
            if($totalbookings == 48){
                $calendar.="<td class='$today'><h4>$currentDayRel</h4> <a href='#' class='btn btn-danger btn-xs'>All booked</a>";
            }else{
                // If changing the total bookings available per day, change this
                $availableslots = 48 - $totalbookings;
                $calendar.="<td class='$today'><h4>$currentDayRel</h4> <a href='book-time.php?date=".$date."&window=".$window."' class='btn btn-success btn-xs'>Book</a> <small><i>$availableslots slots left</i></small>";
            }
            
        }
        
        $calendar.= "</td>";

        //Incrementing counters
        $currentDay++;
        $dayOfWeek++;
    }

    //Completing the row of the last week in month, if necessary

    if($dayOfWeek < 7){
        $remainingDays = 7-$dayOfWeek;
        for($i=0;$i<$remainingDays;$i++){
            $calendar .= "<td class='empty'></td>";
        }
    }

    $calendar .= "</tr></table>";

    return $calendar;
    
}



function checkSlots($mysqli, $date){
    $stmt = $mysqli -> prepare('select * from bookings where date = ?');
    $stmt -> bind_param('s', $date);
    $totalbookings = 0;
    if($stmt->execute()){
        $result = $stmt -> get_result();
        if($result->num_rows > 0){
            while($row = $result -> fetch_assoc()){
                $totalbookings++;
            }
            $stmt->close();
        }
    }

    return $totalbookings;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Calendar</title>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' type='..images/x-icon' media='screen'>
        <link rel='stylesheet' href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="sub-style.css">
        <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    </head>

    <body>
        <header class="header">
            <a class="logo"> <img src="images/tsu-seal.png"> TSU <span>Registrar</span></a>
        </header>
        <div class="container" style="padding-top: 0px">
            <div class="row">
                <div class="col-md-12">

                    <?php

                    $dateComponents = getdate();
                    if(isset($_GET['month']) && isset($_GET['year'])){
                        $month = $_GET['month'];
                        $year = $_GET['year'];
                    }else{
                        $month = $dateComponents['mon'];
                        $year = $dateComponents['year'];
                    }
                    
                    // Builds the calendar

                    echo build_calendar($month, $year);
                    
                    ?>

                </div>
                <form action="booking-page.html" method="get" style="text-align: center;">
                        <button class="btn btn-primary" type="submit">Back</button>
                </form>

            </div>
        </div>
        
    </body>
</html>
