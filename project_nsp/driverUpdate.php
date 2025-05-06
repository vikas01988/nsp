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
	$options = "<option selected value = ''>Select a Bus</option>";
	$sql = "SELECT BusNo FROM bus";
	$result = mysqli_query($conn, $sql);

	while ($row = mysqli_fetch_assoc($result)) {
		$options = $options . "<option value = " . $row['BusNo'] . ">" . $row['BusNo'] . "</option>";
	}
	// define variables and set to empty values
	$noOfSeats = 0;
	$errorCode = 0;
	$busno = $driverID = $driverName = "";
	$busNoErr  = $driverIDErr = $driverNameErr =  "";

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
			$sql = "SELECT MemberID FROM  drives";
			$result = mysqli_query($conn, $sql);
			$f = 0;
			if (mysqli_num_rows($result) > 0) {
				// output data of each row
				while ($row = mysqli_fetch_assoc($result)) {
					if ($row["MemberID"] == $_POST["driverid"]) {
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

			$sql = "INSERT INTO drives(memberid, busno, drivingdate)
				VALUES('" . $_POST["driverid"] . "','" . $_POST["busno"] . "','" . date('y-m-d') . "')";
			if (!mysqli_query($conn, $sql)) {
				echo "Error: " . $sql . "<br>" . mysqli_error($conn);
				$errorCode = 1;
			}
			if (!$errorCode)
				echo "<script>alert('Successfully updated database!');window.location.href='admin.html';</script>";
			else
				echo "<script>alert('Database update error!');window.location.href='admin.html';</script>";
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
				<span>RVCE Transport Management</span>
			</div>
		</center>
	</div>
	</br></br>
	<div style="text-align: center;">
		<div class="container">
			<center><label>Driver Details Update</label></center>
			<p><span class="error">* required field.</span></p>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

				Bus no:<br> <select id="busno" name="busno" required><?php echo $options; ?></select>
				<span class="error">* <?php echo $busNoErr; ?></span>
				<br><br>
				Driver ID:<br> <input type="text" name="driverid" value="<?php echo $driverID; ?>" required>
				<span class="error">* <?php echo $driverIDErr; ?></span>
				<br><br>
				Driver name:<br> <input type="text" name="drivername" value="<?php echo $driverName; ?>">
				<span class="error"> <?php echo $driverNameErr; ?></span>
				<br><br>
				<input type="submit" name="submit" value="Submit">
			</form>
		</div>


</body>

</html>
