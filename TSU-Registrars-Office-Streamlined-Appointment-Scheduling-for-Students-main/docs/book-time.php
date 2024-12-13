<?php

$mysqli = new mysqli('localhost', 'root', '', 'bookingcalendar');

// Getting the date
if(isset($_GET['date'])){
    $date = $_GET['date'];
    $stmt = $mysqli -> prepare('select * from bookings where date = ?');
    $stmt -> bind_param('s', $date);
    $bookings = array();
    if($stmt->execute()){
        $result = $stmt -> get_result();
        if($result->num_rows > 0){
            while($row = $result -> fetch_assoc()){
                $bookings[] = $row['timeslot'];
            }
            $stmt->close();
        }
    }
}

// Submitting Info
if(isset($_POST['submit'])){
    // $name = $_POST['name'];
    // email = $_POST['transactionType']
    $timeslot = $_POST['timeslot'];
    $stmt = $mysqli -> prepare('select * from bookings where date = ? AND timeslot = ?');
    $stmt -> bind_param('ss', $date, $timeslot);
    if($stmt->execute()){
        $result = $stmt -> get_result();
        if($result->num_rows > 0){
            $msg = "<div class='alert alert-danger'>Already Booked</div>";
        }else{
            $stmt = $mysqli -> prepare("INSERT INTO bookings (timeslot, date) VALUES (?, ?)");
            $stmt -> bind_param("ss", $timeslot, $date);
            $stmt -> execute();
            $msg = "<div class='alert alert-success'>Booking Successfull</div>";
            $bookings[]=$timeslot;
            $stmt -> close();
            $mysqli -> close();
        }
    }
}

// Time Slot Logic

$duration = 10;
$cleanup = 0;
$start = "08:00";
$end = "17:00";

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
            <h1 class="text-center"> Book for Date: <?php echo date('m/d/Y', strtotime($date)); ?> </h1><hr>
            
            <div class="row">
                <class class="col-md-12">
                    <?php echo isset($msg)?$msg:"";?>
                </class>
                <?php $timeslots = timeslots($duration, $cleanup, $start, $end);
                    foreach($timeslots as $ts){              
                ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <?php if(in_array($ts, $bookings)){ ?>
                            <button class="btn btn-danger book"><?php echo $ts; ?></button>
                        <?php }else{ ?>
                            <button class="btn btn-success book" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?></button>
                        <?php } ?>
                        
                    </div>
                </div>
                <?php } ?>
                <div class="col-md-6 col-md-offset-3">
                <form action="calendar.php" method="get" style="text-align: center;">
                    <button class="btn btn-primary" type="submit">Back</button>
                </form>
                </div>
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
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label for="">Timeslot</label>
                                        <input type="text" readonly name="timeslot" id="timeslot" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Date</label>
                                        <input type="text" readonly name="date" id="date" class="form-control" value="<?php echo date('m/d/Y', strtotime($date)); ?>">
                                    </div>
                                    <!--
                                    <div class="form-group">
                                        <label for="">Name</label>
                                        <input type="text" readonly name="Name" id="Name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Transaction Type</label>
                                        <input type="text" readonly name="transactionType" id="transactionType" class="form-control">
                                    </div>
                                    -->
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
        </script>
    </body>
</html>