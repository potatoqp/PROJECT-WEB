<?php
require_once "db.php"; 


$jsonFilePath = 'stuff.json';
$jsonData = file_get_contents($jsonFilePath);
$data = json_decode($jsonData, true);


if ($data === null) {
    die('Error decoding JSON data');
}


$categoryQuery = "INSERT INTO categories (id, category_name) VALUES (?, ?) ON DUPLICATE KEY UPDATE category_name = VALUES(category_name)";
$categoryStmt = $conn->prepare($categoryQuery);

foreach ($data['categories'] as $category) {
    $categoryStmt->bind_param("is", $category['id'], $category['category_name']);
    $categoryStmt->execute();
}

$categoryStmt->close();


$itemQuery = "INSERT INTO items (id, category_id, item_name, quantity) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE item_name = VALUES(item_name), quantity = VALUES(quantity)";
$itemStmt = $conn->prepare($itemQuery);

$detailQuery = "INSERT INTO item_details (item_id, detail_name, detail_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE detail_value = VALUES(detail_value)";
$detailStmt = $conn->prepare($detailQuery);

foreach ($data['items'] as $item) {
    $quantity = null;
    $otherDetails = [];

    
    foreach ($item['details'] as $detail) {
        if ($detail['detail_name'] === 'quantity') {
            $quantity = (int)$detail['detail_value'];
        } else {
            $otherDetails[] = $detail;
        }
    }

    //insert item with quantity
    $itemStmt->bind_param("iisi", $item['id'], $item['category'], $item['name'], $quantity);
    $itemStmt->execute();

    //insert the other details
    foreach ($otherDetails as $detail) {
        $detailStmt->bind_param("iss", $item['id'], $detail['detail_name'], $detail['detail_value']);
        $detailStmt->execute();
    }
}

$itemStmt->close();
$detailStmt->close();
$conn->close();

echo "Database successfully populated.";
?>
