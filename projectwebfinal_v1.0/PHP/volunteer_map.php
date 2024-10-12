<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	session_start();
	if (!isset($_SESSION["user"])) {
		header("Location: login.php");
		exit();
	}
	if ($_SESSION['user_role'] !== 'volunteer') {
		header("Location: login.php");
		exit();
	}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Map</title>
    <link rel="stylesheet" href="../CSS/MY_stylesheet.css">
    <link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
		.leaflet-popup-content-wrapper, .leaflet-popup-tip {
			background-color: #000;
			color:white;
		}
		.leaflet-popup-content {
			width:auto !important; 
		}
		
        .usertype:hover { cursor: pointer; }
        .mapContainer {
            min-width: 400px;
            min-height: 90vh; 
            position: relative; 
        }

		#map {
			z-index:1;
			position: absolute;
			width: 100%;
			height: 100%;
		}
		
		
		.modal-container {
			position: fixed;
			display: none;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.4);
			z-index: 2;
		}

		
		.modal-content {
			position: relative;
			width: 100%;
			height: 100%;
			overflow-y: scroll;
			scrollbar-width: none;
		}

		
		.modal {
			background-color: #1f2933;
			position: absolute;
			top: 50%;
			left: 50%;
			max-width: fit-content;
			margin-left: auto;
			margin-right: auto;
			height: 65%;
			transform: translate(-50%, -50%);
			border: 1px solid #888;
			padding: 25px;
			box-sizing: border-box;
			overflow: hidden;
		}

		.modal-header {
			width: 100%;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		.modal-header h2 {
			margin: 0;
		}

		.modal-footer {
			width: 100%;
			display: flex;
			justify-content: flex-end;
			align-items: center;
		}

		.modal-body {
			width: 100%;
			overflow-x: auto;
		}

		#modalCargo { display: none; }
		#modalTasks { display: none; }

		#itemsTable {
			width: 100%;
			border-collapse: collapse;
		}

		#itemsTable th, #itemsTable td {
			padding: 4px 8px;
			text-align: left;
		}
		
		.edit-quantity-form {
			display: flex;
			align-items: center;
			max-width:fit-content;
		}

		.edit-quantity-form input {
			margin-right: 10px;
			max-width: 50px;
		}

		#tasksTableRequests,#tasksTableOffers {
			width: 100%;
	        max-width: fit-content;
			table-layout: auto; 
			border-collapse: collapse;
		}

		#tasksTableRequests th, #tasksTableRequests td,
		#tasksTableOffers th, #tasksTableOffers td {
			padding: 4px 8px;
			text-align: left;
		}

		#tasksTableRequests td, #tasksTableOffers td {
			white-space: nowrap;
		}


		.close {
			position:fixed;
			color: #aaa;
			top:2%;
			right:2%;
			font-size: 28px;
			font-weight: bold;
		}

		.close:hover,
		.close:focus {
			color:  rgba(0,0,0,0.4);
			text-decoration: none;
			cursor: pointer;
		}

		.unload-btn {padding: 5px; margin:1%;}

		#tasks-btn {
			text-decoration: none;
			color: rgba(255, 255, 255, 0.8);
			background: rgb(145, 92, 182);
			font-weight: normal;
			transition: all 0.2s ease-in-out;
		}
		#tasks-btn:hover {
			color: rgba(255, 255, 255, 1);
			box-shadow: 0 5px 15px rgba(145, 92, 182, .4);
		}
		
    </style>
</head>
<body onload=init()>
<div class="navbar">
    <div class="usertype" onclick="window.location.href='loggedvolunteer.php'">Return to login</div>
	<button id="tasks-btn" class="button">Show Tasks</button>
</div>
<div class="mapContainer tab">
	<div id="map"></div>
</div>

