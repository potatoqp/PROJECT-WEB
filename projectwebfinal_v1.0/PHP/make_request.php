<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/MY_stylesheet.css">
    <link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
    <title>Make Request</title>
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
            background-color: #fff;
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
        #container {
            background-color: #1f2933;
            background-color: rgba(0, 0, 0, 0.3);
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
        h3{padding-top:1%;}
    </style>
</head>
<body>
    <div class="navbar">
        <div class="usertype" onclick="window.location.href='loggedcivilian.php'">Go back to the dashboard</div>
    </div>
    <h2>Make a Request</h2>
    <div id="container">
        <form id="requestForm" method="post">
            <h3>Select Items for Request</h3>
            <button type="button" onclick="showPopup()">Add Items</button>
            <table id="selectedItemsTable">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>People</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <!--items will be here-->
                </tbody>
            </table><br>

            <input type="submit" value="Submit Request">
        </form>
    </div>
    
    <div class="overlay" id="overlay" onclick="hidePopup()"></div>
    <div class="popup" id="itemPopup">
        <h3>Select Items</h3>
        <div id="itemsContainer">
            <!--items will be here -->
        </div>
        <button onclick="hidePopup()">Close</button>
    </div>

    <script>
    
    document.getElementById('requestForm').addEventListener('submit', function(event) {
        event.preventDefault(); //prevent the default form submission

        //formdata object
        const formData = new FormData(this);

        fetch('civilian_request.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Requests successfully submitted.');
                document.getElementById('selectedItemsTable').getElementsByTagName('tbody')[0].innerHTML = '';
            } else {
                alert('An error occurred: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    });

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

    async function loadItems() {
        try {
            const checkedItems = Array.from(document.querySelectorAll('#itemsContainer input[type="checkbox"]:checked'))
                                      .map(input => input.value);

            const response = await fetch('get_items.php');
            const items = await response.json();

            const itemsContainer = document.getElementById('itemsContainer');
            itemsContainer.innerHTML = '';

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
            if (!document.querySelector(`#selectedItemsTable tr[data-item-id="${item.value}"]`)) {
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

    function adjustPopupHeight() {
        const itemPopup = document.getElementById('itemPopup');
        const maxHeight = window.innerHeight * 0.8; 
        itemPopup.style.maxHeight = `${maxHeight}px`;
        itemPopup.style.overflowY = 'auto';
    }

    document.querySelector('#itemPopup button[onclick="hidePopup()"]').addEventListener('click', addItemToTable);

    function Polling() {
        loadItems(); 
        pollingInterval = setInterval(loadItems, 5000); //5 sec
    }

    document.addEventListener('DOMContentLoaded', Polling);
    </script>
</body>
</html>
