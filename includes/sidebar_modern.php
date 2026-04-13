<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Modern Collapsible Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-screen w-64 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white shadow-2xl z-50 flex flex-column transition-all duration-300 ease-in-out print:hidden">
    <!-- Logo Section -->
    <div class="flex items-center justify-between p-4 border-b border-slate-700/50">
        <div class="flex items-center space-x-3">
            <div class="relative">
                <img src="images/logo.png" alt="Logo" class="w-10 h-10 rounded-lg object-cover ring-2 ring-indigo-500/50">
                <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-slate-900"></div>
            </div>
            <div class="sidebar-text">
                <h2 class="text-lg font-bold text-white">OFW System</h2>
                <p class="text-xs text-slate-400">Management Portal</p>
            </div>
        </div>
        <button id="sidebarToggle" class="lg:hidden p-2 rounded-lg hover:bg-slate-700/50 transition-colors">
            <i class="bi bi-x-lg text-xl"></i>
        </button>
    </div>

    <!-- User Profile Card -->
    <div class="p-4 border-b border-slate-700/50">
        <div class="flex items-center space-x-3 p-3 bg-slate-800/50 rounded-xl hover:bg-slate-700/50 transition-all cursor-pointer">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                    <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-slate-800"></div>
            </div>
            <div class="flex-1 sidebar-text">
                <p class="text-sm font-semibold text-white truncate"><?php echo $_SESSION['full_name'] ?? 'User'; ?></p>
                <p class="text-xs text-slate-400 capitalize"><?php echo $_SESSION['role'] ?? 'Role'; ?></p>
            </div>
        </div>
    </div>
        
    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 scrollbar-thin scrollbar-thumb-slate-700 scrollbar-track-transparent">
        <!-- Dashboard -->
        <a href="dashboard.php" class="group flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 <?= ($current_page == 'dashboard.php' || $current_page == 'main_dashboard.php') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white' ?>">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg <?= ($current_page == 'dashboard.php' || $current_page == 'main_dashboard.php') ? 'bg-white/10' : 'bg-slate-800/50 group-hover:bg-slate-700/50' ?> transition-colors">
                <i class="bi bi-speedometer2 text-lg"></i>
            </div>
            <span class="ml-3 font-medium sidebar-text">Dashboard</span>
            <?php if ($current_page == 'dashboard.php' || $current_page == 'main_dashboard.php'): ?>
                <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
            <?php endif; ?>
        </a>

        <?php if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin"): ?>
            <!-- Admin Section -->
            <div class="pt-4 pb-2 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider sidebar-text">Administration</p>
            </div>

            <a href="manage_users.php" class="group flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 <?= in_array($current_page, ['manage_users.php', 'add_user.php', 'edit_user.php']) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white' ?>">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg <?= in_array($current_page, ['manage_users.php', 'add_user.php', 'edit_user.php']) ? 'bg-white/10' : 'bg-slate-800/50 group-hover:bg-slate-700/50' ?> transition-colors">
                    <i class="bi bi-people-fill text-lg"></i>
                </div>
                <span class="ml-3 font-medium sidebar-text">Users</span>
                <?php if (in_array($current_page, ['manage_users.php', 'add_user.php', 'edit_user.php'])): ?>
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
                <?php endif; ?>
            </a>

            <a href="manage_jobs.php" class="group flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 <?= in_array($current_page, ['manage_jobs.php', 'add_job.php', 'edit_job.php']) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white' ?>">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg <?= in_array($current_page, ['manage_jobs.php', 'add_job.php', 'edit_job.php']) ? 'bg-white/10' : 'bg-slate-800/50 group-hover:bg-slate-700/50' ?> transition-colors">
                    <i class="bi bi-briefcase-fill text-lg"></i>
                </div>
                <span class="ml-3 font-medium sidebar-text">Jobs</span>
                <?php if (in_array($current_page, ['manage_jobs.php', 'add_job.php', 'edit_job.php'])): ?>
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
                <?php endif; ?>
            </a>

            <a href="update_news.php" class="group flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 <?= in_array($current_page, ['update_news.php', 'add_news.php', 'edit_news.php']) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white' ?>">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg <?= in_array($current_page, ['update_news.php', 'add_news.php', 'edit_news.php']) ? 'bg-white/10' : 'bg-slate-800/50 group-hover:bg-slate-700/50' ?> transition-colors">
                    <i class="bi bi-newspaper text-lg"></i>
                </div>
                <span class="ml-3 font-medium sidebar-text">News</span>
                <?php if (in_array($current_page, ['update_news.php', 'add_news.php', 'edit_news.php'])): ?>
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
                <?php endif; ?>
            </a>

            <a href="update_benefits.php" class="group flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 <?= in_array($current_page, ['update_benefits.php', 'add_benefits.php', 'edit_benefits.php']) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white' ?>">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg <?= in_array($current_page, ['update_benefits.php', 'add_benefits.php', 'edit_benefits.php']) ? 'bg-white/10' : 'bg-slate-800/50 group-hover:bg-slate-700/50' ?> transition-colors">
                    <i class="bi bi-cash-coin text-lg"></i>
                </div>
                <span class="ml-3 font-medium sidebar-text">Benefits</span>
                <?php if (in_array($current_page, ['update_benefits.php', 'add_benefits.php', 'edit_benefits.php'])): ?>
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
                <?php endif; ?>
            </a>

            <!-- Applications Section -->
            <div class="pt-4 pb-2 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider sidebar-text">Applications</p>
            </div>

            <a href="view_benefit_applications.php" class="group flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 <?= $current_page == 'view_benefit_applications.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white' ?>">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg <?= $current_page == 'view_benefit_applications.php' ? 'bg-white/10' : 'bg-slate-800/50 group-hover:bg-slate-700/50' ?> transition-colors">
                    <i class="bi bi-file-earmark-person text-lg"></i>
                </div>
                <span class="ml-3 font-medium sidebar-text">Benefits Apps</span>
                <?php if ($current_page == 'view_benefit_applications.php'): ?>
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
                <?php endif; ?>
            </a>

            <a href="manage_applications.php" class="group flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 <?= $current_page == 'manage_applications.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white' ?>">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg <?= $current_page == 'manage_applications.php' ? 'bg-white/10' : 'bg-slate-800/50 group-hover:bg-slate-700/50' ?> transition-colors">
                    <i class="bi bi-person-lines-fill text-lg"></i>
                </div>
                <span class="ml-3 font-medium sidebar-text">Job Apps</span>
                <?php if ($current_page == 'manage_applications.php'): ?>
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
                <?php endif; ?>
            </a>

            <a href="activity_logs.php" class="group flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 <?= $current_page == 'activity_logs.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white' ?>">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg <?= $current_page == 'activity_logs.php' ? 'bg-white/10' : 'bg-slate-800/50 group-hover:bg-slate-700/50' ?> transition-colors">
                    <i class="bi bi-clock-history text-lg"></i>
                </div>
                <span class="ml-3 font-medium sidebar-text">Activity Logs</span>
                <?php if ($current_page == 'activity_logs.php'): ?>
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
                <?php endif; ?>
            </a>

        <?php elseif (isset($_SESSION["role"]) && $_SESSION["role"] == "ofw"): ?>
            <!-- OFW Section -->
            <div class="pt-4 pb-2 px-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider sidebar-text">Services</p>
            </div>

            <a href="benefits.php" class="group flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 <?= $current_page == 'benefits.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white' ?>">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg <?= $current_page == 'benefits.php' ? 'bg-white/10' : 'bg-slate-800/50 group-hover:bg-slate-700/50' ?> transition-colors">
                    <i class="bi bi-gift text-lg"></i>
                </div>
                <span class="ml-3 font-medium sidebar-text">Benefits</span>
                <?php if ($current_page == 'benefits.php'): ?>
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
                <?php endif; ?>
            </a>

            <a href="view_jobs.php" class="group flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 <?= $current_page == 'view_jobs.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white' ?>">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg <?= $current_page == 'view_jobs.php' ? 'bg-white/10' : 'bg-slate-800/50 group-hover:bg-slate-700/50' ?> transition-colors">
                    <i class="bi bi-search text-lg"></i>
                </div>
                <span class="ml-3 font-medium sidebar-text">Find Jobs</span>
                <?php if ($current_page == 'view_jobs.php'): ?>
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
                <?php endif; ?>
            </a>

            <a href="news.php" class="group flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 <?= $current_page == 'news.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white' ?>">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg <?= $current_page == 'news.php' ? 'bg-white/10' : 'bg-slate-800/50 group-hover:bg-slate-700/50' ?> transition-colors">
                    <i class="bi bi-newspaper text-lg"></i>
                </div>
                <span class="ml-3 font-medium sidebar-text">News</span>
                <?php if ($current_page == 'news.php'): ?>
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
                <?php endif; ?>
            </a>

            <a href="profile.php" class="group flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 <?= $current_page == 'profile.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/50' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white' ?>">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg <?= $current_page == 'profile.php' ? 'bg-white/10' : 'bg-slate-800/50 group-hover:bg-slate-700/50' ?> transition-colors">
                    <i class="bi bi-person-circle text-lg"></i>
                </div>
                <span class="ml-3 font-medium sidebar-text">Profile</span>
                <?php if ($current_page == 'profile.php'): ?>
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white"></div>
                <?php endif; ?>
            </a>
        <?php endif; ?>
    </nav>
    
    <!-- Logout Button -->
    <div class="p-4 border-t border-slate-700/50">
        <button onclick="document.getElementById('logoutModal').classList.remove('hidden')" class="group w-full flex items-center px-3 py-2.5 rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all duration-200">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-red-500/10 group-hover:bg-red-500/20 transition-colors">
                <i class="bi bi-box-arrow-right text-lg"></i>
            </div>
            <span class="ml-3 font-medium sidebar-text">Logout</span>
        </button>
    </div>
