<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$username = 'base';


$sql = "SELECT latitude, longitude FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$coords = [];
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $coords = [
        'status' => 'success',
        'latitude' => $row['latitude'],
        'longitude' => $row['longitude']
    ];
} else {
    $coords = [
        'status' => "Error: User 'base' not found"
    ];
}

$stmt->close();
$conn->close();

echo json_encode($coords);
?>
