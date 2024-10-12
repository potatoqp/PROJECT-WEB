<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['user_role'] !== 'civilian') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/MY_stylesheet.css">
    <link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
    <title>View Announcements</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        #container {
            background-color: #1f2933;
            background-color: rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="usertype" onclick="window.location.href='loggedcivilian.php'">Go back to the dashboard</div>
    </div>
    <h2>View Announcements</h2>
    <div id = "container">
        <table id="announcementsTable">
            <thead>
                <tr>
                    <th>Announcement ID</th>
                    <th>Description</th>
                    <th>Items Needed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <script>
    async function loadAnnouncements() {
        try {
            const response = await fetch('get_announcements.php');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const announcements = await response.json();
            
            const tableBody = document.querySelector('#announcementsTable tbody');
            tableBody.innerHTML = '';

            announcements.forEach(announcement => {
                const row = document.createElement('tr');
                
                const itemsList = announcement.items.map(item => `
                    <li>${item.name}: ${item.needed_quantity}</li>
                `).join('');
                
                row.innerHTML = `
                    <td>${announcement.id}</td>
                    <td>${announcement.description}</td>
                    <td>
                        <ul>${itemsList}</ul>
                    </td>
                    <td>
                        <button onclick="makeOffer(${announcement.id})">Make an Offer</button>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
        } catch (error) {
            console.error('Error fetching announcements:', error);
        }
    }

    async function makeOffer(announcementId) {
        try {
            const response = await fetch('make_offer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ announcement_id: announcementId })
            });
            const result = await response.json();
            if (result.success) {
                alert('Offer made successfully');
            } else {
                alert('Failed to make an offer: ' + result.message);
            }
        } catch (error) {
            console.error('Error making an offer:', error);
            alert('An error occurred while making the offer');
        }
    }

    function Polling() {
        loadAnnouncements(); 
        setInterval(loadAnnouncements, 5000); //5 sec
    }

    document.addEventListener('DOMContentLoaded', Polling);
    </script>
</body>
</html>

