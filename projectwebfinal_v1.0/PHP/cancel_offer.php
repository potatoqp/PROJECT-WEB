<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$offer_id = $data['offer_id'];
$user_id = $_SESSION["user_id"];

if (!isset($offer_id) || !is_numeric($offer_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid offer ID']);
    exit();
}


$query = "SELECT status FROM offers WHERE id = ? AND donator_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $offer_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Offer not found or does not belong to user']);
    $conn->close();
    exit();
}

$row = $result->fetch_assoc();
if ($row['status'] !== 'pending') {
    echo json_encode(['success' => false, 'message' => 'Only pending offers can be canceled']);
    $conn->close();
    exit();
}


$deleteQuery = "DELETE FROM offers WHERE id = ? AND donator_id = ?";
$deleteStmt = $conn->prepare($deleteQuery);
$deleteStmt->bind_param("ii", $offer_id, $user_id);

if ($deleteStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Offer canceled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel offer']);
}

$deleteStmt->close();
$conn->close();
?>
