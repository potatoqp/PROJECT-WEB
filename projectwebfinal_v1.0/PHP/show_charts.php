
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
    <title>Offers and Requests Charts</title>
    <script src="https://cdn.jsdelivr.net/npm/moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
    <link rel="stylesheet" href="../CSS/MY_stylesheet.css">
    <link rel="stylesheet" href="../CSS/MY_stylesheetNavbar.css">
    <style>
        .container {
            padding: 20px;
            background-color: #2e3a47; 
        }

        .date-selection {
            display: flex;
            gap: 10px;
        }

        .date-selection label {
            display: block;
            font-weight: bold;
            margin-right: 10px;
        }

        .chart-container {
            width: 100%;
            height: 65vh;
            border-radius: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="usertype" onclick="window.location.href='loggedadmin.php'">Go back to the dashboard</div>
    </div>
    <h2>Offers and Requests Charts</h2>
    
    <div class="container">
        <!-- Date Selection -->
        <div class="date-selection">
            <div>
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date">
            </div>
            <div>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date">
            </div>
            <button id="update_chart">Update Chart</button>
        </div>

        <!-- canvas for Chart.js -->
        <div class="chart-container">
            <canvas id="offersChart"></canvas>
        </div>
    </div>

    <script>
        let chart;

        //update the chart with new data
        function updateChart(dates, newOffers, newRequests, completedOffers, completedRequests) {
            const ctx = document.getElementById('offersChart').getContext('2d');

            //destroy the previous chart instance if it exists
            if (chart) {
                chart.destroy();
            }

        
            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'New Offers',
                            data: newOffers,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.6)', 
                            borderWidth: 2 
                        },
                        {
                            label: 'New Requests',
                            data: newRequests,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.6)', 
                            borderWidth: 2 
                        },
                        {
                            label: 'Completed Offers',
                            data: completedOffers,
                            borderColor: 'rgba(255, 206, 86, 1)',
                            backgroundColor: 'rgba(255, 206, 86, 0.6)', 
                            borderWidth: 2 
                        },
                        {
                            label: 'Completed Requests',
                            data: completedRequests,
                            borderColor: 'rgba(153, 102, 255, 1)',
                            backgroundColor: 'rgba(153, 102, 255, 0.6)', 
                            borderWidth: 2 
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'day'
                            },
                            title: {
                                display: true,
                                text: 'Date',
                                color: '#ffffff' 
                            },
                            ticks: {
                                color: '#ffffff' 
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Count',
                                color: '#ffffff' 
                            },
                            ticks: {
                                color: '#ffffff' 
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#ffffff' 
                            }
                        }
                    }
                }
            });
        }

        document.getElementById('update_chart').addEventListener('click', function () {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            if (startDate && endDate) {
                fetch(`get_chart_data.php?start_date=${startDate}&end_date=${endDate}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            const dates = data.new_offers.map(item => item.date);
                            const newOffers = data.new_offers.map(item => item.count);
                            const newRequests = data.new_requests.map(item => item.count);
                            const completedOffers = data.completed_offers.map(item => item.count);
                            const completedRequests = data.completed_requests.map(item => item.count);

                            updateChart(dates, newOffers, newRequests, completedOffers, completedRequests);
                        }
                    })
                    .catch(error => console.error('Error fetching chart data:', error));
            } else {
                alert("Please select both start date and end date.");
            }
        });
    </script>
</body>
</html>