</aside>

<!-- Mobile Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

<!-- Logout Modal -->
<div id="logoutModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mx-auto mb-4">
                <i class="bi bi-exclamation-triangle-fill text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Confirm Logout</h3>
            <p class="text-gray-600 text-center mb-6">Are you sure you want to log out of your account?</p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('logoutModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                    Cancel
                </button>
                <a href="logout.php" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors text-center">
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Sidebar Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    // Mobile menu toggle
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    }
    
    // Overlay click to close
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    }
    
    // Desktop sidebar collapse
    const collapseBtn = document.createElement('button');
    collapseBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
    collapseBtn.className = 'hidden lg:flex absolute -right-3 top-20 w-6 h-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full items-center justify-center shadow-lg transition-all duration-300 z-50';
    sidebar.appendChild(collapseBtn);
    
    collapseBtn.addEventListener('click', function() {
        sidebar.classList.toggle('w-64');
        sidebar.classList.toggle('w-20');
        const icon = this.querySelector('i');
        icon.classList.toggle('bi-chevron-left');
        icon.classList.toggle('bi-chevron-right');
        
        // Toggle text visibility
        document.querySelectorAll('.sidebar-text').forEach(el => {
            el.classList.toggle('hidden');
        });
    });
});
</script>

<style>
/* Custom scrollbar */
.scrollbar-thin::-webkit-scrollbar {
    width: 4px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: rgb(51 65 85);
    border-radius: 20px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: rgb(71 85 105);
}
</style>
