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
		$busno = $_POST['BusNo'];
		$servername = "localhost";
		$username = "root";
		$password = "root";
		$dbname = "transport";
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		if (!$conn) {
			die("Connection failed: " . mysqli_connect_error());
		}
		$sql = "SELECT ID, Name from member WHERE ID in (SELECT MemberID from enrollsfor WHERE BusNo='$busno')";
		$result = mysqli_query($conn, $sql);
		$_SESSION['BusNo'] = $busno;
		$table = "<table>
					<tr>
						<th>Sr No</th>
						<th>ID</th>
						<th>Name</th>
						<th>Date</th>
						<th>Attendance</th>
					</tr>";
		$count = 1;
		while($row = mysqli_fetch_assoc($result)) {
			$table = $table."<tr><td>".$count."</td>
								 <td>".$row['ID']."</td>
								 <td>".$row['Name']."</td>
								 <td align='center'>".date('d-m-y')."</td>
								 <td align='center'>Y:<input type='radio' value='Y' name='".$row['ID']."'/checked>&nbsp&nbsp&nbsp&nbsp
													N:<input type='radio' value='N' name='".$row['ID']."' />
								 </td>
							</tr>";
			$count+=1;
		}
		$total = '<label>Total number of students: '.$count."</label>";
		$table .= "</table>";
		mysqli_close($conn);
		?>
		<div class="listBody"></div>
		<div class="header">
			<a href="AttendanceGenerator.php" class="previous" onClick="showAlert()">&laquo; Previous</a>
			<center>
				<div>
					<span>Poornima Transport Management</span>
				</div>
			</center>
		</div>
		</br></br>
		<div class="module">
			<label style="font-size: 20px;">Attendance for <?php echo $busno?></label><br><br>
			<?php echo $total;?>
			<label id="noOfStds">Number of students absent: 0</label>
			<form method="post" action="AttendanceUpdator.php" id="AttendanceTabel"> 
				<?php echo $table; ?>
				</br></br>
				<input type="submit" id="submit" name="submit" value="Submit">
			</form>
		</div>
	</body>
</html>
