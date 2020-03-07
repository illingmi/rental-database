<!--Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  Modified by Kareem Shammout (2019-10-23)
  This file shows the very basics of how to execute PHP commands
  on Oracle.
  Specifically, it will drop a table, create a table, insert values
  update values, and then query for values

  IF YOU HAVE A TABLE CALLED "Reservation" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the
  OCILogon below to be your ORACLE username and password -->

<html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
        body {
          font-family: Arial;
        }

        /* Header/Logo Title */
        .header {
          padding: 30px;
          text-align: center;
          background: #0356fc;
          color: yellow;
          font-size: 25px;
          font-style: oblique;
        }

          table, th, td {
            border: 1px solid black;
          }
          th {
            text-align: left;
          }

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

      <div class="header">
        <h1>SuperRent Database</h1>
        <img src="superrentlogo.png" alt="">
      </div>

      <h2>Our Prices</h2>
        <table style="width:100%">
          <tr>
            <th>Vehicle Type</th>
            <th>Hourly-Rate</th>
            <th>Daily-Rate</th>
            <th>Weekly-Rate</th>
          </tr>
          <tr>
            <td>Economy</td>
            <td>$10</td>
            <td>$80</td>
            <td>$520</td>
          </tr>
          <tr>
            <td>Compact</td>
            <td>$10</td>
            <td>$80</td>
            <td>$520</td>
          </tr>
          <tr>
            <td>Mid-Size</td>
            <td>$11</td>
            <td>$90</td>
            <td>$600</td>
          </tr>
          <tr>
            <td>Standard</td>
            <td>$12</td>
            <td>$100</td>
            <td>$660</td>
          </tr>
          <tr>
            <td>Full-Size</td>
            <td>$13</td>
            <td>$110</td>
            <td>$730</td>
          </tr>
          <tr>
            <td>SUV</td>
            <td>$14</td>
            <td>$120</td>
            <td>$800</td>
          </tr>
          <tr>
            <td>Truck</td>
            <td>$13</td>
            <td>$110</td>
            <td>$730</td>
          <tr>
      </table>

        <hr />

        <h2>Additional Charges (Same for all vehicle types)</h2>

          <table style="width:100%">
          <caption>Note: The per-kilometer charge only applies to customers who exceed the kilometer limit per rental (1000 km)</caption>
            <tr>
              <th>Hourly Insurance</th>
              <th>Daily Insurance</th>
              <th>Weekly Insurance</th>
              <th>Battery Surcharge</th>
              <th>Per-Kilometer Charge</th>
           </tr>
           <tr>
              <td>$.50</td>
              <td>$10</td>
              <td>$60</td>
              <td>$50</td>
              <td>$.50</td>
           <tr>
         </table>


        <hr />

        <h2>Rent a vehicle</h2>

        <form method="POST" action="has_reservation.php">
            <input type="submit" value="Find my reservation" name="insertSubmit"></p>
        </form>

        <form method="POST" action="no_reservation.php">
            <input type="submit" value="I don't have a reservation" name="insertSubmit"></p>
        </form>

<hr />

   <h2>Return a vehicle</h2>
        <form method="POST" action="return.php">
          Rental ID: <input type="number" name="rid"> <br /> <br />

          <input type="submit" value="Return my vehicle" name="return"></p>
        </form>
<h3><?php
  if ($_GET["err"] == 2) {
    echo "You must submit a rental id.";
 }

