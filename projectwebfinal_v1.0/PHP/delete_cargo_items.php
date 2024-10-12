<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php"; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['volunteer_id'])) {
    $volunteerId = filter_input(INPUT_POST, 'volunteer_id', FILTER_VALIDATE_INT);

    if ($volunteerId === false) {
        echo json_encode(['success' => false, 'message' => 'Invalid volunteer ID']);
        exit;
    }

    $conn->begin_transaction();

    try {
        //all cargo items for this volunteer
        $cargoQuery = "
            SELECT ci.item_id, ci.item_quantity
            FROM cargo_items ci
            JOIN cargo c ON ci.cargo_id = c.id
            WHERE c.vol_id = ?
        ";
        $stmt = $conn->prepare($cargoQuery);
        $stmt->bind_param('i', $volunteerId);
        $stmt->execute();
        $result = $stmt->get_result();

        //update the quantities
        while ($row = $result->fetch_assoc()) {
            $itemId = $row['item_id'];
            $itemQuantity = $row['item_quantity'];

            $updateQuery = "UPDATE items SET quantity = quantity + ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('ii', $itemQuantity, $itemId);
            $updateStmt->execute();
            $updateStmt->close();
        }
        $stmt->close();

        //delete all cargo items for this volunteer
        $deleteCargoItemsQuery = "
            DELETE ci
            FROM cargo_items ci
            JOIN cargo c ON ci.cargo_id = c.id
            WHERE c.vol_id = ?
        ";
        $deleteStmt = $conn->prepare($deleteCargoItemsQuery);
        $deleteStmt->bind_param('i', $volunteerId);
        $deleteStmt->execute();
        $deleteStmt->close();

        // delete the cargo record if no cargo items exist
        $deleteCargoQuery = "
            DELETE FROM cargo
            WHERE vol_id = ?
        ";
        $deleteCargoStmt = $conn->prepare($deleteCargoQuery);
        $deleteCargoStmt->bind_param('i', $volunteerId);
        $deleteCargoStmt->execute();
        $deleteCargoStmt->close();

        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Cargo items unloaded successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method or missing parameters']);
}
?>
