<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";
include "includes/notifications.php";

$user_id = $_SESSION["user_id"];

// Handle mark as read
if (isset($_GET["mark_read"]) && is_numeric($_GET["mark_read"])) {
    markNotificationAsRead($conn, $_GET["mark_read"], $user_id);
    header("Location: notifications.php");
    exit();
}

// Handle mark all as read
if (isset($_GET["mark_all_read"])) {
    markAllNotificationsAsRead($conn, $user_id);
    header("Location: notifications.php");
    exit();
}

// Get all notifications
$notifications = getAllNotifications($conn, $user_id, 50);
$unread_count = getUnreadNotificationCount($conn, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - OFW Management System</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="alternate icon" href="images/favicon.svg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #343a40;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            text-align: left !important;
            display: block;
        }
        .sidebar a:hover {
            background: #495057ff;
        }
        .content {
            margin-left: 256px;
            padding: 20px;
            background: skyblue;
            min-height: 100vh;
        }
        body {
            background: skyblue;
        }
        .notification-item {
            border-left: 4px solid #007bff;
            background: white;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .notification-item.unread {
            border-left-color: #28a745;
            background: #f8fff9;
        }
        .notification-item.success {
            border-left-color: #28a745;
        }
        .notification-item.warning {
            border-left-color: #ffc107;
        }
        .notification-item.error {
            border-left-color: #dc3545;
        }
        .notification-badge {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            margin-left: 5px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar text-center">
    <img src="images/logo.png" alt="OFW Management Logo" class="img-fluid mb-0" style="max-width: 150px; border-radius: 0px; margin-top: -20px">
    <h4 class="text-center">OFW Management</h4>
    <a href="dashboard.php">🏠 Dashboard</a>
    <?php if ($_SESSION["role"] == "admin") { ?>
        <a href="manage_users.php">👤 Manage Users</a>
        <a href="manage_jobs.php">📄 Manage Jobs</a>
        <a href="update_news.php">📰 Update News</a>
        <a href="update_benefits.php">💰 Update Benefits</a>
        <a href="view_benefit_applications.php">📁 Benefits Applicants</a>
        <a href="manage_applications.php">📁 Manage Applicants</a>
    <?php } elseif ($_SESSION["role"] == "ofw") { ?>
        <a href="benefits.php">💰 Benefits</a>
        <a href="view_jobs.php">🔍 Jobs</a>
        <a href="news.php">📰 News</a>
        <a href="profile.php">📝 Update Profile</a>
    <?php } ?>
    <a href="notifications.php" class="fw-bold">
        🔔 Notifications
        <?php if ($unread_count > 0) { ?>
            <span class="notification-badge"><?= $unread_count; ?></span>
        <?php } ?>
    </a>
    <a href="logout.php" class="text-danger">🚪 Logout</a>
</div>

<!-- CONTENT -->
<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Notifications</h2>
        <?php if ($unread_count > 0) { ?>
            <a href="?mark_all_read=1" class="btn btn-outline-primary btn-sm">Mark All as Read</a>
        <?php } ?>
    </div>

    <?php if ($notifications->num_rows == 0) { ?>
        <div class="alert alert-info text-center">
            <h5>No notifications yet</h5>
            <p>You'll see notifications here when there are updates about your applications.</p>
        </div>
    <?php } else { ?>
        <?php while ($notification = $notifications->fetch_assoc()) { ?>
            <div class="notification-item <?= $notification['is_read'] ? '' : 'unread'; ?> <?= $notification['type']; ?> p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">
                            <?= htmlspecialchars($notification['title']); ?>
                            <?php if (!$notification['is_read']) { ?>
                                <span class="badge bg-success ms-2">New</span>
                            <?php } ?>
                        </h6>
                        <p class="mb-1"><?= htmlspecialchars($notification['message']); ?></p>
                        <small class="text-muted">
                            <?= date('M j, Y g:i A', strtotime($notification['created_at'])); ?>
                        </small>
                    </div>
                    <?php if (!$notification['is_read']) { ?>
                        <a href="?mark_read=<?= $notification['id']; ?>" class="btn btn-sm btn-outline-secondary ms-2">
                            Mark as Read
                        </a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>