<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

// Approve Job
if (isset($_GET["approve_id"])) {
    $approve_id = $_GET["approve_id"];
    $query = "UPDATE jobs SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $approve_id);
    $stmt->execute();
    header("Location: manage_jobs.php");
    exit();
}

// Delete Job
if (isset($_GET["delete_id"])) {
    $delete_id = $_GET["delete_id"];
    $query = "DELETE FROM jobs WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: manage_jobs.php");
    exit();
}

// Pagination
$limit_options = [5, 10, 20, 50];
$limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $limit_options) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total rows
$total_result = $conn->query("SELECT COUNT(id) as total FROM jobs");
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Sorting
$sort_columns = ['job_title', 'company_name', 'location', 'preferred_sex', 'salary'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sort_columns) ? $_GET['sort'] : 'created_at';
$order = isset($_GET['order']) && strtoupper($_GET['order']) == 'ASC' ? 'ASC' : 'DESC';

// Fetch Jobs with pagination
$jobs_query = "SELECT id, job_title, company_name, location, preferred_sex, salary, image FROM jobs ORDER BY $sort $order LIMIT $limit OFFSET $offset";
$jobs_result = $conn->query($jobs_query);

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
    <title>Manage Jobs - OFW Management System</title>
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
        @media print {
            .sidebar, .no-print { display: none !important; }
            main { margin: 0 !important; padding: 20px !important; }
            body { background: none !important; }
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
                    <i class="bi bi-briefcase-fill mr-2"></i>Manage Jobs
                </h1>
                <p class="text-gray-600">Create, edit, and manage job postings</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="add_job.php" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                    <i class="bi bi-plus-circle mr-2"></i> Add Job
                </a>
            </div>
        </div>
    </header>

    <!-- Controls Section -->
    <section class="bg-white/95 p-6 rounded-2xl mb-6 shadow-lg">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-gray-700 font-medium">Show:</span>
                <form method="GET" class="inline">
                    <select name="limit" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            onchange="this.form.submit()">
                        <option value="5" <?= ($limit == 5) ? 'selected' : '' ?>>5 entries</option>
                        <option value="10" <?= ($limit == 10) ? 'selected' : '' ?>>10 entries</option>
                        <option value="20" <?= ($limit == 20) ? 'selected' : '' ?>>20 entries</option>
                        <option value="50" <?= ($limit == 50) ? 'selected' : '' ?>>50 entries</option>
                    </select>
                </form>
            </div>
            <div class="text-gray-600">
                <i class="bi bi-info-circle mr-1"></i>
                Showing <span class="font-semibold"><?= $offset + 1 ?></span> to 
                <span class="font-semibold"><?= min($offset + $limit, $total_rows) ?></span> of 
                <span class="font-semibold"><?= $total_rows ?></span> jobs
            </div>
        </div>
    </section>

    <!-- Jobs Table -->
    <section class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">#</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Image</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('job_title', 'Job Title', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('company_name', 'Company/Agency', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('location', 'Location', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('preferred_sex', 'Preferred Sex', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('salary', 'Salary', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php 
                    $count = $offset + 1;
                    while ($row = $jobs_result->fetch_assoc()) { ?>
                        <tr class="hover:bg-blue-50 transition duration-150">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $count++; ?></td>
                            <td class="px-6 py-4">
                                <?php if (!empty($row["image"])) { ?>
                                    <button onclick="openImageModal('uploads/<?= htmlspecialchars($row['image']); ?>')" 
                                            class="group relative">
                                        <img src="uploads/<?= htmlspecialchars($row["image"]); ?>" 
                                             alt="Job Image" 
                                             class="w-20 h-20 object-cover rounded-lg shadow-md group-hover:shadow-xl transition duration-300 cursor-pointer">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition duration-300 rounded-lg flex items-center justify-center">
                                            <i class="bi bi-zoom-in text-white text-2xl"></i>
                                        </div>
                                    </button>
                                <?php } else { ?>
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="bi bi-image text-gray-400 text-2xl"></i>
                                    </div>
                                <?php } ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                <i class="bi bi-briefcase text-blue-600 mr-1"></i>
                                <?= htmlspecialchars($row["job_title"]) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <i class="bi bi-building text-gray-400 mr-1"></i>
                                <?= htmlspecialchars($row["company_name"]) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <i class="bi bi-geo-alt text-gray-400 mr-1"></i>
                                <?= htmlspecialchars($row["location"]) ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full <?= strtolower($row['preferred_sex']) == 'male' ? 'bg-blue-100 text-blue-800' : (strtolower($row['preferred_sex']) == 'female' ? 'bg-pink-100 text-pink-800' : 'bg-purple-100 text-purple-800') ?>">
                                    <i class="bi bi-gender-<?= strtolower($row['preferred_sex']) == 'male' ? 'male' : (strtolower($row['preferred_sex']) == 'female' ? 'female' : 'ambiguous') ?> mr-1"></i>
                                    <?= htmlspecialchars($row["preferred_sex"]) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-green-700">
                                <i class="bi bi-cash-coin mr-1"></i>
                                <?= htmlspecialchars($row["salary"]) ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex gap-2">
                                    <a href="edit_job.php?id=<?= $row['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md"
                                       title="Edit Job">
                                        <i class="bi bi-pencil-square mr-1"></i> Edit
                                    </a>
                                    <a href="?delete_id=<?= $row['id']; ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md" 
                                       onclick="return confirm('Are you sure you want to delete this job?');"
                                       title="Delete Job">
                                        <i class="bi bi-trash mr-1"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if($jobs_result->num_rows == 0): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="bi bi-inbox text-6xl mb-4"></i>
                                    <p class="text-lg font-semibold">No jobs found</p>
                                    <p class="text-sm">Start by adding a new job posting</p>
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
            
            // Previous button
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
            // Next button
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

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
        <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-4xl font-bold transition">
            &times;
        </button>
        <img id="modalImage" src="" class="w-full h-auto rounded-2xl shadow-2xl" alt="Job Image">
    </div>
</div>

<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>

</body>
</html>
