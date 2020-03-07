<html>
<head><title>Return Vehicle</title>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
 $(function () {
 $(".datepicker").datepicker({
 dateFormat: 'd-M-yy'
   });
 });
</script>
<script>
 $(function () {
 $('.timepicker').timepicker({
 timeFormat: 'HH.mm.ss',
 interval: 60,
 minTime: '0',
 maxTime: '11:00pm',
  // defaultTime: '12',
 startTime: '00:00',
 dynamic: false,
 dropdown: true,
 scrollbar: true
 });
});
</script>



<style>

.button {
  display: inline-block;
  border-radius: 4px;
  background-color: #0356fc;;
  border: none;
  color: #FFFFFF;
  text-align: center;
  font-size: 14px;
  padding: 5px;
  width: 175px;
  transition: all 0.5s;
  cursor: pointer;
  margin: 5px;
}

</style>
</head>
<body>

<?php

if (empty($_POST["rid"])) {
  header("Location: https://www.students.cs.ubc.ca/~rsimm/Rental-DB-master/super_rent_db.php?err=2");
} ?>

<h2>Return a vehicle</h2>
         <form method="POST">
            <input type="hidden" id="rid" name="rid" value="<?php echo $_POST["rid"] ?>">
            Date: <input type="tex" name="toDate" class="datepicker"><br /><br />
            Time: <input type="text" name="toTime" class="timepicker"><br /><br />
            Odometer Reading: <input type="text" name="odometer"><br /><br />
            Full Gas Tank?: <input type="text" name="gasTank"><br /><br />
            <input class="button" type="submit" value="Return vehicle" name="checkReturnRequest">
         </form>

        <form method="POST" action="super_rent_db.php">
          <input class="button" type="submit" value="Back" name="backBtn">
        </form>

 <hr />

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;
            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work
            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }
            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }
			return $statement;
}




function connectToDB() {
            global $db_conn;
            $db_conn = OCILogon("ora_rsimm", "a11466620", "dbhost.students.cs.ubc.ca:1522/stu");
            if ($db_conn) {
               return true;
            } else {
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

function disconnectFromDB() {
            global $db_conn;
//            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

function handleReturn() {
  global $db_conn;
//cost starts with battery surcharge
  $cost = 50;
  $rid = $_POST["rid"];
  $rid = trim($rid);
  $toDate = trim($_POST["toDate"]);
  $toTime = trim($_POST["toTime"]);

  $q1 = executePlainSQL("SELECT * FROM Rent WHERE rid = ".$rid."");
  $res1 = oci_fetch_array($q1);

//  if ($res1 == false) {
//   header("Location: https://www.students.cs.ubc.ca/~rsimm/Rental-DB-master/super_rent_db.php?err=2");
//    echo "There is no rental associated with the rental id entered.";
//  } else

    $fromDate = trim($res1[3]);
    $fromTime = trim($res1[4]);
    $toDate = date('Y-m-d', strtotime($toDate));
    $toDate = strval($toDate);
    $toTime = strval($toTime);
    $toDate = $toDate." ".$toTime;
    $toDate = $toDate;
    $toDate = str_replace('.', ':', $toDate);
    $toDate = strval($toDate);
    $toDate = trim($toDate);
    $datetime1 = DateTime::createFromFormat('Y-m-d H:i:s', $toDate);
    $newDate = date("Y-m-d", strtotime($fromDate));
    $fromTime = substr($fromTime, 9, 9);
    $fromTime = str_replace('.', ':', $fromTime);
    $fromDate = $newDate." ".$fromTime;
    $datetime2 = DateTime::createFromFormat('Y-m-d H:i:s', $fromDate);
    $interval = $datetime1->diff($datetime2);
    $days = $interval->format('%d');
    $hours = $interval->format('%H');
    $vid = trim($res1[1]);
    $vid = "'".$vid."'";


  $q2 = executePlainSQL("SELECT vtname FROM Vehicle WHERE vid = ".$vid."");
  $res2 = oci_fetch_row($q2);
  $vtname = "'".$res2[0]."'";
  $q3 = executePlainSQL("SELECT hrate FROM VehicleType WHERE vtname = ".$vtname."");
  $res3 = oci_fetch_row($q3);
  $hrate = (int)$res3[0];
  $q4 = executePlainSQL("SELECT drate FROM VehicleType WHERE vtname = ".$vtname."");
  $res4 = oci_fetch_row($q4);
  $drate = (int)$res4[0];
  $q5 = executePlainSQL("SELECT wrate FROM VehicleType WHERE vtname = ".$vtname."");
  $res5 = oci_fetch_row($q5);
  $wrate = (int)$res5[0];

  if ($days < 1) {
//cost of car rental
    $cost += ($hrate * $hours);
//cost of car insurance
    $cost += ($hours * .5);

    echo "The cost of your rental and insurance for ".$hours." hours is $".$cost."";

  } else if ($days < 7) {
//cost of car rental
    $cost += ($hrate * $hours);
    $cost += ($drate * $days);
//cost of car insurance
    $cost += ($hours * .5);
    $cost += ($days * 60);

    echo "The cost of your rental and insurance for ".$days." days and ".$hours." hours is $".$cost."";

  } else {
//cost of car rental
    $weeks = $days/7;
    $days = $days - ($weeks * 7);
    $cost += ($hrate * $hours);
    $cost += ($drate * $days);
    $cost += ($wrate * $weeks);
//cost of car insurance
    $cost += ($hours * .5);
    $cost += ($days * 60);
    $cost += ($weeks * 100);

   echo "The cost of your rental and insurance for ".$weeks." weeks, ".$days." days and ".$hours." hours is $".$cost."";

  }





   echo  "<p><font size = '4'> Thank you for choosing SuperRent. Your vehicle has been returned. Here is the information from your latest trip: </p>";
   echo "<p><font size = '3'> Rental ID: ".$rid." </p>";
   echo "<p><font size = '3'> Total Cost: $".$cost." </p>";
   echo "<p><font size = '3'> From: ".$fromDate."</p>";
   echo "<p><font size = '3'> Until: ".$toDate."</p>";


   $q6 = executePlainSQL("UPDATE Vehicle SET status = 'not_rented' WHERE vid = ".$vid."");
//   $insDate = trim($_POST["toDate"]);
//   $insTime = trim($_POST["toTime"]);
//   $q7 = executePlainSQL("INSERT INTO Return VALUES (".$rid", null, null, 'true', ".$cost.")");*/
   OCICommit($db_conn);
}

function handlePOSTRequest() {
            if (connectToDB()) {
               handleReturn();
               disconnectFromDB();
            }
        }


if (array_key_exists('checkReturnRequest', $_POST)) {
	 handlePOSTRequest();
        }

?>

</body>
</html>
