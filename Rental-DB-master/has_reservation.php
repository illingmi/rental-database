<html>
<head>
<title>Has Reservation</title>

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

<form method="POST" action="purchase_rental.php">
  Confirmation Number: <input type="number" name="confNo"> <br /><br />
  OR <br /><br />
  Driver's License: <input type="text" name="cellphone"> <br /><br />

  <input class="button" type="submit" value="Find Reservation" name="checkReservationRequest">
</form>

<form method="POST" action="super_rent_db.php">
  <input class="button" type="submit" value="Back" name="backBtn">
</form>


<h4><?php
if ($_GET["err"] == 1) {
  echo "Please submit a driver's license or confirmation number to confirm your reservation.";
}
?></h4>



<?php


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
            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

function handleReservation() {

    if ($_POST["cellphone"] != NULL) {
    $dlicense = $_POST["cellphone"];
    $dlicense = "'".$dlicense."'";
    $query = executePlainSQL("SELECT * FROM Reservation WHERE cellphone = ".$dlicense."");
    $res = oci_fetch_array($query);


    } else {
      header("Location: https://www.students.cs.ubc.ca/~illingmi/purchase_rental.php");
/*    $confNo = $_POST["confNo"];
    $confNo = "'".$confNo."'";
    $query = executePlainSQL("SELECT * FROM Reservation WHERE confNo = ".$confNo."");
    $res = oci_fetch_array($query);
    unset($_POST["cellphone"]);*/
    }

}


function handlePOSTRequest() {
            if (connectToDB()) {
               handleReservation();
               disconnectFromDB();
            }
        }


if (array_key_exists('checkReservationRequest', $_POST)) {
	 handlePOSTRequest();
        }

?>

</body>
</html>
