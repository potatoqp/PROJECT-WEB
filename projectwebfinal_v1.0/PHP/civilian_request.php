<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["user"])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SESSION['user_role'] !== 'civilian') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['items']) || !is_array($_POST['items'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit();
    }
    
    $requests = $_POST['items'];
    $user_id = $_SESSION["user_id"];
    $date_made = date('Y-m-d H:i:s');

    try {
        $conn->begin_transaction();
        $stmt = $conn->prepare("INSERT INTO requests (item_id, people, user_id, date_made) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $item_id, $people, $user_id, $date_made);

        foreach ($requests as $itemId => $people) {
            $item_id = (int)$itemId;
            $people = (int)$people;
            $stmt->execute();
        }

        $stmt->close();
        $conn->commit();
        $conn->close();

        echo json_encode(['success' => true, 'message' => 'Requests successfully submitted.']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error processing request: ' . $e->getMessage()]);
    }
    exit();
}
?>
