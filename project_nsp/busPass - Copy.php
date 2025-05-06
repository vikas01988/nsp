<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="navButton.css">
	<script type='text/javascript'>
		  function showAlert() { 
			alert ("The bus pass will be lost!");
		  }
  </script>
<style>
@import url(http://fonts.googleapis.com/css?family=Exo:100,200,400);
@import url(http://fonts.googleapis.com/css?family=Source+Sans+Pro:700,400,300);
body{
	background: rgba(230,230,230,0.85);
}
.card {
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    transition: 0.3s;
    width: 40%;
    border-radius: 10px;
	margin: auto;
	background: rgba(255,255,255,0.85);
}

.card:hover {
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}

img {
    border-radius: 5px 5px 0 0;
}

.container {
    padding: 2px 16px;
}
.header{
	position: relative;
	padding: 20px;
	z-index: 2;
}

.header div{
	color: #000;
	font-family: 'Exo', sans-serif;
	font-size: 35px;
	font-weight:1000;
	
}

.header div span{
	color: #000 !important;
}
label{
	width: 300px;
	border-radius: 5px;
	background: rgb(77,77,77);
	color: #fff;
	font-family: 'Exo', sans-serif;
	font-size: 16px;
	font-weight: 800;
	padding: 10px;
}
</style>
</head>
<body>
<div class="header">
		<a href="regular.html" class="previous" onClick="showAlert()">&laquo; Previous</a>
		<center><div><span>RVCE Transport Management</span></div></center><br><br>
</div>
        <center><label>Bus Pass</label></center><br><br>
<?php session_start(); ?>
<div class="card">
  <img src="RVCE.jpg" alt="Avatar" style="width:100%">
  <div class="container">
    <p><b>Name</b> <?php echo $_SESSION["name"];?></p>
    <p><b>Member ID</b> <?php echo $_SESSION["id"]; ?><p> 
	<p><b>Route name</b> <?php echo $_SESSION["routeName"];?></p>
	<p><b>Stop name</b> <?php echo $_SESSION["stopName"];?></p>
	<p><b>Bus number</b> <?php echo $_SESSION["busNo"];?></p>
	<p><b>Seat number</b> <?php echo $_SESSION["seatNo"];?></p>
  </div>
</div>

</body>
</html> 
