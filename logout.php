<?php
session_start();
include "config/database.php";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $log_action = "Logout";
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    $log_stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    $log_stmt->bind_param("isss", $user_id, $log_action, $ip_address, $user_agent);
    $log_stmt->execute();
}

session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Clear Remember Me cookie
setcookie("remember_me", "", time() - 3600, "/");

// Ensure no output is sent before redirection
ob_start();
header("Location: index.php");
ob_end_flush();
exit();
?>
    