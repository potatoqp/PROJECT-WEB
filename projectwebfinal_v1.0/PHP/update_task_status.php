<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$data = json_decode(file_get_contents('php://input'), true);

$taskId = $data['task_id'];
$newStatus = $data['status'];

if (is_numeric($taskId) && in_array($newStatus, ['completed', 'canceled'])) {
    try {
        if ($newStatus === 'completed') {
            $dateCompleted = date('Y-m-d H:i:s');

            $update_task_sql = "UPDATE tasks SET status = 'completed' WHERE id = ?";
            $update_task_stmt = $conn->prepare($update_task_sql);
            $update_task_stmt->bind_param("i", $taskId);

            if (!$update_task_stmt->execute()) {
                throw new mysqli_sql_exception('Failed to update task: ' . $update_task_stmt->error);
            }
            $update_task_stmt->close();

            //check if task is related to a request or offer
            $check_sql = "SELECT request_id, offer_id FROM tasks WHERE id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("i", $taskId);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            $row = $result->fetch_assoc();
            $check_stmt->close();

            if ($row) {
                if ($row['request_id']) {
                    $update_request_sql = "UPDATE requests SET status = 'completed', date_completed = ? WHERE id = ?";
                    $update_request_stmt = $conn->prepare($update_request_sql);
                    $update_request_stmt->bind_param("si", $dateCompleted, $row['request_id']);
                    
                    if (!$update_request_stmt->execute()) {
                        throw new mysqli_sql_exception('Failed to update request: ' . $update_request_stmt->error);
                    }
                    $update_request_stmt->close();
                } elseif ($row['offer_id']) {
                    $update_offer_sql = "UPDATE offers SET status = 'completed', date_completed = ? WHERE id = ?";
                    $update_offer_stmt = $conn->prepare($update_offer_sql);
                    $update_offer_stmt->bind_param("si", $dateCompleted, $row['offer_id']);
                    
                    if (!$update_offer_stmt->execute()) {
                        throw new mysqli_sql_exception('Failed to update offer: ' . $update_offer_stmt->error);
                    }
                    $update_offer_stmt->close();
                }
            } else {
                throw new mysqli_sql_exception('Related task record not found.');
            }

            $response = ['success' => true];

        } elseif ($newStatus === 'canceled') {
            //check if the task exists
            $check_task_sql = "SELECT request_id, offer_id FROM tasks WHERE id = ?";
            $check_task_stmt = $conn->prepare($check_task_sql);
            $check_task_stmt->bind_param("i", $taskId);
            $check_task_stmt->execute();
            $result = $check_task_stmt->get_result();
            $row = $result->fetch_assoc();
            $check_task_stmt->close();

            if ($row) {
                $delete_task_sql = "DELETE FROM tasks WHERE id = ?";
                $delete_task_stmt = $conn->prepare($delete_task_sql);
                $delete_task_stmt->bind_param("i", $taskId);

                if (!$delete_task_stmt->execute()) {
                    throw new mysqli_sql_exception('Failed to delete task: ' . $delete_task_stmt->error);
                }
                $delete_task_stmt->close();

                if ($row['request_id']) {
                    $update_request_sql = "UPDATE requests SET status = 'pending', date_accepted = NULL, date_completed = NULL WHERE id = ?";
                    $update_request_stmt = $conn->prepare($update_request_sql);
                    $update_request_stmt->bind_param("i", $row['request_id']);
                    
                    if (!$update_request_stmt->execute()) {
                        throw new mysqli_sql_exception('Failed to update request: ' . $update_request_stmt->error);
                    }
                    $update_request_stmt->close();
                } elseif ($row['offer_id']) {
                    $update_offer_sql = "UPDATE offers SET status = 'pending', date_accepted = NULL, date_completed = NULL WHERE id = ?";
                    $update_offer_stmt = $conn->prepare($update_offer_sql);
                    $update_offer_stmt->bind_param("i", $row['offer_id']);
                    
                    if (!$update_offer_stmt->execute()) {
                        throw new mysqli_sql_exception('Failed to update offer: ' . $update_offer_stmt->error);
                    }
                    $update_offer_stmt->close();
                }
            } else {
                throw new mysqli_sql_exception('Task record not found.');
            }

            $response = ['success' => true];
        }

    } catch (mysqli_sql_exception $e) {
        $response = [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
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
