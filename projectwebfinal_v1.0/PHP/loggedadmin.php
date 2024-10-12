<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
}
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="../CSS/MY_stylesheet.css">
    <link rel="stylesheet" href="../CSS/MY_stylesheetNavbarAdmin.css">
    <title>Welcome Admin!</title>

</head>
<body>
	<div class="navbar">
	
	  <div class="usertype">Welcome Admin : <?php echo $_SESSION["user"]?></div>
	  <a href="edit_items.php">Manage Items</a>
	  <a href="add_categories_items.php">Add Categories/Items</a>
	  <a href="admin_map.php">Map</a>
	  <a href="add_announcment.php"> Make Announcements</a>
	  <div class="dropdown-phone">
		  <button class="dropbtn"> 
			  <i class="fa fa-bars" aria-hidden="true"></i>
		  </button>
			<div class="dropdown-content">
			  <a href="edit_items.php">Manage Items</a>
			  <a href="add_categories_items.php">Add Categories/Items</a>
			  <a href="admin_map.php">Map</a>
			  <a href="add_announcment.php">Make Announcements</a>
			  <a href="inventory.php">Show Inventory Status</a>
			  <a href="cargo_inventory.php">Show Cargo Status</a>
			  <a href="show_charts.php">Service Statistics</a>
			  <a href="registervolunteer.php">Create Volunteer Account</a>
			  <a href="populate_db.php">Populate db with json</a>
			  <a href="logout.php">Logout</a>
			</div>
	  </div>
	  <div class="dropdown-tablet">
		  <button class="dropbtn"> 
			  <i class="fa fa-bars" aria-hidden="true"></i>
		  </button>
			<div class="dropdown-content">
			  <a href="edit_items.php">Manage Items</a>
			  <a href="add_categories_items.php">Add Categories/Items</a>
			  <a href="#">Map</a>
			  <a href="add_announcment.php">Make Announcements</a>
			</div>
	  </div>
	  <div class="navbar-right">
		  <div class="dropdown">
			<button class="dropbtn">Options 
			  <i class="fa fa-caret-down"></i>
			</button>
			<div class="dropdown-content">
			  <a href="inventory.php">Show Inventory Status</a>
			  <a href="cargo_inventory.php">Show Cargo Status</a>
			  <a href="show_charts.php">Service Statistics</a>
			  <a href="registervolunteer.php">Create Volunteer Account</a>
			  <a href="populate_db.php">Populate db with json</a>
			</div>
		  </div>
		  <a class="navbar-right" href="logout.php">Logout</a>
	  </div>
	</div>
	
</body>
</html>