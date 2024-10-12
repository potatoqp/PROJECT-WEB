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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/MY_stylesheet.css">
    <link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
    <title>Cargo Inventory</title>
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
        .nested-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .nested-table, .nested-th, .nested-td {
            border: 1px solid white;
        }
        .nested-th, .nested-td {
            padding: 8px;
            text-align: left;
        }
        .details {
            margin: 0;
            padding: 0;
            list-style-type: none;
        }
        .details li {
            margin: 0;
            padding: 0;
        }
        #container {
            background-color: #1f2933;
            background-color: rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="usertype" onclick="window.location.href='loggedadmin.php'">Go back to the dashboard</div>
    </div>
    <h1>Cargo Inventory</h1>
    <div id="container">
        <table id="cargoTable">
            <thead>
                <tr>
                    <th>Cargo ID</th>
                    <th>Volunteer Username</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>

    <script>
        let allCargos = []; // array for all the cargos 
        let pollingInterval;

        async function loadCargos() {
            try {
                const response = await fetch('get_cargo.php');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                allCargos = await response.json(); // store all the cargos
                displayCargos(allCargos);
            } catch (error) {
                console.error('Error fetching cargos:', error);
            }
        }

        function formatDetails(details) {
            return details.map(detail => `${detail.name}: ${detail.value}`).join(', ');
        }

        function displayCargos(cargos) {
            const tableBody = document.querySelector('#cargoTable tbody');
            tableBody.innerHTML = ''; // clear previous data

            cargos.forEach(cargo => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${cargo.cargo_id}</td>
                    <td>${cargo.volunteer_username}</td>
                    <td>
                        <table class="nested-table">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${cargo.items.map(item => `
                                    <tr>
                                        <td>${item.name}</td>
                                        <td>${item.quantity}</td>
                                        <td>
                                            ${item.details.length > 0 ? formatDetails(item.details) : 'No details available'}
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadCargos();
            Polling();
        });

        function Polling() {
            loadCargos();
            pollingInterval = setInterval(loadCargos, 5000); //5 seconds
        }
    </script>
</body>
</html>







