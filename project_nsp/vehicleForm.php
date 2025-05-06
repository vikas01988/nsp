<html>

<head>
	<link rel="stylesheet" href="navButton.css">
	<link rel="stylesheet" href="formStyles.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" />
	</script>
	<script>
		$(document).ready(function() {
			$('#noOfVehicle').change(function() {
				if ($("#noOfVehicle").val() == "") {
					$("#vehicleDetails").html("");
				} else {
					var n = parseInt($("#noOfVehicle").val(), 10);
					var s = '';
					for (var i = 0; i < n; i++) {
						s += "Vehicle " + (i + 1) + ":</br></br>";
						s += "Vehicle name: <br> <input type='text' name='vname" + (i + 1) + "' id='vname" + (i + 1) + "' required></br></br>";
						s += "Vehicle number:<br><input type='text' name='vno" + (i + 1) + "' id='vno" + (i + 1) + "' required></br></br>";

					}

					$("#vehicleDetails").html(s);
				}
			});
		});
	</script>
</head>

<body>

	<?php

	session_start();
	date_default_timezone_set('Asia/Kolkata');
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
	$nameErr = $emailErr = $genderErr = $idErr = $routeErr = $receiptNoErr = $vnameErr = $vnoErr = "";
	$name = $email = $gender = $comment = $id = $route = $vname = $vno = $vid = $PAName = $SlotID = "";
	$seatno = $errorCode = $receiptno = 0;
	$busNumberAllotted = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (empty($_POST["name"])) {
		} else {
			$name = test_input($_POST["name"]);
			// check if name only contains letters and whitespace
			if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
				$nameErr = "Only letters and white space allowed";
				$errorCode = 1;
			}
		}

		if (empty($_POST["email"])) {
		} else {
			$email = test_input($_POST["email"]);
			// check if e-mail address is well-formed
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$emailErr = "Invalid email format";
				$errorCode = 1;
			}
		}

		if (empty($_POST["id"])) {
		} else {
			$id = test_input($_POST["id"]);
			$sql = "SELECT ID,DNumber FROM member";
			$result = mysqli_query($conn, $sql);
			$f = 0;
			if (mysqli_num_rows($result) > 0) {
				// output data of each row
				while ($row = mysqli_fetch_assoc($result)) {
					if ($row["ID"] == $_POST["id"]) {
						$f = 1;
						$dNum = $row["DNumber"];
						break;
					}
				}
				if ($f == 0) {
					$idErr = "Invalid ID!";
					$errorCode = 1;
				}
			} else {
				echo "0 results";
			}
			$sql = "SELECT MemberID FROM registersfor";
			$result = mysqli_query($conn, $sql);
			$f = 0;
			if (mysqli_num_rows($result) > 0) {
				// output data of each row
				while ($row = mysqli_fetch_assoc($result)) {
					if ($row["MemberID"] == $_POST["id"]) {
						$f = 1;
						break;
					}
				}
				if ($f == 1) {
					$idErr = "Already registered!";
					$errorCode = 1;
				}
			}
		}

		if (!empty($_POST["vtype"]) && $errorCode == 0) {
			$vtype = $_POST["vtype"];
			$sql = "SELECT Type from member WHERE ID= '" . $_POST["id"] . "'";
			$result = mysqli_query($conn, $sql);
			$row = mysqli_fetch_assoc($result);
			if ($row["Type"] == 1)
				$Type = "S";
			if ($row["Type"] == 2 || $row["Type"] == 5)
				$Type = "F";
			if ($row["Type"] == 3 || $row["Type"] == 4)
				$Type = "H";
			$sql = "SELECT Max(VID) from registersfor WHERE VID LIKE '2018" . $vtype . $Type . "%'";
			$result = mysqli_query($conn, $sql);
			$row = mysqli_fetch_assoc($result);
			$digits = (int)substr($row["Max(VID)"], 7, strlen($row["Max(VID)"])) + 1;
			$digits = str_pad($digits, 4, "0", STR_PAD_LEFT);
			$vid = "2018" . $vtype . $Type . "$digits";
		}
		if (!empty($_POST["noOfVehicle"]) && $errorCode == 0) {
			$arr = array();
			if ($Type != "H" && $dNum != 13 && $dNum != 14) {
				$sql = "select parkingareaid,vicinityno from associatedwith where dno=$dNum and vicinityno!=0 order by vicinityno";
				$result = mysqli_query($conn, $sql);
				while ($row = mysqli_fetch_assoc($result)) {
					array_push($arr, $row["parkingareaid"]);
				}
			} else {
				for ($x = 1; $x <= 12; $x++) {
					array_push($arr, $x);
				}
			}
			foreach ($arr as $i) {

				$sql = "SELECT " . $vtype . $Type . ",ParkingAreaName FROM parkinglot WHERE Parkingareaid = " . $i;
				$result = mysqli_query($conn, $sql);
				$row = mysqli_fetch_assoc($result);
				$maxSlots = $row[$vtype . $Type];
				$vamt = ($vtype == '2W') ? 1250 : 2300;
				$netType = $vtype . $Type;
				$PAName = $row["ParkingAreaName"];
				$sql = "SELECT MAX(SlotID) FROM vehicle WHERE Parkingareaid = " . $i . " AND vid like '%$netType%'";
				$result = mysqli_query($conn, $sql);
				$row = mysqli_fetch_assoc($result);
				$maxSlotAlloted = $row["MAX(SlotID)"];
				$maxSlotAlloted = (int)substr($row["MAX(SlotID)"], 7, strlen($row["MAX(SlotID)"]));
				//echo "<br>Maximum Slots: ".$maxSlots." Maximum Slot ID alloted: ".$maxSlotAlloted;
				$paID = ($i > 9) ? $i : "0" . $i;
				if ($maxSlots > $maxSlotAlloted) {

					$num = (int)$maxSlotAlloted + 1;
					$num = str_pad($num, 3, "0", STR_PAD_LEFT);
					$SlotID = "PID" . $paID . $vtype . $num;
					$noOfVehicles = $_POST['noOfVehicle'];
					if ($errorCode == 0) {
						$sql = "INSERT INTO vehicle (vid, vehicletype,parkingareaid,slotid)
						VALUES ('$vid', '$vtype' , $i,'$SlotID')";
						if (!mysqli_query($conn, $sql)) {
							echo "Error: " . $sql . "<br>" . mysqli_error($conn);
							$errorCode = 1;
							break;
						}
						for ($j = 1; $j <= $noOfVehicles; $j++) {
							$vno = $_POST['vno' . $j];
							$vname = $_POST['vname' . $j];
							$sql = "INSERT INTO vehicledetails (vid, vno,vname)
						VALUES ('$vid', '$vno' , '$vname')";
							if (!mysqli_query($conn, $sql)) {
								echo "Error: " . $sql . "<br>" . mysqli_error($conn);
								$errorCode = 1;
								break;
							}
						}
						$sql = "INSERT INTO registersfor (memberid, vid, receiptno,regamount,date)
					  VALUES ('$id', '$vid' , $receiptno, $vamt,'" . date('y-m-d') . "')";
						if (!mysqli_query($conn, $sql)) {
							echo "Error: " . $sql . "<br>" . mysqli_error($conn);
							$errorCode = 1;
							break;
						}
						if ($errorCode == 0) {
							$_SESSION["name"] = $_POST["name"];
							$_SESSION["id"] = $_POST["id"];
							$_SESSION["vid"] = $vid;
							$_SESSION["PAname"] = $PAName;
							$_SESSION["slotid"] = $SlotID;
							header('location: vehiclePass.php');
							exit;
							break;
						}
					}
				}
			}
			if ($errorCode == 1) {
				echo "Error!";
			}
		}
		if (!empty($_POST["receiptno"])) {
			$sql = "(SELECT receiptno FROM registersfor) union (SELECT receiptno FROM enrollsfor)";
			$result = mysqli_query($conn, $sql);
			$f = 0;
			if (mysqli_num_rows($result) > 0) {
				// output data of each row
				while ($row = mysqli_fetch_assoc($result)) {
					if ($row["receiptno"] == $_POST["receiptno"]) {
						$f = 1;
						break;
					}
				}
				if ($f == 1) {
					$receiptNoErr = "Duplicate Receipt no.!";
					$errorCode = 1;
				}
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
		<a href="regular.html" class="previous">&laquo; Previous</a>
		<center>
			<div>
				<span>RVCE Transport Management</span>
			</div>
		</center>
	</div>
	</br></br>

	<div style="text-align: center;">
		<div class="container">
			<center><label>Vehicle Parking Registration</label></center>
			<p><span class="error">* required field.</span></p>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

				Name:<br> <input type="text" name="name" value="<?php echo $name; ?>" required>
				<span class="error">* <?php echo $nameErr; ?></span>
				<br><br>
				ID: <br> <input type="text" name="id" value="<?php echo $id; ?>" required>
				<span class="error">* <?php echo $idErr; ?></span>
				<br><br>
				E-mail:<br> <input type="text" name="email" value="<?php echo $email; ?>" required>
				<span class="error">* <?php echo $emailErr; ?></span>
				<br><br>
				Gender:<br>
				<input type="radio" name="gender" <?php if (isset($gender) && $gender == "female") echo "checked"; ?> value="female">Female
				<input type="radio" name="gender" <?php if (isset($gender) && $gender == "male") echo "checked"; ?> value="male">Male
				<br><br>
				Address:<br> <textarea name="comment" rows="5" cols="40"><?php echo $comment; ?></textarea>
				<br><br>
				Vehicle Type: <br><select id="vtype" name="vtype">
					<option value="2W">Two Wheeler</option>
					<option value="4W">Four Wheeler</option>

				</select>
				<br><br>
				Number of Vehicles:<br><select id="noOfVehicle" name="noOfVehicle">
					<option value="" selected>0</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>

				</select>
				<br><br>
				<div id="vehicleDetails" name="vehicleDetails">
				</div>
				Receipt No: <br><input type="number" name="receiptno" min=0 max=999999 value="<?php echo $receiptno; ?>" required>
				<span class="error">* <?php echo $receiptNoErr; ?></span>
				<br><br>
				<input type="submit" name="submit" value="Submit">
			</form>
		</div>


</body>

</html>
