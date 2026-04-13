<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Mobile Menu Button -->
<button id="mobile-sidebar-toggle" 
        class="lg:hidden fixed top-4 left-4 z-[60] bg-gray-800 text-white p-3 rounded-lg shadow-lg hover:bg-gray-700 transition-all"
        aria-label="Toggle sidebar menu">
    <i class="bi bi-list text-2xl"></i>
</button>

<!-- Sidebar Overlay for Mobile -->
<div id="sidebar-overlay" 
     class="hidden lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40"
     onclick="closeMobileSidebar()"></div>

<!-- Sidebar with HTML5 and Tailwind CSS -->
<aside id="sidebar" class="fixed left-0 top-0 h-screen w-64 bg-gradient-to-b from-gray-800 to-gray-900 text-white shadow-2xl z-50 flex flex-col print:hidden transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out overflow-hidden">
    <!-- Logo Section -->
    <header class="flex-shrink-0 text-center p-6 border-b border-gray-700">
        <button onclick="closeMobileSidebar()" 
                class="lg:hidden absolute top-4 right-4 text-white/70 hover:text-white text-2xl"
                aria-label="Close sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
        <a href="index.php" class="block hover:opacity-80 transition-opacity cursor-pointer">
            <img src="images/logo5.png" alt="OFW Management Logo" class="h-24 w-auto mx-auto mb-3 shadow-lg">
            <h1 class="text-xl font-bold tracking-wide">OFW Management</h1>
        </a>
    </header>

    <!-- Active User Profile Section -->
    <?php
    // Fetch current user details
    if (isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
        $user_query = "SELECT name, email, profile_picture, role FROM users WHERE id = ?";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $current_user = $user_result->fetch_assoc();
        $user_stmt->close();
    ?>
    <div class="flex-shrink-0 p-4 border-b border-gray-700 bg-gray-800/50">
        <a href="profile.php" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-white/10 transition-all duration-200 group">
            <?php 
            // Display profile picture or avatar
            if (!empty($current_user['profile_picture']) && file_exists($current_user['profile_picture'])) {
                echo '<img src="' . htmlspecialchars($current_user['profile_picture']) . '" 
                      class="w-12 h-12 rounded-full object-cover border-2 border-blue-400 shadow-lg group-hover:border-blue-300 transition-all" 
                      alt="' . htmlspecialchars($current_user['name']) . '"
                      onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
                echo '<div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg border-2 border-blue-400 shadow-lg hidden">
                      ' . strtoupper(substr($current_user['name'], 0, 1)) . '
                      </div>';
            } else {
                echo '<div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg border-2 border-blue-400 shadow-lg group-hover:border-blue-300 transition-all">
                      ' . strtoupper(substr($current_user['name'], 0, 1)) . '
                      </div>';
            }
            ?>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate group-hover:text-blue-300 transition-colors">
                    <?= htmlspecialchars($current_user['name']) ?>
                </p>
                <p class="text-xs text-gray-400 truncate">
                    <?= htmlspecialchars($current_user['email']) ?>
                </p>
                <span class="inline-flex items-center px-2 py-0.5 mt-1 text-xs font-medium rounded-full <?= $current_user['role'] == 'admin' ? 'bg-purple-500/20 text-purple-300' : 'bg-blue-500/20 text-blue-300' ?>">
                    <i class="bi bi-<?= $current_user['role'] == 'admin' ? 'shield-fill-check' : 'person-badge' ?> mr-1"></i>
                    <?= ucfirst($current_user['role']) ?>
                </span>
            </div>
            <i class="bi bi-chevron-right text-gray-400 group-hover:text-blue-300 transition-colors"></i>
        </a>
    </div>
    <?php } ?>

    <!-- Notification Bell (OFW Users Only) -->
    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "ofw") { ?>
    <div class="flex-shrink-0 px-4 py-3 border-b border-gray-700 bg-gray-800/50">
        <button onclick="toggleNotifications()" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-white/10 transition-all duration-200 group relative">
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <i class="bi bi-bell-fill text-2xl text-gray-300 group-hover:text-blue-300 transition-colors"></i>
                    <span id="notification-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse">0</span>
                </div>
                <span class="text-sm font-semibold text-gray-300 group-hover:text-white transition-colors">Notifications</span>
            </div>
            <i class="bi bi-chevron-down text-gray-400 group-hover:text-blue-300 transition-colors" id="notification-chevron"></i>
        </button>
        
        <!-- Notifications Dropdown -->
        <div id="notifications-dropdown" class="hidden mt-2 bg-gray-900 rounded-lg shadow-2xl max-h-96 overflow-y-auto">
            <div class="p-3 border-b border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-bold text-white">Notifications</h3>
                <button onclick="markAllAsRead()" class="text-xs text-blue-400 hover:text-blue-300 transition">Mark all read</button>
            </div>
            <div id="notifications-list" class="divide-y divide-gray-700">
                <div class="p-4 text-center text-gray-400 text-sm">
                    <i class="bi bi-hourglass-split animate-spin text-2xl mb-2"></i>
                    <p>Loading notifications...</p>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
        
    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto py-4 scrollbar-thin min-h-0" role="navigation" aria-label="Main navigation">
        <a href="dashboard.php" 
           class="flex items-center px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'dashboard.php' || $current_page == 'main_dashboard.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'dashboard.php' || $current_page == 'main_dashboard.php') ? 'page' : 'false' ?>">
            <i class="bi bi-speedometer2 text-xl w-7" aria-hidden="true"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    
    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin") { 
        // Fetch counts for admin
        $approved_users_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'approved'")->fetch_assoc()['count'];
        $pending_users_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'pending'")->fetch_assoc()['count'];
        $total_users_count = $approved_users_count + $pending_users_count;
        $jobs_count = $conn->query("SELECT COUNT(*) as count FROM jobs")->fetch_assoc()['count'];
        $job_applications_count = $conn->query("SELECT COUNT(*) as count FROM job_applications WHERE status = 'pending'")->fetch_assoc()['count'];
        $benefit_applications_count = $conn->query("SELECT COUNT(*) as count FROM benefit_applications WHERE status = 'pending'")->fetch_assoc()['count'];
        $news_count = $conn->query("SELECT COUNT(*) as count FROM news")->fetch_assoc()['count'];
        $benefits_count = $conn->query("SELECT COUNT(*) as count FROM benefits")->fetch_assoc()['count'];
    ?>
        <a href="manage_users.php" 
           class="flex items-center justify-between px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'manage_users.php' || $current_page == 'add_user.php' || $current_page == 'edit_user.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'manage_users.php' || $current_page == 'add_user.php' || $current_page == 'edit_user.php') ? 'page' : 'false' ?>">
            <div class="flex items-center">
                <i class="bi bi-people-fill text-xl w-7" aria-hidden="true"></i>
                <span class="ml-3">Manage Users</span>
            </div>
            <?php if ($total_users_count > 0): ?>
                <span data-count="total_users" class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-blue-500 text-white shadow-sm">
                    <?= $total_users_count ?>
                </span>
            <?php else: ?>
                <span data-count="total_users" class="hidden ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-blue-500 text-white shadow-sm">0</span>
            <?php endif; ?>
        </a>
        
        <a href="manage_jobs.php" 
           class="flex items-center justify-between px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'manage_jobs.php' || $current_page == 'add_job.php' || $current_page == 'edit_job.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'manage_jobs.php' || $current_page == 'add_job.php' || $current_page == 'edit_job.php') ? 'page' : 'false' ?>">
            <div class="flex items-center">
                <i class="bi bi-briefcase-fill text-xl w-7" aria-hidden="true"></i>
                <span class="ml-3">Manage Jobs</span>
            </div>
            <?php if ($jobs_count > 0): ?>
                <span data-count="jobs" class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-green-500 text-white shadow-sm">
                    <?= $jobs_count ?>
                </span>
            <?php else: ?>
                <span data-count="jobs" class="hidden ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-green-500 text-white shadow-sm">0</span>
            <?php endif; ?>
        </a>
        
        <a href="update_news.php" 
           class="flex items-center justify-between px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'update_news.php' || $current_page == 'add_news.php' || $current_page == 'edit_news.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'update_news.php' || $current_page == 'add_news.php' || $current_page == 'edit_news.php') ? 'page' : 'false' ?>">
            <div class="flex items-center">
                <i class="bi bi-newspaper text-xl w-7" aria-hidden="true"></i>
                <span class="ml-3">Update News</span>
            </div>
            <?php if ($news_count > 0): ?>
                <span data-count="news" class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-yellow-500 text-white shadow-sm">
                    <?= $news_count ?>
                </span>
            <?php else: ?>
                <span data-count="news" class="hidden ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-yellow-500 text-white shadow-sm">0</span>
            <?php endif; ?>
        </a>
        
        <a href="update_benefits.php" 
           class="flex items-center justify-between px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'update_benefits.php' || $current_page == 'add_benefits.php' || $current_page == 'edit_benefits.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'update_benefits.php' || $current_page == 'add_benefits.php' || $current_page == 'edit_benefits.php') ? 'page' : 'false' ?>">
            <div class="flex items-center">
                <i class="bi bi-cash-coin text-xl w-7" aria-hidden="true"></i>
                <span class="ml-3">Update Benefits</span>
            </div>
            <?php if ($benefits_count > 0): ?>
                <span data-count="benefits" class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-purple-500 text-white shadow-sm">
                    <?= $benefits_count ?>
                </span>
            <?php else: ?>
                <span data-count="benefits" class="hidden ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-purple-500 text-white shadow-sm">0</span>
            <?php endif; ?>
        </a>
        
        <a href="view_benefit_applications.php" 
           class="flex items-center justify-between px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'view_benefit_applications.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'view_benefit_applications.php') ? 'page' : 'false' ?>">
            <div class="flex items-center">
                <i class="bi bi-file-earmark-person text-xl w-7" aria-hidden="true"></i>
                <span class="ml-3">Benefits Applicants</span>
            </div>
            <?php if ($benefit_applications_count > 0): ?>
                <span data-count="benefit_applications" class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-orange-500 text-white shadow-sm animate-pulse">
                    <?= $benefit_applications_count ?>
                </span>
            <?php else: ?>
                <span data-count="benefit_applications" class="hidden ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-orange-500 text-white shadow-sm">0</span>
            <?php endif; ?>
        </a>
        
        <a href="manage_applications.php" 
           class="flex items-center justify-between px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'manage_applications.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'manage_applications.php') ? 'page' : 'false' ?>">
            <div class="flex items-center">
                <i class="bi bi-person-lines-fill text-xl w-7" aria-hidden="true"></i>
                <span class="ml-3">Job Applicants</span>
            </div>
            <?php if ($job_applications_count > 0): ?>
                <span data-count="job_applications" class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-cyan-500 text-white shadow-sm">
                    <?= $job_applications_count ?>
                </span>
            <?php else: ?>
                <span data-count="job_applications" class="hidden ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-cyan-500 text-white shadow-sm">0</span>
            <?php endif; ?>
        </a>
        
        <a href="manage_case_intake.php" 
           class="flex items-center justify-between px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'manage_case_intake.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'manage_case_intake.php') ? 'page' : 'false' ?>">
            <div class="flex items-center">
                <i class="bi bi-file-earmark-text-fill text-xl w-7" aria-hidden="true"></i>
                <span class="ml-3">Case Intake</span>
            </div>
            <?php
            $case_intake_count = $conn->query("SELECT COUNT(*) as count FROM case_intake WHERE status='pending'")->fetch_assoc()['count'];
            if ($case_intake_count > 0): ?>
                <span class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-red-500 text-white shadow-sm animate-pulse">
                    <?= $case_intake_count ?>
                </span>
            <?php endif; ?>
        </a>

        <a href="archived_applications.php" 
           class="flex items-center px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'archived_applications.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'archived_applications.php') ? 'page' : 'false' ?>">
            <i class="bi bi-archive text-xl w-7" aria-hidden="true"></i>
            <span class="ml-3">Archive</span>
        </a>
        
        <a href="activity_logs.php" 
           class="flex items-center px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'activity_logs.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'activity_logs.php') ? 'page' : 'false' ?>">
            <i class="bi bi-clock-history text-xl w-7" aria-hidden="true"></i>
            <span class="ml-3">Activity Logs</span>
        </a>
        
        <!-- Logout Button (Mobile Only - Inside Nav) -->
        <button onclick="openLogoutModal()" 
                class="lg:hidden w-full flex items-center px-5 py-3 text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all duration-200 border-l-4 border-transparent"
                aria-label="Logout">
            <i class="bi bi-box-arrow-right text-xl w-7" aria-hidden="true"></i>
            <span class="ml-3 font-medium">Logout</span>
        </button>
        
    <?php } elseif (isset($_SESSION["role"]) && $_SESSION["role"] == "ofw") { 
        // Fetch counts for OFW
        $user_id = $_SESSION["user_id"];
        $my_applications_count = $conn->query("SELECT COUNT(*) as count FROM benefit_applications WHERE user_id = $user_id")->fetch_assoc()['count'];
        $available_jobs_count = $conn->query("SELECT COUNT(*) as count FROM jobs")->fetch_assoc()['count'];
        $available_benefits_count = $conn->query("SELECT COUNT(*) as count FROM benefits")->fetch_assoc()['count'];
        $news_count = $conn->query("SELECT COUNT(*) as count FROM news")->fetch_assoc()['count'];
    ?>
        <a href="benefits.php" 
           class="flex items-center justify-between px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'benefits.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'benefits.php') ? 'page' : 'false' ?>">
            <div class="flex items-center">
                <i class="bi bi-gift text-xl w-7" aria-hidden="true"></i>
                <span class="ml-3">Benefits</span>
            </div>
            <?php if ($available_benefits_count > 0): ?>
                <span data-count="available_benefits" class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-purple-500 text-white shadow-sm">
                    <?= $available_benefits_count ?>
                </span>
            <?php else: ?>
                <span data-count="available_benefits" class="hidden ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-purple-500 text-white shadow-sm">0</span>
            <?php endif; ?>
        </a>
        
        <a href="view_jobs.php" 
           class="flex items-center justify-between px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'view_jobs.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'view_jobs.php') ? 'page' : 'false' ?>">
            <div class="flex items-center">
                <i class="bi bi-search text-xl w-7" aria-hidden="true"></i>
                <span class="ml-3">Jobs</span>
            </div>
            <?php if ($available_jobs_count > 0): ?>
                <span data-count="available_jobs" class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-green-500 text-white shadow-sm">
                    <?= $available_jobs_count ?>
                </span>
            <?php else: ?>
                <span data-count="available_jobs" class="hidden ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-green-500 text-white shadow-sm">0</span>
            <?php endif; ?>
        </a>
        
        <a href="news.php" 
           class="flex items-center justify-between px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'news.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'news.php') ? 'page' : 'false' ?>">
            <div class="flex items-center">
                <i class="bi bi-newspaper text-xl w-7" aria-hidden="true"></i>
                <span class="ml-3">News</span>
            </div>
            <?php if ($news_count > 0): ?>
                <span data-count="news_ofw" class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-yellow-500 text-white shadow-sm">
                    <?= $news_count ?>
                </span>
            <?php else: ?>
                <span data-count="news_ofw" class="hidden ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-yellow-500 text-white shadow-sm">0</span>
            <?php endif; ?>
        </a>
        
        <a href="case_intake.php" 
           class="flex items-center px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'case_intake.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'case_intake.php') ? 'page' : 'false' ?>">
            <i class="bi bi-file-earmark-text-fill text-xl w-7" aria-hidden="true"></i>
            <span class="ml-3">Case Intake</span>
        </a>

        <a href="profile.php" 
           class="flex items-center px-5 py-3 text-gray-300 hover:bg-white/10 hover:text-white transition-all duration-200 border-l-4 <?= ($current_page == 'profile.php') ? 'border-blue-500 bg-blue-500/15 text-white font-semibold' : 'border-transparent' ?>"
           aria-current="<?= ($current_page == 'profile.php') ? 'page' : 'false' ?>">
            <i class="bi bi-person-circle text-xl w-7" aria-hidden="true"></i>
            <span class="ml-3">Update Profile</span>
        </a>
        
        <!-- Logout Button (Mobile Only - Inside Nav) -->
        <button onclick="openLogoutModal()" 
                class="lg:hidden w-full flex items-center px-5 py-3 text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all duration-200 border-l-4 border-transparent"
                aria-label="Logout">
            <i class="bi bi-box-arrow-right text-xl w-7" aria-hidden="true"></i>
            <span class="ml-3 font-medium">Logout</span>
        </button>
    <?php } ?>
    </nav>
    
    <!-- Logout Button (Desktop Only - At Bottom) -->
    <footer class="hidden lg:block flex-shrink-0 border-t border-gray-700">
        <?php
        // Track active users (users active in last 5 minutes)
        if (isset($_SESSION["user_id"])) {
            $current_user_id = $_SESSION["user_id"];
            $current_time = date('Y-m-d H:i:s');
            
            // Update or insert last activity
            $activity_check = $conn->query("SELECT id FROM user_activity WHERE user_id = $current_user_id");
            if ($activity_check && $activity_check->num_rows > 0) {
                $conn->query("UPDATE user_activity SET last_activity = '$current_time' WHERE user_id = $current_user_id");
            } else {
                $conn->query("INSERT INTO user_activity (user_id, last_activity) VALUES ($current_user_id, '$current_time')");
            }
            
            // Count active users (active in last 5 minutes)
            $five_minutes_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));
            $active_users_result = $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM user_activity WHERE last_activity >= '$five_minutes_ago'");
            $active_users_count = $active_users_result ? $active_users_result->fetch_assoc()['count'] : 0;
        }
        ?>
        
        <!-- Active Users Indicator (Admin Only) -->
        <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin"): ?>
        <div class="px-5 py-3 bg-gray-800/50 border-b border-gray-700 cursor-pointer hover:bg-gray-700/50 transition-colors duration-200" onclick="openActiveUsersModal()">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center">
                    <div class="relative">
                        <i class="bi bi-people-fill text-green-400 text-lg"></i>
                        <span class="absolute -top-1 -right-1 w-2 h-2 bg-green-400 rounded-full animate-ping"></span>
                        <span class="absolute -top-1 -right-1 w-2 h-2 bg-green-400 rounded-full"></span>
                    </div>
                    <span class="ml-2 text-gray-300 font-medium">Active Users</span>
                </div>
                <span data-count="active_users" class="px-2.5 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-sm">
                    <?= $active_users_count ?? 0 ?>
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-1 ml-6">Online in last 5 minutes • Click to view</p>
        </div>
        <?php endif; ?>
        
        <button onclick="openLogoutModal()" 
                class="w-full flex items-center px-5 py-4 text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all duration-200"
                aria-label="Logout">
            <i class="bi bi-box-arrow-right text-xl w-7" aria-hidden="true"></i>
            <span class="ml-3 font-medium">Logout</span>
        </button>
    </footer>
