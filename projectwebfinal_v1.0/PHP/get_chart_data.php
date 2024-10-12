<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php"; 

header('Content-Type: application/json');

$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];


//needed queries for new/completed offers/requests
$query_new_offers = "
    SELECT COUNT(*) AS count, DATE(date_made) AS date
    FROM offers
    WHERE date_made BETWEEN ? AND ?
    GROUP BY DATE(date_made);
";

$query_new_requests = "
    SELECT COUNT(*) AS count, DATE(date_made) AS date
    FROM requests
    WHERE date_made BETWEEN ? AND ?
    GROUP BY DATE(date_made);
";

$query_completed_offers = "
    SELECT COUNT(*) AS count, DATE(date_completed) AS date
    FROM offers
    WHERE status = 'completed' AND date_completed BETWEEN ? AND ?
    GROUP BY DATE(date_completed);
";

$query_completed_requests = "
    SELECT COUNT(*) AS count, DATE(date_completed) AS date
    FROM requests
    WHERE status = 'completed' AND date_completed BETWEEN ? AND ?
    GROUP BY DATE(date_completed);
";

function fetch_data($conn, $query, $start_date, $end_date) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}


$new_offers = fetch_data($conn, $query_new_offers, $start_date, $end_date);
$new_requests = fetch_data($conn, $query_new_requests, $start_date, $end_date);
$completed_offers = fetch_data($conn, $query_completed_offers, $start_date, $end_date);
$completed_requests = fetch_data($conn, $query_completed_requests, $start_date, $end_date);


$response = [
    'new_offers' => $new_offers,
    'new_requests' => $new_requests,
    'completed_offers' => $completed_offers,
    'completed_requests' => $completed_requests
];

echo json_encode($response);

$conn->close();
?>
