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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/MY_stylesheet.css">
    <link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
    <title>View Cargo</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid white;
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
    </style>
</head>
<body>
    <div class="navbar">
        <div class="usertype" onclick="window.location.href='loggedvolunteer.php'">Go back to the dashboard</div>
    </div>
    <h2>View Cargo</h2>
    <table id="cargoTable">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Available Quantity</th>
                <th>Cargo Quantity</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <script>
    async function loadCargoData() {
        try {
            const response = await fetch('get_cargo_items.php');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const cargoItems = await response.json();
            
            const tableBody = document.querySelector('#cargoTable tbody');
            tableBody.innerHTML = '';

            cargoItems
                .filter(item => item.cargo_quantity > 0 )
                .forEach(item => {
                    const row = document.createElement('tr');
                    
                    const detailsList = item.details.map(detail => `
                        <li>${detail.name}: ${detail.value}</li>
                    `).join('');
                    
                    row.innerHTML = `
                        <td>${item.name}</td>
                        <td>${item.category}</td>
                        <td>${item.available_quantity}</td>
                        <td>${item.cargo_quantity}</td>
                        <td>
                            <ul>${detailsList}</ul>
                        </td>
                    `;
                    
                    tableBody.appendChild(row);
                });
        } catch (error) {
            console.error('Error fetching cargo data:', error);
        }
    }

    function Polling() {
        loadCargoData();
        setInterval(loadCargoData, 5000); //5 sec
    }

    document.addEventListener('DOMContentLoaded', Polling);
    </script>
</body>
</html>
