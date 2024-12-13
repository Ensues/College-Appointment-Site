f<?php
function build_calendar($month, $year) {

    // Database
    $mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
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

    $calendar .= "<table class='table table-bordered'>";
    $calendar .= "<tr>";

    //Creating the calendar headers
    foreach($daysOfWeek as $day){
        $calendar .= "<th class='header'>$day</th>";
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
        
        $currentDayRel = str_pad($currentDay,2,"0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $dayName = strtolower(date("I", strtotime($date)));
        $eventNum = 0;
        $today = $date==date('Y-m-d')?'today':'';
        if(in_array($date, $bookings)){
            $calendar.="<td class='$today'><h4>$currentDayRel</h4> <a class='btn btn-danger btn-xs'>Booked</a>";
        }elseif($date<date('Y-m-d')){
            $calendar.="<td class='$today'><h4>$currentDayRel</h4> <a class='btn btn-danger btn-xs'>N/A</a>";
        }else{
            $calendar.="<td class='$today'><h4>$currentDayRel</h4> <a href='book-time.php?date=".$date." class='btn btn-success btn-xs''>Book</a>";
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
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Calendar</title>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' type='..images/x-icon' media='screen'>
    <link rel='stylesheet' href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <style>
        @media only screen and (max-width:760px),
        (min-device-width:802px) and (max-device-width:1020px){
            /* force table to not be like tables anymore */
            table,
            thead,
            tbody,
            th,
            td,
            tr{
                display: block;
            }

            .empty{
                display: none;
            }

            /* Hide table headers (but not display: none;, for accessibility) */
            th{
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr{
                border: 1px solid #ccc;
            }

            td{
                /* Behave like a "row" */
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
            }

            /* Label the data */
            td:nth-of-type(1):before {
                content: "Sunday";
            }
            td:nth-of-type(2):before {
                content: "Monday";
            }
            td:nth-of-type(3):before {
                content: "Tuesday";
            }
            td:nth-of-type(4):before {
                content: "Wednesday";
            }
            td:nth-of-type(5):before {
                content: "Thursday";
            }
            td:nth-of-type(6):before {
                content: "Friday";
            }
            td:nth-of-type(7):before {
                content: "Saturday";
            }          
        }

        /* Smartphones (portrait and landscape3) ---- */
        @media only screen and (max-width:320px) and (max-device-width: 480px){
            body{
                padding: 0;
                margin: 0;
            }
        }

        /* iPads and Tablets (portrait and landscape3) ---- */
        @media only screen and (max-width:820px) and (max-device-width: 1020px){
            body{
                width: 495px;
            }
        }

        @media(min-width:641px){

            table{
                table-layout: fixed;
            }

            td{
                width: 33%;
            }

        }

        .row{
            margin-top: 20px;
        }

        .today{
            background: yellow;
        }

    </style>
</head>
<body>
    <div class="container">
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

                echo build_calendar($month, $year);
                
                ?>
            </div>
        </div>
    </div>
    
</body>
</html>