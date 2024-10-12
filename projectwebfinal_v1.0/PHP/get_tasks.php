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

$requestQuery = "
    SELECT 
        t.id AS task_id, 
        r.user_id AS user_id,
        t.volunteer_id,
        r.item_id,
        u_c_r.fullname AS fullname,
        u_c_r.phone AS phone,
        t.request_id, 
        t.offer_id, 
        t.date_made, 
        t.status, 
        i.item_name,
        r.people, 
        d.detail_name, 
        d.detail_value
    FROM tasks t
    LEFT JOIN requests r ON t.request_id = r.id
    LEFT JOIN users u_c_r ON r.user_id = u_c_r.id
    LEFT JOIN items i ON r.item_id = i.id
    LEFT JOIN item_details d ON i.id = d.item_id
    WHERE t.volunteer_id = ? AND t.request_id IS NOT NULL
";
$offerQuery = "
    SELECT 
        t.id AS task_id, 
        o.donator_id AS user_id,
        t.volunteer_id,
        ai.item_id,
        u_c_o.fullname AS fullname,
        u_c_o.phone AS phone,
        t.request_id, 
        t.offer_id, 
        t.date_made,
        t.status, 
        ai.needed_quantity, 
        i.item_name, 
        d.detail_name, 
        d.detail_value
    FROM tasks t
    LEFT JOIN offers o ON t.offer_id = o.id
    LEFT JOIN users u_c_o ON o.donator_id = u_c_o.id
    LEFT JOIN announcements a ON o.announcement_id = a.id
    LEFT JOIN announcement_items ai ON a.id = ai.announcement_id
    LEFT JOIN items i ON ai.item_id = i.id
    LEFT JOIN item_details d ON i.id = d.item_id
    WHERE t.volunteer_id = ? AND t.offer_id IS NOT NULL
";


$stmtRequest = $conn->prepare($requestQuery);
if (!$stmtRequest) {
    echo json_encode(['error' => 'Failed to prepare request statement']);
    exit();
}

$stmtRequest->bind_param("i", $user_id);
$stmtRequest->execute();
$requestResult = $stmtRequest->get_result();

$requestTasks = [];
while ($row = $requestResult->fetch_assoc()) {
    $task_id = $row['task_id'];
    $item_name = $row['item_name'];
    
    if (!isset($requestTasks[$task_id])) {
        $requestTasks[$task_id] = [
            'task_id' => $row['task_id'],
            'user_id' => $row['user_id'],
            'volunteer_id' => $row['volunteer_id'],
            'item_id' => $row['item_id'],
            'fullname' => $row['fullname'],
            'phone' => $row['phone'],
            'request_id' => $row['request_id'],
            'offer_id' => $row['offer_id'],
            'date_made' => $row['date_made'],
            'status' => $row['status'],
            'item_name' => $row['item_name'],
            'people' => $row['people']
        ];
    }
}

$stmtOffer = $conn->prepare($offerQuery);
if (!$stmtOffer) {
    echo json_encode(['error' => 'Failed to prepare offer statement']);
    exit();
}

$stmtOffer->bind_param("i", $user_id);
$stmtOffer->execute();
$offerResult = $stmtOffer->get_result();

$offerTasks = [];
while ($row = $offerResult->fetch_assoc()) {
    $task_id = $row['task_id'];
    $item_name = $row['item_name'];
    
    if (!isset($offerTasks[$task_id])) {
        $offerTasks[$task_id] = [
            'task_id' => $task_id,
            'user_id' => $row['user_id'],
            'volunteer_id' =>$row['volunteer_id'],
            'fullname' => $row['fullname'],
            'phone' => $row['phone'],
            'request_id' => $row['request_id'],
            'offer_id' => $row['offer_id'],
            'date_made' => $row['date_made'],
            'status' => $row['status'],
            'items' => []
        ];
    }

    if ($item_name) {
        $itemIndex = array_search($row['item_name'], array_column($offerTasks[$task_id]['items'], 'name'));
        
        if ($itemIndex === false) {
            $offerTasks[$task_id]['items'][] = [
                'id' => $row['item_id'],
                'name' => $row['item_name'],
                'needed_quantity' => $row['needed_quantity'],
                'details' => []
            ];
            $itemIndex = count($offerTasks[$task_id]['items']) - 1;
        } else {
            $offerTasks[$task_id]['items'][$itemIndex]['needed_quantity'] = $row['needed_quantity'];
        }
        
        if ($row['detail_name'] && $row['detail_value']) {
            $offerTasks[$task_id]['items'][$itemIndex]['details'][] = [
                'name' => $row['detail_name'],
                'value' => $row['detail_value']
            ];
        }
    }
}

echo json_encode([
    'requests' => array_values($requestTasks),
    'offers' => array_values($offerTasks)
]);
$conn->close();
?>



