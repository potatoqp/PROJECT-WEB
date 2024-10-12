<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php"; 

header('Content-Type: application/json');

$user_id = $_SESSION["user_id"];

$query = "
    SELECT r.id AS request_id, r.item_id, r.people, r.date_made, r.status, r.date_accepted, r.date_completed, i.item_name, d.detail_name, d.detail_value
    FROM requests r
    JOIN items i ON r.item_id = i.id
    LEFT JOIN item_details d ON i.id = d.item_id
    WHERE r.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
while ($row = $result->fetch_assoc()) {
    $request_id = $row['request_id'];
    
    if (!isset($requests[$request_id])) {
        $requests[$request_id] = [
            'request_id' => $request_id,
            'item_id' => $row['item_id'],
            'item_name' => $row['item_name'],
            'people' => $row['people'],
            'date_made' => $row['date_made'],
            'status' => $row['status'],
            'date_accepted' => $row['date_accepted'],
            'date_completed' => $row['date_completed'],
            'details' => []
        ];
    }
    
    if ($row['detail_name'] && $row['detail_value']) {
        $requests[$request_id]['details'][] = [
            'name' => $row['detail_name'],
            'value' => $row['detail_value']
        ];
    }
}

echo json_encode(array_values($requests));

$conn->close();
?>

