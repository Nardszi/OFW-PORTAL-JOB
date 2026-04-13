<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

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

// Fetch job applicants (pending only)
$job_applicants_query = "SELECT COUNT(*) AS total_job_applicants FROM job_applications WHERE status = 'pending'";
$job_applicants_result = $conn->query($job_applicants_query);
$total_job_applicants = $job_applicants_result->fetch_assoc()["total_job_applicants"];

// Fetch benefits applicants (pending only)
$benefits_applicants_query = "SELECT COUNT(*) AS total_benefits_applicants FROM benefit_applications WHERE status = 'pending'";
$benefits_applicants_result = $conn->query($benefits_applicants_query);
$total_benefits_applicants = $benefits_applicants_result->fetch_assoc()["total_benefits_applicants"];

// Fetch latest job postings
$latest_jobs_query = "SELECT id, job_title, company_name, location, preferred_sex, salary, image, created_at FROM jobs ORDER BY created_at DESC LIMIT 6";
$latest_jobs_result = $conn->query($latest_jobs_query);

// Check if the query executed successfully
if (!$latest_jobs_result) {
    die("Error fetching jobs: " . $conn->error);
}

// Fetch OFW Applications (if role is OFW)
$my_applications_result = null;
$completion_percentage = 0;
$news_count = 0;
if (isset($_SESSION["role"]) && $_SESSION["role"] == "ofw") {
    $user_id = $_SESSION["user_id"];
    $app_query = "SELECT ba.status, ba.applied_at, b.title, ba.application_type 
                  FROM benefit_applications ba 
                  JOIN benefits b ON ba.benefit_id = b.id 
                  WHERE ba.user_id = ? 
                  ORDER BY ba.applied_at DESC LIMIT 5";
    $stmt = $conn->prepare($app_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $my_applications_result = $stmt->get_result();

    // Fetch available benefits count
    $benefits_count_query = "SELECT COUNT(*) as count FROM benefits WHERE expiration_date IS NULL OR expiration_date >= CURDATE()";
    $benefits_count_result = $conn->query($benefits_count_query);
    $available_benefits_count = $benefits_count_result->fetch_assoc()['count'];

    // Fetch news count from database (excluding expired)
    $news_count_query = "SELECT COUNT(*) as count FROM news WHERE expiration_date IS NULL OR expiration_date >= CURDATE()";
    $news_count_result = $conn->query($news_count_query);
    $news_count = $news_count_result->fetch_assoc()['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - OFW Management System</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="alternate icon" href="images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: url('images/wall234.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="lg:ml-64 p-4 lg:p-6 min-h-screen pt-20 lg:pt-8">
    <!-- Welcome Banner -->
    <header class="bg-white/95 p-4 md:p-6 rounded-xl md:rounded-2xl mb-6 md:mb-8 shadow-lg border-l-4 border-blue-600 flex flex-col md:flex-row justify-between items-start md:items-center gap-3 md:gap-4">
        <div>
            <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-800 mb-1">Welcome back, <?php echo ucfirst($_SESSION["role"]); ?>! 👋</h2>
            <p class="text-sm md:text-base text-gray-600">Here is your dashboard overview.</p>
        </div>
        <div class="w-full md:w-auto">
            <span class="inline-flex items-center px-3 md:px-4 py-2 bg-white text-blue-600 border border-blue-600 rounded-full shadow-sm text-xs md:text-sm">
                <i class="bi bi-calendar-event mr-2"></i><?= date("F d, Y") ?>
            </span>
        </div>
    </header>

    <?php if ($_SESSION["role"] == "admin") { ?>
        <!-- Admin Dashboard Stats -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4 mb-6 md:mb-8" aria-label="Admin Statistics">
            <!-- Approved Users Card -->
            <a href="manage_users.php?status=approved" class="block group">
                <div class="bg-blue-600 text-white rounded-lg md:rounded-xl shadow-lg p-4 md:p-6 h-full transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                    <div class="flex justify-between items-center">
                        <div>
                            <h6 class="text-xs uppercase mb-1 md:mb-2 opacity-75">Approved Users</h6>
                            <p class="text-2xl md:text-3xl lg:text-4xl font-bold"><?php echo $approved_users; ?></p>
                        </div>
                        <i class="bi bi-person-check-fill text-3xl md:text-4xl lg:text-5xl opacity-50"></i>
                    </div>
                </div>
            </a>

            <!-- Pending Users Card -->
            <a href="manage_users.php?status=pending" class="block group">
                <div class="bg-red-600 text-white rounded-lg md:rounded-xl shadow-lg p-4 md:p-6 h-full transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                    <div class="flex justify-between items-center">
                        <div>
                            <h6 class="text-xs uppercase mb-1 md:mb-2 opacity-75">Pending Users</h6>
                            <p class="text-2xl md:text-3xl lg:text-4xl font-bold"><?php echo $pending_users; ?></p>
                        </div>
                        <i class="bi bi-person-exclamation text-3xl md:text-4xl lg:text-5xl opacity-50"></i>
                    </div>
                </div>
            </a>

            <!-- Total Jobs Card -->
            <a href="manage_jobs.php" class="block group">
                <div class="bg-yellow-500 text-white rounded-lg md:rounded-xl shadow-lg p-4 md:p-6 h-full transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                    <div class="flex justify-between items-center">
                        <div>
                            <h6 class="text-xs uppercase mb-1 md:mb-2 opacity-75">Total Jobs</h6>
                            <p class="text-2xl md:text-3xl lg:text-4xl font-bold"><?php echo $total_jobs; ?></p>
                        </div>
                        <i class="bi bi-briefcase-fill text-3xl md:text-4xl lg:text-5xl opacity-50"></i>
                    </div>
                </div>
            </a>

            <!-- Job Applicants Card -->
            <a href="manage_applications.php" class="block group">
                <div class="bg-green-600 text-white rounded-lg md:rounded-xl shadow-lg p-4 md:p-6 h-full transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                    <div class="flex justify-between items-center">
                        <div>
                            <h6 class="text-xs uppercase mb-1 md:mb-2 opacity-75">Job Applicants</h6>
                            <p class="text-2xl md:text-3xl lg:text-4xl font-bold"><?php echo $total_job_applicants; ?></p>
                        </div>
                        <i class="bi bi-file-earmark-person-fill text-3xl md:text-4xl lg:text-5xl opacity-50"></i>
                    </div>
                </div>
            </a>

            <!-- Benefits Applicants Card -->
            <a href="view_benefit_applications.php" class="block group">
                <div class="bg-cyan-500 text-white rounded-lg md:rounded-xl shadow-lg p-4 md:p-6 h-full transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                    <div class="flex justify-between items-center">
                        <div>
                            <h6 class="text-xs uppercase mb-1 md:mb-2 opacity-75">Benefits Applicants</h6>
                            <p class="text-2xl md:text-3xl lg:text-4xl font-bold"><?php echo $total_benefits_applicants; ?></p>
                        </div>
                        <i class="bi bi-heart-pulse-fill text-3xl md:text-4xl lg:text-5xl opacity-50"></i>
                    </div>
                </div>
            </a>
        </section>

        <!-- Latest Jobs Section -->
        <section class="bg-white rounded-xl shadow-lg overflow-hidden" aria-label="Latest Jobs">
            <div class="bg-green-600 text-white px-6 py-4">
                <h5 class="text-xl font-bold">Latest Job Postings</h5>
            </div>
            <div class="p-6">
                <?php if ($latest_jobs_result->num_rows > 0) { ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while ($job = $latest_jobs_result->fetch_assoc()) { ?>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden cursor-pointer transition-all duration-300 hover:shadow-xl hover:-translate-y-1" 
                                 onclick="openJobModal('<?= htmlspecialchars($job['job_title'], ENT_QUOTES) ?>', 
                                                       '<?= htmlspecialchars($job['company_name'], ENT_QUOTES) ?>', 
                                                       '<?= htmlspecialchars($job['location'], ENT_QUOTES) ?>', 
                                                       '<?= htmlspecialchars($job['salary'], ENT_QUOTES) ?>', 
                                                       '<?= htmlspecialchars($job['preferred_sex'], ENT_QUOTES) ?>', 
                                                       '<?= !empty($job['image']) ? 'uploads/'.htmlspecialchars($job['image'], ENT_QUOTES) : '' ?>')">
                                <?php if (!empty($job["image"])) { ?>
                                    <img src="uploads/<?= htmlspecialchars($job["image"]); ?>" class="w-full h-48 object-cover" alt="Job Image">
                                <?php } else { ?>
                                    <div class="w-full h-48 bg-gray-300 flex items-center justify-center text-gray-600">
                                        <span>No Image</span>
                                    </div>
                                <?php } ?>
                                <div class="p-4">
                                    <h5 class="font-bold text-lg truncate"><?= htmlspecialchars($job["job_title"]); ?></h5>
                                    <p class="text-sm text-gray-600 mt-2"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($job["location"]); ?></p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <p class="text-gray-500">No jobs added yet.</p>
                <?php } ?>
            </div>
        </section>

    <?php } elseif ($_SESSION["role"] == "ofw") { ?>
        <!-- OFW Dashboard -->
        
        <!-- Quick Stats Cards -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- Available Benefits Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl shadow-lg p-6 transform transition hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Available Benefits</p>
                        <h3 class="text-3xl font-bold"><?= $available_benefits_count ?></h3>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="bi bi-gift-fill text-3xl"></i>
                    </div>
                </div>
                <a href="benefits.php" class="mt-4 inline-flex items-center text-sm font-semibold hover:underline">
                    View All <i class="bi bi-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Available Jobs Card -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-2xl shadow-lg p-6 transform transition hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Available Jobs</p>
                        <h3 class="text-3xl font-bold"><?= $total_jobs ?></h3>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="bi bi-briefcase-fill text-3xl"></i>
                    </div>
                </div>
                <a href="view_jobs.php" class="mt-4 inline-flex items-center text-sm font-semibold hover:underline">
                    Browse Jobs <i class="bi bi-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Latest News Card -->
            <div class="bg-gradient-to-br from-yellow-500 to-orange-500 text-white rounded-2xl shadow-lg p-6 transform transition hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Latest Updates</p>
                        <h3 class="text-3xl font-bold"><?= $news_count ?></h3>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="bi bi-newspaper text-3xl"></i>
                    </div>
                </div>
                <a href="news.php" class="mt-4 inline-flex items-center text-sm font-semibold hover:underline">
                    Read News <i class="bi bi-arrow-right ml-1"></i>
                </a>
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Main Content (Left & Center - 2 columns) -->
            <div class="xl:col-span-2 space-y-6">
                <!-- My Benefit Applications Section -->
                <section class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 flex justify-between items-center">
                        <h5 class="text-lg font-bold flex items-center">
                            <i class="bi bi-file-earmark-text mr-2"></i>My Benefit Applications
                        </h5>
                        <a href="benefits.php" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-lg font-semibold text-sm transition">
                            View All
                        </a>
                    </div>
                    <div class="p-0">
                        <?php if ($my_applications_result && $my_applications_result->num_rows > 0) { ?>
                            <!-- Mobile/Tablet Card View -->
                            <div class="divide-y divide-gray-200">
                                <?php 
                                $my_applications_result->data_seek(0);
                                while ($app = $my_applications_result->fetch_assoc()) { 
                                    $status = strtolower($app['status']);
                                    $statusConfig = [
                                        'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'bi-check-circle-fill'],
                                        'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'bi-clock-history'],
                                        'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'bi-x-circle-fill']
                                    ];
                                    $config = $statusConfig[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'bi-info-circle'];
                                ?>
                                    <div class="p-4 hover:bg-gray-50 transition">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <h6 class="font-bold text-gray-900 mb-1"><?= htmlspecialchars($app['title']) ?></h6>
                                                <p class="text-sm text-gray-600">
                                                    <i class="bi bi-tag mr-1"></i><?= htmlspecialchars($app['application_type']) ?>
                                                </p>
                                            </div>
                                            <span class="<?= $config['bg'] ?> <?= $config['text'] ?> px-3 py-1 text-xs font-semibold rounded-full flex items-center ml-3">
                                                <i class="bi <?= $config['icon'] ?> mr-1"></i><?= ucfirst($status) ?>
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            <i class="bi bi-calendar3 mr-1"></i>Applied on <?= date("F d, Y", strtotime($app['applied_at'])) ?>
                                        </p>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="text-center py-16">
                                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                                    <i class="bi bi-inbox text-gray-400 text-4xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium mb-2">No applications yet</p>
                                <p class="text-sm text-gray-400 mb-4">Start applying for benefits to see them here</p>
                                <a href="benefits.php" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                                    <i class="bi bi-plus-circle mr-2"></i>Apply for Benefits
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </section>

                <!-- Latest Job Postings Section -->
                <section class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-800 to-gray-900 text-white px-6 py-4 flex justify-between items-center">
                        <h5 class="text-lg font-bold flex items-center">
                            <i class="bi bi-briefcase-fill mr-2"></i>Latest Job Postings
                        </h5>
                        <a href="view_jobs.php" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-lg font-semibold text-sm transition">
                            View All
                        </a>
                    </div>
                    <div class="p-6">
                        <?php if ($latest_jobs_result->num_rows > 0) { ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <?php mysqli_data_seek($latest_jobs_result, 0); ?>
                                <?php 
                                $count = 0;
                                while ($job = $latest_jobs_result->fetch_assoc()) { 
                                    if ($count >= 4) break; // Show only 4 jobs
                                    $count++;
                                ?>
                                    <div class="group bg-white border border-gray-200 rounded-xl overflow-hidden cursor-pointer transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-blue-500" 
                                         onclick="window.location.href='view_jobs.php'">
                                        <?php if (!empty($job["image"])) { ?>
                                            <div class="relative overflow-hidden h-40">
                                                <img src="uploads/<?= htmlspecialchars($job["image"]); ?>" 
                                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" 
                                                     alt="<?= htmlspecialchars($job["job_title"]); ?>">
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="w-full h-40 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                                <i class="bi bi-briefcase text-gray-400 text-4xl"></i>
                                            </div>
                                        <?php } ?>
                                        <div class="p-4">
                                            <h6 class="font-bold text-base text-gray-900 mb-2 line-clamp-1 group-hover:text-blue-600 transition">
                                                <?= htmlspecialchars($job["job_title"]); ?>
                                            </h6>
                                            <p class="text-sm text-gray-600 mb-1 flex items-center">
                                                <i class="bi bi-building mr-2 text-gray-400"></i>
                                                <span class="truncate"><?= htmlspecialchars($job["company_name"]); ?></span>
                                            </p>
                                            <p class="text-sm text-gray-600 flex items-center">
                                                <i class="bi bi-geo-alt mr-2 text-gray-400"></i>
                                                <span class="truncate"><?= htmlspecialchars($job["location"]); ?></span>
                                            </p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="text-center py-12">
                                <i class="bi bi-briefcase text-gray-300 text-5xl mb-3"></i>
                                <p class="text-gray-500">No jobs available at the moment</p>
                            </div>
                        <?php } ?>
                    </div>
                </section>
            </div>

            <!-- Sidebar (Right - 1 column) -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <section class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
                    <h5 class="text-lg font-bold mb-4 flex items-center">
                        <i class="bi bi-lightning-charge-fill mr-2"></i>Quick Actions
                    </h5>
                    <div class="space-y-3">
                        <a href="benefits.php" class="flex items-center p-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg transition group">
                            <div class="bg-white/20 rounded-lg p-2 mr-3">
                                <i class="bi bi-heart-pulse-fill text-xl"></i>
                            </div>
                            <span class="font-semibold">Apply for Benefits</span>
                            <i class="bi bi-arrow-right ml-auto group-hover:translate-x-1 transition"></i>
                        </a>
                        <a href="view_jobs.php" class="flex items-center p-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg transition group">
                            <div class="bg-white/20 rounded-lg p-2 mr-3">
                                <i class="bi bi-search text-xl"></i>
                            </div>
                            <span class="font-semibold">Browse Jobs</span>
                            <i class="bi bi-arrow-right ml-auto group-hover:translate-x-1 transition"></i>
                        </a>
                        <a href="profile.php" class="flex items-center p-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg transition group">
                            <div class="bg-white/20 rounded-lg p-2 mr-3">
                                <i class="bi bi-person-fill text-xl"></i>
                            </div>
                            <span class="font-semibold">Update Profile</span>
                            <i class="bi bi-arrow-right ml-auto group-hover:translate-x-1 transition"></i>
                        </a>
                    </div>
                </section>
            </div>
        </div>
    <?php } ?>
</main>

<!-- Footer -->
<footer class="lg:ml-64 bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white mt-12">
    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-bold mb-4 flex items-center">
                    <i class="bi bi-link-45deg mr-2 text-blue-400"></i>Quick Links
                </h4>
                <ul class="space-y-2">
                    <li>
                        <a href="dashboard.php" class="text-gray-400 hover:text-white transition-colors duration-200 flex items-center group">
                            <i class="bi bi-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="view_jobs.php" class="text-gray-400 hover:text-white transition-colors duration-200 flex items-center group">
                            <i class="bi bi-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                            Browse Jobs
                        </a>
                    </li>
                    <li>
                        <a href="benefits.php" class="text-gray-400 hover:text-white transition-colors duration-200 flex items-center group">
                            <i class="bi bi-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                            Benefits
                        </a>
                    </li>
                    <li>
                        <a href="news.php" class="text-gray-400 hover:text-white transition-colors duration-200 flex items-center group">
                            <i class="bi bi-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                            News & Updates
                        </a>
                    </li>
                    <li>
                        <a href="profile.php" class="text-gray-400 hover:text-white transition-colors duration-200 flex items-center group">
                            <i class="bi bi-chevron-right text-xs mr-2 group-hover:translate-x-1 transition-transform"></i>
                            My Profile
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="text-lg font-bold mb-4 flex items-center">
                    <i class="bi bi-envelope mr-2 text-blue-400"></i>Contact Us
                </h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start text-gray-400">
                        <i class="bi bi-geo-alt-fill mr-3 text-blue-400 mt-1"></i>
                        <span>123 OFW Street, Manila, Philippines</span>
                    </li>
                    <li class="flex items-center text-gray-400">
                        <i class="bi bi-telephone-fill mr-3 text-blue-400"></i>
                        <span>+63 123 456 7890</span>
                    </li>
                    <li class="flex items-center text-gray-400">
                        <i class="bi bi-envelope-fill mr-3 text-blue-400"></i>
                        <span>support@ofwmanagement.ph</span>
                    </li>
                    <li class="flex items-center text-gray-400">
                        <i class="bi bi-clock-fill mr-3 text-blue-400"></i>
                        <span>Mon - Fri: 8:00 AM - 5:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-700 pt-6">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <!-- Copyright -->
                <div class="text-gray-400 text-sm text-center md:text-left">
                    <p>&copy; <?php echo date("Y"); ?> OFW Management System. All Rights Reserved.</p>
                    <p class="text-xs mt-1">Developed with <i class="bi bi-heart-fill text-red-500"></i> for Filipino Workers</p>
                </div>

                <!-- Footer Links -->
                <div class="flex space-x-6 text-sm">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Privacy Policy</a>
                    <span class="text-gray-600">|</span>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Terms of Service</a>
                    <span class="text-gray-600">|</span>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Help Center</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop" class="fixed bottom-8 right-8 bg-blue-600 hover:bg-blue-700 text-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center transition-all duration-300 opacity-0 invisible hover:scale-110 z-50">
        <i class="bi bi-arrow-up text-xl"></i>
    </button>
</footer>

<script>
// Scroll to Top functionality
const scrollToTopBtn = document.getElementById('scrollToTop');

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        scrollToTopBtn.classList.remove('opacity-0', 'invisible');
        scrollToTopBtn.classList.add('opacity-100', 'visible');
    } else {
        scrollToTopBtn.classList.add('opacity-0', 'invisible');
        scrollToTopBtn.classList.remove('opacity-100', 'visible');
    }
});

scrollToTopBtn.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});
</script>

<!-- Job Details Modal -->
<div id="jobDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
            <h5 class="text-xl font-bold" id="modalJobTitle">Job Details</h5>
            <button onclick="closeJobModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <div class="p-6">
            <img id="modalJobImage" src="" class="w-full h-64 object-cover rounded-lg mb-4 hidden">
            <h4 id="modalJobTitleHeader" class="text-2xl font-bold mb-4"></h4>
            <div class="space-y-3">
                <p class="flex items-center"><strong class="w-32"><i class="bi bi-building mr-2"></i>Company:</strong> <span id="modalCompany" class="text-gray-700"></span></p>
                <p class="flex items-center"><strong class="w-32"><i class="bi bi-geo-alt mr-2"></i>Location:</strong> <span id="modalLocation" class="text-gray-700"></span></p>
                <p class="flex items-center"><strong class="w-32"><i class="bi bi-cash mr-2"></i>Salary:</strong> <span id="modalSalary" class="text-gray-700"></span></p>
                <p class="flex items-center"><strong class="w-32"><i class="bi bi-gender-ambiguous mr-2"></i>Preferred Sex:</strong> <span id="modalSex" class="text-gray-700"></span></p>
            </div>
        </div>
        <div class="border-t px-6 py-4 flex justify-end">
            <button onclick="closeJobModal()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">Close</button>
        </div>
    </div>
</div>

<script>
function openJobModal(title, company, location, salary, sex, image) {
    document.getElementById('modalJobTitle').innerText = title;
    document.getElementById('modalJobTitleHeader').innerText = title;
    document.getElementById('modalCompany').innerText = company;
    document.getElementById('modalLocation').innerText = location;
    document.getElementById('modalSalary').innerText = salary;
    document.getElementById('modalSex').innerText = sex;
    
    const img = document.getElementById('modalJobImage');
    if (image) {
        img.src = image;
        img.classList.remove('hidden');
    } else {
        img.classList.add('hidden');
    }
    
    document.getElementById('jobDetailsModal').classList.remove('hidden');
}

function closeJobModal() {
    document.getElementById('jobDetailsModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('jobDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeJobModal();
    }
});
</script>

</body>
</html>
