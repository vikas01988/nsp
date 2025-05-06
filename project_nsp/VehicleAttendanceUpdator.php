<html>
	<body>
		<?php
		session_start();
		date_default_timezone_set('Asia/Kolkata');
		$servername = "localhost";
		$username = "root";
		$password = "root";
		$dbname = "transport";
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		if (!$conn) {
			die("Connection failed: " . mysqli_connect_error());
		}
		$ParkingAreaID = $_SESSION['ParkingAreaID'];
		$sql = "Select vehicledetails.VID,VNo from vehicledetails join 
				(Select VID from vehicle 
				Where ParkingAreaID = '$ParkingAreaID') as t1
				on vehicledetails.VID=t1.VID";
		$result = mysqli_query($conn, $sql);
		$date = date('y-m-d');
		$query = "";
		while($row = mysqli_fetch_assoc($result))
		{
			$selected = $_POST[$row['VID']];
			$query .= "INSERT INTO vehicleattendance values('".$row['VID']."','".$date."','".$selected."');";
		}
		if ($conn->multi_query($query) === TRUE) 
		{
			echo "<script type='text/javascript'>alert('Attendance has been updated!');window.location.href='security.html';</script>";
		} else {
			$length = strlen("Duplicate");
			if (substr($conn->error, 0, $length) === "Duplicate")
			{
				echo "<script type='text/javascript'>alert('Attendance already recorded!');window.location.href='security.html';</script>";
			}
			else
			{
				echo "<script type='text/javascript'>alert('".$conn->error."');window.location.href='security.html';</script>";
			}
		}
		mysqli_close($conn);
		?>
	</body>
</html>
