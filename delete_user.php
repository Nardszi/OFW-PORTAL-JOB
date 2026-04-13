<?php
session_start();
include "config/database.php"; // Ensure this connects to your database

// Check if admin is logged in
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    $_SESSION["error_message"] = "Unauthorized access!";
    header("Location: manage_users.php");
    exit();
}

// Check if user_id is provided
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["user_id"])) {
    $user_id = $_POST["user_id"];

    // Prevent self-deletion (optional but recommended)
    if ($user_id == $_SESSION["user_id"]) {
        $_SESSION["error_message"] = "You cannot delete your own account!";
        header("Location: manage_users.php");
        exit();
    }

    // Prepare delete statement
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $_SESSION["success_message"] = "User deleted successfully.";
    } else {
        $_SESSION["error_message"] = "Error deleting user: " . $conn->error;
    }

    $stmt->close();
} else {
    $_SESSION["error_message"] = "Invalid request.";
}

header("Location: manage_users.php");
exit();
?>
