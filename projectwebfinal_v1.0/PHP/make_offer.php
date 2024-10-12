<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db.php';

    $data = json_decode(file_get_contents('php://input'), true); //get the files from what called this file
    $announcement_id = $data['announcement_id'];
    $donator_id = $_SESSION["user_id"];

    if (!isset($announcement_id) || !is_numeric($announcement_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid announcement ID']);
        exit();
    }

    $query = "INSERT INTO offers (announcement_id, donator_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $announcement_id, $donator_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Offer made successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to make an offer']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
