<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$data = json_decode(file_get_contents('php://input'), true);

$lat = $data['lat'];
$lng = $data['lng'];
$username = 'base';

if (is_numeric($lat) && is_numeric($lng)) {
    $sql = "UPDATE users SET latitude = ?, longitude = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dds", $lat, $lng, $username);

    if ($stmt->execute()) {
        $response = ['status' => 'success'];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Database update failed: ' . $stmt->error
        ];
    }

    $stmt->close();
} else {
    $response = [
        'status' => 'error',
        'message' => 'Invalid latitude or longitude values.'
    ];
}

$conn->close();

echo json_encode($response);
?>
