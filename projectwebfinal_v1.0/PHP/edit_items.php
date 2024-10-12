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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/MY_stylesheet.css">
    <link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
    <title>Edit Items</title>
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
        .edit-quantity-form {
            display: inline;
        }
        .update-btn {
            padding: 2px 8px;
            margin-left: 5px;
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
    <h1>Edit Item Quantities</h1>
    <div id="container">
        <table id="itemsTable">
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    <script>
    async function loadItems() {
        try {
            const response = await fetch('get_items.php');
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
                    <td>${item.category}</td>
                    <td>
                        <form class="edit-quantity-form" onsubmit="return updateQuantity(event, ${item.id})">
                            <input type="number" name="quantity" value="${item.quantity}" min="0" required>
                            <button type="submit" class="update-btn">Update</button>
                        </form>
                    </td>
                    <td>${details}</td>
                `;
                
                tableBody.appendChild(row);
            });
        } catch (error) {
            console.error('Error fetching items:', error);
        }
    }

    async function updateQuantity(event, itemId) {
        event.preventDefault(); //no redirect
        const form = event.target;
        const quantity = form.quantity.value;

        try {
            const response = await fetch('update_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({ item_id: itemId, quantity })
            });

            const result = await response.json();
            if (result.success) {
                alert(result.message);
                loadItems(); 
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error updating item:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', loadItems);
    </script>
</body>
</html>





