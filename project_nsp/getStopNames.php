<!DOCTYPE html>
<html>
<body>

	<?php

	$q = $_GET['q'];
	$servername = "localhost";
	$username = "root";
	$password = "root";
	$dbname = "transport";
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	$sql="SELECT StopName,StopNo FROM routedetails WHERE RouteNo=$q";
	//echo "<option selected value = ''>Select a Bus No</option>";
	$result = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($result)) {
		echo "<option value = ".$row['StopNo'].">".$row['StopName']."</option>";
	}
	mysqli_close($conn);
	?>
</body>
</html>
