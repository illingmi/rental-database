<html>

<head><title>Purchase Rental</title>

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

<?php
  if (empty($_POST["cellphone"]) and empty($_POST["confNo"])) {
    header("Location: https://www.students.cs.ubc.ca/~illingmi/has_reservation.php?err=1");
  } else if (!(empty($_POST["cellphone"]))) {?>
  <form method="POST" action="purchase_rental.php">
    <input type="hidden" id="cellphone" name="cellphone" value=<?php echo $_POST["cellphone"] ?>>
    Cardholder Name: <input type="text" name="name"> <br /><br />
    Credit Card Number: <input type="number" name="cnum"> <br /><br />
    Expiration Date: <input type="date" name="expdate"> <br /><br />

    <input class="button" type="submit" value="Purchase Rental" name="checkPurchaseRequest">
  </form>


<?php } else { ?>

  <form method="POST" action="purchase_rental.php">
    Driver's License: <input type="text" name="cellphone"> <br /><br />
    Cardholder Name: <input type="text" name="name"> <br /><br />
    Credit Card Number: <input type="number" name="cnum"> <br /><br />
    Expiration Date: <input type="date" name="expdate"> <br /><br />

    <input class="button" type="submit" value="Purchase Rental" name="checkPurchaseRequest">
  </form>
<?php } ?>

<form method="POST" action="has_reservation.php">
  <input class="button" type="submit" value="Back" name="backBtn">
</form>

<form method="POST" action="super_rent_db.php">
  <input class="button" type="submit" value="Homepage" name="backHome">
</form>

<body>

<?php

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
             debugAlertMessage("Disconnect from Database");
             OCILogoff($db_conn);
}

function executePlainSQL($cmdstr) {
         global $db_conn, $success;
         $statement = OCIParse($db_conn, $cmdstr);
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



function handlePurchaseRequest() {

  global $db_conn;

  if(isset($_POST["cellphone"]) != true or $_POST["cnum"] == NULL or $_POST["expdate"] == NULL) {
    echo "All fields are required to complete rental purchase.";
  } else {

     $visa = substr_compare($_POST["cnum"], '4', 0, 1, true);
     $m1 = substr_compare($_POST["cnum"], '51', 0, 2, true);
     $m2 = substr_compare($_POST["cnum"], '52', 0, 2, true);
     $m3 = substr_compare($_POST["cnum"], '53', 0, 2, true);
     $m4 = substr_compare($_POST["cnum"], '54', 0, 2, true);
     $m5 = substr_compare($_POST["cnum"], '55', 0, 2, true);

     $cnum = $_POST["cnum"];



     if (($visa == 0 and (strlen((string)$cnum) == 13 or strlen((string)$cnum) == 16))
        or (($m1 == 0 or $m2 == 0 or $m3 == 0 or $m4 == 0 or $m5 == 0) and (strlen((string)$cnum) == 16))){

     $cellNum = $_POST["cellphone"];
     $cellNum = "'".trim($cellNum)."'";

     $query = executePlainSQL("SELECT * FROM Reservation WHERE cellphone = ".$cellNum."");
     $res = oci_fetch_array($query);
     $cn = trim($res[0]);
     $vt = trim($res[1]);
     $pn = trim($res[2]);
     $fd = trim($res[3]);
     $ft = trim($res[4]);
     $td = trim($res[5]);
     $tt = trim($res[6]);

     $typecheck = "'".trim($vt)."'";
     $vassign = executePlainSQL("SELECT vid FROM Vehicle WHERE vtname = ".$typecheck." AND status = 'not_rented'");
     $vehicle = oci_fetch_row($vassign);
     $vid = trim($vehicle[0]);
     $vid = "'".$vid."'";

     executePlainSQL("UPDATE Vehicle SET status = 'rented' WHERE vid = ".$vid."");

     $checkcustomer = executePlainSQL("SELECT * FROM Customer WHERE cellphone = ".$cellNum."");
     $iscustomer = oci_fetch_array($checkcustomer);
     $customername = trim($_POST["name"]);
     $customername = "'".$customername."'";


     if ($iscustomer == false) {
       executePlainSQL("INSERT INTO Customer VALUES (".$cellNum.", ".$customername.", null)");
       OCICommit($db_conn);
     }

     $maxrid = executePlainSQL("SELECT rid FROM Rent order by rid DESC");
     $newrid = oci_fetch_row($maxrid);
     $newrid = $newrid[0] + 1;

     $getodometer = executePlainSQL("SELECT odometer FROM Vehicle WHERE vid = ".$vid."");
     $vodometer = oci_fetch_row($getodometer);
     $odom = trim($vodometer[0]);
     $cardname = trim($_POST["name"]);
     $cardname = "'".$cardname."'";
     $cardno = trim($_POST["cnum"]);
     $cardno = "'".$cardno."'";
     $expdate = trim($_POST["expdate"]);
     $expdate = date('d-M-y', strtotime($expdate));
     $expdate = "'".$expdate."'";
     $fdtype = "'".$fd."'";
     $fttype = "'".$ft."'";
     $tdtype = "'".$td."'";
     $tttype = "'".$tt."'";


     executePlainSQL("INSERT INTO Rent VALUES (".$newrid.", ".$vid.", ".$cellNum.", ".$fdtype.", ".$fttype.", ".$tdtype.",
						".$tttype.",". 0 .", ".$cardname.", ".$cardno.",".$expdate.", ".$cn.")");
     OCICommit($db_conn);

     if ($res != false) {
       echo "<p><font size = '4'> Your purchase has been confirmed and you can now rent your car. Here is your rental information: </p>";
       echo "<p><font size = '3'> Confirmation #: ".$cn." </p>";
       echo "<p><font size = '3'>  Vehicle type: ".$vt."</p>";
       echo "<p><font size = '3'> License Number: ".$pn."</p>";
       echo "<p><font size = '3'> From: ".$ft."</p>";
       echo "<p><font size = '3'> Until: ".$tt."</p>";
       echo "<p><font size = '3'> Your rental id is ".$newrid.". Please keep a record of this as you will need it when you return your vehicle.";


    } else {
       echo "There is no reservation with the given driver's license.";

    }


  }  else {

    echo "Invalid card number: SuperRent only accepts Visa or Mastercard.";
  }

 }


}

function handlePOSTRequest() {
    if (connectToDB()) {
        handlePurchaseRequest();
        disconnectFromDB();
    }
}


if (array_key_exists('checkPurchaseRequest', $_POST)) {
  handlePOSTRequest();
}

?>

</body>
</html>
