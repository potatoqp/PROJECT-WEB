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
    <title>Inventory</title>
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
        #tableContainer {
            background-color: #1f2933;
            background-color: rgba(0, 0, 0, 0.3);
        }
        #filterSection {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            background-color: #1f2933;
            background-color: rgba(0, 0, 0, 0.3);
            color: white;
            margin-bottom:1%;
            padding-bottom:1%;
        }
        #categoryFilters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        #filterButton {
            display: flex;
            align-self: flex-start;
            gap: 10px;
        }
        #filterIndent{ margin-left:15px; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="usertype" onclick="window.location.href='loggedadmin.php'">Go back to the dashboard</div>
    </div>
    <h1>Inventory</h1>
    <div id="filterSection">
        <div id="filterIndent">
            <h2>Filter by Category</h2>
            <div id="filterButton">
                <div id="categoryFilters"></div>
                <button onclick="applyFilter()">Apply Filter</button>
            </div>
        </div>
    </div>
    <div id="tableContainer">
        <table id="inventoryTable">
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Category</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>

    <script>
        let allItems = []; //store all items here
        let uniqueCategories = new Set();
        let pollingInterval;

        async function loadItems() {
            try {
                const response = await fetch('get_items.php');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                allItems = await response.json(); 
                extractCategories(allItems); //extract categories from items
                applyFilter(); 
            } catch (error) {
                console.error('Error fetching items:', error);
            }
        }

        function extractCategories(items) {
            uniqueCategories.clear(); //clear previous categories
            items.forEach(item => uniqueCategories.add(item.category));
            populateCategoryFilters();
        }

        function populateCategoryFilters() {
            const categoryFilters = document.getElementById('categoryFilters');
            const savedState = getSavedCheckboxState();
            categoryFilters.innerHTML = Array.from(uniqueCategories).map(category => `
                <label>
                    <input type="checkbox" value="${category}" ${savedState[category] ? 'checked' : ''} /> ${category}
                </label>
            `).join('');
        }

        function displayItems(items) {
            const tableBody = document.querySelector('#inventoryTable tbody');
            tableBody.innerHTML = ''; //clear previous data

            items.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.category}</td>
                    <td>
                        <ul>
                            ${item.details.map(detail => `<li>${detail.name}: ${detail.value}</li>`).join('')}
                        </ul>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        function applyFilter() {
            const selectedCategories = Array.from(document.querySelectorAll('#categoryFilters input:checked')).map(cb => cb.value);
            const filteredItems = selectedCategories.length > 0 ? allItems.filter(item => selectedCategories.includes(item.category)) : allItems;
            displayItems(filteredItems);
        }

        function getSavedCheckboxState() {
            const state = {};
            document.querySelectorAll('#categoryFilters input').forEach(input => {
                state[input.value] = input.checked;
            });
            return state;
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadItems();
            Polling();
        });

        function Polling() {
            loadItems(); 
            pollingInterval = setInterval(loadItems, 5000); //5 sec
        }
    </script>
</body>
</html>






                  