<!--modal Container -->
<div class="modal-container">
	<!--modal Class -->
	<div id="modalCargo" class="modal">
		<div class="modal-content">
			<div class="modal-header"> 
				<h2>Cargo Inventory</h2>
				<span class="close">&times;</span>
			</div>
			<div class="modal-body">
				<table id="itemsTable">
					<thead>
					<tr>
						<th>Item ID</th>
						<th>Item Name</th>
						<th>Category</th>
						<th>Available <br> Quantity</th>
						<th>Cargo Quantity</th>
						<th style="padding-left:1%;">Details</th>
					</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
			<button id="unload-btn" class="unload-btn">Unload</button>
			</div>
		</div>
	</div>

	<div id="modalTasks" class="modal">
		<div class="modal-content">
			<div class="modal-header"> 
				<h2>Tasks</h2>
				<span class="close">&times;</span>
			</div>
			<div class="modal-body">
				<table id="tasksTableRequests">
					<thead><h3><u>Requests</u></h3>
						<tr>
							<th>Task ID</th>
							<th>Full Name</th>
							<th>Phone</th>
							<th>Request ID</th>
							<th>Offer ID</th>
							<th>Date Made</th>
							<th>Status</th>
							<th>Item</th>
							<th>People</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
				<table id="tasksTableOffers">
					<thead><h3><u>Offers</u></h3>
						<tr>
							<th>Task ID</th>
							<th>Full Name</th>
							<th>Phone</th>
							<th>Request ID</th>
							<th>Offer ID</th>
							<th>Date Made</th>
							<th>Status</th>
							<th>Item</th>
							<th>Needed Quantity</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				
			</div>
		</div>
	</div>
</div>


<script>
	var map = L.map('map').setView([38.28990, 21.78859], 15);
	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution: 'Â© OpenStreetMap'
	}).addTo(map);

	var myIcon = L.Icon.extend({
		options: { 
			iconSize: [40, 60], 
			iconAnchor: [20, 60],
			popupAnchor:  [0, -55]
		}
	});
    //our colours
	var greenIcon = new myIcon({iconUrl: '../markers/green_icon.png'}),
	greyIcon = new myIcon({iconUrl: '../markers/grey_icon.png'})
	redIcon = new myIcon({iconUrl: '../markers/red_icon.png'}),
	orangeIcon = new myIcon({iconUrl: '../markers/orange_icon.png'}),
	blueIcon = new myIcon({iconUrl: '../markers/blue_icon.png'}),
	blackIcon = new myIcon({iconUrl: '../markers/black_icon.png'}),
	goldIcon = new myIcon({iconUrl: '../markers/gold_icon.png'}),
	violetIcon = new myIcon({iconUrl: '../markers/violet_icon.png'});

