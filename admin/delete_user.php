<?php
session_start();
include "../config/database.php";

if ($_SESSION["role"] != "admin") {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];
    $query = "DELETE FROM users WHERE id='$user_id'";

    if (mysqli_query($conn, $query)) {
        echo "User deleted successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<br>
<a href="manage_users.php">Back to User Management</a>
