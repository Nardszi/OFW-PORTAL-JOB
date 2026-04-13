<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<!-- Sidebar -->
<div class="d-flex flex-column bg-light" style="width: 250px; min-height: 100vh; position: fixed; left: 0; top: 0; bottom: 0;">
    <div class="text-center py-3   text-black">
        <h4>OFW System</h4>
    </div>
    <ul class="nav flex-column p-3">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link active">🏠 Dashboard</a>
        </li>

        <?php if ($_SESSION["role"] == "admin") { ?>
            <li><a href="manage_users.php" class="nav-link">👤 Manage Users</a></li>
            <li><a href="manage_jobs.php" class="nav-link">💼 Manage Jobs</a></li>
        <?php } elseif ($_SESSION["role"] == "ofw") { ?>
            <li><a href="view_jobs.php" class="nav-link">🔍 View Jobs</a></li>
            <li><a href="profile.php" class="nav-link">✏️ Update Profile</a></li>
            <li><a href="job_hiring.php" class="nav-link">📄 Job Hiring</a></li>
            <li><a href="agency_list.php" class="nav-link">🏢 Agencies</a></li>
        <?php } elseif ($_SESSION["role"] == "employer") { ?>
            <li><a href="post_job.php" class="nav-link">➕ Post Job</a></li>
            <li><a href="manage_applications.php" class="nav-link">📑 Manage Applications</a></li>
        <?php } ?>

        <li><a href="auth/logout.php" class="nav-link text-danger">🚪 Logout</a></li>
    </ul>
</div>
