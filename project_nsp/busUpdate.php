<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
?>

<html>

<head>
	<link rel="stylesheet" href="navButton.css">
	<link rel="stylesheet" href="formStyles.css">
</head>

<body>

	<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "vikas";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	$options = "<option selected value = ''>Select a Route Name</option>";
	$sql = "SELECT RouteName FROM route";
	$result = mysqli_query($conn, $sql);

	while ($row = mysqli_fetch_assoc($result)) {
		$options = $options . "<option value = " . $row['RouteName'] . ">" . $row['RouteName'] . "</option>";
	}
	// define variables and set to empty values
	$noOfSeats = 0;
	$errorCode = 0;
	$busno = $insuranceno = $driverID = $driverName = "";
	$busNoErr = $insuranceNoErr = $driverIDErr = $driverNameErr = $noOfSeatsErr = $routeErr = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (!empty($_POST["drivername"])) {
			$name = test_input($_POST["drivername"]);
			// check if name only contains letters and whitespace
			if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
				$driverNameErr = "Only letters and white space allowed";
				$errorCode = 1;
			}
		}


		if (!empty($_POST["driverid"])) {
			$id = test_input($_POST["driverid"]);
			$sql = "SELECT ID FROM member where ID not in (select memberid from drives)";
			$result = mysqli_query($conn, $sql);
			$f = 0;
			if (mysqli_num_rows($result) > 0) {
				// output data of each row
				while ($row = mysqli_fetch_assoc($result)) {
					if ($row["ID"] == $_POST["driverid"]) {
						$f = 1;
						break;
					}
				}
				if ($f == 0) {
					$driverIDErr = "Invalid ID!";
					$errorCode = 1;
				}
			}
		}
		if (!empty($_POST["busno"]) && $errorCode == 0) {

			$sql = "SELECT busno from bus";
			$result = mysqli_query($conn, $sql);
			$f = 0;
			if (mysqli_num_rows($result) > 0) {
				// output data of each row
				while ($row = mysqli_fetch_assoc($result)) {
					if ($row["busno"] == $_POST["busno"]) {
						$f = 1;
						break;
					}
				}
				if ($f == 1) {
					$busNoErr = "Bus number already exists!";
					$errorCode = 1;
				}
			}
		}
		if (!empty($_POST["route"]) && !empty($_POST["noofseats"]) && !empty($_POST["busno"]) && $errorCode == 0) {
			$sql = "SELECT RouteNo,Date FROM route WHERE RouteName LIKE '" . $_POST['route'] . "%'";
			$result = mysqli_query($conn, $sql);
			$row = mysqli_fetch_assoc($result);
			$sql = "INSERT INTO bus (busno, noofseats,insuranceno, routeno, date)
					  VALUES ('" . $_POST['busno'] . "', " . $_POST['noofseats'] . " ,'" . $_POST['insuranceno'] . "' ," . $row['RouteNo'] . ",'" . $row['Date'] . "')";
			if (!mysqli_query($conn, $sql)) {
				echo "Error: " . $sql . "<br>" . mysqli_error($conn);
				$errorCode = 1;
			}
			if (!$errorCode) {
				$sql = "INSERT INTO drives (memberid,busno, drivingdate)
					  VALUES ('$_POST[driverid]','$_POST[busno]','" . date('d-m-y') . "')";
				if (!mysqli_query($conn, $sql)) {
					echo "<script>alert('Error:  " . $sql . "<br>" . mysqli_error($conn) . "');window.location.href='admin.html';</script>";
					$errorCode = 1;
				}
				if (!$errorCode)
					echo "<script>alert('Successfully updated database!');window.location.href='admin.html';</script>";
			}
		}
	}

	function test_input($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	mysqli_close($conn);
	?>
	
	<div class="body"></div>
	<div class="header">
		<a href="admin.html" class="previous">&laquo; Previous</a>
		<center>
			<div>
				<span>Poornima  Transport Management</span>
			</div>
		</center>
	</div>
	</br></br>
	<div style="text-align: center;">
		<div class="container">
			<center><label>Bus and Driver Details Update</label></center>
			<p><span class="error">* required field.</span></p>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

				Driver name:<br> <input type="text" name="drivername" value="<?php echo $driverName; ?>">
				<span class="error"> <?php echo $driverNameErr; ?></span>
				<br><br>
				Driver ID:<br> <input type="text" name="driverid" value="<?php echo $driverID; ?>" required>
				<span class="error">* <?php echo $driverIDErr; ?></span>
				<br><br>
				Bus no:<br> <input type="text" name="busno" value="<?php echo $busno; ?>" required>
				<span class="error">* <?php echo $busNoErr; ?></span>
				<br><br>
				Insurance No: <br> <input type="text" name="insuranceno" value="<?php echo $insuranceno; ?>">
				<span class="error"><?php echo $insuranceNoErr; ?></span>
				<br><br>
				No of seats: <br><input type="number" name="noofseats" min=10 max=100 value="<?php echo $noOfSeats; ?>" required>
				<span class="error">* <?php echo $noOfSeatsErr; ?></span>
				<br><br>
				Route: <br><select id="route" name="route" required><?php echo $options; ?></select>
				<span class="error">* <?php echo $routeErr; ?></span>
				<br><br>
				<input type="submit" name="submit" value="Submit">
			</form>
		</div>


</body>

</html>