</aside>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" 
     class="hidden fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
     role="dialog"
     aria-labelledby="logoutModalTitle"
     aria-modal="true">
    <div class="bg-white rounded-xl md:rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-fade-in max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <header class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 md:px-6 py-4 md:py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-white/20 rounded-full flex items-center justify-center mr-2 md:mr-3">
                        <i class="bi bi-shield-exclamation text-xl md:text-2xl" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 id="logoutModalTitle" class="text-lg md:text-xl font-bold">End Session</h2>
                        <p class="text-white/80 text-xs md:text-sm">OFW Management System</p>
                    </div>
                </div>
                <button onclick="closeLogoutModal()" 
                        class="text-white/80 hover:text-white text-2xl md:text-3xl leading-none transition-colors"
                        aria-label="Close modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </header>
        
        <!-- Modal Body -->
        <div class="p-4 md:p-6">
            <div class="text-center mb-4 md:mb-6">
                <div class="flex justify-center mb-3 md:mb-4">
                    <img src="images/logo5.png" alt="OFW Management" class="h-16 md:h-20 w-auto">
                </div>
                <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">Confirm Logout</h3>
                <p class="text-sm md:text-base text-gray-600 mb-3 md:mb-4">You are about to end your current session in the OFW Management System.</p>
            </div>
            
            <!-- Info Box -->
            <div class="bg-indigo-50 border-l-4 border-blue-500 p-3 md:p-4 rounded-lg mb-4">
                <div class="flex items-start">
                    <i class="bi bi-info-circle-fill text-blue-600 text-lg md:text-xl mr-2 md:mr-3 mt-0.5" aria-hidden="true"></i>
                    <div class="text-xs md:text-sm text-gray-700">
                        <p class="font-semibold mb-1">Before you go:</p>
                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                            <li>All unsaved changes will be lost</li>
                            <li>Your session will be terminated</li>
                            <li>You'll need to login again</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <footer class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 flex flex-col sm:flex-row justify-end gap-2 md:gap-3 border-t border-gray-200">
            <button onclick="closeLogoutModal()" 
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 md:px-5 py-2 md:py-2.5 bg-white hover:bg-gray-100 text-gray-700 font-semibold rounded-lg transition-all duration-200 border-2 border-gray-300 shadow-sm text-sm md:text-base">
                <i class="bi bi-x-circle mr-2" aria-hidden="true"></i>
                Stay Logged In
            </button>
            <a href="logout.php" 
               class="w-full sm:w-auto inline-flex items-center justify-center px-4 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl text-sm md:text-base">
                <i class="bi bi-box-arrow-right mr-2" aria-hidden="true"></i>
                Yes, Logout
            </a>
        </footer>
    </div>
