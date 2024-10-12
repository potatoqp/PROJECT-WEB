<?php
header('Content-Type: application/json');
session_start();

require_once "db.php";

$categories = [];
$categoryQuery = "SELECT id, category_name FROM categories";
$categoryResult = $conn->query($categoryQuery);

if ($categoryResult->num_rows > 0) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

echo json_encode($categories);
$conn->close();
?>
