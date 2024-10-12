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
    <title>Show Offers</title>
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
        .message {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid transparent;
            border-radius: 5px;
            display: none;
        }
        .message.success {
            color: green;
            border-color: green;
        }
        .message.error {
            color: red;
            border-color: red;
        }
        .cancel-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .cancel-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
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
    <h2>Your Offers</h2>
    <div id="message" class="message"></div>
    <div id="loading">Loading...</div>
    <div id="container">
        <table id="offersTable">
            <thead>
                <tr>
                    <th>Offer ID</th>
                    <th>Announcement Description</th>
                    <th>Date Made</th>
                    <th>Status</th>
                    <th>Date Accepted</th>
                    <th>Date Completed</th>
                    <th>Item Names</th>
                    <th>Needed Quantities</th>
                    <th>Item Details</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- offers will be here-->
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchOffers();
        });

        async function fetchOffers() {
            const loadingIndicator = document.getElementById('loading');
            loadingIndicator.style.display = 'block'; //show loading indicator

            try {
                const response = await fetch('get_offers.php');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const offers = await response.json();
                populateOffersTable(offers);
            } catch (error) {
                console.error('Error fetching offers:', error);
                showMessage('An error occurred while fetching offers.', 'error');
            } finally {
                loadingIndicator.style.display = 'none'; //hide loading indicator
            }
        }

        function populateOffersTable(offers) {
            const tableBody = document.getElementById('offersTable').getElementsByTagName('tbody')[0];
            tableBody.innerHTML = ''; //clear existing rows

            offers.forEach(offer => {
                const itemNames = [];
                const neededQuantities = [];
                const itemDetails = [];

                offer.items.forEach(item => {
                    itemNames.push(item.name);
                    neededQuantities.push(item.needed_quantity);
                    itemDetails.push(item.details.map(detail => `${detail.name}: ${detail.value}`).join(', '));
                });

                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${offer.offer_id}</td>
                    <td>${offer.description}</td>
                    <td>${formatDate(offer.date_made)}</td>
                    <td>${offer.status}</td>
                    <td>${formatDate(offer.date_accepted) || 'N/A'}</td>
                    <td>${formatDate(offer.date_completed) || 'N/A'}</td>
                    <td>${itemNames.join('<br>')}</td>
                    <td>${neededQuantities.join('<br>')}</td>
                    <td>${itemDetails.join('<br>')}</td>
                    <td>
                        <button class="cancel-btn" 
                                ${offer.status.toLowerCase() === 'pending' ? '' : 'disabled'}
                                onclick="cancelOffer(${offer.offer_id})">
                            Cancel Offer
                        </button>
                    </td>
                `;

                tableBody.appendChild(row);
            });
        }

        async function cancelOffer(offerId) {
            try {
                const response = await fetch('cancel_offer.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ offer_id: offerId })
                });
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const result = await response.json();
                if (result.success) {
                    showMessage('Offer canceled successfully.', 'success');
                    fetchOffers(); //refresh the list of offers
                } else {
                    showMessage(result.message || 'Failed to cancel the offer.', 'error');
                }
            } catch (error) {
                console.error('Error canceling offer:', error);
                showMessage('An error occurred while canceling the offer.', 'error');
            }
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        function showMessage(message, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = message;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';
        }
    </script>
</body>
</html>


