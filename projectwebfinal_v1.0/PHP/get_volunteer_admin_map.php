<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$sql = "SELECT u.id, u.username, u.latitude, u.longitude
        FROM volunteers v
        JOIN users u ON v.user_id = u.id";
$result = $conn->query($sql);

$volunteers = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $volunteers[] = [
            'userId' => $row['id'],
            'username' => $row['username'],
            'lat' => $row['latitude'],
            'lng' => $row['longitude']
        ];
    }
}

$conn->close();
echo json_encode($volunteers);
?>
