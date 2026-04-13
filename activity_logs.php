<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

// Automatically delete logs older than 30 days
$conn->query("DELETE FROM activity_logs WHERE created_at < NOW() - INTERVAL 30 DAY");

// Pagination
$limit_options = [10, 20, 50, 100, 200];
$limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $limit_options) ? (int)$_GET['limit'] : 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

$where_sql = "";
$params = [];
$types = "";

if (!empty($search)) {
    $where_sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($date)) {
    $where_sql .= " AND DATE(l.created_at) = ?";
    $params[] = $date;
    $types .= "s";
}

// Sorting
$sort_mapping = [
    'name' => 'u.name',
    'role' => 'u.role',
    'action' => 'l.action',
    'ip_address' => 'l.ip_address',
    'created_at' => 'l.created_at'
];
$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $sort_mapping) ? $_GET['sort'] : 'created_at';
$order = isset($_GET['order']) && strtoupper($_GET['order']) == 'ASC' ? 'ASC' : 'DESC';
$sort_sql = $sort_mapping[$sort];

// Count total logs with filters
$total_query = "SELECT COUNT(*) as total FROM activity_logs l JOIN users u ON l.user_id = u.id WHERE 1=1 $where_sql";
$stmt = $conn->prepare($total_query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_result = $stmt->get_result();
$total_logs = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_logs / $limit);

// Fetch logs with filters
$query = "SELECT l.*, u.name, u.email, u.role, u.profile_picture 
          FROM activity_logs l 
          JOIN users u ON l.user_id = u.id 
          WHERE 1=1 $where_sql
          ORDER BY $sort_sql $order 
          LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Helper for sort links
