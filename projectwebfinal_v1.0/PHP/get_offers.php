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
    SELECT o.id AS offer_id, o.announcement_id, o.date_made, o.status, o.date_accepted, o.date_completed, 
           a.description, i.item_name, d.detail_name, d.detail_value, ai.needed_quantity
    FROM offers o
    JOIN announcements a ON o.announcement_id = a.id
    JOIN announcement_items ai ON a.id = ai.announcement_id
    JOIN items i ON ai.item_id = i.id
    LEFT JOIN item_details d ON i.id = d.item_id
    WHERE o.donator_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$offers = [];
while ($row = $result->fetch_assoc()) {
    $offer_id = $row['offer_id'];
    
    if (!isset($offers[$offer_id])) {
        $offers[$offer_id] = [
            'offer_id' => $offer_id,
            'announcement_id' => $row['announcement_id'],
            'description' => $row['description'],
            'date_made' => $row['date_made'],
            'status' => $row['status'],
            'date_accepted' => $row['date_accepted'],
            'date_completed' => $row['date_completed'],
            'items' => []
        ];
    }


    //show multiple items in one offer
    if ($row['item_name']) {
        // check if the item already exists
        $itemIndex = array_search($row['item_name'], array_column($offers[$offer_id]['items'], 'name'));
        
        if ($itemIndex === false) {
            // add new item if it doesn't exist
            $offers[$offer_id]['items'][] = [
                'name' => $row['item_name'],
                'needed_quantity' => $row['needed_quantity'],
                'details' => []
            ];
            $itemIndex = count($offers[$offer_id]['items']) - 1;
        } else {
            // update the quanitty if item already exists
            $offers[$offer_id]['items'][$itemIndex]['needed_quantity'] = $row['needed_quantity'];
        }
        
        //add details
        if ($row['detail_name'] && $row['detail_value']) {
            $offers[$offer_id]['items'][$itemIndex]['details'][] = [
                'name' => $row['detail_name'],
                'value' => $row['detail_value']
            ];
        }
    }
}

echo json_encode(array_values($offers));

$conn->close();
?>


