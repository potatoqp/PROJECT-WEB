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
    
    


require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $items = $_POST['items'];

    $stmt = $conn->prepare("INSERT INTO announcements (description) VALUES (?)");
    $stmt->bind_param("s", $description);
    $stmt->execute();
    $announcementId = $stmt->insert_id;

    if ($items) {
        $stmt = $conn->prepare("INSERT INTO announcement_items (announcement_id, item_id, needed_quantity) VALUES (?, ?, ?)");
        foreach ($items as $itemId => $neededQuantity) {
            $stmt->bind_param("iii", $announcementId, $itemId, $neededQuantity);
            $stmt->execute();
        }
        $stmt->close();
    }

    $conn->close();
    echo "Announcement and items successfully saved.";
} else {
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/MY_stylesheet.css">
    <link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
    <title>Create Announcement</title>
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
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            border: 1px solid #ccc;
            background-color: #1f2933;
            color: white;
            padding: 20px;
            z-index: 1000;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 500;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="usertype" onclick="window.location.href='loggedadmin.php'">Go back to the dashboard</div>
    </div>
    <h1>Create Announcement</h1>
    <form id="announcementForm" method="post" action="">
        <label>Description:</label><br>
        <textarea name="description" rows="4" cols="50" required></textarea><br><br>

        <h3>Items for Announcement</h3>
        <button type="button" onclick="showPopup()">Add Items</button>
        <table id="selectedItemsTable">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Needed Quantity</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <!-- items will be here -->
            </tbody>
        </table><br>

        <input type="submit" value="Create Announcement">
    </form>

    <div class="overlay" id="overlay" onclick="hidePopup()"></div>
    <div class="popup" id="itemPopup">
        <h3>Select Items</h3>
        <div id="itemsContainer">
            
        </div>
        <button onclick="hidePopup()">Close</button>
    </div>

    <script>
    
function showPopup() {
    document.getElementById('overlay').style.display = 'block';
    const itemPopup = document.getElementById('itemPopup');
    itemPopup.style.display = 'block';
    loadItems();

    adjustPopupHeight();
}


function hidePopup() {
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('itemPopup').style.display = 'none';
}


function adjustPopupHeight() {
    const itemPopup = document.getElementById('itemPopup');
    const maxHeight = window.innerHeight * 0.8; // 80% viewpoint
    itemPopup.style.maxHeight = `${maxHeight}px`;
    itemPopup.style.overflowY = 'auto';
}

async function loadItems() {
    try {
        // store the checked state of the items for ajax
        const checkedItems = Array.from(document.querySelectorAll('#itemsContainer input[type="checkbox"]:checked'))
                                  .map(input => input.value);

        
        const response = await fetch('get_items.php');
        const items = await response.json();

        
        const itemsContainer = document.getElementById('itemsContainer');
        itemsContainer.innerHTML = '';

        // add item to container
        items.forEach(item => {
            let detailsHtml = '';
            if (item.details.length > 0) {
                detailsHtml = item.details.map(detail => `<small>${detail.name}: ${detail.value}</small><br>`).join('');
            }

            const itemElement = document.createElement('div');
            itemElement.className = 'item-container'; 
            itemElement.innerHTML = `
                <input type="checkbox" id="item-${item.id}" value="${item.id}" name="${item.name}">
                <label for="item-${item.id}">
                    ${item.name}<br>
                    <div class="item-details">${detailsHtml}</div>
                </label>
            `;
            itemsContainer.appendChild(itemElement);
        });

        // reapply checked state for the checked items
        document.querySelectorAll('#itemsContainer input[type="checkbox"]').forEach(input => {
            if (checkedItems.includes(input.value)) {
                input.checked = true;
            }
        });
    } catch (error) {
        console.error('Error loading items:', error);
    }
}


function addItemToTable() {
    const selectedItems = document.querySelectorAll('#itemsContainer input[type="checkbox"]:checked');
    const itemsTable = document.getElementById('selectedItemsTable').getElementsByTagName('tbody')[0];

    selectedItems.forEach(item => {
        // check if the item is already in the table
        if (!document.querySelector(`#selectedItemsTable tr[data-item-id="${item.value}"]`)) {
            // create a new row for the item
            const row = document.createElement('tr');
            row.setAttribute('data-item-id', item.value);
            row.innerHTML = `
                <td>${item.name}</td>
                <td><input type="number" name="items[${item.value}]" min="1" required></td>
                <td><button type="button" onclick="removeItem(this)">Remove</button></td>
            `;
            itemsTable.appendChild(row);
        }

        
        item.checked = false;
    });

    
    hidePopup();
}


function removeItem(button) {
    button.closest('tr').remove();
}

// event listener for the close button
document.querySelector('#itemPopup button[onclick="hidePopup()"]').addEventListener('click', addItemToTable);


function Polling() {
    loadItems(); 
    pollingInterval = setInterval(loadItems, 5000); // 5 sec
}


document.addEventListener('DOMContentLoaded', Polling);

</script>



</body>
</html>



