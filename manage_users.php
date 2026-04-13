<?php
session_start();
include "config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: auth/login.php");
    exit();
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$query = "SELECT id, name, email, role, address, country, contact_number, gender, birth_date, civil_status, status, profile_picture FROM users WHERE 1=1";

if ($status_filter == 'approved') {
    $query .= " AND status = 'approved'";
} elseif ($status_filter == 'pending') {
    $query .= " AND status = 'pending'";
}
if (!empty($search)) {
    $query .= " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
}

// Sorting
$sort_columns = ['name', 'email', 'role', 'address', 'country', 'contact_number', 'gender', 'birth_date', 'civil_status', 'status'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sort_columns) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) && strtoupper($_GET['order']) == 'DESC' ? 'DESC' : 'ASC';

$query .= " ORDER BY $sort $order";

$result = mysqli_query($conn, $query);

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
    <title>Manage Users - OFW Management System</title>
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
        
        /* Print Styles */
        @media print {
            /* Hide everything except the table */
            .sidebar, 
            .no-print,
            header,
            footer,
            button,
            form {
                display: none !important;
            }
            
            /* Reset body styles for print */
            body {
                background: white !important;
                margin: 0;
                padding: 0;
            }
            
            body::before {
                display: none !important;
            }
            
            /* Reset main content */
            main {
                margin: 0 !important;
                padding: 20px !important;
            }
            
            /* Show print header */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 2px solid #000;
            }
            
            /* Table styles for print */
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10pt;
            }
            
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
                color: #000 !important;
            }
            
            th {
                background-color: #f0f0f0 !important;
                font-weight: bold;
            }
            
            /* Hide Actions column */
            th:last-child,
            td:last-child {
                display: none !important;
            }
            
            /* Hide profile pictures for cleaner print */
            img, .w-10.h-10.rounded-full {
                display: none !important;
            }
            
            /* Adjust name cell */
            td:first-child .flex {
                display: block !important;
            }
            
            /* Adjust badges for print */
            .px-3 {
                padding: 2px 6px !important;
                border: 1px solid #000;
                border-radius: 3px;
            }
            
            /* Make text visible */
            .text-white {
                color: #000 !important;
            }
            
            .bg-gradient-to-r {
                background: #f0f0f0 !important;
            }
        }
        
        /* Hide print header on screen */
        .print-header {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="lg:ml-64 p-4 lg:p-6 min-h-screen pt-20 lg:pt-6">
    <!-- Header Section -->
    <header class="bg-white/95 p-6 rounded-2xl mb-8 shadow-lg border-l-4 border-blue-600 no-print">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-1">
                    <i class="bi bi-people-fill mr-2"></i>Manage Users
                </h1>
                <p class="text-gray-600">View, approve, and manage user accounts</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="add_user.php" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                    <i class="bi bi-plus-circle mr-2"></i> Add User
                </a>
                <button onclick="window.print()" class="no-print inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                    <i class="bi bi-printer mr-2"></i> Print
                </button>
            </div>
        </div>
    </header>

    <!-- Filter and Search Section -->
    <section class="bg-white/95 p-6 rounded-2xl mb-6 shadow-lg no-print">
        <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
            <!-- Status Filter Tabs -->
            <div class="flex gap-2 bg-gray-100 p-1 rounded-lg">
                <a href="manage_users.php<?= !empty($search) ? '?search='.urlencode($search) : '' ?>" 
                   class="px-6 py-2 text-sm font-semibold rounded-md transition duration-200 <?= $status_filter == '' ? 'bg-gray-800 text-white shadow-md' : 'text-gray-700 hover:bg-gray-200' ?>">
                    <i class="bi bi-people mr-1"></i> All
                </a>
                <a href="manage_users.php?status=approved<?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                   class="px-6 py-2 text-sm font-semibold rounded-md transition duration-200 <?= $status_filter == 'approved' ? 'bg-green-600 text-white shadow-md' : 'text-gray-700 hover:bg-gray-200' ?>">
                    <i class="bi bi-check-circle mr-1"></i> Approved
                </a>
                <a href="manage_users.php?status=pending<?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                   class="px-6 py-2 text-sm font-semibold rounded-md transition duration-200 <?= $status_filter == 'pending' ? 'bg-yellow-500 text-white shadow-md' : 'text-gray-700 hover:bg-gray-200' ?>">
                    <i class="bi bi-clock-history mr-1"></i> Pending
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" class="flex gap-2 flex-1 lg:flex-initial lg:min-w-[300px]">
                <?php if($status_filter): ?>
                    <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                <?php endif; ?>
                <div class="relative flex-1">
                    <input type="text" 
                           name="search" 
                           class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="Search name or email..." 
                           value="<?= htmlspecialchars($search) ?>">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200">
                    Search
                </button>
                <?php if(!empty($search)): ?>
                    <a href="manage_users.php<?= $status_filter ? '?status='.$status_filter : '' ?>" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200">
                        Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </section>

    <!-- Print Header (only visible when printing) -->
    <div class="print-header">
        <h1 style="margin: 0; font-size: 24px; font-weight: bold;">OFW Management System</h1>
        <h2 style="margin: 10px 0; font-size: 18px;">Manage Users - OFW Management System</h2>
        <p style="margin: 5px 0; font-size: 12px;">Generated on: <?= date('n/j/y, g:i A') ?></p>
        <?php if (!empty($search) || $status_filter): ?>
            <p style="margin: 5px 0; font-size: 11px; color: #666;">
                Filters Applied: 
                <?php if (!empty($search)): ?>Search: "<?= htmlspecialchars($search) ?>" | <?php endif; ?>
                <?php if ($status_filter): ?>Status: <?= ucfirst($status_filter) ?><?php endif; ?>
            </p>
        <?php endif; ?>
        <p style="margin: 5px 0; font-size: 11px;">Total Users: <?= $count ?></p>
    </div>

    <!-- Users Table -->
    <section class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('name', 'Name', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('email', 'Email', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('role', 'Role', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('address', 'Address', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('country', 'Country', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('contact_number', 'Contact', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('gender', 'Gender', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('birth_date', 'Birth Date', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('civil_status', 'Civil Status', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('status', 'Status', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                <?php 
                $count = 0;
                while ($row = mysqli_fetch_assoc($result)) { 
                    $count++;
                ?>
                    <tr class="hover:bg-blue-50 transition duration-150">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            <div class="flex items-center">
                                <?php 
                                // Check if user has profile picture
                                if (!empty($row['profile_picture']) && file_exists($row['profile_picture'])) {
                                    // Show actual profile picture
                                    echo '<img src="' . htmlspecialchars($row['profile_picture']) . '" 
                                          class="w-10 h-10 rounded-full object-cover mr-3 border-2 border-blue-200" 
                                          alt="' . htmlspecialchars($row['name']) . '"
                                          onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
                                    // Fallback avatar (hidden by default)
                                    echo '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold mr-3 hidden">
                                          ' . strtoupper(substr($row['name'], 0, 1)) . '
                                          </div>';
                                } else {
                                    // Show initial avatar with gradient
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
                                <?= htmlspecialchars($row['name']) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <i class="bi bi-envelope mr-1 text-gray-400"></i><?= htmlspecialchars($row['email']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $row['role'] == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                                <i class="bi bi-<?= $row['role'] == 'admin' ? 'shield-fill-check' : 'person-badge' ?> mr-1"></i>
                                <?= ucfirst(htmlspecialchars($row['role'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <i class="bi bi-geo-alt mr-1 text-gray-400"></i><?= htmlspecialchars($row['address']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <i class="bi bi-globe mr-1 text-gray-400"></i><?= !empty($row['country']) ? htmlspecialchars($row['country']) : 'N/A' ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <i class="bi bi-telephone mr-1 text-gray-400"></i><?= htmlspecialchars($row['contact_number']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <i class="bi bi-gender-<?= strtolower($row['gender']) == 'male' ? 'male' : 'female' ?> mr-1 text-gray-400"></i>
                            <?= htmlspecialchars($row['gender']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <i class="bi bi-calendar mr-1 text-gray-400"></i>
                            <?= !empty($row['birth_date']) ? date('M d, Y', strtotime($row['birth_date'])) : 'N/A' ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <i class="bi bi-heart mr-1 text-gray-400"></i>
                            <?= htmlspecialchars($row['civil_status']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <?php if ($row['role'] == 'ofw' && $row['status'] != 'approved') { ?>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="bi bi-clock-history mr-1"></i>Pending
                                </span>
                            <?php } else { ?>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="bi bi-check-circle-fill mr-1"></i>Approved
                                </span>
                            <?php } ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <?php if ($row['role'] == 'ofw' && $row['status'] != 'approved') { ?>
                                    <form method="POST" action="approve_user.php" class="inline">
                                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md"
                                                title="Approve User">
                                            <i class="bi bi-check-lg mr-1"></i> Approve
                                        </button>
                                    </form>
                                <?php } ?>
                        
                                <a href="edit_user.php?id=<?= $row['id'] ?>" 
                                   class="inline-flex items-center px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md"
                                   title="Edit User">
                                    <i class="bi bi-pencil-square mr-1"></i> Edit
                                </a>

                                <form method="POST" action="delete_user.php" class="inline">
                                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md" 
                                            onclick="return confirm('Are you sure you want to delete this user?');"
                                            title="Delete User">
                                        <i class="bi bi-trash mr-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if($count == 0): ?>
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <i class="bi bi-inbox text-6xl mb-4"></i>
                                <p class="text-lg font-semibold">No users found</p>
                                <p class="text-sm">Try adjusting your search or filter criteria</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Footer Info -->
    <footer class="mt-6 text-center text-white/80 text-sm no-print">
        <p>Total Users: <span class="font-bold"><?= $count ?></span></p>
    </footer>
</main>

</body>
</html>
