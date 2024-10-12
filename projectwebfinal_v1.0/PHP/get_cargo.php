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
    SELECT c.id AS cargo_id, u.username AS volunteer_username, i.item_name, d.detail_name, d.detail_value, ci.item_quantity AS item_quantity
    FROM cargo c
    JOIN users u ON c.vol_id = u.id
    JOIN cargo_items ci ON c.id = ci.cargo_id
    JOIN items i ON ci.item_id = i.id
    LEFT JOIN item_details d ON i.id = d.item_id
";

$result = $conn->query($query);

$cargos = [];
while ($row = $result->fetch_assoc()) {
    $cargo_id = $row['cargo_id'];
    
    if (!isset($cargos[$cargo_id])) {
        $cargos[$cargo_id] = [
            'cargo_id' => $cargo_id,
            'volunteer_username' => $row['volunteer_username'],
            'items' => []
        ];
    }
    
    $item_name = $row['item_name'];
    if (!isset($cargos[$cargo_id]['items'][$item_name])) {
        $cargos[$cargo_id]['items'][$item_name] = [
            'name' => $item_name,
            'quantity' => $row['item_quantity'],
            'details' => []
        ];
    }

    if ($row['detail_name'] && $row['detail_value']) {
        $cargos[$cargo_id]['items'][$item_name]['details'][] = [
            'name' => $row['detail_name'],
            'value' => $row['detail_value']
        ];
    }
}

// convert the associative array to a numeric array
$cargos = array_map(function($cargo) {
    $cargo['items'] = array_values($cargo['items']);
    return $cargo;
}, $cargos);

echo json_encode(array_values($cargos));

$conn->close();
?>


