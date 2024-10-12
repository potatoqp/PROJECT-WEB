<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
require_once "db.php";
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$civilian_id = $input['civilianId'];

$data = ["code" => 0, "message" => "", "requests" => [], "offers" => []];

if (mysqli_connect_error()) {
    $data["message"] = mysqli_connect_error();
} else {
    //fetch requests
    $reqQuery = "
        SELECT 
            r.id AS request_id,
            r.item_id,
            r.people, 
            r.date_made, 
            r.date_accepted, 
            r.status,
            i.item_name, 
            u.fullname, 
            u.phone,
            vu.username AS volunteer_username
        FROM requests r
        JOIN users u ON r.user_id = u.id
        JOIN items i ON r.item_id = i.id
        LEFT JOIN tasks t ON r.id = t.request_id
        LEFT JOIN users vu ON t.volunteer_id = vu.id
        WHERE r.user_id = ?
    ";

    //fetch offers
    $offQuery = "
        SELECT 
            o.id AS offer_id, 
            o.date_made, 
            o.date_accepted, 
            o.status,
            i.item_name,
            ai.item_id,
            ai.needed_quantity, 
            u.fullname, 
            u.phone,
            vu.username AS volunteer_username,
            d.detail_name,
            d.detail_value
        FROM offers o
        JOIN users u ON o.donator_id = u.id
        JOIN announcements a ON o.announcement_id = a.id
        JOIN announcement_items ai ON a.id = ai.announcement_id
        JOIN items i ON ai.item_id = i.id
        LEFT JOIN tasks t ON o.id = t.offer_id
        LEFT JOIN users vu ON t.volunteer_id = vu.id
        LEFT JOIN item_details d ON i.id = d.item_id
        WHERE o.donator_id = ?
    ";

    $stmt = $conn->prepare($reqQuery);
    $stmt->bind_param("i", $civilian_id);
    $stmt->execute();
    $request_result = $stmt->get_result();

    while ($row = $request_result->fetch_assoc()) {
        $data["requests"][] = [
            'request_id' => (int)$row['request_id'],
            'item_id' => (int)$row['item_id'],
            'fullname' => $row['fullname'],
            'phone' => $row['phone'],
            'date_made' => $row['date_made'],
            'item_name' => $row['item_name'],
            'people' => (int)$row['people'],
            'date_accepted' => $row['date_accepted'],
            'status' => $row['status'],
            'volunteer_username' => $row['volunteer_username'],
        ];
    }

    $stmt = $conn->prepare($offQuery);
    $stmt->bind_param("i", $civilian_id);
    $stmt->execute();
    $offer_result = $stmt->get_result();

    while ($row = $offer_result->fetch_assoc()) {
        $offer_id = (int)$row['offer_id'];
        
        if (!isset($data["offers"][$offer_id])) {
            $data["offers"][$offer_id] = [
                'offer_id' => $offer_id,
                'fullname' => $row['fullname'],
                'phone' => $row['phone'],
                'status' => $row['status'],
                'date_made' => $row['date_made'],
                'date_accepted' => $row['date_accepted'],
                'volunteer_username' => $row['volunteer_username'],
                'items' => []
            ];
        }

        if ($row['item_name']) {
            //check if the item already exists
            $itemIndex = array_search($row['item_name'], array_column($data['offers'][$offer_id]['items'], 'name'));
            
            if ($itemIndex === false) {
                //add new item if it doesn't exist
                $data['offers'][$offer_id]['items'][] = [
                    'id' => $row['item_id'],
                    'name' => $row['item_name'],
                    'needed_quantity' => $row['needed_quantity'],
                    'details' => []
                ];
                $itemIndex = count($data['offers'][$offer_id]['items']) - 1;
            } else {
                //update the quantity if item already exists
                $data['offers'][$offer_id]['items'][$itemIndex]['needed_quantity'] = $row['needed_quantity'];
            }
            
            //add details to the item
            if ($row['detail_name'] && $row['detail_value']) {
                $data['offers'][$offer_id]['items'][$itemIndex]['details'][] = [
                    'name' => $row['detail_name'],
                    'value' => $row['detail_value']
                ];
            }
        }
    }

    $data["offers"] = array_values($data["offers"]); //reindex offers array



    if (empty($data['requests']) && empty($data['offers'])) {
        $data["code"] = 0;
        $data["message"] = "No data found";
    } elseif (!empty($data['requests']) && (empty($data['offers']))) {
        $data["code"] = 1;
        $data["message"] = "Requests Retrieved Successfully";
    } elseif (!empty($data['offers']) && (empty($data['requests']))) {
        $data["code"] = 2;
        $data["message"] = "Offers Retrieved Successfully";
    } else {
        $data["code"] = 3;
        $data["message"] = "Requests and Offers Retrieved Successfully";
    }
}

echo json_encode($data);

$conn->close();
?>
