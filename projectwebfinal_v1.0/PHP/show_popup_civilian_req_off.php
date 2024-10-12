<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Requests and Offers</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
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
        .navbar {
            margin-top: 20px;
        }
        .usertype {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Your Requests and Offers</h1>
    <div id="message" class="message"></div>
    <div id="loading">Loading...</div>

    <h2>Requests</h2>
    <table id="requestsTable">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Date Made</th>
                <th>Item Name</th>
                <th>People</th>
                <th>Date Accepted</th>
                <th>Status</th>
                <th>Volunteer Username</th>
            </tr>
        </thead>
        <tbody>
            <!--requests will be here-->
        </tbody>
    </table>

    <h2>Offers</h2>
    <table id="offersTable">
        <thead>
            <tr>
                <th>Offer ID</th>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Date Made</th>
                <th>Item Name</th>
                <th>Item Details</th>
                <th>Needed Quantity</th>
                <th>Date Accepted</th>
                <th>Status</th>
                <th>Volunteer Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!--offers will be here-->
        </tbody>
    </table>

    <div class="navbar">
        <div class="usertype" onclick="window.location.href='loggedcivilian.php'">Go back to the dashboard</div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchRequestsAndOffers();
        });

        async function fetchRequestsAndOffers() {
            const loadingIndicator = document.getElementById('loading');
            loadingIndicator.style.display = 'block'; //show loading indicator

            try {
                const response = await fetch('get_popup_civilian_req_off.php');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                populateRequestsTable(data.requests);
                populateOffersTable(data.offers);
            } catch (error) {
                console.error('Error fetching data:', error);
                showMessage('An error occurred while fetching data.', 'error');
            } finally {
                loadingIndicator.style.display = 'none'; //hide loading indicator
            }
        }

        function populateRequestsTable(requests) {
            const tableBody = document.getElementById('requestsTable').getElementsByTagName('tbody')[0];
            tableBody.innerHTML = ''; 

            requests.forEach(request => {
                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${request.request_id}</td>
                    <td>${request.fullname}</td>
                    <td>${request.phone}</td>
                    <td>${formatDate(request.date_made)}</td>
                    <td>${request.item_name}</td>
                    <td>${request.people}</td>
                    <td>${formatDate(request.date_accepted) || 'N/A'}</td>
                    <td>${request.status}</td>
                    <td>${request.volunteer_username || 'N/A'}</td>
                `;

                tableBody.appendChild(row);
            });
        }

        function populateOffersTable(offers) {
            const tableBody = document.getElementById('offersTable').getElementsByTagName('tbody')[0];
            tableBody.innerHTML = ''; 

            offers.forEach(offer => {
                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${offer.offer_id}</td>
                    <td>${offer.fullname}</td>
                    <td>${offer.phone}</td>
                    <td>${formatDate(offer.date_made)}</td>
                    <td>${offer.item_name}</td>
                    <td>
                        ${offer.item_details.map(detail => `${detail.name}: ${detail.value}`).join('<br>')}
                    </td>
                    <td>${offer.needed_quantity}</td>
                    <td>${formatDate(offer.date_accepted) || 'N/A'}</td>
                    <td>${offer.status}</td>
                    <td>${offer.volunteer_username || 'N/A'}</td>
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
                    fetchRequestsAndOffers(); // Refresh the list of offers
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
