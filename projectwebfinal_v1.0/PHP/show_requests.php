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
    <title>Show Requests</title>
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
    <h2>Your Requests</h2>
    <div id="message" class="message"></div>
    <div id="loading">Loading...</div>
    <div id = "container">
        <table id="requestsTable">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Item Name</th>
                    <th>Item Details</th>
                    <th>People</th>
                    <th>Date Made</th>
                    <th>Status</th>
                    <th>Date Accepted</th>
                    <th>Date Completed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!--requests will be here -->
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchRequests();
        });

        async function fetchRequests() {
            const loadingIndicator = document.getElementById('loading');
            loadingIndicator.style.display = 'block'; //show loading indicator

            try {
                const response = await fetch('get_requests.php');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const requests = await response.json();
                populateRequestsTable(requests);
            } catch (error) {
                console.error('Error fetching requests:', error);
                showMessage('An error occurred while fetching requests.', 'error');
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
                    <td>${request.item_name}</td>
                    <td>${request.details.map(detail => `${detail.name}: ${detail.value}`).join('<br>')}</td>
                    <td>${request.people}</td>
                    <td>${formatDate(request.date_made)}</td>
                    <td>${request.status}</td>
                    <td>${formatDate(request.date_accepted) || 'N/A'}</td>
                    <td>${formatDate(request.date_completed) || 'N/A'}</td>
                    <td>
                        <button class="cancel-btn" 
                                ${request.status.toLowerCase() === 'pending' ? '' : 'disabled'}
                                onclick="cancelRequest(${request.request_id})">
                            Cancel Request
                        </button>
                    </td>
                `;

                tableBody.appendChild(row);
            });
        }

        async function cancelRequest(requestId) {
            try {
                const response = await fetch('cancel_request.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ request_id: requestId })
                });
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const result = await response.json();
                if (result.success) {
                    showMessage('Request canceled successfully.', 'success');
                    fetchRequests(); //refresh rqwuests
                } else {
                    showMessage('Failed to cancel the request.', 'error');
                }
            } catch (error) {
                console.error('Error canceling request:', error);
                showMessage('An error occurred while canceling the request.', 'error');
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




