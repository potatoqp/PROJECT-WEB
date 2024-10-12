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

<html>
    <head>
        <meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
        <link rel="stylesheet" href="../CSS/MY_stylesheet.css">
		<link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
		<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
		<style>
			.usertype:hover { cursor: pointer; }
			input {
				background-color: #2e2e2e; 
				color: #ffffff; 
				border: 1px solid #444; 
				border-radius: 5px; 
				padding: 5px; 
				font-size: 16px; 
				outline: none; 
				transition: border-color 0.3s ease; 
			}
			
			input::placeholder {
				color: #bbbbbb; 
				opacity: 1; 
			}
			
			label {
				display: block; 
				margin-bottom: 0px; 
				color: #cccccc; 
				font-size: 14px; 
				font-weight: 600; 
				text-transform: uppercase; 
				letter-spacing: 1px; 
			}


			.tab {display: inline-block; padding: 0px 10px;}
			
			#map {position: absolute; width: 50%; height:65%;}
		</style>
    </head>

    <body>
	<div class="navbar">
		  <div class="usertype" onclick="window.location.href='loggedadmin.php'">Return to dashboard</div>
	</div>


	<h3 id = "registerNow">Make an account of a volunteer!!!!</h3>
	
		<div id = "regClass">
			<?php
			header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
			if (isset($_POST["submit"])) {
				$newUsername = $_POST["newusername"];
				$fullName = $_POST["fullname"];
				$telephone = $_POST["telephone"];
				$password = $_POST["newpassword"];
				$repeatPassword = $_POST["repeatpassword"];
				$latitude = $_POST["newlatitude"];
				$longitude = $_POST["newlongitude"];

				$errors = array();

				if (empty($newUsername) OR empty($fullName) OR empty($telephone) OR empty($password) OR empty($repeatPassword)){
					array_push($errors,"All fields are required");
				}else{

				if (!filter_var($telephone, FILTER_VALIDATE_INT)) {
					array_push($errors, "Telephone is invalid");
				}

				if($password!==$repeatPassword) {
					array_push($errors, "Password does not match");
				}
			}
				require_once "db.php";
				$sql = "SELECT * FROM users WHERE username = '$newUsername'";
				$result = mysqli_query($conn,$sql);
				$rowCount = mysqli_num_rows($result);
				if ($rowCount>0) {
					array_push($errors, "Username already exists!");
				}
				if(count($errors)>0) {
					foreach($errors as $error){
						echo "<div regClass= 'errormessage'>$error</div>";
					}
				}else{
					require_once "db.php";
					$sql = "INSERT INTO users (username,fullname,phone,password,latitude,longitude) VALUES (?, ?, ?, ?, ?, ?) ";
					$stmt = mysqli_stmt_init($conn);
					$prepare = mysqli_stmt_prepare($stmt, $sql);
					if($prepare) {
						mysqli_stmt_bind_param($stmt,"ssssss", $newUsername, $fullName, $telephone, $password, $latitude, $longitude);
						mysqli_stmt_execute($stmt);
						$userId = mysqli_insert_id($conn);
                        $sqlVolunteers = "INSERT INTO volunteers (user_id) VALUES (?)";
                        $stmtVolunteers = mysqli_stmt_init($conn);
                        if (mysqli_stmt_prepare($stmtVolunteers, $sqlVolunteers))
                        {
                            mysqli_stmt_bind_param($stmtVolunteers, "i", $userId);
                            mysqli_stmt_execute($stmtVolunteers);
                            mysqli_commit($conn);
                            echo "<div class='registersuccess'>Volunteer registered successfully.</div>";
                        }
					}else{
						die("Something went wrong");
					}
				}
			}
			
			?>
			<form action="registervolunteer.php" method="post">
				<div class = "tab">
					<br><br>
					<label for="newUsername">Username:</label>
					<input type="text" id="newUsername" name="newusername" placeholder="Enter username" minlength="5" maxlength="15">
					<br><br>
					<label for="fullName">Full Name:</label>
					<input type="text" id="fullName" name="fullname" placeholder="Enter Full Name">
					<br><br>
					<label for="telephone">Phone Number:</label>
					<input type="text" id="telephone" name="telephone" placeholder="Enter Phone Number">
					<br><br>
					<label for="newPassword">Password:</label>
					<input type="password" id="newPassword" name="newpassword" placeholder="Enter Password" minlength="7" maxlength="20">
					<br><br>
					<label for="repeatPassword">Repeat Password:</label>
					<input type="password" id="repeatPassword" name="repeatpassword" placeholder="Repeat Password" minlength="7" maxlength="20">
					<br><br>
					<label for="newlatitude">Latitude:</label>
					<input type="text" id="newlatitude" name="newlatitude" placeholder="Enter Using Map" readonly minlength="7" maxlength="20">
					<br><br>
					<label for="newlongitude">Longitude:</label>
					<input type="text" id="newlongitude" name="newlongitude" placeholder="Enter Using Map" readonly minlength="7" maxlength="20">
					<br><br>
				</div> 
				
				<div class = "tab" id="map"> 
					<script>
						var map = L.map('map').setView([38.28990,21.78859],18);
						L.tileLayer('https://api.maptiler.com/maps/openstreetmap/{z}/{x}/{y}.jpg?key=N48ri6j2LZgBtYKuH43O', 
						{ attributionControl: false, maxZoom: 17 }).addTo(map);
						
						var LeafIcon = L.Icon.extend({
							options: {
								iconSize:     [24, 40],
								iconAnchor:   [12, 40],
								popupAnchor:  [-3, -76]
							}
						});
						var greenIcon = new LeafIcon({iconUrl: '../markers/green_icon.png'});
						var redIcon = new LeafIcon({iconUrl: '../markers/red_icon.png'});

						//var selectedPoint;
						var markerBase = L.marker([38.28990,21.78859]).addTo(map).bindPopup('Base');
						
						//Civilian
						function createCivilian(e) {
							var clickedPoint = e.latlng;
							
							var markerCivilian = L.marker(clickedPoint,{
									icon: greenIcon,
									draggable: true,
									autoPan: true 
							}).addTo(map);
							
							
							markerCivilian.bindPopup('Lat: ' + markerCivilian.getLatLng().lat.toFixed(6) + ', Lng: ' + markerCivilian.getLatLng().lng.toFixed(6));
							
							//initial coordinates
							document.getElementById('newlatitude').value = clickedPoint.lat.toFixed(6);
							document.getElementById('newlongitude').value = clickedPoint.lng.toFixed(6);
							
							markerCivilian.on("dragend", function(e) {
								selectedPoint = e.target.getLatLng();
								console.log(selectedPoint.lat + " " + selectedPoint.lng);
								markerCivilian.setPopupContent('Lat: ' + selectedPoint.lat.toFixed(6) + ', Lng: ' + selectedPoint.lng.toFixed(6));
								
								//updated coordinates
								document.getElementById('newlatitude').value = selectedPoint.lat.toFixed(6);
								document.getElementById('newlongitude').value = selectedPoint.lng.toFixed(6);
							})
							//remove the click event listener after the first click
							map.off('click', createCivilian);
						}
						//add click event listener to the map to initiate marker creation only one time
						map.on('click', createCivilian);					
						
						var circle = L.circle([38.28990,21.78859],{
							color: 'red',
							fillColor: '#f03',
							fillOpacity: 0.1,
							radius: 250
						}).addTo(map);
					</script>
				</div>
				
				<div class = "registerbuttons">
					<input type="reset" name="cancel" id="refreshBox" value="Cancel">
					<input type="submit" name="submit" id="registerButton" value="Submit">
				</div>
				
				
			</form>
			
			
		
		</div>
		
	
    </body>
</html>