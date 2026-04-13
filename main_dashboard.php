<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

// Initialize variables
$approved_users = 0;
$pending_users = 0;
$total_jobs = 0;

// Fetch stats only for admin to save resources
if ($_SESSION["role"] == "admin") {
    // Fetch approved users
    $approved_users_query = "SELECT COUNT(*) AS approved_users FROM users WHERE status = 'approved'";
    $approved_users_result = $conn->query($approved_users_query);
    $approved_users = $approved_users_result->fetch_assoc()["approved_users"];

    // Fetch pending users
    $pending_users_query = "SELECT COUNT(*) AS pending_users FROM users WHERE status = 'pending'";
    $pending_users_result = $conn->query($pending_users_query);
    $pending_users = $pending_users_result->fetch_assoc()["pending_users"];

    // Fetch total jobs
    $total_jobs_query = "SELECT COUNT(*) AS total_jobs FROM jobs";
    $total_jobs_result = $conn->query($total_jobs_query);
    $total_jobs = $total_jobs_result->fetch_assoc()["total_jobs"];
}

// Fetch latest job postings (visible to all roles)
$latest_jobs_query = "SELECT id, job_title, location, created_at FROM jobs ORDER BY created_at DESC LIMIT 5";
$latest_jobs_result = $conn->query($latest_jobs_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Dashboard - OFW Management System</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="alternate icon" href="images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Content section */
        .main-content {
            margin-left: 256px;
            padding: 20px;
            min-height: 100vh;
            background: url('images/wall234.jpg') no-repeat center center fixed;
            background-size: cover;
            padding-bottom: 60px; /* Space for footer */
            position: relative;
        }
        main::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }
        }
        /* Footer */
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
        /* Clickable cards hover effect */
        a.text-decoration-none .card {
            transition: transform 0.2s;
        }
        a.text-decoration-none .card:hover {
            transform: scale(1.05);
        }
        @media print {
            .main-content {
                margin-left: 0;
                padding: 20px;
                background: none;
            }
            .card {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <h2>Welcome to Main Dashboard, <?php echo ucfirst($_SESSION["role"]); ?>!</h2>
        <button class="btn btn-light" onclick="window.print()"><i class="bi bi-printer"></i> Print Page</button>
    </div>

    <?php if ($_SESSION["role"] == "admin") { ?>
        <div class="alert alert-primary d-print-none">Manage users, job listings, news, and benefits.</div>

        <!-- Admin Dashboard Stats -->
        <div class="row">
            <div class="col-md-4">
                <a href="manage_users.php?status=approved" class="text-decoration-none">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Approved Users</h5>
                            <p class="card-text display-5"><?php echo $approved_users; ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="manage_users.php?status=pending" class="text-decoration-none">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Pending Users</h5>
                            <p class="card-text display-5"><?php echo $pending_users; ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="manage_jobs.php" class="text-decoration-none">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total Jobs</h5>
                            <p class="card-text display-5"><?php echo $total_jobs; ?></p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    <?php } elseif ($_SESSION["role"] == "ofw") { ?>
        <div class="alert alert-success d-print-none">Browse job listings and manage your profile.</div>
    <?php } ?>

    <!-- Latest Jobs Section (Visible to All) -->
    <div class="card mt-4">
        <div class="card-header bg-success text-white">
            <h5>Latest Job Postings</h5>
        </div>
        <div class="card-body">
            <?php if ($latest_jobs_result && $latest_jobs_result->num_rows > 0) { ?>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Job Title</th>
                            <th>Location</th>
                            <th>Date Posted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($job = $latest_jobs_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($job["job_title"]); ?></td>
                                <td><?php echo htmlspecialchars($job["location"]); ?></td>
                                <td><?php echo date("M d, Y", strtotime($job["created_at"])); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p class="text-muted">No jobs added yet.</p>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer d-print-none">
    &copy; <?php echo date("Y"); ?> OFW Management System | All Rights Reserved
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>