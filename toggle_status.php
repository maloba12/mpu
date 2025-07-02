<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['id']) || !isset($_POST['status'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit();
    }

    $user_id = $_POST['id'];
    $status = $_POST['status'];

    try {
        // Update user status
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$status, $user_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "User status updated successfully!";
            $_SESSION['message_type'] = "success";
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
