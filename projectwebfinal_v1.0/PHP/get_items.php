<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php"; 

header('Content-Type: application/json');


$query = "
    SELECT i.id AS item_id, i.item_name, i.quantity, c.category_name, d.detail_name, d.detail_value
    FROM items i
    JOIN categories c ON i.category_id = c.id
    LEFT JOIN item_details d ON i.id = d.item_id
";

$result = $conn->query($query);

$items = [];
while ($row = $result->fetch_assoc()) {
    $item_id = $row['item_id'];
    
    
    if (!isset($items[$item_id])) {
        $items[$item_id] = [
            'id' => $item_id,
            'name' => $row['item_name'],
            'quantity' => $row['quantity'],
            'category' => $row['category_name'],
            'details' => []
        ];
    }
    
    
    if ($row['detail_name'] && $row['detail_value']) {
        $items[$item_id]['details'][] = [
            'name' => $row['detail_name'],
            'value' => $row['detail_value']
        ];
    }
}


echo json_encode(array_values($items));

$conn->close();
?>


