<html>
	<head>
		<link rel="stylesheet" href="navButton.css">
		<link rel="stylesheet" href="AttendanceList.css">
	</head>
	<body>
		<?php
		$select = $_POST['select'];
		$from = $_POST['from'];
		$where = $_POST['where'];
		$groupby = $_POST['groupby'];
		$having = $_POST['having'];
		$orderby = $_POST['orderby'];
		$query = '';
		echo "ohh hello>>".$select."<<";
		if(strcmp($select,"")){
			$query .= "SELECT ";
			$query .= $select;
		}
		if(strcmp($from,"")){
			$query .= " FROM ";
			$query .= $from;
		}
		if(strcmp($where,"")){
			$query .= " WHERE ";
			$query .= $where;
		}
		if(strcmp($groupby,"")){
			$query .= " GROUP BY ";
			$query .= $groupby;
		}
		if(strcmp($having,"")){
			$query .= " HAVING ";
			$query .= $having;
		}
		if(strcmp($orderby,"")){
			$query .= " ORDER BY ";
			$query .= $orderby;
		}
		if(!strcmp($query,"")){
			echo "<script type='text/javascript'>alert('All fields are empty!');window.location.href='queryForm.html';</script>";
		}
		$servername = "localhost";
		$username = "root";
		$password = "root";
		$dbname = "transport";
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		if (!$conn) {
			die("Connection failed: " . mysqli_connect_error());
		}
		$result = mysqli_query($conn, $query);
		if($result==false){
			 $err = mysqli_error($conn);
			 echo '<script type="text/javascript">alert("Error in the query:\n'.$err.'");window.location.href="queryForm.html";</script>';
			
		}
		else
		{
			$resultRows = "<table>";
		$first_row = true;
		while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
			if ($first_row) {
				$first_row = false;
				// Output header row from keys.
				$resultRows .= '<tr>';
				foreach($row as $key => $field) {
					$resultRows .= '<th>' . htmlspecialchars($key) . '</th>';
				}
				$resultRows .= '</tr>';
			}
			$resultRows .= '<tr>';
			foreach($row as $key => $field) {
				$resultRows .= '<td>' . htmlspecialchars($field) . '</td>';
			}
			$resultRows .= '</tr>';
		}
		$resultRows .= "</table>";
		}
		mysqli_close($conn);
		?>
		<div class="listBody"></div>
		<div class="header">
		<a href="queryForm.html" class="previous">&laquo; Previous</a>
		<center><div><span>Poornima Transport Management</span></div></center>
		</div>
		</br></br>
		<div class="module">
			<label style="font-size: 20px;">Query Result</label><br><br>
			<label>Query: <?php echo $query; ?></label>
			<div class="module">
				<?php echo $resultRows; ?>
				<form action="queryForm.html" method="post">
				<br><br><input type="submit" id="submit" name="submit" value="Okay">
				</form>
			</div>
		</div>
	</body>
</html>