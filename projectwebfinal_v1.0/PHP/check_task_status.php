<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";  // Ensure this file contains the necessary code to establish a database connection

header('Content-Type: application/json');

$volunteer_id = $_SESSION["user_id"];  // Get volunteer_id from session

// Updated SQL query with status priority logic
$sql = "
    SELECT 
        u.id AS user_id,
        IF(
            FIND_IN_SET('ongoing', GROUP_CONCAT(DISTINCT COALESCE(r.status, o.status) ORDER BY COALESCE(r.status, o.status) ASC)),
            'ongoing',
            IF(
                FIND_IN_SET('pending', GROUP_CONCAT(DISTINCT COALESCE(r.status, o.status) ORDER BY COALESCE(r.status, o.status) ASC)),
                'pending',
                'completed'
            )
        ) AS priority_status,
        GROUP_CONCAT(DISTINCT t.volunteer_id ORDER BY t.volunteer_id ASC) AS volunteer_ids,
        GROUP_CONCAT(DISTINCT r.id ORDER BY r.id ASC) AS request_ids,
        GROUP_CONCAT(DISTINCT o.id ORDER BY o.id ASC) AS offer_ids
    FROM users u
    LEFT JOIN requests r ON u.id = r.user_id
    LEFT JOIN offers o ON u.id = o.donator_id
    LEFT JOIN tasks t ON (
        (t.request_id = r.id OR t.offer_id = o.id) 
        AND t.volunteer_id = ?
    )
    WHERE 
        (r.status = 'pending' OR o.status = 'pending')
        OR ((r.status = 'ongoing' OR o.status = 'ongoing') AND t.volunteer_id IS NOT NULL)
        OR ((r.status = 'completed' OR o.status = 'completed') AND t.volunteer_id IS NOT NULL)
    GROUP BY u.id
    ORDER BY u.id;
";

// Prepare and execute the SQL statement
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('i', $volunteer_id);  // Bind the volunteer_id parameter
    $stmt->execute();
    $result = $stmt->get_result();

    $civilians = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $civilian = [
                'user_id' => $row['user_id'],
                'volunteer_id' => $row['volunteer_ids'],
                'status' => $row['priority_status'],
                'request_ids' => $row['request_ids'],
                'offer_ids' => $row['offer_ids']
            ];
            $civilians[] = $civilian;
        }
    }
    $stmt->close();
} else {
    // Error handling in case the SQL statement couldn't be prepared
    echo json_encode(["error" => "Failed to prepare SQL statement"]);
    $conn->close();
    exit();
}

// Output the results in JSON format
echo json_encode(["civilians" => $civilians]);

$conn->close();  // Close the database connection
?>
