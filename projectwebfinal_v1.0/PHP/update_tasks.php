<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$data = json_decode(file_get_contents('php://input'), true);

$volunteerId = $data['volunteerId'];
$taskId = $data['taskId'];
$taskType = $data['taskType'];

if (is_numeric($volunteerId) && is_numeric($taskId) && in_array($taskType, ['request', 'offer'])) {
    //check the current number of ongoing tasks for the volunteer
    $check_sql = "SELECT COUNT(*) AS task_count FROM tasks WHERE volunteer_id = ? AND status = 'ongoing'";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $volunteerId);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    $ongoingTasks = $row['task_count'];
    $check_stmt->close();

    if ($ongoingTasks < 4) {
        //check if the task has already been accepted or is ongoing
        $status_check_sql = $taskType == 'request' ? 
            "SELECT status FROM requests WHERE id = ?" : 
            "SELECT status FROM offers WHERE id = ?";

        $status_check_stmt = $conn->prepare($status_check_sql);
        $status_check_stmt->bind_param("i", $taskId);
        $status_check_stmt->execute();
        $result = $status_check_stmt->get_result();
        $row = $result->fetch_assoc();
        $status_check_stmt->close();

        if ($row && $row['status'] === 'ongoing') {
            $response = [
                'success' => false,
                'message' => 'Task has already been accepted by another volunteer or is ongoing.'
            ];
        } else {
            //check if the task already exists for this volunteer
            $exists_sql = "SELECT COUNT(*) AS task_exists FROM tasks WHERE volunteer_id = ? AND ";
            $exists_sql .= $taskType == 'request' ? "request_id = ?" : "offer_id = ?";

            $exists_stmt = $conn->prepare($exists_sql);
            $exists_stmt->bind_param("ii", $volunteerId, $taskId);
            $exists_stmt->execute();
            $result = $exists_stmt->get_result();
            $row = $result->fetch_assoc();
            $taskExists = $row['task_exists'];
            $exists_stmt->close();

            if ($taskExists > 0) {
                $response = [
                    'success' => false,
                    'message' => 'Task has already been accepted.'
                ];
            } else {
                // insert the new task if it doesn't already exist and is not ongoing
                try {
                    if ($taskType == 'request') {
                        $sql = "INSERT INTO tasks (volunteer_id, request_id, status) VALUES (?, ?, 'ongoing')";
                        $update_sql = "UPDATE requests SET status = 'ongoing', date_accepted = NOW() WHERE id = ?";
                    } else if ($taskType == 'offer') {
                        $sql = "INSERT INTO tasks (volunteer_id, offer_id, status) VALUES (?, ?, 'ongoing')";
                        $update_sql = "UPDATE offers SET status = 'ongoing', date_accepted = NOW() WHERE id = ?";
                    }

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $volunteerId, $taskId);
                    if (!$stmt->execute()) {
                        throw new mysqli_sql_exception('Database update failed: ' . $stmt->error);
                    }
                    $stmt->close();

                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("i", $taskId);
                    if (!$update_stmt->execute()) {
                        throw new mysqli_sql_exception('Database update failed: ' . $update_stmt->error);
                    }
                    $update_stmt->close();

                    $response = ['success' => true];
                } catch (mysqli_sql_exception $e) {
                    $response = [
                        'success' => false,
                        'message' => 'Database error: ' . $e->getMessage()
                    ];
                }
            }
        }
    } else {
        //maximum limit of ongoing tasks
        $response = [
            'success' => false,
            'message' => 'You cannot accept more than 4 ongoing tasks.'
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid input values.'
    ];
}

$conn->close();

echo json_encode($response);
?>
