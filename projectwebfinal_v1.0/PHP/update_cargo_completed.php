<?php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    session_start();
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit();
    }

    require_once "db.php"; 
    header('Content-Type: application/json');

    
    $input = json_decode(file_get_contents('php://input'), true);
    $itemIds = $input['itemId']; //array or a single value
    $quantities = $input['quantity']; //array or a single value
    $volunteerId = $input['volunteerId'];
    $offerId = $input['offerId'];
    $requestId = $input['requestId'];

    
    if (!is_array($itemIds)) {
        $itemIds = [$itemIds];
    }
    if (!is_array($quantities)) {
        $quantities = [$quantities];
    }

    $conn->begin_transaction();

    try {
        //check if there's an existing cargo for this volunteer
        $cargoCheckQuery = "SELECT id FROM cargo WHERE vol_id = ?";
        $stmt = $conn->prepare($cargoCheckQuery);
        $stmt->bind_param('i', $volunteerId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $cargoId = $row['id'];
        } else {
            $stmt = $conn->prepare("INSERT INTO cargo (vol_id) VALUES (?)");
            $stmt->bind_param("i", $volunteerId);
            $stmt->execute();
            $cargoId = $stmt->insert_id;
        }
        $stmt->close();

        $success = true;

        //loop through each itemId and quantity
        foreach ($itemIds as $index => $itemId) {
            $quantity = isset($quantities[$index]) ? $quantities[$index] : 0;

            //check if the item exists in cargo_items for this cargo
            $cargoItemQuery = "SELECT item_quantity FROM cargo_items WHERE item_id = ? AND cargo_id = ?";
            $stmt = $conn->prepare($cargoItemQuery);
            $stmt->bind_param('ii', $itemId, $cargoId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $currentQuantity = $row['item_quantity'];

                if ($requestId) {
                    if ($quantity > $currentQuantity) {
                        $success = false;
                        continue; 
                    }

                    
                    $newQuantity = $currentQuantity - $quantity;

                    if ($newQuantity > 0) {
                        $updateCargoQuery = "UPDATE cargo_items SET item_quantity = ? WHERE item_id = ? AND cargo_id = ?";
                        $stmt = $conn->prepare($updateCargoQuery);
                        $stmt->bind_param('iii', $newQuantity, $itemId, $cargoId);
                        $stmt->execute();
                    } else {
                        $deleteCargoQuery = "DELETE FROM cargo_items WHERE item_id = ? AND cargo_id = ?";
                        $stmt = $conn->prepare($deleteCargoQuery);
                        $stmt->bind_param('ii', $itemId, $cargoId);
                        $stmt->execute();
                    }

                } elseif ($offerId) {
                    $newQuantity = $currentQuantity + $quantity;
                    $updateCargoQuery = "UPDATE cargo_items SET item_quantity = ? WHERE item_id = ? AND cargo_id = ?";
                    $stmt = $conn->prepare($updateCargoQuery);
                    $stmt->bind_param('iii', $newQuantity, $itemId, $cargoId);
                    $stmt->execute();
                }

            } else {
                //item does not exist in cargo_items, insert a new entry
                if ($offerId) {
                    $insertQuery = "INSERT INTO cargo_items (cargo_id, item_id, item_quantity) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($insertQuery);
                    $stmt->bind_param('iii', $cargoId, $itemId, $quantity);
                    $stmt->execute();
                } else {
                    //for requests, if the item does not exist, it's an error condition
                    $success = false;
                }
            }
        }

        if (!$success) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Failed to process the request.']);
        } else {
            $conn->commit();
            echo json_encode(['success' => true]);
        }

    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
    }

    $conn->close();
?>
