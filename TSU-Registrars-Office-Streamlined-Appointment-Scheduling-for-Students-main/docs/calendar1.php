<?php
function build_calendar($month, $year) {

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

    //Creating the HTML table
    $calendar = "<table class='table table-bordered'";
    $calendar .= "<center><h2>$monthName $year</h2></center>";

    $calendar.="<tr>";

    //Creating the calendar headers
    foreach($daysOfWeek as $day){
        $calendar .= "<th class='header'>$day</th>";
    }

    $calendar .= "</tr><tr>";


    //The variable $dayOfWeek will make sure that there must be only 7 columns on our table
    if($dayOfWeek > 0){
        for($k=0;$k<$dayOfWeek;$k++){
            $calendar .= "<td></td>";
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

        if($dateToday==$date){
            $calendar.="<td class='today'><h4>$currentDay</h4>";
        }else{
            $calendar.="<td><h4>$currentDay</h4>";
        }

        $calendar .= "</td>";

        //Incrementing counters
        $currentDay++;
        $dayOfWeek++;
    }

    //Completing the row of the last week in month, if necessary
    if($dayOfWeek != 7){
        $remainingDays = 7-$dayOfWeek;
        for($i=0;$i<$remainingDays;$i++){
            $calendar .= "<td></td>";
        }
    }

    $calendar .= "</tr>";
    $calendar .= "</table>";

    echo $calendar;

}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Calendar</title>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' type='..images/x-icon' media='screen' href='style.css'>
    <link rel='stylesheet' href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src='script.js'></script>
    <style>
        table{
            table-layout: fixed;
        }
        td{
            width: 33%;
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
                $month = $dateComponents['mon'];
                $year = $dateComponents['year'];
                echo build_calendar($month, $year);
                ?>
            </div>
        </div>
    </div>
    
</body>
</html>