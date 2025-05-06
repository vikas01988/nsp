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
	$sql="SELECT BusNo FROM bus WHERE RouteNo=".$q;
	$result = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($result)) {
	
		echo "<option value = ".$row['BusNo'].">".$row['BusNo']."</option>";
		
	}
	mysqli_close($conn);
	?>
