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
$itemId = $input['itemId'];
$quantity = $input['quantity'];
$volunteerId = $input['volunteerId'];

$conn->begin_transaction();

try {
    //query if there's an existing cargo for this volunteer
    $cargoCheckQuery = "SELECT id FROM cargo WHERE vol_id = ?";
    $stmt = $conn->prepare($cargoCheckQuery);
    $stmt->bind_param('i', $volunteerId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        //fetch the ID
        $row = $result->fetch_assoc();
        $cargoId = $row['id'];
    } else {
        //create a new one
        $stmt = $conn->prepare("INSERT INTO cargo (vol_id) VALUES (?)");
        $stmt->bind_param("i", $volunteerId);
        $stmt->execute();
        $cargoId = $stmt->insert_id;
    }
    $stmt->close();

    //check if the item exists in this cargo
    $quantityQuery = "
        SELECT ci.item_quantity AS cargo_quantity, i.quantity AS available_quantity
        FROM cargo_items ci
        JOIN items i ON ci.item_id = i.id
        WHERE ci.item_id = ? AND ci.cargo_id = ?
    ";
                    
    $stmt = $conn->prepare($quantityQuery);
    $stmt->bind_param('ii', $itemId, $cargoId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentCargoQuantity = $row['cargo_quantity'];
        $currentAvailableQuantity = $row['available_quantity'];
        
        if ($quantity > $currentAvailableQuantity + $currentCargoQuantity) {
            echo json_encode(['success' => false, 'message' => 'Quantity exceeds available stock.']);
            exit;
        }
        $difference = $quantity - $currentCargoQuantity;
        //cargo table
        $updateCargoQuery = "
            UPDATE cargo_items
            SET item_quantity = ?
            WHERE item_id = ? AND cargo_id = ?
        ";
        $stmt = $conn->prepare($updateCargoQuery);
        $stmt->bind_param('iii', $quantity, $itemId, $cargoId);
        $stmt->execute();

        //items table
        $newAvailableQuantity = $currentAvailableQuantity - $difference;
        $updateItemQuery = "UPDATE items SET quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($updateItemQuery);
        $stmt->bind_param('ii', $newAvailableQuantity, $itemId);
        $stmt->execute();

    } else {
        //check quantity
        $stmt = $conn->prepare("SELECT quantity FROM items WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $stmt->bind_result($availableQuantity);
        $stmt->fetch();
        $stmt->close();

        if ($availableQuantity < $quantity) {
            throw new Exception("Not enough items available.");
        }
         //subtract the selected quantity
        $stmt = $conn->prepare("UPDATE items SET quantity = quantity - ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity, $itemId);
        $stmt->execute();
        $stmt->close();
        
        //new entry into cargo_items table
        $insertQuery = "INSERT INTO cargo_items (cargo_id, item_id, item_quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param('iii', $cargoId, $itemId, $quantity);
        $stmt->execute();
    }    
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}

$conn->close();
?>
