<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
?>

<html>

<head>
	<link rel="stylesheet" href="navButton.css">
	<link rel="stylesheet" href="formStyles.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" />
	</script>
	<script>
		$(document).ready(function() {
			$('#noofstops').change(function() {
				if ($("#noofstops").val() == "") {
					$("#stopDetails").html("");
				} else {
					var n = parseInt($("#noofstops").val(), 10);
					var s = '';
					for (var i = 0; i < n; i++) {
						s += "Stop " + (i + 1) + ":</br></br>";
						s += "Stop name: <br> <input type='text' name='stopname" + (i + 1) + "' id='stopname" + (i + 1) + "' required></br></br>";

					}

					$("#stopDetails").html(s);
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

	$errorCode = 0;
	$routeNameErr = $routeno = $routenoErr = $routeName = $routeNameErr = "";

	$options = "<option selected value = 0>0</option>";
	for ($i = 1; $i <= 20; $i++)
		$options = $options . "<option value = " . $i . ">" . $i . "</option>";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (!empty($_POST["routename"])) {
			$routeName = test_input($_POST["routename"]);
			if (!preg_match("/^[a-zA-Z ]*$/", $routeName)) {
				$routeNameErr = "Only letters and white space allowed";
				$errorCode = 1;
			}
		}

		if (!empty($_POST["noofstops"]) && $errorCode == 0) {
			$sql = "INSERT INTO route(routeno, date, routename, noofstops)
				VALUES(" . $_POST["routeno"] . ",'" . date('y-m-d') . "','" . $_POST["routename"] . "'," . $_POST["noofstops"] . ")";
			if (!mysqli_query($conn, $sql)) {
				echo "Error: " . $sql . "<br>" . mysqli_error($conn);
				$errorCode = 1;
			}

			if (!$errorCode) {
				for ($j = 1; $j <= $_POST["noofstops"]; $j++) {
					$stopno = $j;
					$stopname = $_POST['stopname' . $j];
					$sql = "INSERT INTO routedetails (routeno, date, stopno, stopname)
						VALUES (" . $_POST["routeno"] . ",'" . date('y-m-d') . "'," . $stopno . ",'" . $stopname . "')";
					if (!mysqli_query($conn, $sql)) {
						echo "Error: " . $sql . "<br>" . mysqli_error($conn);
						$errorCode = 1;
						break;
					}
				}
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
				<span>Poornima Transport Management</span>
			</div>
		</center>
	</div>
	</br></br>
	<div style="text-align: center;">
		<div class="container">
			<center><label>Route Details Update</label></center>
			<p><span class="error">* required field.</span></p>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
				Route Number: <br><input type="number" name="routeno" min=0 max=50 value="<?php echo $routeno; ?>" required>
				<span class="error">* <?php echo $routenoErr; ?></span>
				<br><br>
				Route name:<br> <input type="text" name="routename" value="<?php echo $routeName; ?>" required>
				<span class="error">* <?php echo $routeNameErr; ?></span>
				<br><br>
				No of stops: <br><select id="noofstops" name="noofstops" required><?php echo $options; ?></select>
				<br><br>
				<div id="stopDetails" name="stopDetails">
				</div>
				<input type="submit" name="submit" value="Submit">
			</form>
		</div>


</body>

</html>
