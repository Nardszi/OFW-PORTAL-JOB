<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

// Get filter type (approved or rejected)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'approved';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch archived job applications
$job_query = "SELECT ja.*, j.job_title, j.company_name, u.name, u.email, u.profile_picture 
              FROM job_applications ja
              JOIN jobs j ON ja.job_id = j.id
              JOIN users u ON ja.ofw_id = u.id
              WHERE ja.status = ?";

if (!empty($search)) {
    $job_query .= " AND (u.name LIKE ? OR j.job_title LIKE ? OR j.company_name LIKE ?)";
}

$job_query .= " ORDER BY ja.applied_at DESC";

$job_stmt = $conn->prepare($job_query);

if (!empty($search)) {
    $search_param = "%" . $search . "%";
    $job_stmt->bind_param("ssss", $filter, $search_param, $search_param, $search_param);
} else {
    $job_stmt->bind_param("s", $filter);
}

$job_stmt->execute();
$job_result = $job_stmt->get_result();

// Fetch archived benefit applications
$benefit_query = "SELECT ba.*, b.title as benefit_title, u.name, u.email, u.profile_picture 
                  FROM benefit_applications ba
                  JOIN benefits b ON ba.benefit_id = b.id
                  JOIN users u ON ba.user_id = u.id
                  WHERE ba.status = ?";

if (!empty($search)) {
    $benefit_query .= " AND (u.name LIKE ? OR b.title LIKE ?)";
}

$benefit_query .= " ORDER BY ba.applied_at DESC";

$benefit_stmt = $conn->prepare($benefit_query);

if (!empty($search)) {
    $search_param = "%" . $search . "%";
    $benefit_stmt->bind_param("sss", $filter, $search_param, $search_param);
} else {
    $benefit_stmt->bind_param("s", $filter);
}

$benefit_stmt->execute();
$benefit_result = $benefit_stmt->get_result();

$job_count = $job_result->num_rows;
$benefit_count = $benefit_result->num_rows;
$total_count = $job_count + $benefit_count;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Applications - OFW Management</title>
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

<?php include 'sidebar.php'; ?>

