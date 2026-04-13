<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include "config/database.php";

$user_id = $_SESSION["user_id"];
$notification_id = isset($_POST['notification_id']) ? intval($_POST['notification_id']) : 0;
$mark_all = isset($_POST['mark_all']) ? $_POST['mark_all'] : 0;

if ($mark_all) {
    // Mark all notifications as read
    $query = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
} elseif ($notification_id > 0) {
    // Mark single notification as read
    $query = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $notification_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

header('Content-Type: application/json');
echo json_encode(['success' => true]);