//pending - ongoing civilians and logged in volunteer
	async function loadUserData() {
		try {
			const response = await fetch('get_user_coords_vol_map.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const result = await response.json();
			
		return result;	
		} catch (error) {
			console.error('Error fetching data:', error);
			return null;
		}
	}

	var volunteerMarker;
	
	async function init() {
		loadUserData()
        .then(userData => {
            if (userData) {
                const volunteer = userData.volunteer;
                const civilians = userData.civilians;

               
				
				var baseMarker;
				var baseLat;
				var baseLng;
				var selectedPoint;
				fetch('get_base_coords.php')
				.then(response => response.json())
				.then(data => {
					if (data.status === 'success') {
						baseLat = data.latitude;
						baseLng = data.longitude;
						baseMarker = L.marker([baseLat, baseLng]).addTo(map)
						.bindPopup('Base')
						.openPopup();

						var circle = L.circle([baseLat,baseLng],{
							color: 'red',
							fillColor: '#f03',
							fillOpacity: 0.1,
							radius: 100
						}).addTo(map);

						volunteerMarker = L.marker([volunteer.lat, volunteer.lng], {icon: redIcon, draggable: true}).addTo(map)
						.bindPopup();
						
						var distance = baseMarker.getLatLng().distanceTo(volunteerMarker.getLatLng());
						loadCargoItemsPopup(volunteerMarker, distance);

						volunteerMarker.on("dragend", function(e) {
							selectedPoint = e.target.getLatLng();
							volunteerMarker.setPopupContent('Lat: ' + selectedPoint.lat.toFixed(6) + ', Lng: ' + selectedPoint.lng.toFixed(6));
							
							distance = baseMarker.getLatLng().distanceTo(selectedPoint);
							loadCargoItemsPopup(volunteerMarker, distance);
							updateCompleteButtonTaskState();
						});
						//drag events to the volunteer marker
						volunteerMarker.on('drag', function() {
							updateVolunteerLines(); //refresh all lines when the volunteer is dragged
						});

					}
				}).catch(error => console.error('Error fetching base coordinates:', error));

				function loadCargoItemsPopup(volunteerMarker, distance) {
					if (distance <= 100) {
						const button = document.createElement("button");
						button.style = "overflow: hidden; white-space: nowrap;"
						button.innerHTML = "Load Items";
						
						button.onclick = function() {
							loadCargoItems();
							
							document.querySelector('.modal-container').style.display = "block";
							document.getElementById('modalCargo').style.display = "block";
						};
						
						volunteerMarker.setPopupContent(button);
						volunteerMarker.openPopup();
					} else {
						//set the popup content to the coordinates if not within the distance
						volunteerMarker.setPopupContent('Lat: ' + volunteerMarker.getLatLng().lat.toFixed(6) + ', Lng: ' + volunteerMarker.getLatLng().lng.toFixed(6));
					}
				}

				//add civilian markers associated with logged volunteer and set up their popups
				fetch('get_session_data.php')
					.then(response => response.json())
					.then(vol => {
					addCivilianMarkers(vol.userId, civilians);
					checkOngoingTasks();
				}).catch(error => console.error('Error fetching session data:', error));

			} else {
                console.log('No user data available');
            }
        })
        .catch(error => {
            console.error('Error fetching or displaying user data:', error);
        });
		
	}

	//close all modals if either close button is clicked
	Array.from(document.getElementsByClassName('close')).forEach(button => {
		button.addEventListener('click', function() {
			document.querySelector('.modal-container').style.display = "none";
			Array.from(document.getElementsByClassName('modal')).forEach(function(modal) {
				modal.style.display = 'none';
			});
		});
	});

	//close modals if clicked outside of them
	window.onclick = function(event) {
		if (event.target.classList.contains('modal-container')) {
			document.querySelector('.modal-container').style.display = "none";
			Array.from(document.getElementsByClassName('modal')).forEach(function(modal) {
				modal.style.display = 'none';
			});
		}
	};

	// store leaflet markers by civilian ID
	const markers = [];

	async function addCivilianMarkers(volunteerId, civilians) {
		for (let civilian of civilians) {
			//marker for each civilian
			const civilianMarker = L.marker([civilian.lat, civilian.lng], {icon: greyIcon}).addTo(map);
			civilianMarker.bindPopup('Loading...');

			markers[civilian.user_id] = civilianMarker;
			try {
				//get_popup_civilian_req_off.php
				const data = await fetchRequestsOffers(civilian.user_id);
				let content = reqOffPopupTable(volunteerId, civilian.user_id, data);

				civilianMarker.setPopupContent(content || 'No offers/requests available');

			} catch (error) {
				console.error('Error fetching data for civilian ID:', civilian.user_id, error);
				civilianMarker.setPopupContent('Error loading data.');
			}
		}
		checkOngoingTasks();
	}

	async function loadTaskStatus() {
		try {
			const response = await fetch('check_task_status.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const result = await response.json();
			
		return result;	
		} catch (error) {
			console.error('Error fetching data:', error);
			return null;
		}
	}
	


	//controls the civilian marker colors, lines, tasks status and date
	function checkOngoingTasks(){
		loadTaskStatus()
        .then(statusData => {
            // Check if statusData is not null
            if (statusData) {
                // Access the properties of userData
				var civilianMarker;
				statusData.civilians.forEach(civilian => {
					console.log(civilian.status);
					civilianMarker = markers[civilian.user_id];
					if (civilianMarker){
						if (civilian.request_ids && civilian.status === 'pending'){
							civilianMarker.setIcon(orangeIcon);
							removeLines(civilian.user_id);
							console.log('setMarker to orange');
						} else if (civilian.request_ids && civilian.status === 'ongoing'){
							civilianMarker.setIcon(blackIcon);
							drawLines(civilian);
							console.log('setMarker to black');
						} else if (civilian.offer_ids && civilian.status === 'pending'){
							civilianMarker.setIcon(goldIcon);
							removeLines(civilian.user_id);
							console.log('setMarker to gold');
						} else if (civilian.offer_ids && civilian.status === 'ongoing'){
							civilianMarker.setIcon(violetIcon);
							drawLines(civilian);
							console.log('setMarker to violet');
						}
						checkRemoveCivilianMarker(civilian);
					} else { console.log('civilianMarker Not Found'); }
				});
            } else {
                console.log('No user status data available');
            }
        })
        .catch(error => {
            console.error('Error fetching or processing tasks:', error);
        });
	}

	function checkRemoveCivilianMarker(civilian) {
		//check if the civilian is done and the marker exists for the given civilianId
		if (civilian.status == 'completed' && markers[civilian.user_id]) {
			map.removeLayer(markers[civilian.user_id]);  
			delete markers[civilian.user_id];  
			removeLines(civilian.user_id)
		}
	}


	var polylines = {};
	function drawLines(civilian) {
		//check if task is defined and has a volunteer_id
		if (civilian.volunteer_id) {
			var civilianMarker = markers[civilian.user_id];
			if (civilianMarker) {
				if (!polylines[civilian.user_id]) {
					//add the polyline to the map
					var latlngs = [
						volunteerMarker.getLatLng(),
						civilianMarker.getLatLng()
					];

					var polyline = L.polyline(latlngs, { color: 'blue' }).addTo(map);
					polylines[civilian.user_id] = polyline;

					//attach drag events for the polylines
					civilianMarker
						.on('dragstart', dragStartHandler)
						.on('drag', dragHandler)
						.on('dragend', dragEndHandler);
				}
			} else {
				console.error('Civilian marker not found for user_id:', civilian.user_id);
			}
		}
	}

	function removeLines(civilian_id) {
		if (civilian_id) {
			var polyline = polylines[civilian_id];
			
			if (polyline) {
				map.removeLayer(polyline);
				
				delete polylines[civilian_id];

			} else {
			}
		}
	}

	function updateVolunteerLines() {
		for (var userId in markers) {
			var polyline = polylines[userId];
			if (polyline) { 
				var latlngs = [
					volunteerMarker.getLatLng(),
					markers[userId].getLatLng()
				];
				polyline.setLatLngs(latlngs);
			}
		}
	}

	function dragStartHandler(e) {
		var polyline = polylines[this.user_id];
		if (polyline) {
			var latlngs = polyline.getLatLngs(),
				latlng = this.getLatLng();

			for (var i = 0; i < latlngs.length; i++) {
				if (latlng.equals(latlngs[i])) {
					this.polylineLatlng = i; //store index of polyline's latlng
				}
			}
		}
	}

	function dragHandler(e) {
		var polyline = polylines[this.user_id];
		if (polyline) {
			var latlngs = polyline.getLatLngs(),
				latlng = this.getLatLng();

			latlngs.splice(this.polylineLatlng, 1, latlng); //update polyline's latlng
			polyline.setLatLngs(latlngs); //refresh the polyline
		}
	}

	function dragEndHandler(e) {
		delete this.polylineLatlng; //clean up stored index
	}


	async function loadCargoItems() {
		try {
			const response = await fetch('get_cargo_items.php');
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			const items = await response.json();
			
			const tableBody = document.querySelector('#itemsTable tbody');
			tableBody.innerHTML = ''; 

			items.forEach(item => {
				const row = document.createElement('tr');
				
				const details = item.details.map(detail => `${detail.name}: ${detail.value}`).join(', ');

				row.innerHTML = `
					<td>${item.id}</td>
					<td>${item.name}</td>
					<td style="padding-left:1%;">${item.category}</td>
					<td style="padding-left:2%;">${item.available_quantity}</td>
					<td>
						<form class="edit-quantity-form">
							<input type="number" name="quantity" value="${item.cargo_quantity || 0}" min="0" max="${item.available_quantity + item.cargo_quantity}" required>
							<button type="submit" class="update-btn">Update</button>
						</form>
					</td>
					<td>${details}</td> <!-- Display item details -->
				`;

				tableBody.appendChild(row);

				const form = row.querySelector('.edit-quantity-form');
				form.addEventListener('submit', function(event) {
					event.preventDefault(); //prevent form submission
					fetch('get_session_data.php')
					.then(response => response.json())
        			.then(vol => {
						updateQuantity(event, vol.userId, item.id, item.available_quantity);
					}).catch(error => console.error('Error fetching session data:', error));
				});
			});
		} catch (error) {
			console.error('Error fetching items:', error);
		}
	}


	async function updateQuantity(event, userId, itemId, maxQuantity) {
		event.preventDefault();
		const form = event.target;
		const quantity = parseInt(form.quantity.value, 10);
		const volunteerId = userId;

		if (quantity > maxQuantity) {
			alert('Cannot select more than available quantity');
			return false;
		}
		
		try {
			const response = await fetch('update_cargo.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ itemId, quantity, volunteerId })
			});
			const result = await response.json();
			if (result.success) {
				alert('Cargo updated successfully');
				loadCargoItems(); //refresh the cargo items
			} else {
				alert('Error updating cargo');
			}
		} catch (error) {
			console.error('Error updating cargo:', error);
		}
	}

	async function unloadCargoItems(userId) {
		const volunteerId = userId;

		try {
			const response = await fetch('delete_cargo_items.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: new URLSearchParams({ volunteer_id: volunteerId })
			});

			const result = await response.json();
			if (result.success) {
				alert(result.message);
				loadCargoItems();//refresh the cargo items
			} else {
				alert(result.message);
			}
		} catch (error) {
			console.error('Error unloading cargo items:', error);
			alert('An error occurred while unloading the cargo items.');
		}
	}

	const unloadBtn = document.getElementById("unload-btn");
	unloadBtn.addEventListener('click', function() {
		event.preventDefault(); //prevent form submission
		fetch('get_session_data.php')
		.then(response => response.json())
		.then(data => {
			unloadCargoItems(data.userId);
		}).catch(error => console.error('Error fetching session data:', error));
	});

	async function fetchRequestsOffers(civilianId) {
		try {
			const response = await fetch('get_popup_civilian_req_off.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ civilianId })
			});
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return await response.json();
			
		} catch (error) {
			console.error('Error fetching data:', error);
		}
	}

	function reqOffPopupTable(volunteerId, civilianId, data) {
		const requestHeaders = ['Request ID', 'Full Name', 'Phone', 'Date Made', 'Date Accepted', 'Item Name', 'People', 'Status', 'Volunteer Username', 'Action'];
		const offerHeaders = ['Offer ID', 'Full Name', 'Phone', 'Status', 'Date Made', 'Date Accepted', 'Item Name', 'Needed Quantity', 'Item Details', 'Volunteer Username', 'Action'];

		let headers = [];
		let content = '';
		
		if (data.code === 0) { return content; }
		else if (data.code === 1) {
			headers = requestHeaders;
			content = data.requests
			.filter(request => request.status !== 'completed')
			.map(request => `
				<tr>
					<td>${request.request_id}</td>
					<td>${request.fullname}</td>
					<td>${request.phone}</td>
					<td>${formatDate(request.date_made)}</td>
					<td>${formatDate(request.date_accepted) || 'N/A'}</td>
					<td>${request.item_name}</td>
					<td>${request.people}</td>
					<td>${request.status}</td>
					<td>${request.volunteer_username || 'N/A'}</td>
					<td><button onclick="acceptTask(${volunteerId}, ${civilianId}, ${request.request_id}, 'request')">Accept Task</button></td>
				</tr>
			`).join('');
		} else if (data.code === 2) {
			headers = offerHeaders;
			content = data.offers
			.filter(offer => offer.status !== 'completed')
			.map(offer => {
            const itemNames = [];
            const neededQuantities = [];
            const itemDetails = [];

            offer.items.forEach(item => {
                itemNames.push(item.name);
                neededQuantities.push(item.needed_quantity);
                itemDetails.push(item.details.map(detail => `${detail.name}: ${detail.value}`).join(', '));
            });

            return `
                <tr>
                    <td>${offer.offer_id}</td>
                    <td>${offer.fullname}</td>
					<td>${offer.phone}</td>
					<td>${offer.status}</td>
					<td>${formatDate(offer.date_made)}</td>
                    <td>${formatDate(offer.date_accepted) || 'N/A'}</td>
                    <td>${itemNames.join('<br>')}</td>
                    <td>${neededQuantities.join('<br>')}</td>
                    <td>${itemDetails.join('<br>')}</td>
                    <td>${offer.volunteer_username || 'N/A'}</td>
					<td>
                        <button onclick="acceptTask(${volunteerId}, ${civilianId}, ${offer.offer_id}, 'offer')">Accept Task</button>
                    </td>
                </tr>
            `;
        }).join('');

		} else if(data.code === 3) {return 'Error: You cannot have multiple types of tasks';}

		return `
			<table border="1">
				<thead>
					<tr>${headers.map(header => `<th>${header}</th>`).join('')}</tr>
				</thead>
				<tbody>
					${content}
				</tbody>
			</table>
		`;
	}

	function acceptTask(volunteerId, civilianId, taskId, taskType) {
		fetch('update_tasks.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				volunteerId: volunteerId,
				taskId: taskId,
				taskType: taskType
			}),
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				alert('Task accepted successfully.');
				//get the marker associated with the civilian ID 
				const civilianMarker = markers[civilianId];
				if (civilianMarker) {
					//get_popup_civilian_req_off.php
					fetchRequestsOffers(civilianId)
					.then(updatedData => {
						let updatedContent = reqOffPopupTable(volunteerId, civilianId, updatedData);
						civilianMarker.setPopupContent(updatedContent || 'No offers/requests available');
						civilianMarker.openPopup();
					});
					checkOngoingTasks();
				}
			} else {
				alert('Error accepting task: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Error accepting task:', error);
			alert('Error accepting task. Please try again later.');
		});
	}

	function fetchTasks(){
		return fetch('get_tasks.php')
		.then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
		.catch(error => console.error('Error fetching tasks:', error));
	}
	
	function showTasks(){
		fetchTasks()
		.then(tasks => {
			populateTasksTable(tasks);
			document.querySelector('.modal-container').style.display = "block";
			document.getElementById('modalTasks').style.display = "block";
		}).catch(error => console.error('Error fetching tasks:', error));
	}

	const tasksBtn = document.getElementById("tasks-btn");
	tasksBtn.addEventListener('click', function() {
		event.preventDefault(); //prevent form submission
		showTasks();
	});

	function populateTasksTable(data) {
		const tableBodyRequests = document.getElementById('tasksTableRequests').getElementsByTagName('tbody')[0];
		const tableBodyOffers = document.getElementById('tasksTableOffers').getElementsByTagName('tbody')[0];

		tableBodyRequests.innerHTML = ''; //clear existing rows for requests
		tableBodyOffers.innerHTML = ''; //clear existing rows for offers

		//initialize flags to check if there are no tasks of each type
		let noRequests = true;
		let noOffers = true;

		data.requests.forEach(task => {
			const requestRow = document.createElement('tr');
			requestRow.innerHTML = `
				<td>${task.task_id}</td>
				<td>${task.fullname}</td>
				<td>${task.phone}</td>
				<td>${task.request_id || 'N/A'}</td>
				<td>${task.offer_id || 'N/A'}</td>
				<td>${formatDate(task.date_made)}</td>
				<td>${task.status}</td>
				<td>${task.item_name}</td>
				<td>${task.people || 'N/A'}</td>
				<td>
					<button class='complete-btn' task_status='${task.status}', task_id='${task.task_id}', task_user_id='${task.user_id}', task_volunteer_id='${task.volunteer_id}', task_item_id='${task.item_id}', task_quantity='${task.people}', task_offer_id='${task.offer_id}', task_request_id='${task.request_id}'>Complete</button>
					<button onclick="updateTaskStatus(${task.task_id}, ${task.user_id},  ${task.volunteer_id}, 'canceled')">Cancel</button>
				</td>
			`;
			tableBodyRequests.appendChild(requestRow);
			noRequests = false;
		});

		data.offers.forEach(task => {
			const itemIds = [];
			const itemNames = [];
			const neededQuantities = [];
			const itemDetails = [];

			task.items?.forEach(item => {
				itemIds.push(item.id);
				itemNames.push(item.name);
				neededQuantities.push(item.needed_quantity);
				itemDetails.push(item.details.map(detail => `${detail.name}: ${detail.value}`).join(', '));
			});
			
			const offerRow = document.createElement('tr');
			offerRow.innerHTML = `
				<td>${task.task_id}</td>
				<td>${task.fullname}</td>
				<td>${task.phone}</td>
				<td>${task.request_id || 'N/A'}</td>
				<td>${task.offer_id || 'N/A'}</td>
				<td>${formatDate(task.date_made)}</td>
				<td>${task.status}</td>
				<td>${itemNames.join('<br>')}</td>
				<td>${(neededQuantities ||'N/A').join('<br>')}</td>
				<td>
					<button class='complete-btn' task_status='${task.status}', task_id='${task.task_id}', task_user_id='${task.user_id}', task_volunteer_id='${task.volunteer_id}', task_item_id='${itemIds}', task_quantity='${neededQuantities}', task_offer_id='${task.offer_id}', task_request_id='${task.request_id}'>Complete</button>
					<button onclick="updateTaskStatus(${task.task_id}, ${task.user_id}, ${task.volunteer_id}, 'canceled')">Cancel</button>
				</td>
			`;
			tableBodyOffers.appendChild(offerRow);
			noOffers = false;
		});


		//no tasks are available
		if (noRequests) {
			const noRequestsRow = document.createElement('tr');
			noRequestsRow.innerHTML = `<td colspan="10">- No requests available</td>`; // Adjust colspan if needed
			tableBodyRequests.appendChild(noRequestsRow);
		}

		if (noOffers) {
			const noOffersRow = document.createElement('tr');
			noOffersRow.innerHTML = `<td colspan="10">- No offers available</td>`; // Adjust colspan if needed
			tableBodyOffers.appendChild(noOffersRow);
		}
		
		updateCompleteButtonTaskState();
	}
	function updateCompleteButtonTaskState() {
        //add event listeners to complete buttons in both tables
        const completeButtons = document.querySelectorAll('.complete-btn');
        completeButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                //retrieve the task data from the data attribute
                //variables for updateTaskStatus()
                const task_status = event.target.getAttribute('task_status');
                const task_id = event.target.getAttribute('task_id');
                const task_user_id = event.target.getAttribute('task_user_id'); 
                const task_volunteer_id = event.target.getAttribute('task_volunteer_id'); 
                
                //variables for update_cargo_completed()
                const task_item_id = event.target.getAttribute('task_item_id');
                const task_quantity = event.target.getAttribute('task_quantity');
                const task_offer_id = event.target.getAttribute('task_offer_id');
                const task_request_id = event.target.getAttribute('task_request_id');

                var civilianMarker = markers[task_user_id];  
                const distance = civilianMarker.getLatLng().distanceTo(volunteerMarker.getLatLng());

                console.log('task_status: ' + task_status);
                if (distance <= 50 && task_status === 'ongoing') {
                    update_cargo_completed(task_item_id, task_quantity, task_volunteer_id, task_offer_id, task_request_id, task_id, task_user_id);
                } else if (distance > 50){
                    alert('The distance between markers is greater than 50 meters. Task cannot be completed.');
                } else {
                    alert('Task has already been completed.');
                }
            });
        });
    }

	//refresh popup status and check if the line or marker needs to be removed 
	function updateTaskStatus(taskId, civilian_id, volunteer_id, newStatus) {
        fetch('update_task_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                task_id: taskId,
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
				showTasks();
				checkOngoingTasks();
				////get_popup_civilian_req_off.php
				fetchRequestsOffers(civilian_id)
					.then(updatedData => {
						var civilianMarker = markers[civilian_id];
						let updatedContent = reqOffPopupTable(volunteer_id, civilian_id, updatedData);
						civilianMarker.setPopupContent(updatedContent || 'No offers/requests available');
					});
			} else {
                alert('Failed to update task status.');
            }
        });
    }

	function update_cargo_completed(itemIds, quantities, volunteerId, offerId, requestId, task_id, task_user_id) {
		const data = {
			itemId: itemIds.split(',').map(id => parseInt(id.trim(), 10)),
			quantity: quantities.split(',').map(qty => parseInt(qty.trim(), 10)),
			volunteerId: parseInt(volunteerId, 10) || null, 
			offerId: parseInt(offerId, 10) || null,  
			requestId: parseInt(requestId, 10) || null  
		};

		fetch('update_cargo_completed.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify(data)
		})
		.then(response => response.text())
		.then(text => {
			try {
				const data = JSON.parse(text);

				if (data.success) {
					alert('Cargo items updated successfully.');
					updateTaskStatus(task_id, task_user_id, volunteerId, 'completed');
				} else {
					alert('Failed to update cargo items: ' + (data.message || 'Unknown error.'));
				}
			} catch (e) {
				console.error('Error parsing JSON:', e);
				console.error('Response text:', text);
				alert('Failed to parse server response.');
			}
		})
		.catch(error => {
			console.error('Network or unexpected error:', error);
			alert('An error occurred while updating cargo items.');
		});
	}


	function formatDate(dateString) {
		if (!dateString) return '';
		const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
		return new Date(dateString).toLocaleDateString('en-US', options);
	}



	

</script>

</body>
</html>
