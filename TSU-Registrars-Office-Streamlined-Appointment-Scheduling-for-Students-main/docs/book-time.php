<?php 
if(isset($_GET['date'])){
    $date = $_GET['date'];
}

if(isset($_POST['submit'])){
    $mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');
    $stmt = $mysqli -> prepare("INSERT INTO bookings (date) VALUES (?)");
    $stmt -> execute();
    $msg = "<div class='alert alert-success'>Booking Successfull</div>";
    $stmt -> close();
    $mysqli -> close();
}

$duration = 10;
$cleanup = 0;
$start = "09:00";
$end = "15:00";

function timeslots($duration, $cleanup, $start, $end){
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT".$duration."M");
    $cleanupInterval = new DateInterval("PT".$cleanup."M");
    $slots = array();


    for($intStart = $start; $intStart<$end; $intStart->add($interval)->add($cleanupInterval)){
        $endPeriod = clone $intStart;
        $endPeriod -> add($interval);
        if($endPeriod>$end){
            break;
        }
        $slots[] = $intStart->format("H:iA")."-".$endPeriod->format("H:iA");
    }
    return $slots;
}
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
            <h1 class="text-center"> Book for Date: <?php echo $date ?> </h1><hr>
        <!-- idk where that "class==" coming from lmao -->
            
            <div class="row">
                <?php $timeslots = timeslots($duration, $cleanup, $start, $end);
                    foreach($timeslots as $ts){              
                ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <button class="btn btn-su"><?php echo $ts; ?></button>
                    </div>
                </div>
                <?php } ?>
                <div class="col-md-6 col-md-offset-3">
                <form action="calendar.php" method="get" style="text-align: center;">
                    <button class="btn btn-primary" type="submit">Back</button>
                </form>
                </div>
                <div class="col-md-6 col-md-offset-3">
                    <?php echo isset($msg)?$msg:'';?>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <button class="btn btn-primary" type="submit" style='text-align: center;'>Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>