<main class="lg:ml-64 p-4 lg:p-6 min-h-screen pt-20 lg:pt-6">
    <!-- Header Section -->
    <header class="bg-white/95 p-6 rounded-2xl mb-8 shadow-lg border-l-4 border-blue-600">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-1">
                    <i class="bi bi-archive mr-2"></i>Archived Applications
                </h1>
                <p class="text-gray-600"><?= ucfirst($filter) ?> applications archive</p>
                <div class="mt-2 inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                    <i class="bi bi-info-circle mr-1"></i>
                    Total: <?= $total_count ?> applications
                </div>
            </div>
        </div>
    </header>

    <!-- Filters Section -->
    <section class="bg-white/95 p-6 rounded-2xl mb-6 shadow-lg">
        <form method="GET" class="flex flex-col lg:flex-row gap-4 items-start lg:items-center">
            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
            
            <!-- Status Filter Buttons -->
            <div class="flex gap-2">
                <a href="?filter=approved<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                   class="px-4 py-2 rounded-lg font-semibold transition duration-200 shadow-sm <?= $filter === 'approved' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?>">
                    <i class="bi bi-check-circle-fill mr-1"></i>Approved
                </a>
                <a href="?filter=rejected<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                   class="px-4 py-2 rounded-lg font-semibold transition duration-200 shadow-sm <?= $filter === 'rejected' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' ?>">
                    <i class="bi bi-x-circle-fill mr-1"></i>Rejected
                </a>
            </div>

            <!-- Search -->
            <div class="relative flex-1 lg:min-w-[300px]">
                <input type="text" 
                       name="search" 
                       class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Search by name, job, or benefit..." 
                       value="<?= htmlspecialchars($search) ?>">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md">
                    <i class="bi bi-search mr-1"></i> Search
                </button>
                <?php if(!empty($search)): ?>
                    <a href="?filter=<?= $filter ?>" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200">
                        Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <!-- Job Applications Archive -->
    <?php if ($job_count > 0): ?>
    <section class="mb-8">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="bi bi-briefcase-fill mr-2"></i>
                    Job Applications (<?= $job_count ?>)
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Applicant</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Job Title</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Company</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Applied Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($app = $job_result->fetch_assoc()): ?>
                        <tr class="hover:bg-blue-50 transition duration-150">
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center">
                                    <?php 
                                    // Display profile picture or avatar
                                    if (!empty($app['profile_picture']) && file_exists($app['profile_picture'])) {
                                        echo '<img src="' . htmlspecialchars($app['profile_picture']) . '" 
                                              class="w-10 h-10 rounded-full object-cover mr-3 border-2 border-blue-200 shadow-md" 
                                              alt="' . htmlspecialchars($app['name']) . '"
                                              onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
                                        echo '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold mr-3 shadow-md hidden">';
                                        $name_parts = explode(' ', $app['name']);
                                        $initials = strtoupper(substr($name_parts[0], 0, 1));
                                        if (count($name_parts) > 1) {
                                            $initials .= strtoupper(substr($name_parts[count($name_parts)-1], 0, 1));
                                        }
                                        echo $initials;
                                        echo '</div>';
                                    } else {
                                        echo '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold mr-3 shadow-md">';
                                        $name_parts = explode(' ', $app['name']);
                                        $initials = strtoupper(substr($name_parts[0], 0, 1));
                                        if (count($name_parts) > 1) {
                                            $initials .= strtoupper(substr($name_parts[count($name_parts)-1], 0, 1));
                                        }
                                        echo $initials;
                                        echo '</div>';
                                    }
                                    ?>
                                    <div>
                                        <div class="font-semibold text-gray-900"><?= htmlspecialchars($app['name']) ?></div>
                                        <div class="text-xs text-gray-500">
                                            <i class="bi bi-envelope mr-1"></i><?= htmlspecialchars($app['email']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($app['job_title']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($app['company_name']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div><i class="bi bi-calendar3 mr-1 text-gray-400"></i><?= date('M d, Y', strtotime($app['applied_at'])) ?></div>
                                <div class="text-xs text-gray-500"><i class="bi bi-clock mr-1"></i><?= date('h:i A', strtotime($app['applied_at'])) ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php if ($app['status'] === 'approved'): ?>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                        <i class="bi bi-check-circle-fill mr-1"></i>Approved
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                        <i class="bi bi-x-circle-fill mr-1"></i>Rejected
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="view_application_details.php?type=job&id=<?= $app['id'] ?>" 
                                   class="text-blue-600 hover:text-blue-800 font-semibold transition">
                                    <i class="bi bi-eye mr-1"></i>View
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Benefit Applications Archive -->
    <?php if ($benefit_count > 0): ?>
    <section>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="bi bi-heart-fill mr-2"></i>
                    Benefit Applications (<?= $benefit_count ?>)
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Applicant</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Benefit</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Applied Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($app = $benefit_result->fetch_assoc()): ?>
                        <tr class="hover:bg-blue-50 transition duration-150">
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center">
                                    <?php 
                                    // Display profile picture or avatar
                                    if (!empty($app['profile_picture']) && file_exists($app['profile_picture'])) {
                                        echo '<img src="' . htmlspecialchars($app['profile_picture']) . '" 
                                              class="w-10 h-10 rounded-full object-cover mr-3 border-2 border-purple-200 shadow-md" 
                                              alt="' . htmlspecialchars($app['name']) . '"
                                              onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
                                        echo '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold mr-3 shadow-md hidden">';
                                        $name_parts = explode(' ', $app['name']);
                                        $initials = strtoupper(substr($name_parts[0], 0, 1));
                                        if (count($name_parts) > 1) {
                                            $initials .= strtoupper(substr($name_parts[count($name_parts)-1], 0, 1));
                                        }
                                        echo $initials;
                                        echo '</div>';
                                    } else {
                                        echo '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold mr-3 shadow-md">';
                                        $name_parts = explode(' ', $app['name']);
                                        $initials = strtoupper(substr($name_parts[0], 0, 1));
                                        if (count($name_parts) > 1) {
                                            $initials .= strtoupper(substr($name_parts[count($name_parts)-1], 0, 1));
                                        }
                                        echo $initials;
                                        echo '</div>';
                                    }
                                    ?>
                                    <div>
                                        <div class="font-semibold text-gray-900"><?= htmlspecialchars($app['name']) ?></div>
                                        <div class="text-xs text-gray-500">
                                            <i class="bi bi-envelope mr-1"></i><?= htmlspecialchars($app['email']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($app['benefit_title']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($app['application_type']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div><i class="bi bi-calendar3 mr-1 text-gray-400"></i><?= date('M d, Y', strtotime($app['applied_at'])) ?></div>
                                <div class="text-xs text-gray-500"><i class="bi bi-clock mr-1"></i><?= date('h:i A', strtotime($app['applied_at'])) ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php if ($app['status'] === 'approved'): ?>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                        <i class="bi bi-check-circle-fill mr-1"></i>Approved
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                        <i class="bi bi-x-circle-fill mr-1"></i>Rejected
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="view_application_details.php?type=benefit&id=<?= $app['id'] ?>" 
                                   class="text-blue-600 hover:text-blue-800 font-semibold transition">
                                    <i class="bi bi-eye mr-1"></i>View
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Empty State -->
    <?php if ($total_count === 0): ?>
    <section class="bg-white rounded-2xl shadow-lg p-12">
        <div class="flex flex-col items-center justify-center text-gray-500">
            <i class="bi bi-archive text-6xl mb-4"></i>
            <p class="text-lg font-semibold">No <?= ucfirst($filter) ?> Applications</p>
            <p class="text-sm">There are no <?= $filter ?> applications in the archive yet.</p>
        </div>
    </section>
    <?php endif; ?>
</main>

</body>
</html>
