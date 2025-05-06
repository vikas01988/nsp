<html>

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
	$id = $_POST['uname'];
	$psw = $_POST['psw'];

	$adminID = '10N0001';
	$securityID = array('10N0002', '10N0003');

	if (!empty($id)) {
		$id = test_input($id);
		$sql = "SELECT ID FROM member";
		$result = mysqli_query($conn, $sql);
		$f = 0;
		if (mysqli_num_rows($result) > 0) {
			// output data of each row
			while ($row = mysqli_fetch_assoc($result)) {
				if ($row["ID"] == $id) {
					$f = 1;
					break;
				}
			}
			if ($f == 0) {
				echo "Invalid ID!";
				$errorCode = 1;
			}
		}
		if ($id != $psw) {
			echo "<script type='text/javascript'>alert('Wrong Password');window.location.href='Login.html';</script>";
			$errorCode = 1;
		}
		if (!$errorCode) {
			if ($id == $adminID)
				echo "<script>window.location.href='admin.html';</script>";

			elseif (in_array($id, $securityID))
				echo "<script>window.location.href='security.html';</script>";

			else
				echo "<script>window.location.href='regular.html';</script>";
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
</body>

</html>
