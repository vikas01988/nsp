<html>
	<head>
		<link rel="stylesheet" href="navButton.css">
		<link rel="stylesheet" href="AttendanceGenerator.css">
	</head>
	<body>
		<?php
		$servername = "localhost";
		$username = "root";
		$password = "root";
		$dbname = "transport";
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		if (!$conn) {
			die("Connection failed: " . mysqli_connect_error());
		}

		$options ="<option selected value = ''>Select a Parking Area Name</option>";
		$sql = "SELECT ParkingAreaID,ParkingAreaName FROM parkinglot";
		$result = mysqli_query($conn, $sql);

		while($row = mysqli_fetch_assoc($result)) {
			$options = $options."<option value = ".$row['ParkingAreaID'].">".$row['ParkingAreaName']."</option>";
		}
		mysqli_close($conn);
		?>
		<div class="body"></div>
		<div class="header">
			<a href="security.html" class="previous">&laquo; Previous</a>
			<center>
				<div>
					<span>Poornima Transport Management</span>
				</div>
			</center>
		</div>
		</br></br>
		<div class="module">
			<label style="background: rgb(77,77,77);color: #fff;width:auto;">Attendance Generator</label></br></br></br>
			<form method="post" action="VehicleAttendanceList.php" id="attendanceGen"> 
				<label>Parking Area Name:</label>
				<select id="ParkingArea"  name="ParkingArea" required><?php echo $options; ?></select>
				</br></br>
				<input type="submit" class="submit" name="submit" value="Generate Attendance List"/>	
			</form>
		</div>
	</body>
</html>