</div>

<script>
// Mobile Sidebar Functions
function openMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
    document.body.style.overflow = '';
}

// Toggle sidebar on mobile
document.getElementById('mobile-sidebar-toggle')?.addEventListener('click', function() {
    openMobileSidebar();
});

// Close sidebar when clicking a link on mobile
document.querySelectorAll('#sidebar nav a').forEach(link => {
    link.addEventListener('click', function() {
        if (window.innerWidth < 1024) {
            closeMobileSidebar();
        }
    });
});

// Logout Modal Functions
function openLogoutModal() {
    const modal = document.getElementById('logoutModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    modal.classList.add('hidden');
    document.body.style.overflow = ''; // Restore scrolling
}

// Close modal when clicking outside
document.getElementById('logoutModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLogoutModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('logoutModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeLogoutModal();
        }
    }
});
</script>

<style>
/* Smooth fade-in animation for modal */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fadeIn 0.2s ease-out;
}

/* Custom scrollbar for sidebar navigation */
nav::-webkit-scrollbar {
    width: 6px;
}

nav::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

nav::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Ensure sidebar content is properly contained */
#sidebar {
    display: flex;
    flex-direction: column;
    max-height: 100vh;
    height: 100vh;
}

#sidebar nav {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
}

#sidebar header,
#sidebar footer {
    flex: 0 0 auto;
}

