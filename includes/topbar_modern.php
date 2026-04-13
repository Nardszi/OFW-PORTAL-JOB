<!-- Modern Top Navigation Bar -->
<nav class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-gray-200 z-30 lg:left-64 transition-all duration-300">
    <div class="h-full px-4 lg:px-6 flex items-center justify-between">
        <!-- Left: Mobile Menu + Breadcrumb -->
        <div class="flex items-center space-x-4">
            <!-- Mobile Menu Button -->
            <button id="mobileMenuBtn" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="bi bi-list text-2xl text-gray-700"></i>
            </button>
            
            <!-- Breadcrumb -->
            <div class="hidden sm:flex items-center space-x-2 text-sm">
                <i class="bi bi-house-door text-gray-400"></i>
                <span class="text-gray-400">/</span>
                <span class="text-gray-900 font-medium capitalize">
                    <?php 
                    $page_name = str_replace(['_', '.php'], [' ', ''], basename($_SERVER['PHP_SELF']));
                    echo ucwords($page_name);
                    ?>
                </span>
            </div>
        </div>

        <!-- Right: Search + Notifications + Profile -->
        <div class="flex items-center space-x-2 lg:space-x-4">
            <!-- Search Bar (Desktop) -->
            <div class="hidden md:flex items-center">
                <div class="relative">
                    <input type="text" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Search Button (Mobile) -->
            <button class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="bi bi-search text-xl text-gray-700"></i>
            </button>

            <!-- Notifications -->
            <div class="relative">
                <button class="relative p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="bi bi-bell text-xl text-gray-700"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                </button>
            </div>

            <!-- Divider -->
            <div class="hidden lg:block w-px h-8 bg-gray-200"></div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <button id="profileDropdownBtn" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="hidden lg:block text-right">
                        <p class="text-sm font-semibold text-gray-900"><?php echo $_SESSION['full_name'] ?? 'User'; ?></p>
                        <p class="text-xs text-gray-500 capitalize"><?php echo $_SESSION['role'] ?? 'Role'; ?></p>
                    </div>
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                            <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                    </div>
                    <i class="bi bi-chevron-down text-gray-400 text-xs hidden lg:block"></i>
                </button>

                <!-- Dropdown Menu -->
                <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-900"><?php echo $_SESSION['full_name'] ?? 'User'; ?></p>
                        <p class="text-xs text-gray-500"><?php echo $_SESSION['email'] ?? 'email@example.com'; ?></p>
                    </div>
                    <a href="profile.php" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="bi bi-person-circle text-lg mr-3 text-gray-400"></i>
                        My Profile
                    </a>
                    <a href="dashboard.php" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="bi bi-speedometer2 text-lg mr-3 text-gray-400"></i>
                        Dashboard
                    </a>
                    <div class="border-t border-gray-100 my-2"></div>
                    <button onclick="document.getElementById('logoutModal').classList.remove('hidden')" class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                        <i class="bi bi-box-arrow-right text-lg mr-3"></i>
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Topbar Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (mobileMenuBtn && sidebar && overlay) {
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        });
    }
    
    // Profile dropdown toggle
    const profileBtn = document.getElementById('profileDropdownBtn');
    const profileDropdown = document.getElementById('profileDropdown');
    
    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });
    }
});
</script>
