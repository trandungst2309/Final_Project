<?php
include 'connect.php'; // Include your database connection file
session_start();

header('Content-Type: application/json'); // Set header for JSON response

$response = ['success' => false, 'message' => ''];

// 1. Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    $response['message'] = 'You must be logged in to delete a comment.';
    echo json_encode($response);
    exit();
}

// 2. Check if it's a POST request and comment_id is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_id'])) {
    $customer_id = $_SESSION['customer_id'];
    $comment_id = intval($_POST['comment_id']);

    if ($comment_id <= 0) {
        $response['message'] = 'Invalid comment ID.';
        echo json_encode($response);
        exit();
    }

    try {
        $conn = new Connect();
        $db_link = $conn->connectToPDO();

        // 3. Verify ownership: Check if the current user owns the comment
        $check_owner_query = "SELECT customer_id FROM `comment` WHERE comment_id = ?";
        $stmt_check = $db_link->prepare($check_owner_query);
        $stmt_check->execute([$comment_id]);
        $comment_owner_id = $stmt_check->fetchColumn();

        if ($comment_owner_id === false || $comment_owner_id != $customer_id) {
            $response['message'] = 'You are not authorized to delete this comment.';
            echo json_encode($response);
            exit();
        }

        // 4. Proceed with deletion
        $delete_query = "DELETE FROM `comment` WHERE comment_id = ? AND customer_id = ?";
        $stmt_delete = $db_link->prepare($delete_query);
        $stmt_delete->execute([$comment_id, $customer_id]);

        if ($stmt_delete->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Comment deleted successfully.';
        } else {
            $response['message'] = 'Comment not found or could not be deleted.';
        }

    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request.';
}

echo json_encode($response);
?>