function sortLink($col, $label, $current_sort, $current_order) {
    $new_order = ($current_sort === $col && $current_order === 'ASC') ? 'DESC' : 'ASC';
    $params = $_GET;
    $params['sort'] = $col;
    $params['order'] = $new_order;
    $url = '?' . http_build_query($params);
    $icon = ($current_sort === $col) ? 
            (($current_order === 'ASC') ? ' <i class="bi bi-arrow-up"></i>' : ' <i class="bi bi-arrow-down"></i>') : 
            ' <i class="bi bi-arrow-down-up opacity-50 text-xs"></i>';
    return "<a href=\"$url\" class=\"text-white hover:text-blue-300 transition no-underline\">$label$icon</a>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - OFW Management System</title>
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
<main class="lg:ml-64 p-4 lg:p-6 min-h-screen pt-20 lg:pt-6">
    <!-- Header Section -->
    <header class="bg-white/95 p-6 rounded-2xl mb-8 shadow-lg border-l-4 border-blue-600">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-1">
                    <i class="bi bi-clock-history mr-2"></i>Activity Logs
                </h1>
                <p class="text-gray-600">Monitor user activities and system access</p>
                <div class="mt-2 inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                    <i class="bi bi-info-circle mr-1"></i>
                    Auto-deleted after 30 days
                </div>
            </div>
        </div>
    </header>

    <!-- Filters Section -->
    <section class="bg-white/95 p-6 rounded-2xl mb-6 shadow-lg">
        <form method="GET" class="flex flex-col lg:flex-row gap-4 items-start lg:items-center">
            <!-- Show Entries -->
            <div class="flex items-center gap-2">
                <span class="text-gray-700 font-medium text-sm">Show:</span>
                <select name="limit" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        onchange="this.form.submit()">
                    <option value="10" <?= ($limit == 10) ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= ($limit == 20) ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= ($limit == 50) ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($limit == 100) ? 'selected' : '' ?>>100</option>
                    <option value="200" <?= ($limit == 200) ? 'selected' : '' ?>>200</option>
                </select>
            </div>

            <!-- Date Filter -->
            <div class="relative">
                <input type="date" 
                       name="date" 
                       class="px-4 py-2 pl-10 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       value="<?= htmlspecialchars($date) ?>">
                <i class="bi bi-calendar3 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>

            <!-- Search -->
            <div class="relative flex-1 lg:min-w-[300px]">
                <input type="text" 
                       name="search" 
                       class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Search by name or email..." 
                       value="<?= htmlspecialchars($search) ?>">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md">
                    <i class="bi bi-search mr-1"></i> Search
                </button>
                <?php if(!empty($search) || !empty($date)): ?>
                    <a href="activity_logs.php" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200">
                        Clear
                    </a>
                <?php endif; ?>
                <a href="export_logs.php?search=<?= urlencode($search) ?>&date=<?= urlencode($date) ?>" 
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md">
                    <i class="bi bi-file-earmark-spreadsheet mr-1"></i> Export
                </a>
            </div>
        </form>

        <div class="mt-4 text-gray-600 text-sm">
            <i class="bi bi-info-circle mr-1"></i>
            Showing <span class="font-semibold"><?= $offset + 1 ?></span> to 
            <span class="font-semibold"><?= min($offset + $limit, $total_logs) ?></span> of 
            <span class="font-semibold"><?= $total_logs ?></span> logs
        </div>
    </section>

    <!-- Logs Table -->
    <section class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('name', 'User', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('role', 'Role', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('action', 'Action', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('ip_address', 'IP Address', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('created_at', 'Time', $sort, $order) ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr class="hover:bg-blue-50 transition duration-150">
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center">
                                    <?php 
                                    // Check if user has profile picture
                                    if (!empty($row['profile_picture']) && file_exists($row['profile_picture'])) {
                                        echo '<img src="' . htmlspecialchars($row['profile_picture']) . '" 
                                              class="w-10 h-10 rounded-full object-cover mr-3 border-2 border-indigo-200 shadow-md" 
                                              alt="' . htmlspecialchars($row['name']) . '"
                                              onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
                                        echo '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold mr-3 shadow-md hidden">
                                              ' . strtoupper(substr($row['name'], 0, 1)) . '
                                              </div>';
                                    } else {
                                        // Show initial avatar with gradient based on first letter
                                        $colors = [
                                            'from-blue-500 to-purple-600',
                                            'from-green-500 to-teal-600',
                                            'from-pink-500 to-rose-600',
                                            'from-yellow-500 to-orange-600',
                                            'from-indigo-500 to-blue-600',
                                            'from-red-500 to-pink-600',
                                            'from-cyan-500 to-blue-600',
                                            'from-purple-500 to-indigo-600'
                                        ];
                                        $colorIndex = ord(strtoupper(substr($row['name'], 0, 1))) % count($colors);
                                        $gradient = $colors[$colorIndex];
                                        
                                        echo '<div class="w-10 h-10 rounded-full bg-gradient-to-br ' . $gradient . ' flex items-center justify-center text-white font-bold mr-3 shadow-md">
                                              ' . strtoupper(substr($row['name'], 0, 1)) . '
                                              </div>';
                                    }
                                    ?>
                                    <div>
                                        <div class="font-semibold text-gray-900"><?= htmlspecialchars($row['name']) ?></div>
                                        <div class="text-xs text-gray-500">
                                            <i class="bi bi-envelope mr-1"></i><?= htmlspecialchars($row['email']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $row['role'] == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                                    <i class="bi bi-<?= $row['role'] == 'admin' ? 'shield-fill-check' : 'person-badge' ?> mr-1"></i>
                                    <?= ucfirst($row['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php if ($row['action'] == 'Login'): ?>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="bi bi-box-arrow-in-right mr-1"></i>Login
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="bi bi-box-arrow-right mr-1"></i>Logout
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-mono">
                                <i class="bi bi-globe mr-1 text-gray-400"></i>
                                <?= htmlspecialchars($row['ip_address']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div><i class="bi bi-calendar3 mr-1 text-gray-400"></i><?= date("M d, Y", strtotime($row['created_at'])) ?></div>
                                <div class="text-xs text-gray-500"><i class="bi bi-clock mr-1"></i><?= date("h:i A", strtotime($row['created_at'])) ?></div>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($result->num_rows == 0): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="bi bi-inbox text-6xl mb-4"></i>
                                    <p class="text-lg font-semibold">No activity logs found</p>
                                    <p class="text-sm">Try adjusting your search or date filter</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation" class="mt-6">
        <ul class="flex justify-center items-center gap-2">
            <?php 
            $query_params = $_GET;
            
            if ($page > 1):
                $query_params['page'] = $page - 1;
                $prev_link = '?' . http_build_query($query_params);
            ?>
                <li>
                    <a href="<?= $prev_link ?>" class="w-10 h-10 flex items-center justify-center bg-white hover:bg-blue-600 hover:text-white rounded-lg transition shadow-md font-semibold">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): 
                $query_params['page'] = $i;
                $page_link = '?' . http_build_query($query_params);
            ?>
                <li>
                    <a href="<?= $page_link ?>" class="w-10 h-10 flex items-center justify-center <?= ($i == $page) ? 'bg-blue-600 text-white' : 'bg-white hover:bg-blue-600 hover:text-white' ?> rounded-lg transition shadow-md font-semibold">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php 
            if ($page < $total_pages):
                $query_params['page'] = $page + 1;
                $next_link = '?' . http_build_query($query_params);
            ?>
                <li>
                    <a href="<?= $next_link ?>" class="w-10 h-10 flex items-center justify-center bg-white hover:bg-blue-600 hover:text-white rounded-lg transition shadow-md font-semibold">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</main>

</body>
</html>
