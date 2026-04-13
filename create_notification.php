<?php
// Helper function to create notifications
function createNotification($conn, $user_id, $type, $title, $message, $link = null) {
    $query = "INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $user_id, $type, $title, $message, $link);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Function to notify all OFW users about new content
function notifyAllOFWs($conn, $type, $title, $message, $link = null) {
    $query = "SELECT id FROM users WHERE role = 'ofw' AND status = 'approved'";
    $result = $conn->query($query);
    
    while ($user = $result->fetch_assoc()) {
        createNotification($conn, $user['id'], $type, $title, $message, $link);
    }
}
?>