/* Scrollbar thin utility */
.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}
</style>

<!-- Active Users Modal (Admin Only) -->
<?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin"): ?>
<div id="activeUsersModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden animate-fade-in">
        <header class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <i class="bi bi-people-fill text-2xl mr-3"></i>
                <h2 class="text-xl font-bold">Active Users</h2>
            </div>
            <button onclick="closeActiveUsersModal()" class="text-white hover:text-gray-200 text-2xl">&times;</button>
        </header>
        <div class="p-6 max-h-96 overflow-y-auto">
            <div id="activeUsersList">
                <div class="text-center py-8">
                    <i class="bi bi-hourglass-split text-4xl text-gray-400 animate-spin"></i>
                    <p class="text-gray-500 mt-2">Loading active users...</p>
                </div>
            </div>
        </div>
        <footer class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t">
            <p class="text-sm text-gray-600">
                <i class="bi bi-info-circle mr-1"></i>
                Users active in the last 5 minutes
            </p>
            <button onclick="closeActiveUsersModal()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                Close
            </button>
        </footer>
    </div>
</div>

<script>
// Active Users Modal Functions
function openActiveUsersModal() {
    const modal = document.getElementById('activeUsersModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    loadActiveUsers();
}

function closeActiveUsersModal() {
    const modal = document.getElementById('activeUsersModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

function loadActiveUsers() {
    fetch('get_active_users.php')
        .then(response => response.json())
        .then(data => {
            const listContainer = document.getElementById('activeUsersList');
            
            if (data.success && data.users.length > 0) {
                let html = '<div class="space-y-3">';
                data.users.forEach(user => {
                    const roleColor = user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800';
                    const statusColor = user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                    
                    html += `
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center gap-3">
                                ${user.profile_picture ? 
                                    `<img src="${user.profile_picture}" class="w-12 h-12 rounded-full object-cover border-2 border-green-400" alt="${user.name}">` :
                                    `<div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center text-white font-bold text-lg">
                                        ${user.name.charAt(0).toUpperCase()}
                                    </div>`
                                }
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-semibold text-gray-900">${user.name}</h3>
                                        <span class="relative flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">${user.email}</p>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 items-end">
                                <span class="px-3 py-1 ${roleColor} text-xs font-semibold rounded-full">
                                    ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                                </span>
                                <span class="px-3 py-1 ${statusColor} text-xs font-semibold rounded-full">
                                    ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                                </span>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                listContainer.innerHTML = html;
            } else {
                listContainer.innerHTML = `
                    <div class="text-center py-8">
                        <i class="bi bi-person-x text-4xl text-gray-400"></i>
                        <p class="text-gray-500 mt-2">No active users found</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading active users:', error);
            document.getElementById('activeUsersList').innerHTML = `
                <div class="text-center py-8">
                    <i class="bi bi-exclamation-triangle text-4xl text-red-400"></i>
                    <p class="text-red-500 mt-2">Failed to load active users</p>
                </div>
            `;
        });
}

function getTimeAgo(datetime) {
    // Parse the datetime string - assume it's in server timezone
    // PHP format: Y-m-d H:i:s (e.g., "2024-03-12 14:30:00")
    const dateStr = datetime.replace(' ', 'T');
    
    // Get current time and past time
    const now = new Date();
    const past = new Date(dateStr);
    
    // If the date is invalid, return the original string
    if (isNaN(past.getTime())) {
        console.error('Invalid date:', datetime);
        return datetime;
    }
    
    // Calculate difference in milliseconds
    const diffMs = now - past;
    
    // If difference is negative, there's a timezone issue
    if (diffMs < 0) {
        console.warn('Negative time difference - possible timezone issue');
        return 'just now';
    }
    
    const diffSecs = Math.floor(diffMs / 1000);
    const diffMins = Math.floor(diffSecs / 60);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);
    
    if (diffSecs < 10) return 'just now';
    if (diffSecs < 60) return `${diffSecs} seconds ago`;
    if (diffMins === 1) return '1 minute ago';
    if (diffMins < 60) return `${diffMins} minutes ago`;
    if (diffHours === 1) return '1 hour ago';
    if (diffHours < 24) return `${diffHours} hours ago`;
    if (diffDays === 1) return '1 day ago';
    return `${diffDays} days ago`;
}

// Close modal when clicking outside
document.getElementById('activeUsersModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeActiveUsersModal();
    }
});
</script>
<?php endif; ?>

<!-- Notification System Script -->
<script>
let notificationsOpen = false;

// Toggle notifications dropdown
function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    const chevron = document.getElementById('notification-chevron');
    notificationsOpen = !notificationsOpen;
    
    if (notificationsOpen) {
        dropdown.classList.remove('hidden');
        chevron.classList.add('rotate-180');
        loadNotifications();
    } else {
        dropdown.classList.add('hidden');
        chevron.classList.remove('rotate-180');
    }
}

// Load notifications
function loadNotifications() {
    fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationBadge(data.unread_count);
                displayNotifications(data.notifications);
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
}

// Update notification badge
function updateNotificationBadge(count) {
    const badge = document.getElementById('notification-badge');
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

// Display notifications
function displayNotifications(notifications) {
    const list = document.getElementById('notifications-list');
    
    if (notifications.length === 0) {
        list.innerHTML = `
            <div class="p-6 text-center text-gray-400">
                <i class="bi bi-bell-slash text-4xl mb-2"></i>
                <p class="text-sm">No notifications yet</p>
            </div>
        `;
        return;
    }
    
    list.innerHTML = notifications.map(notif => {
        const iconMap = {
            'job': 'bi-briefcase-fill text-blue-400',
            'benefit': 'bi-gift-fill text-purple-400',
            'news': 'bi-newspaper text-green-400',
            'application_approved': 'bi-check-circle-fill text-green-400',
            'application_rejected': 'bi-x-circle-fill text-red-400'
        };
        
        const icon = iconMap[notif.type] || 'bi-bell-fill text-gray-400';
        const bgClass = notif.is_read == 0 ? 'bg-blue-500/10' : '';
        
        return `
            <div class="p-3 hover:bg-white/5 transition cursor-pointer ${bgClass}" onclick="handleNotificationClick(${notif.id}, '${notif.link}')">
                <div class="flex items-start space-x-3">
                    <i class="bi ${icon} text-xl flex-shrink-0 mt-1"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white mb-1">${notif.title}</p>
                        <p class="text-xs text-gray-400 mb-1">${notif.message}</p>
                        <p class="text-xs text-gray-500">${notif.time_ago}</p>
                    </div>
                    ${notif.is_read == 0 ? '<div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></div>' : ''}
                </div>
            </div>
        `;
    }).join('');
}

// Handle notification click
function handleNotificationClick(notificationId, link) {
    // Mark as read
    fetch('mark_notification_read.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `notification_id=${notificationId}`
    }).then(() => {
        loadNotifications();
        if (link) {
            window.location.href = link;
        }
    });
}

// Mark all as read
function markAllAsRead() {
    fetch('mark_notification_read.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'notification_id=0'
    }).then(() => {
        loadNotifications();
    });
}

// Auto-refresh notifications every 30 seconds
setInterval(() => {
    if (!notificationsOpen) {
        fetch('get_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationBadge(data.unread_count);
                }
            });
    }
}, 30000);

