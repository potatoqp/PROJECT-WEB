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
$request_id = $data['request_id'];
$user_id = $_SESSION["user_id"];

if (!isset($request_id) || !is_numeric($request_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request ID']);
    exit();
}


$query = "SELECT status FROM requests WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $request_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Request not found or does not belong to user']);
    $conn->close();
    exit();
}

$row = $result->fetch_assoc();
if ($row['status'] !== 'pending') {
    echo json_encode(['success' => false, 'message' => 'Only pending requests can be canceled']);
    $conn->close();
    exit();
}


$deleteQuery = "DELETE FROM requests WHERE id = ? AND user_id = ?";
$deleteStmt = $conn->prepare($deleteQuery);
$deleteStmt->bind_param("ii", $request_id, $user_id);

if ($deleteStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Request canceled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel request']);
}

$deleteStmt->close();
$conn->close();
?>
