<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
require_once "db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if ($itemId === false || $quantity === false) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $updateQuery = "UPDATE items SET quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('ii', $quantity, $itemId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item quantity updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update item quantity']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method or missing parameters']);
}
?>











