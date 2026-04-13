<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

// Fetch total members
$total_members_query = "SELECT COUNT(*) AS total_members FROM members";
$total_members_result = $conn->query($total_members_query);
$total_members = $total_members_result->fetch_assoc()["total_members"];

// Fetch pending membership requests
$pending_members_query = "SELECT COUNT(*) AS pending_members FROM members WHERE status = 'pending'";
$pending_members_result = $conn->query($pending_members_query);
$pending_members = $pending_members_result->fetch_assoc()["pending_members"];

// Fetch total events
$total_events_query = "SELECT COUNT(*) AS total_events FROM events";
$total_events_result = $conn->query($total_events_query);
$total_events = $total_events_result->fetch_assoc()["total_events"];

// Fetch latest events
$latest_events_query = "SELECT id, event_title, event_date FROM events ORDER BY event_date DESC LIMIT 5";
$latest_events_result = $conn->query($latest_events_query);

// Fetch financial contributions
$total_donations_query = "SELECT SUM(amount) AS total_donations FROM donations";
$total_donations_result = $conn->query($total_donations_query);
$total_donations = $total_donations_result->fetch_assoc()["total_donations"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Organization Management</title>
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
            display: block;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background: #f8f9fa;
        }
        .footer {
            position: fixed;
            left: 250px;
            bottom: 0;
            width: calc(100% - 250px);
            background: #343a40;
            color: white;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="sidebar text-center">
    <img src="images/logo.png" alt="Organization Logo" class="img-fluid mb-3" style="max-width: 150px;">
    <h4>Organization Panel</h4>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="manage_members.php">👥 Manage Members</a>
    <a href="events.php">📅 Events</a>
    <a href="donations.php">💰 Financial Reports</a>
    <a href="news.php">📰 News</a>
    <a href="logout.php" class="text-danger">🚪 Logout</a>
</div>

<div class="main-content">
    <h2>Welcome, <?php echo ucfirst($_SESSION["role"]); ?>!</h2>

    <?php if ($_SESSION["role"] == "officer") { ?>
        <div class="alert alert-info">Manage members, events, and financial records.</div>

        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Members</h5>
                        <p class="card-text display-5"><?php echo $total_members; ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Pending Requests</h5>
                        <p class="card-text display-5"><?php echo $pending_members; ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Events</h5>
                        <p class="card-text display-5"><?php echo $total_events; ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Donations</h5>
                        <p class="card-text display-5">$<?php echo number_format($total_donations, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5>Upcoming Events</h5>
            </div>
            <div class="card-body">
                <?php if ($latest_events_result->num_rows > 0) { ?>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Event Title</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($event = $latest_events_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event["event_title"]); ?></td>
                                    <td><?php echo date("M d, Y", strtotime($event["event_date"])); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p class="text-muted">No upcoming events.</p>
                <?php } ?>
            </div>
        </div>

    <?php } elseif ($_SESSION["role"] == "member") { ?>
        <div class="alert alert-success">Stay updated on organization news and events.</div>

        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <h5>Upcoming Events</h5>
            </div>
            <div class="card-body">
                <?php if ($latest_events_result->num_rows > 0) { ?>
                    <ul class="list-group">
                        <?php while ($event = $latest_events_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                <strong><?php echo htmlspecialchars($event["event_title"]); ?></strong> - 
                                <?php echo date("M d, Y", strtotime($event["event_date"])); ?>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p class="text-muted">No upcoming events.</p>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>

<div class="footer">
    &copy; <?php echo date("Y"); ?> Organization Management | All Rights Reserved
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
