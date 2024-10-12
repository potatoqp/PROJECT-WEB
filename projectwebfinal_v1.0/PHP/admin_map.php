<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['user_role'] !== 'admin') {
	header("Location: login.php");
	exit();
}

require_once "db.php";

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Map</title>
    <link rel="stylesheet" href="../CSS/MY_stylesheet.css">
    <link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .usertype:hover { cursor: pointer; }
        #map { width: 100%; height: 100vh; }
		.leaflet-popup-content-wrapper, .leaflet-popup-tip {
			background-color: #000;
			color:white;
		}
		.leaflet-popup-content {
			width:auto !important; 
		}
    </style>
</head>
<body>
<div class="navbar">
    <div class="usertype" onclick="window.location.href='loggedadmin.php'">Return to login</div>
</div>
<div id="map"></div>
<script>

	

    var map = L.map('map').setView([38.28990, 21.78859], 18);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var myIcon = L.Icon.extend({
		options: { 
			iconSize: [40, 60], 
			iconAnchor: [20, 60],
			popupAnchor:  [0, -55]
		}
	});
    
	// our colours
	var greenIcon = new myIcon({iconUrl: '../markers/green_icon.png'}),
	greyIcon = new myIcon({iconUrl: '../markers/grey_icon.png'})
	redIcon = new myIcon({iconUrl: '../markers/red_icon.png'}),
	orangeIcon = new myIcon({iconUrl: '../markers/orange_icon.png'}),
	blueIcon = new myIcon({iconUrl: '../markers/blue_icon.png'}),
	blackIcon = new myIcon({iconUrl: '../markers/black_icon.png'}),
	goldIcon = new myIcon({iconUrl: '../markers/gold_icon.png'}),
	violetIcon = new myIcon({iconUrl: '../markers/violet_icon.png'});
	
	function loadBaseMarker(){
		fetch('get_base_coords.php')
		.then(response => response.json())
		.then(data => {
			if (data.status === 'success') {
				var baseLat = data.latitude;
				var baseLng = data.longitude;
				var baseMarker = L.marker([baseLat, baseLng], { draggable: true }).addTo(map)
					.bindPopup('Base')
					.openPopup();
				
				baseMarker.on('dragend', function(e) {
					var position = baseMarker.getLatLng();
					baseMarker.bindPopup(`
						Confirm change:
						<br><button class="confirm-btn" onclick="confirmPosition(map, ${position.lat}, ${position.lng})">Confirm</button>
					`).openPopup();
				});
			}
		}).catch(error => console.error('Error fetching base coordinates:', error));
	}
	loadBaseMarker();

    function confirmPosition(map, lat, lng) {
		fetch('update_base_coords.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: JSON.stringify({ lat, lng })
		})
		.then(response => response.json())
		.then(data => {
			if (data.status === 'success') {
				alert('Position updated successfully');
				map.closePopup();
			} else {
				alert('Failed to update position: ' + data.message);
			}
		})
		.catch(error => console.error('Error:', error));
	}
	
	const markers = []; // we will store markers by civilian id
	async function addCivilianMarkers() {
		try {
			const response = await fetch('get_civilian_admin_map.php');
			const civilians = await response.json();
			
			for (const civilian of civilians) {
				const civilianMarker = L.marker([civilian.lat, civilian.lng], { icon: greyIcon }).addTo(map)
					.bindPopup(civilian.username);
				
				markers[civilian.user_id] = civilianMarker;
				try {
					//get_popup_civilian_req_off.php
					const data = await fetchRequestsOffers(civilian.user_id);
					const content = createPopupTable(data);
					civilianMarker.setPopupContent(content || 'No offers/requests available');
				} catch (error) {
					console.error('Error fetching data for civilian ID:', civilian.user_id, error);
					civilianMarker.setPopupContent('Error loading data.');
				}
			}
		} catch (error) {
			console.error('Error fetching volunteer data:', error);
		}
		checkOngoingTasks()
	}
	addCivilianMarkers();

	let resolveVolunteersLoaded; //  store the resolve function for the promise

	function createVolunteersLoadedPromise() {
		return new Promise(resolve => {
			resolveVolunteersLoaded = resolve; // store the resolve function
		});
	}

	// create a promise and store its resolve function
	const volunteersLoadedPromise = createVolunteersLoadedPromise();

	const volunteers = {};
	async function loadVolunteers() {
		try {
			const response = await fetch('get_volunteer_admin_map.php');
			const volunteerData = await response.json();
			
			// populate the array with markers
			volunteerData.forEach(function(volunteer) {
				const volunteerMarker = L.marker([volunteer.lat, volunteer.lng], { icon: redIcon }).addTo(map)
					.bindPopup(volunteer.username);
				volunteers[volunteer.userId] = volunteerMarker;
			});
			
			// resolve the promise with the volunteers array
			if (resolveVolunteersLoaded) {
				resolveVolunteersLoaded(volunteers);
			}
		} catch (error) {
			console.error('Error fetching volunteer data:', error);
			return [];  
		}
	}
	loadVolunteers();

	function waitForVolunteers() {
		return volunteersLoadedPromise;
	}

	async function loadTaskStatus() {
		try {
			const response = await fetch('check_task_status_admin.php');
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
            if (statusData) {
				var civilianMarker;
				statusData.civilians.forEach(civilian => {
					civilianMarker = markers[civilian.user_id];
					if (civilianMarker){
						if (civilian.request_ids && civilian.status === 'pending'){
							civilianMarker.setIcon(orangeIcon);
							removeLines(civilian.user_id);
						} else if (civilian.request_ids && civilian.status === 'ongoing'){
							civilianMarker.setIcon(blackIcon);
							drawLines(civilian);
						} else if (civilian.offer_ids && civilian.status === 'pending'){
							civilianMarker.setIcon(goldIcon);
							removeLines(civilian.user_id);
						} else if (civilian.offer_ids && civilian.status === 'ongoing'){
							civilianMarker.setIcon(violetIcon);
							drawLines(civilian);
						}
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


	var polylines = {}; 
	function drawLines(civilian) {
		// make sure the volunteers data is loaded 
		waitForVolunteers().then(volunteers => {
			
			// split volunteer_ids into an array and trim any extra spaces
			const volunteerIds = civilian.volunteer_ids ? civilian.volunteer_ids.split(',').map(id => id.trim()) : [];

			//for every volunteer, find his civilians
			volunteerIds.forEach(volunteer_id => {
				const volunteerMarker = volunteers[volunteer_id];  
				const civilianMarker = markers[civilian.user_id];  

				if (volunteerMarker && civilianMarker) {
					// use a unique key for each polyline
					const polylineKey = `${civilian.user_id}-${volunteer_id}`;
					if (!polylines[polylineKey]) {
						//add the polyline to the map
						const latlngs = [
							volunteerMarker.getLatLng(),
							civilianMarker.getLatLng()
						];

						//add new line
						const polyline = L.polyline(latlngs, { color: 'blue' }).addTo(map);
						polylines[polylineKey] = polyline;  // Store reference with a unique key
					}
				} else {
					console.error(`Marker not found for volunteer_id: ${volunteer_id} or civilian_id: ${civilian.user_id}`);
				}
			});
		}).catch(error => {
			console.error('Error waiting for volunteers:', error);
		});
	}




	function removeLines(civilian_id) {
		//check if there's an existing polyline for this civilian
		if (civilian_id) {
			var polyline = polylines[civilian_id];
			
			if (polyline) {
				map.removeLayer(polyline);
				
				delete polylines[civilian_id];

				
			} else {
			}
		}
	}

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

	function createPopupTable(data) {
		const requestHeaders = ['Request ID', 'Full Name', 'Phone', 'Date Made', 'Date Accepted', 'Item Name', 'People', 'Status', 'Volunteer Username'];
		const offerHeaders = ['Offer ID', 'Full Name', 'Phone', 'Date Made', 'Date Accepted', 'Item Name', 'Item Details', 'Needed Quantity', 'Status', 'Volunteer Username'];

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
					<td>${formatDate(offer.date_made)}</td>
                    <td>${formatDate(offer.date_accepted) || 'N/A'}</td>
                    <td>${itemNames.join('<br>')}</td>
                    <td>${itemDetails.join('<br>')}</td>
					<td>${neededQuantities.join('<br>')}</td>
					<td>${offer.status}</td>
                    <td>${offer.volunteer_username || 'N/A'}</td>
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


	function formatDate(dateString) {
		if (!dateString) return '';
		const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
		return new Date(dateString).toLocaleDateString('en-US', options);
	}
</script>
</body>
</html>

