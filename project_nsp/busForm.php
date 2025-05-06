<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
?>

<html>

<head>
	<link rel="stylesheet" href="navButton.css">
	<link rel="stylesheet" href="formStyles.css">

<body>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" />
	</script>
	<script>
		$(document).ready(function() {
			$("#route").change(function() {

				var ww = $("#route").val();

				if ($("#route").val() == "") {
					$("#stopname").html("")
				} else {
					$.ajax({
						type: "GET",
						url: "getStopNames.php",
						data: {
							'q': ww
						}
					}).done(function(msg) {
						$("#stopname").html(msg)
					});
				}
			});
		});
	</script>
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
		$sql = "SELECT RouteNo,RouteName FROM route";
		$result = mysqli_query($conn, $sql);

		while ($row = mysqli_fetch_assoc($result)) {
			$options = $options . "<option value = " . $row['RouteNo'] . ">" . $row['RouteName'] . "</option>";
		}
		// define variables and set to empty values
		$nameErr = $emailErr = $genderErr = $idErr = $routeErr = $receiptNoErr = "";
		$name = $email = $gender = $comment = $id = $route = "";
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
				$sql = "SELECT ID FROM member";
				$result = mysqli_query($conn, $sql);
				$f = 0;
				if (mysqli_num_rows($result) > 0) {
					// output data of each row
					while ($row = mysqli_fetch_assoc($result)) {
						if ($row["ID"] == $_POST["id"]) {
							$f = 1;
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
				$sql = "SELECT MemberID FROM enrollsfor";
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
			if (!empty($_POST["route"]) && $errorCode == 0 && !empty($_POST["stopname"])) {

				$route = $_POST["route"];
				$receiptno = intval($_POST["receiptno"]);
				$flag = 0;
				$var = mysqli_real_escape_string($conn, $_POST["route"]);
				$sql = "SELECT RouteNo,RouteName,Date FROM route WHERE RouteNo=$route";
				$result = mysqli_query($conn, $sql);
				$row = mysqli_fetch_assoc($result);
				$routeNo = intval($row["RouteNo"]);
				$routeName = $row["RouteName"];
				$routeSetDate = mysqli_real_escape_string($conn, $row["Date"]);
				$sql = "SELECT StopName,StopNo FROM routedetails WHERE StopNo=" . $_POST["stopname"] . " AND RouteNo = " . $_POST["route"] . " AND Date = '" . $row["Date"] . "'";
				$result = mysqli_query($conn, $sql);
				$row = mysqli_fetch_assoc($result);
				$stopName = $row['StopName'];
				$stopNo = $_POST["stopname"];
				$sql = "SELECT BusNo FROM bus WHERE RouteNo=$route";
				$result = mysqli_query($conn, $sql);
				while ($row = mysqli_fetch_assoc($result)) {
					$busNo = mysqli_real_escape_string($conn, $row["BusNo"]);
					$sqlinner = "SELECT MAX(SeatNo) FROM enrollsfor WHERE busno='$busNo'";
					$resultinner = mysqli_query($conn, $sqlinner);
					$rowSeatNo = mysqli_fetch_assoc($resultinner);
					$sqlagain = "SELECT NoOfSeats FROM bus WHERE BusNo='$busNo'";
					$rowagain = mysqli_query($conn, $sqlagain);
					$rowNoOfSeats = mysqli_fetch_assoc($rowagain);
					echo "<script>alert('Hello');</script>";
					if ($rowNoOfSeats["NoOfSeats"] > $rowSeatNo["MAX(SeatNo)"]) {
						$seatno = $rowSeatNo["MAX(SeatNo)"] + 1;
						$busNumberAllotted = $busNo;
						$memberID = mysqli_real_escape_string($conn, $_POST["id"]);

						$sql = "INSERT INTO enrollsfor (memberid, routeno,routesetdate,busno,seatno, receiptno,busamount,enrollmentdate,stopno)
					  VALUES ('$memberID', $routeNo , '$routeSetDate','$busNo',$seatno, $receiptno,22000,'" . date('y-m-d') . "'," . $stopNo . ")";
						if (!mysqli_query($conn, $sql)) {

							echo "Error: " . $sql . "<br>" . mysqli_error($conn);
							$errorCode = 1;
						}
						$flag = 1;
						if ($errorCode == 0) {
							$_SESSION["name"] = $_POST["name"];
							$_SESSION["id"] = $_POST["id"];
							$_SESSION["busNo"] = $busNo;
							$_SESSION["seatNo"] = $seatno;
							$_SESSION["routeName"] = $routeName;
							$_SESSION['stopName'] = $stopName;
							header('location: busPass.php');
							exit;
						}

						break;
					}
				}
				if ($flag == 0)
					print("Sorry! Buses unavailable for this route!");
			}
			if (empty($_POST["comment"])) {
				$comment = "";
			} else {
				$comment = test_input($_POST["comment"]);
			}

			if (empty($_POST["gender"])) {
			} else {
				$gender = test_input($_POST["gender"]);
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
					<span>Poornima Transport Management</span>
				</div>
			</center>
		</div>
		</br></br>
		<div style="text-align: center;">
			<div class="container">
				<center><label>College Bus Registration</label></center>
				<p><span class="error">* required field.</span></p>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

					Name:<br> <input type="text" name="name" value="<?php echo $name; ?>" required>
					<span class="error">* <?php echo $nameErr; ?></span>
					<br><br>
					ID: <br> <input type="text" name="id" value="<?php echo $id; ?>" required>
					<span class="error"><?php echo $idErr; ?></span>
					<br><br>
					Gender:<br>
					<input type="radio" name="gender" <?php if (isset($gender) && $gender == "female") echo "checked"; ?> value="female">Female
					<input type="radio" name="gender" <?php if (isset($gender) && $gender == "male") echo "checked"; ?> value="male">Male
					<span class="error">* <?php echo $genderErr; ?></span>
					<br><br>
					E-mail:<br> <input type="text" name="email" value="<?php echo $email; ?>" required>
					<span class="error">* <?php echo $emailErr; ?></span>
					<br><br>
					Address:<br> <textarea name="comment" rows="5" cols="40"><?php echo $comment; ?></textarea>
					<br><br>
					Route: <br><select id="route" name="route" required><?php echo $options; ?></select>
					<span class="error">* <?php echo $routeErr; ?></span>
					<br><br>
					Stop Name:<br><select id="stopname" name="stopname" required></select>
					<br><br>

					Receipt No: <br><input type="number" name="receiptno" min=0 value="<?php echo $receiptno; ?>" required>
					<span class="error">* <?php echo $receiptNoErr; ?></span>
					<br><br>
					<input type="submit" name="submit" value="Submit" id="submit">
				</form>
			</div>
		</div>

	</body>

</html>