// Auto-refresh sidebar badge counts every 10 seconds
setInterval(() => {
    refreshSidebarCounts();
}, 10000);

function refreshSidebarCounts() {
    fetch('get_sidebar_counts.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update each badge count
                Object.keys(data.counts).forEach(key => {
                    const badge = document.querySelector(`[data-count="${key}"]`);
                    if (badge) {
                        const count = data.counts[key];
                        if (count > 0) {
                            badge.textContent = count;
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    }
                });
            }
        })
        .catch(error => console.error('Error refreshing sidebar counts:', error));
}

// Initial load
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "ofw") { ?>
    loadNotifications();
    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
    <?php } ?>
    
    // Initial sidebar counts refresh
    refreshSidebarCounts();
});

<?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "ofw") { ?>
// Notification Functions
function loadNotifications() {
    fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationBadge(data.unread_count);
                displayNotifications(data.notifications);
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notification-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}

function displayNotifications(notifications) {
    const list = document.getElementById('notifications-list');
    if (!list) return;
    
    if (notifications.length === 0) {
        list.innerHTML = `
            <div class="p-4 text-center text-gray-400 text-sm">
                <i class="bi bi-inbox text-2xl mb-2"></i>
                <p>No notifications yet</p>
            </div>
        `;
        return;
    }
    
    list.innerHTML = notifications.map(notif => {
        const typeIcons = {
            'success': 'bi-check-circle-fill text-green-400',
            'error': 'bi-x-circle-fill text-red-400',
            'warning': 'bi-exclamation-triangle-fill text-yellow-400',
            'info': 'bi-info-circle-fill text-blue-400'
        };
        const icon = typeIcons[notif.type] || typeIcons['info'];
        const unreadClass = notif.is_read == 0 ? 'bg-blue-900/30' : '';
        
        // Format message with line breaks
        const formattedMessage = notif.message.replace(/\n/g, '<br>');
        
        return `
            <div class="p-3 hover:bg-gray-800/50 transition cursor-pointer ${unreadClass}" 
                 onclick="markAsRead(${notif.id}, '${notif.link || '#'}')">
                <div class="flex items-start space-x-3">
                    <i class="bi ${icon} text-xl mt-1 flex-shrink-0"></i>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-1">
                            <h4 class="text-sm font-semibold text-white pr-2">${notif.title}</h4>
                            ${notif.is_read == 0 ? '<span class="w-2 h-2 bg-blue-400 rounded-full flex-shrink-0 mt-1.5"></span>' : ''}
                        </div>
                        <p class="text-xs text-gray-300 leading-relaxed whitespace-pre-line">${notif.message}</p>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="bi bi-clock mr-1"></i>${notif.time_ago}
                        </p>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    const chevron = document.getElementById('notification-chevron');
    
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        chevron.classList.remove('bi-chevron-down');
        chevron.classList.add('bi-chevron-up');
        loadNotifications();
    } else {
        dropdown.classList.add('hidden');
        chevron.classList.remove('bi-chevron-up');
        chevron.classList.add('bi-chevron-down');
    }
}

function markAsRead(notificationId, link) {
    fetch('mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `notification_id=${notificationId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications();
            if (link && link !== '#') {
                window.location.href = link;
            }
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));
}

function markAllAsRead() {
    fetch('mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'mark_all=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications();
        }
    })
    .catch(error => console.error('Error marking all as read:', error));
}
<?php } ?>
</script>
