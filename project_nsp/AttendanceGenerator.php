<html>
	<head>
		<link rel="stylesheet" href="navButton.css">
		<link rel="stylesheet" href="AttendanceGenerator.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"/></script>
		<script>
		$(document).ready(function(){
			$("#route").change(function(){
				var ww = $( "#route" ).val();
				if($("#route").val()=="")
				{
					$("#BusNo").html("")
				}
				else{
				$.ajax({
					type: "GET",
					url: "getBusNo.php",
					data: {'q': ww}
				}).done(function( msg ) {
					$("#BusNo").html(msg)
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
		$password = "root";
		$dbname = "transport";
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		if (!$conn) {
			die("Connection failed: " . mysqli_connect_error());
		}

		$options ="<option selected value = ''>Select a Route Name</option>";
		$sql = "SELECT RouteName,RouteNo FROM route";
		$result = mysqli_query($conn, $sql);

		while($row = mysqli_fetch_assoc($result)) {
			$options = $options."<option value = ".$row['RouteNo'].">".$row['RouteName']."</option>";
		}
		mysqli_close($conn);
		?>
		<div class="body"></div>
		<div class="header">
			<a href="security.html" class="previous">&laquo; Previous</a>
			<center>
				<div>
					<span>RVCE Transport Management</span>
				</div>
			</center>
		</div>
		</br></br>
		<div class="module">
			<label style="background: rgb(77,77,77);color: #fff;width:auto;">Attendance Generator</label></br></br></br>
			<form method="post" action="AttendanceList.php"> 
				<label>Route Name:</label>
				<select id="route"  name="route" required><?php echo $options; ?></select>
				</br></br>
				<label>Bus No:</label><select id="BusNo" name="BusNo" required></select>
				</br></br>
				<input type="submit" name="submit" class="submit" value="Generate Attendance List">
			</form>
		</div>
	</body>
</html>
