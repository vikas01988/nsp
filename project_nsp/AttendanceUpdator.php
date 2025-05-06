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
		$busno = $_SESSION['BusNo'];
		$sql = "SELECT ID, Name from member WHERE ID in (SELECT MemberID from enrollsfor WHERE BusNo='$busno')";
		$result = mysqli_query($conn, $sql);
		$date = date('y-m-d');
		$query = "";
		while($row = mysqli_fetch_assoc($result))
		{
			$selected = $_POST[$row['ID']];
			$query .= "INSERT INTO memberattendance values('".$row['ID']."','".$date."','".$selected."');";
		}
		if ($conn->multi_query($query) === TRUE) 
		{
			echo "<script type='text/javascript'>alert('Attendance has been updated!');window.location.href='security.html';</script>";
		} else {
			$length = strlen("Duplicate");
			if (substr($conn->error, 0, $length) === "Duplicate")
			{
				echo "<script type='text/javascript'>alert('Attendance was already recorded!');window.location.href='security.html';</script>";
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