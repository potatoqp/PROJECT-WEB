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
    SELECT a.id AS announcement_id, a.description, ai.item_id, i.item_name, ai.needed_quantity
    FROM announcements a
    JOIN announcement_items ai ON a.id = ai.announcement_id
    JOIN items i ON ai.item_id = i.id
";

$result = $conn->query($query);

$announcements = [];
while ($row = $result->fetch_assoc()) {
    $announcement_id = $row['announcement_id'];
    
    
    if (!isset($announcements[$announcement_id])) {
        $announcements[$announcement_id] = [
            'id' => $announcement_id,
            'description' => $row['description'],
            'items' => []
        ];
    }
    
    
    $announcements[$announcement_id]['items'][] = [
        'id' => $row['item_id'],
        'name' => $row['item_name'],
        'needed_quantity' => $row['needed_quantity']
    ];
}

echo json_encode(array_values($announcements));

$conn->close();
?>
