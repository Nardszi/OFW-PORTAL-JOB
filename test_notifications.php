<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    die("Please login first");
}

include "config/database.php";
include "includes/notifications.php";

echo "<h2>Notification System Test</h2>";

// Check if notifications table exists
$table_check = $conn->query("SHOW TABLES LIKE 'notifications'");
if ($table_check->num_rows == 0) {
    echo "<p style='color: red;'>❌ Notifications table does NOT exist!</p>";
    echo "<p>Run this SQL to create it:</p>";
    echo "<pre>";
    echo file_get_contents('create_notifications_table.sql');
    echo "</pre>";
} else {
    echo "<p style='color: green;'>✅ Notifications table exists!</p>";
    
    // Show table structure
    echo "<h3>Table Structure:</h3>";
    $structure = $conn->query("DESCRIBE notifications");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Count notifications for current user
    $user_id = $_SESSION["user_id"];
    $count_query = "SELECT COUNT(*) as total, 
                    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread 
                    FROM notifications WHERE user_id = ?";
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $counts = $result->fetch_assoc();
    $stmt->close();
    
    echo "<h3>Your Notifications:</h3>";
    echo "<p>Total: {$counts['total']}</p>";
    echo "<p>Unread: {$counts['unread']}</p>";
    
    // Show recent notifications
    echo "<h3>Recent Notifications:</h3>";
    $notifs = getAllNotifications($conn, $user_id, 10);
    if ($notifs->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Type</th><th>Title</th><th>Message</th><th>Read</th><th>Created</th></tr>";
        while ($notif = $notifs->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$notif['id']}</td>";
            echo "<td>{$notif['type']}</td>";
            echo "<td>{$notif['title']}</td>";
            echo "<td>{$notif['message']}</td>";
            echo "<td>" . ($notif['is_read'] ? 'Yes' : 'No') . "</td>";
            echo "<td>{$notif['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No notifications found.</p>";
    }
    
    // Test creating a notification
    echo "<h3>Test Notification Creation:</h3>";
    echo "<form method='POST'>";
    echo "<button type='submit' name='create_test'>Create Test Notification</button>";
    echo "</form>";
    
    if (isset($_POST['create_test'])) {
        $result = createNotification($conn, $user_id, 'info', 'Test Notification', 'This is a test notification message', 'dashboard.php');
        if ($result) {
            echo "<p style='color: green;'>✅ Test notification created successfully!</p>";
            echo "<script>setTimeout(() => window.location.reload(), 1000);</script>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create test notification</p>";
        }
    }
}

$conn->close();
?>
