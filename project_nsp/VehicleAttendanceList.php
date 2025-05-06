<html>
	<head>
	
		<link rel="stylesheet" href="AttendanceList.css">
		<link rel="stylesheet" href="navButton.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"/></script>
		<script>
		$(document).ready(function(){
		var absent = 0;
		$('input:radio').change(function()
			{
				var value = $( this ).val();
				if(value=="N"){
					absent += 1;
				}
				else{
					absent -= 1;
				}
				$("#noOfStds").html("Number of students absent: "+absent);
			});	
		$("#submit").click(function(){
				alert("Number of students absent = "+absent);
			});
		});
		</script>
		<script type='text/javascript'>
		  function showAlert() { 
			alert ("Attendance will be lost");
		  }
  </script>
	</head>
	<body>
		<?php
		session_start();
		date_default_timezone_set('Asia/Kolkata');
		$ParkingAreaID = $_POST['ParkingArea'];
		$servername = "localhost";
		$username = "root";
		$password = "root";
		$dbname = "transport";
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		if (!$conn) {
			die("Connection failed: " . mysqli_connect_error());
		}
		$sql = "Select vehicledetails.VID,VNo from vehicledetails join 
				(Select VID from vehicle 
				Where ParkingAreaID = '$ParkingAreaID') as t1
				on vehicledetails.VID=t1.VID";
		$result = mysqli_query($conn, $sql);
		$_SESSION['ParkingAreaID'] = $ParkingAreaID;
		$table = "<table>
					<tr>
						<th>Sr No</th>
						<th>VID</th>
						<th>Vehicle No</th>
						<th>Date</th>
						<th>Attendance</th>
					</tr>";
		$count = 0;
		$vid = array();
		$dataComplete = 1;
		$vehicle = "";
		$secondHalf = "";
		while($row = mysqli_fetch_assoc($result)) {
			if(in_array($row['VID'],$vid))
			{
				$vehicle .= " / ".$row['VNo'];
				$dataComplete = 0;
			}
			else{
				if(!$dataComplete)
				{
					if(!strcmp($vehicle,""))
					{
						$table .= $secondHalf;
					}
					else{
						$table .= $vehicle . $secondHalf;
					}
				}
				$table = $table."<tr><td>".$count."</td>
							 <td>".$row['VID']."</td>
							 <td>".$row['VNo'];
				$vehicle = "";
				$secondHalf = "</td>
							 <td align='center'>".date('d-m-y')."</td>
							 <td align='center'>Y:<input type='radio' value='Y' name='".$row['VID']."'checked/>&nbsp&nbsp&nbsp&nbsp
												N:<input type='radio' value='N' name='".$row['VID']."' />
							 </td>
						</tr>";
				$dataComplete = 0;
				array_push($vid,$row['VID']);
				$count+=1;
			}
		}
		if(!strcmp($vehicle,""))
		{
			$table .= $secondHalf;
		}
		else{
			$table .= $vehicle . $secondHalf;
		}
		$total = "<label>Total number of students: ".$count."<label>";
		$table .= "</table>";
		$sql = "SELECT ParkingAreaName FROM parkinglot WHERE ParkingAreaID='$ParkingAreaID' limit 1";
		$result = mysqli_query($conn,$sql);
		$ParkingAreaName = mysqli_fetch_object($result);
		$ParkingAreaName = $ParkingAreaName->ParkingAreaName;
		mysqli_close($conn);
		?>
		<div class="listBody"></div>
		<div class="header">
			<a href="VehicleAttendanceGenerator.php" class="previous" onClick="showAlert()">&laquo; Previous</a>
			<center>
				<div>
					<span>RVCE Transport Management</span>
				</div>
			</center>
		</div>
		</br></br>
		<div class="module">
			<label style="font-size: 20px;">Attendance for <?php echo $ParkingAreaName?></label><br><br>
			<?php echo $total;?>
			<label id='noOfStds'>Number of students absent: 0</label>
			<form method="post" action="VehicleAttendanceUpdator.php" id="AttendanceTabel"> 
				<?php echo $table; ?>
				</br></br>
				<input type="submit" id="submit" name="submit" value="Submit">
			</form>
		</div>
	</body>
</html>
