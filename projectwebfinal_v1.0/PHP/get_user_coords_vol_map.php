<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    session_start();
    require_once "db.php";
    
    $userId = $_SESSION["user_id"];

    
    $sql = "SELECT u.latitude, u.longitude
            FROM volunteers v
            JOIN users u ON v.user_id = u.id
            WHERE v.user_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $volunteer = [];
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $volunteer = [
                'lat' => $row['latitude'],
                'lng' => $row['longitude']
            ];
        }
        $stmt->close();
    }

    //select pending and ongoing requests and offers
    $sql = "SELECT DISTINCT u.id, u.latitude, u.longitude
            FROM users u
            LEFT JOIN requests r ON u.id = r.user_id
            LEFT JOIN offers o ON u.id = o.donator_id
            LEFT JOIN tasks t1 ON r.id = t1.request_id AND t1.volunteer_id = ?
            LEFT JOIN tasks t2 ON o.id = t2.offer_id AND t2.volunteer_id = ?
            WHERE (r.status = 'pending' OR (r.status = 'ongoing' AND t1.volunteer_id = ?))
            OR (o.status = 'pending' OR (o.status = 'ongoing' AND t2.volunteer_id = ?))";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iiii", $userId, $userId, $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $civilians = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $civilians[] = [
                    'user_id' => $row['id'],
                    'lat' => $row['latitude'],
                    'lng' => $row['longitude']
                ];
            }
        }
        $stmt->close();
    }

    $data = ["volunteer" => $volunteer, "civilians" => $civilians];
    echo json_encode($data);
    $conn->close();
?>
