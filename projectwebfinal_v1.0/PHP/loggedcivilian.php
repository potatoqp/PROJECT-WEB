<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}

if ($_SESSION['user_role'] !== 'civilian') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>

<html>
    <head>
        <title>Welcome Civilian!!!</title>
        <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="../CSS/MY_stylesheet.css">
		<link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
    </head>

    <body>
		
		<div class="navbar">
	
		  <div class="usertype">Welcome: <?php echo $_SESSION["user"]?></div>
		  <a href="make_request.php">Make Request</a>
		  <a href="show_requests.php">View Requests</a>
		  <a href="view_announcements.php">Announcements</a>
		  <a href="show_offers.php">View Offers</a>
		  
		  <div class="dropdown-phone">
			  <button class="dropbtn"> 
				  <i class="fa fa-bars" aria-hidden="true"></i>
			  </button>
				<div class="dropdown-content">
				  <a href="make_request.php">Make Request</a>
				  <a href="show_requests.php">View Requests</a>
				  <a href="view_announcements.php">Announcements</a>
				  <a href="show_offers.php">View Offers</a>
				  <a href="logout.php">Logout</a>
				</div>
		  </div>
		  <a class="navbar-right" href="logout.php">Logout</a>
		
    </body>
</html>