?></h3>

        <hr />

        <h2>Check Available Cars</h2>
        <form method="POST" action="super_rent_db.php"> <!--refresh page when submitted-->
            <input type="hidden" id="checkAvQueryRequest" name="checkAvQueryRequest">
            Car type: <input type="text" name="cType"> <br /><br />
            Location: <input type="text" name="location"> <br /><br />
						From: <input type="text" name="tIntervalF" class="datepicker"> <br /><br />
            To: <input type="text" name="tIntervalT" class="datepicker"> <br /><br />
            <input type="submit" value="CheckAv" name="checkAvSubmit"></p>
            <input type="submit" value="Show available cars" name="showAllAv"></p>
        </form>

        <hr />

        <h2>Make a Reservation</h2>
        <form method="POST" action="super_rent_db.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            <!-- Confirmation Number: <input type="text" name="confNo"> <br /><br /> -->
            Vehicle Type: <input type="text" name="vtname"> <br /><br />
						Phone Number: <input type="text" name="cellphone"> <br /><br />
            <!-- datepicker/timepicker class allows for the time/date ui -->
						Pickup Date: <input type="text" name="fromDate" class="datepicker"> <br /><br />
						Pickup Time: <input type="text" name="fromTime" class="timepicker"> <br /><br />
						Return Date: <input type="text" name="toDate" class="datepicker"> <br /><br />
						Return Time: <input type="text" name="toTime" class="timepicker"> <br /><br />

            <input type="submit" value="Reserve" name="reserve"></p>
        </form>

        <hr />

        <h2>Company Daily Rentals</h2>
       <form method="POST" action="super_rent_db.php">
           <input type="hidden" id="companyRentalsRequest" name="companyRentalsRequest">
               Date: <input type="text" name="requestedDate" class="datepicker"> <br /><br />

           <input type="submit" value="Get Company Rentals Report" name="showCompanyRentals"></p>
       </form>

       <hr />

       <h2>Branch Daily Rentals</h2>
           <p>A branch is one of Main, Granville, or Davie</p>
       <form method="POST" action="super_rent_db.php">
           <input type="hidden" id="branchRentalsRequest" name="branchRentalsRequest">
               Date: <input type="text" name="requestedDate" class="datepicker"> <br /><br />
               Branch: <input type="text" name="requestedBranch"> <br /><br />

           <input type="submit" value="Get Branch Rentals Report" name="showBranchRentals"></p>
       </form>


       <hr />

       <h2>Company Daily Returns</h2>
       <form method="POST" action="super_rent_db.php">
           <input type="hidden" id="companyReturnsRequest" name="companyReturnsRequest">
               Date: <input type="text" name="requestedDate" class="datepicker"> <br /><br />

           <input type="submit" value="Get Company Returns Report" name="showCompanyReturns"></p>
       </form>

       <hr />

       <h2>Branch Daily Returns</h2>
           <p>A branch is one of Main, Granville, or Davie</p>
       <form method="POST" action="super_rent_db.php">
           <input type="hidden" id="branchRetrunsRequest" name="branchReturnsRequest">
               Date: <input type="text" name="requestedDate" class="datepicker"> <br /><br />
               Branch: <input type="text" name="requestedBranch"> <br /><br />

           <input type="submit" value="Get Branch Returns Report" name="showBranchReturns"></p>
       </form>

       <hr />


        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function printVehicleCategories($row, $date, $tabletype, $stid){

            echo"<br>Table of vehicles " . $tabletype . " with counts vehicle category for " . $date . "</br>";
            echo"<table>";
            echo"<tr><th>Vehicle Type</th> <th>Total vehicles " . $tabletype . "</th></tr>";
            echo "</table>";

            print '<table border="1">';
						while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
   							print '<tr>';
   							foreach ($row as $item) {
       							print '<td>'.($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp').'</td>';
   							}
   					print '</tr>';
						}
						print '</table>';

        }

        function printVehicleBranches($row, $date, $tabletype, $stid){

            echo"<br>Retrieved count of vehicles" . $tabletype . " in each branch for " . $date . "</br>";
            echo"<table>";
            echo"<tr><th>Branch</th> <th>Total vehicles " . $tabletype . "</th></tr>";
            echo"</table>";

            print '<table border="1">';
						while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
   							print '<tr>';
   							foreach ($row as $item) {
       							print '<td>'.($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp').'</td>';
   							}
   					print '</tr>';
						}
						print '</table>';


        }

        function printBranchesRev($row, $date, $tabletype, $stid){

            echo"<br>Revenue for each vehicle" . $tabletype . " in each branch for " . $date . "</br>";
            echo"<table>";
            echo"<tr><th>Branch</th> <th>Total Revenue" . $tabletype . "</th></tr>";
            echo"</table>";

            print '<table border="1">';
            while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
                print '<tr>';
                foreach ($row as $item) {
                    print '<td>'.($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp').'</td>';
                }
            print '</tr>';
            }
            print '</table>';


        }

        // TODO - Don't know what I should label the columns as?
        // Prints out table of data for a specific date
        function printTable($row, $date, $tabletype, $stid){

            // echo"<br> Vehicles " . $tabletype . " for " . $date . "</br>";
            // echo"<table>";
            // //echo"<tr><th>Vehicle Type</th> <th>Total Vehicles Rented</th></tr>";
            //
            // while ($row = OCI_Fetch_ARRAY($result, OCI_BOTH)){
            //     echo $row[0]; // get the entire row
            // }

            echo "</table>";

            print '<table border="1">';
            while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
                print '<tr>';
                foreach ($row as $item) {
                    print '<td>'.($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp').'</td>';
                }
            print '</tr>';
            }
            print '</table>';

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
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

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
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
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


        function printResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table Reservation:<br>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["NID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_illingmi", "a28008712", "dbhost.students.cs.ubc.ca:1522/stu");
            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
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

        function handleBranchRentalsRequest(){

            global $db_conn;

            // TODO - fix date stuff
            $req_date = $_POST['requestedDate'];
            $req_branch = $_POST['requestedBranch'];
            $rented = "rented"; // TODO - this could be the wrong way to declare this

            // View table of Vehicles that match the inputted date and branch, ordered by vehicle type
            // executePlainSQL("CREATE VIEW vehiclesRented AS
            //                 SELECT Vehicle.vid, Vehicle.vtname, Vehicle.location, Vehicle.city
            //                 FROM Rent, Vehicle
            //                 WHERE Rent.vid = Vehicle.vid AND Rent.fromdate ='" . $req_date . "' AND Vehicle.location ='" . $req_location . "'
            //                 ORDER BY Vehicle.vtname");
            //
            // // TODO - query needs to be tested
            // // Lists the number of rented vehicles per vehicle type
            // // Counts the number of vids in vehiclesRented, per vehicle type
            // executePlainSQL("CREATE VIEW numVehicleType AS
            //                 SELECT COUNT(vehiclesRented.vid) AS numVehicles, vehiclesRented.vtname AS vehicleType
            //                 FROM vehiclesRented
            //                 GROUP BY vehiclesRented.vtname");

            executePlainSQL("CREATE VIEW vehiclesRented AS
                            SELECT Vehicle.vid, Vehicle.vtname, Vehicle.location, Vehicle.city
                            FROM Rent, Vehicle
                            WHERE Rent.vid = Vehicle.vid AND Rent.fromdate ='" . $req_date . "' AND Vehicle.location ='" . $req_branch . "'
                            ORDER BY Vehicle.vtname");
            echo $req_branch;

                            // TODO - query needs to be tested
                            // Lists the number of rented vehicles per vehicle type
                            // Counts the number of vids in vehiclesRented, per vehicle type
            executePlainSQL("CREATE VIEW numVehicleType AS
                            SELECT vehiclesRented.vtname AS vehicleType, COUNT(vehiclesRented.vid) AS numVehicles
                            FROM vehiclesRented
                            GROUP BY vehiclesRented.vtname");

           // ERROR HANDLING: what if date, branch isn't selected, what if branch doesn't exist
           // Makes sure all fields are filled
            if(empty($_POST['requestedDate'])) {
              alertM("Please choose a correct date");
            }if(empty($_POST['requestedBranch'])){
                alertM("Please fill in a correct Branch location");
            }


           // Queries for printing - TODO - these are supposed to have executePlainSQL right??
           $rentals_countQ = executePlainSQL("SELECT COUNT(*) FROM vehiclesRented");
           // $rented_per_catQ = "SELECT vehicleType, numVehicles FROM numVehicleType";
           $rented_per_catQ = "SELECT * FROM numVehicleType";
           $branch_rentalsQ = "SELECT * FROM vehiclesRented";




           $stidPC = oci_parse($db_conn, $rented_per_catQ);
           $rpc = oci_execute($stidPC);

           $stidR = oci_parse($db_conn, $branch_rentalsQ);
           $rr = oci_execute($stidR);

           //Printing out information
           // Prints out:
           //       1. Total Branch Rentals
           //       2. Table of Number of Vehicles Rented per Category
           //       3. Branch Rentals Report of the Selected Date
           printTable($rr, $req_date, $rented, $stidR); // prints table of vehicles rented at requested date and branch
           printVehicleCategories($rpc, $req_date, $rented, $stidPC); // prints table of count of vehicles rented per category

           $total_branch_rentals = oci_fetch_array($rentals_countQ);
           $total = $total_branch_rentals[0];
           echo "The total branch rentals for " . $req_branch . " on " . $req_date . " is " . $total . "!";

            // Drop view tables
            executePlainSQL("DROP VIEW vehiclesRented");
            executePlainSQL("DROP VIEW numVehicleType");

        }

        function handleCompanyRentalsRequest(){

            global $db_conn;

            $req_date = $_POST['requestedDate'];
            $rented = $rented;

            // View table of Vehicles that match the inputted date and branch, ordered by branch (location), then vehicle type
            // Vehicles rented table of req_date, ordered by BRANCH, then VEHICLE TYPE
            executePlainSQL("CREATE VIEW vehiclesRented AS
                            SELECT Vehicle.vid, Vehicle.vtname, Vehicle.location, Vehicle.city
                            FROM Rent, Vehicle
                            WHERE Rent.vid = Vehicle.vid AND Rent.fromdate ='" . $req_date . "'
                            ORDER BY Vehicle.location, Vehicle.vtname");

            // Lists the number of rented vehicles per branch
            executePlainSQL("CREATE VIEW numBranch AS
                            SELECT vehiclesRented.location AS branchLocation, COUNT(vehiclesRented.vid) AS numVehicles
                            FROM vehiclesRented
                            GROUP BY vehiclesRented.location"); // TODO - should that be "branchLocation" instead??
            //Lists the number of rented vehicles per category
            executePlainSQL("CREATE VIEW numVehicleType AS
                            SELECT vehiclesRented.vtname AS vehicleType, COUNT(vehiclesRented.vid) AS numVehicles
                            FROM vehiclesRented
                            GROUP BY vehiclesRented.vtname"); // TODO - should that be "vehicleType" instead??

           // Queries for printing

           $num_rentals_per_company = executePlainSQL("SELECT COUNT (*)
                                                     FROM vehiclesRented");

           $all_rentals_per_company = "SELECT * FROM vehiclesRented";

           $rentals_per_branch = "SELECT branchLocation, numVehicles FROM numBranch";

           $rentals_per_category = "SELECT vehicleType, numVehicles FROM numVehicleType";

           $stidAR = oci_parse($db_conn, $all_rentals_per_company);
           $rar = oci_execute($stidAR);

           $stidRB = oci_parse($db_conn, $rentals_per_branch);
           $rrb = oci_execute($stidRB);

           $stidPC = oci_parse($db_conn, $rentals_per_category);
           $rpc = oci_execute($stidPC);


            // Print Information

            printTable($rar, $req_date, $rented, $stidAR); // prints table of company rentals (for date)
            printVehicleBranches($rrb, $req_date, $rented, $stidRB); // prints table of total rented vehicles for each branch (for date)
            printVehicleCategories($rpc, $req_date, $rented, $stidPC); // prints table of total rented vehicles for each category (for date)


            // Prints New Company Rentals Total

           $new_company_rentals = oci_fetch_array($num_rentals_per_company);
           $total = $new_company_rentals[0];
           echo "The total new rentals on " . $req_date . " is " . $total . "!";

            executePlainSQL("DROP VIEW vehiclesRented");
            executePlainSQL("DROP VIEW numVehicleType");
            executePlainSQL("DROP VIEW numBranch");

        }

        function handleBranchReturnsRequest(){

            global $db_Conn;

            $req_date = $_POST['requestedDate'];
            $req_branch = $_POST['requestedBranch'];
            $returned = "returned";

            // Lists the number of returned vehicles per branch
            executePlainSQL("CREATE VIEW numBranch AS
                            SELECT COUNT(vehiclesReturned.vid) AS numVehicles, vehiclesReturned.location AS branchLocation
                            FROM vehiclesReturned
                            GROUP BY vehiclesReturned.location"); // TODO - should that be "branchLocation" instead??

            // Lists the number of returned vehicles per category
            executePlainSQL("CREATE VIEW numVehicleType AS
                            SELECT COUNT(vehiclesReturned.vid) AS numVehicles, vehiclesReturned.vtname AS vehicleType
                            FROM vehiclesReturned
                            GROUP BY vehiclesReturned.vtname"); // TODO - should that be "vehicleType" instead??

        }

        function handleCompanyReturnsRequest(){

            global $db_Conn;

            $req_date = $_POST['requestedDate'];
            $returned = "returned";

            // View table of Vehicles that match the inputted date and branch, ordered by branch (location), then vehicle type
            // Vehicles returned table of req_date, ordered by BRANCH, then VEHICLE TYPE
            executePlainSQL("CREATE VIEW vehiclesReturned AS
                            SELECT Vehicle.vid, Vehicle.vtname, Vehicle.location, Vehicle.city, Return.value
                            FROM Return, Vehicle, Rent
                            WHERE Return.rid = Rent.rid AND Vehicle.vid = Rent.vid AND Return.todate = '" . $req_date . "'
                            ORDER BY Vehicle.location, Vehicle.vtname");

            // Lists the number of returned vehicles per branch
            executePlainSQL("CREATE VIEW numBranch AS
                            SELECT COUNT(vehiclesReturned.vid) AS numVehicles, vehiclesReturned.location AS branchLocation
                            FROM vehiclesReturned
                            GROUP BY vehiclesReturned.location"); // TODO - should that be "branchLocation" instead??

            // Lists the number of returned vehicles per category
            executePlainSQL("CREATE VIEW numVehicleType AS
                            SELECT COUNT(vehiclesReturned.vid) AS numVehicles, vehiclesReturned.vtname AS vehicleType
                            FROM vehiclesReturned
                            GROUP BY vehiclesReturned.vtname"); // TODO - should that be "vehicleType" instead??

            // Revenue per vehicle category
            executePlainSQL("CREATE VIEW revVehicleType AS
                            SELECT SUM(vehiclesReturned.value) AS revenue, vehiclesReturned.vtname AS vehicleType
                            FROM vehiclesReturned
                            GROUP BY vehiclesReturned.vtname"); // TODO - should that be "vehicleType" instead??

            //
            // // Revneue per branch TODO - THINK ABOUT THIS PART! YOU WERE DOING THIS
            // executePlainSQL("CREATE VIEW revBranch
            //                 SELECT SUM(vehiclesReturned.value) AS revenue, vehiclesReturned.vtname AS vehicleType
            //                 FROM vehiclesReturned
            //                 GROUP BY vehiclesReturned.vtname"); // TODO - should that be "vehicleType" instead??


            // Grand total company revenue

            // Queries for printing

            $num_returns_per_company = executePlainSQL("SELECT COUNT (*)
                                                      FROM vehiclesReturned");

            $all_returns_per_company = "SELECT * FROM vehiclesReturned";

            $returns_per_branch = "SELECT numVehicles, branchLocation FROM numBranch";

            $returns_per_category = "SELECT vehicleType, numVehicles FROM numVehicleType";

            $rev_per_category = "SELECT vehicleType, revVehicleType FROM numVehicleType";

            $stidAR = oci_parse($db_conn, $all_returns_per_company);
            $rar = oci_execute($stidAR);

            $stidRB = oci_parse($db_conn, $returns_per_branch);
            $rrb = oci_execute($stidRB);

            $stidPC = oci_parse($db_conn, $returns_per_category);
            $rpc = oci_execute($stidPC);

            $stidRevC = oci_parse($db_conn, $rev_per_category);
            $revC = oci_execute($stidRevC);

            printTable($rar, $req_date, $returned, $stidAR); // prints table of company rentals (for date)
            printVehicleBranches($rrb, $req_date, $returned, $stidRB); // prints table of total rented vehicles for each branch (for date)
            printVehicleCategories($rpc, $req_date, $returned, $stidPC); // prints table of total rented vehicles for each category (for date)
            printBranchesRev($revV, $req_date, $returned, $stidRevC);

            $new_company_returns = oci_fetch_array($num_returns_per_company);
            $total_returns = $new_company_returns[0];
            echo "The total new returns on " . $req_date . " is " . $total_returns . "!";

            executePlainSQL("DROP VIEW vehiclesReturned");
            executePlainSQL("DROP VIEW numBranch");
            executePlainSQL("DROP VIEW numVehicleType");
            executePlainSQL("DROP VIEW revVehicleType");

        }

        function handleUpdateRequest() {
            global $db_conn;

            $conf_no = $_POST['confNo'];
            $phone_no = $_POST['cellphone'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE Reservation SET cellphone='" . $phone_no . "' WHERE confNo='" . $conf_no . "'");
            OCICommit($db_conn);
        }


        function handleInsertRequest() {
            global $db_conn;

            // Getting max confirmation number and setting new one to +1
            $maxConfQ = executePlainSQL("SELECT confNo FROM Reservation order by confNo DESC");
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
              alertM("Please fill in vehicle type");
            } else if (empty($_POST['fromDate']) || empty($_POST['toDate'])) {
              alertM("Please fill in a correct date");
            } else if (empty($_POST['fromTime']) || empty($_POST['toTime'])) {
              alertM("Please fill in a correct time");
            } else {
              if (getNumAv() <= 0) {
                alertM("Sorry we do not have any vehicles of that type at the moment");
              } else {
                $numAv = getNumAv(); // Number of available vehicles of that type
              }

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
                alertM("Please choose a valid date and time interval.");
              } else {
                // Insert into Reservation
                executeBoundSQL("insert into Reservation values (" . $conf . ", :bind2, :bind3, :bind4, :bind5, :bind6, :bind7)", $alltuples);
                OCICommit($db_conn);
                echo "Inserted into table";
              }

            }


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

        // Show all available vehicles
        function showAllAv() {
          global $db_conn;

						if($_POST['cType'] == NULL) {
							$cType = "IS NOT NULL";
						} else {

							$cType = "=" . "'" . $_POST['cType'] . "'";
						}

						if($_POST['location'] == NULL) {
							$location = "IS NOT NULL";
						} else {
							$location = "=" . "'" . $_POST['location'] . "'";
						}

						$query = "SELECT make,model,year,color,vtname,location,city FROM Vehicle WHERE vtname " . $cType . " AND location " . $location . "
						AND status='not_rented' ORDER BY vtname asc";
						$stid = oci_parse($db_conn, $query);
						$r = oci_execute($stid);

            print '<table border="1">';
						while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
   							print '<tr>';
   							foreach ($row as $item) {
       							print '<td>'.($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp').'</td>';
   							}
   					print '</tr>';
						}
						print '</table>';
        }

        // Show number of vehicles available
				function handleCheckAvRequest() {
					global $db_conn;

						if($_POST['cType'] == NULL) {
							$cType = "IS NOT NULL";
						} else {
							$cType = "=" . "'" . $_POST['cType'] . "'";
						}

						if($_POST['location'] == NULL) {
							$location = "IS NOT NULL";
						} else {
							$location = "=" . "'" . $_POST['location'] . "'";
						}

						$result = executePlainSQL("SELECT count(*) FROM Vehicle WHERE vtname " . $cType . " AND location " . $location . "
						AND status='not_rented'");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of available cars: " . $row[0] . "<br>";
            }

				}

        // Alert message
        function alertM($message) {
          echo '<script type="text/JavaScript">
     alert(" ' . $message . '");
     </script>';
        }

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM Reservation");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in Reservation: " . $row[0] . "<br>";
            }
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
              if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['updateSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest'])) {
            handleGETRequest();
        }

    if (isset($_POST['showAllAv'])) {
      if(connectToDB()) {
        showAllAv();

        disconnectFromDB();
      }
    } else if (isset($_POST['checkAvQueryRequest'])) {
      if(connectToDB()) {
        handleCheckAvRequest();

        disconnectFromDB();
      }
    } else if (isset($_POST['reserve'])) {
      if(connectToDB()) {
        handleInsertRequest();

        disconnectFromDB();
      }
    } else if (isset($_POST['showCompanyRentals'])){
        if(connectToDB()){
            handleCompanyRentalsRequest();

            disconnectFromDB();
        }
    } else if (isset($_POST['showBranchRentals'])){
        if(connectToDB()){
            handleBranchRentalsRequest();

            disconnectFromDB();
        }
    } else if (isset($_POST['showCompanyReturns'])){
        if(connectToDB()){
            handleCompanyReturnsRequest();

            disconnectFromDB();
        }
    } else if (isset($_POST['showBranchReturns'])){
        if(connectToDB()){
            handleBranchReturnsRequest();

            disconnectFromDB();
        }
    }
		?>
	</body>
</html>
