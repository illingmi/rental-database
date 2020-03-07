<html>
<head><title>No Reservation</title></head>


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
      startTime: '00:00',
      dynamic: false,
      dropdown: true,
      scrollbar: true
    });
  });
</script>



<hr />
<h2>Make a Reservation</h2>

<form method="POST"> <!--refresh page when submitted-->
  <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
  <!-- Confirmation Number: <input type="text" name="confNo"> <br /><br /> -->
  Vehicle Type: <input type="text" name="vtname"> <br /><br />
  Driver's License: <input type="text" name="cellphone"> <br /><br />
  <!-- datepicker/timepicker class allows for the time/date ui -->
  Pickup Date: <input type="text" name="fromDate" class="datepicker"> <br /><br />
  Pickup Time: <input type="text" name="fromTime" class="timepicker"> <br /><br />
  Return Date: <input type="text" name="toDate" class="datepicker"> <br /><br />
  Return Time: <input type="text" name="toTime" class="timepicker"> <br /><br />

<input type="submit" value="Reserve" name="reserve"></p>
</form>

<form method="POST" action="super_rent_db.php">
  <input type="submit" value="Back" name="backBtn"></p>
</form>

<hr />

<body>

<?php

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = NULL; // edit the login credentials in connectToDB()


     // Alert message
        function alertM($message) {
          echo '<script type="text/JavaScript">
     alert(" ' . $message . '");
     </script>';
        }

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


function executeBoundSQL($cmdstr, $list) {
  global $db_conn, $success;
  $statement = OCIParse($db_conn, $cmdstr);
  if (!$statement) {
    echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
    $e = OCI_Error($db_conn);
    echo htmlentities($e['message']);
    $success = False;
  }
  foreach ($list as $tuple) {
    foreach ($tuple as $bind => $val) {
      OCIBindByName($statement, $bind, $val);
      unset ($val);
    }
   $r = OCIExecute($statement, OCI_DEFAULT);
   if (!$r) {
     echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
     $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
     echo htmlentities($e['message']);
     echo "<br>";
     $success = False;
   }
 }
}

function connectToDB() {
  global $db_conn;
  $db_conn = OCILogon("ora_illingmi", "a28008712", "dbhost.students.cs.ubc.ca:1522/stu");
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
  OCILogoff($db_conn);
}



function handleInsertRequest() {
  global $db_conn;
  // Getting max confirmation number and setting new one to +1
  $maxConfQ = executePlainSQL("SELECT MAX(confNo) FROM Reservation");
  $newConf = oci_fetch_row($maxConfQ);
  $newConf = $newConf[0] + 1;
  // echo $newConf;
  $phone = $_POST['cellphone'];
   // Checks if phone number is in DB
   if ($phone == NULL) {
     alertM("Please enter a phone number");
     } else {
     $phoneCountQ = executePlainSQL("SELECT count(*) FROM Reservation WHERE cellphone = " . $phone);
     $phoneCount = oci_fetch_row($phoneCountQ);
     $phoneCount = $phoneCount[0];
     // If phone # is not in DB, create new confirmation number
   if ($phoneCount == 0) {
       $conf = $newConf;
       } else {
       $confQ = executePlainSQL("SELECT confNo FROM Reservation WHERE cellphone = " . $phone);
       $conf = oci_fetch_row($confQ);
       $conf = $conf[0];
       }
   }
// Makes sure all fields are filled
  if(empty($_POST['vtname'])) {
     alertM("Please choose a vehicle type");
     } else if (empty($_POST['fromDate']) || empty($_POST['toDate'])) {
     alertM("Please choose a from date");
     } else if (empty($_POST['fromTime']) || empty($_POST['toTime'])) {
     alertM("Please choose a from time");
     } else if (getNumAv() <= 0) {
     alertM("Sorry, we do not have any vehicles of that type at the moment.");
     } else {
      // Gets next available vehicle of that type
     $nextAvVID = getNextAvV();
     // Next vehicle is now set to rented
     updateStatus($nextAvVID);
     echo $nextAvVID;
     // Binds field entries to array
     $tuple = array (
     // ":bind1" => $conf,
                  ":bind2" => $_POST['vtname'],
  								":bind3" => $_POST['cellphone'],
                  ":bind4" => $_POST['fromDate'],
  								":bind5" => $_POST['fromDate']." ".$_POST['fromTime'],
                  ":bind6" => $_POST['toDate'],
  								":bind7" => $_POST['toDate']." ".$_POST['toTime']
 );
 $alltuples = array (
 $tuple
 );
 echo $conf;
 $date1 = strtotime($_POST['fromDate']." ".$_POST['fromTime']);
 $date2 = strtotime($_POST['toDate']." ".$_POST['toTime']);
  // Checks if date and time interval is valid
   if (($date2 - $date1) < 0) {
      alertM("Please choose a valid date interval.");
      } else {
       // Insert into Reservation
      executeBoundSQL("insert into Reservation values (" . $conf . ", :bind2, :bind3, :bind4, :bind5, :bind6, :bind7)", $alltuples);
      OCICommit($db_conn);
      if (checkInsert()) {
       header("Location: https://www.students.cs.ubc.ca/~illingmi/purchase_rental.php");
     } else {
       alertM("There was an error while making your reservation, please try again.");
     }

    }
  }
 }

function checkInsert() {
 $dlicense = $_POST["cellphone"];
 $dlicense = "'".$dlicense."'";
 $query = executePlainSQL("SELECT * FROM Reservation WHERE cellphone = ".$dlicense."");
 $res = oci_fetch_array($query);

 return $res;
}



// Get number of available vehicles of the input type
function getNumAv() {
          $vType = $_POST['vtname'];
          $vType = "'" . $vType . "'";
          $vTypeQ = executePlainSQL("SELECT count(*) FROM Vehicle WHERE vtname = " . $vType . " AND status = 'not_rented' ");
          $avVehicle = oci_fetch_row($vTypeQ);
          $avVehicle = $avVehicle[0];
          return $avVehicle;
}


 // Get next available vehicle of that type
 function getNextAvV() {
          $vType = $_POST['vtname'];
          $vType = "'" . $vType . "'";
          $vTypeQ = executePlainSQL("SELECT vid FROM Vehicle WHERE vtname = " . $vType . " AND status = 'not_rented' ");
          $avVehicle = oci_fetch_row($vTypeQ);
          $avVehicle = $avVehicle[0];
          return $avVehicle;
 }

// Updates status of vehicle with vid $vid to "rented"
function updateStatus($vid) {
          global $db_conn;
          $vid = "'" . $vid . "'";
          executePlainSQL("UPDATE Vehicle SET status = 'rented' WHERE vid = " . $vid);
          OCICommit($db_conn);
 }


// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
  if (connectToDB()) {
     if (array_key_exists('insertQueryRequest', $_POST)) {
       handleInsertRequest();
     }
     disconnectFromDB();
  }
}


if (array_key_exists('insertQueryRequest', $_POST)) {
  handlePOSTRequest();
}


?>

</body>
</html>
