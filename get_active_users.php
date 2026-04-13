<?php
session_start();

// Check if user is admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include "config/database.php";

// Get active users (active in last 5 minutes)
$five_minutes_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));

$query = "
    SELECT 
        u.id,
        u.name,
        u.email,
        u.role,
        u.status,
        u.profile_picture,
        ua.last_activity
    FROM user_activity ua
    INNER JOIN users u ON ua.user_id = u.id
    WHERE ua.last_activity >= ?
    ORDER BY ua.last_activity DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $five_minutes_ago);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    // Format the datetime to be JavaScript-friendly
    $last_activity = $row['last_activity'];
    
    $users[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'email' => $row['email'],
        'role' => $row['role'],
        'status' => $row['status'],
        'profile_picture' => $row['profile_picture'],
        'last_activity' => $last_activity
    ];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'users' => $users,
    'count' => count($users)
]);
