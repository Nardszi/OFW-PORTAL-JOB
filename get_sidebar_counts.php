<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

include "config/database.php";

$counts = [];

if ($_SESSION["role"] == "admin") {
    // Admin counts
    $approved_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'approved'")->fetch_assoc()['count'];
    $pending_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'pending'")->fetch_assoc()['count'];
    $counts['total_users'] = $approved_users + $pending_users;
    
    $counts['jobs'] = $conn->query("SELECT COUNT(*) as count FROM jobs")->fetch_assoc()['count'];
    $counts['news'] = $conn->query("SELECT COUNT(*) as count FROM news")->fetch_assoc()['count'];
    $counts['benefits'] = $conn->query("SELECT COUNT(*) as count FROM benefits")->fetch_assoc()['count'];
    
    // Only count pending applications for benefits that still exist
    $counts['benefit_applications'] = $conn->query("
        SELECT COUNT(*) as count 
        FROM benefit_applications ba
        INNER JOIN benefits b ON ba.benefit_id = b.id
        WHERE ba.status = 'pending'
    ")->fetch_assoc()['count'];
    
    $counts['job_applications'] = $conn->query("SELECT COUNT(*) as count FROM job_applications WHERE status = 'pending'")->fetch_assoc()['count'];
    
    // Active users count
    $five_minutes_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));
    $counts['active_users'] = $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM user_activity WHERE last_activity >= '$five_minutes_ago'")->fetch_assoc()['count'];
    
} elseif ($_SESSION["role"] == "ofw") {
    // OFW counts
    $counts['available_benefits'] = $conn->query("SELECT COUNT(*) as count FROM benefits")->fetch_assoc()['count'];
    $counts['available_jobs'] = $conn->query("SELECT COUNT(*) as count FROM jobs")->fetch_assoc()['count'];
    $counts['news_ofw'] = $conn->query("SELECT COUNT(*) as count FROM news")->fetch_assoc()['count'];
}

echo json_encode([
    'success' => true,
    'counts' => $counts
]);
?>
