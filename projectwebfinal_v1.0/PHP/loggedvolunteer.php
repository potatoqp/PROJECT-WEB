<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}
if ($_SESSION['user_role'] !== 'volunteer') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../CSS/MY_stylesheet.css">
	<link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
    <title>Welcome Volunteer!</title>

</head>
<body>
    <div class="navbar">
	
	  <div class="usertype">Welcome Volunteer : <?php echo $_SESSION["user"]?></div>
	  <a href="view_cargo.php">Cargo Management</a>
	  <a href="volunteer_map.php">Map</a>
	
	  
	  <div class="dropdown-phone">
		  <button class="dropbtn"> 
			  <i class="fa fa-bars" aria-hidden="true"></i>
		  </button>
			<div class="dropdown-content">
			  <a href="view_cargo.php">Cargo Management</a>
			  <a href="volunteer_map.php">Map</a>
			  <a href="logout.php">Logout</a>
			</div>
	  </div>
	  <a class="navbar-right" href="logout.php">Logout</a>
	</div>
</body>
</html>