<?php
session_start();
include "config/database.php";

if ($_SESSION["role"] != "admin") {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];

    // Update the status to 'approved'
    $query = "UPDATE users SET status='approved' WHERE id=$user_id";
    if (mysqli_query($conn, $query)) {
        $_SESSION["success_message"] = "User approved successfully!";
    } else {
        $_SESSION["error_message"] = "Error approving user: " . mysqli_error($conn);
    }
}

header("Location: manage_users.php");
exit();
