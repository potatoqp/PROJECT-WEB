<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$sql = "SELECT u.id, u.username, u.latitude, u.longitude
        FROM users u
        LEFT JOIN admins a ON u.id = a.user_id
        LEFT JOIN volunteers v ON u.id = v.user_id
        WHERE a.user_id IS NULL AND v.user_id IS NULL";

$result = $conn->query($sql);

$civilians = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $civilians[] = [
            'user_id' => $row['id'],
            'username' => $row['username'],
            'lat' => $row['latitude'],
            'lng' => $row['longitude']
        ];
    }
}

$conn->close();
echo json_encode($civilians);
?>
