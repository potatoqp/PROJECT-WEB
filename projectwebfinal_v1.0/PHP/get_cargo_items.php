<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php"; 

header('Content-Type: application/json');
$volunteerId = $_SESSION["volunteer_id"];

$query = "
    SELECT 
        i.id AS item_id, 
        i.item_name, 
        i.quantity, 
        COALESCE(
            (SELECT ci.item_quantity
             FROM cargo_items ci
             JOIN cargo cg ON ci.cargo_id = cg.id
             WHERE ci.item_id = i.id AND cg.vol_id = ?
             LIMIT 1), 0
        ) AS cargo_quantity, 
        c.category_name, 
        d.detail_name, 
        d.detail_value
    FROM items i
    JOIN categories c ON i.category_id = c.id
    LEFT JOIN item_details d ON i.id = d.item_id
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $volunteerId);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $item_id = $row['item_id'];
    
    
    if (!isset($items[$item_id])) {
        $items[$item_id] = [
            'id' => $item_id,
            'name' => $row['item_name'],
            'available_quantity' => $row['quantity'],
            'cargo_quantity' => $row['cargo_quantity'],
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