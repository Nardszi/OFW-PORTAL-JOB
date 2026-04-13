<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

// Get filter parameter
$country_filter = isset($_GET['country']) ? mysqli_real_escape_string($conn, $_GET['country']) : '';

// Handle Delete Benefit
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    
    // First, delete all pending applications for this benefit
    $delete_apps_query = "DELETE FROM benefit_applications WHERE benefit_id = ? AND status = 'pending'";
    $apps_stmt = $conn->prepare($delete_apps_query);
    $apps_stmt->bind_param("i", $id);
    $apps_stmt->execute();
    $apps_stmt->close();
    
    // Then delete the benefit itself
    $query = "DELETE FROM benefits WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Benefit and all pending applications deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete benefit.";
    }
    $stmt->close();
    header("Location: update_benefits.php");
    exit();
}

// Fetch all benefits with country filter
$benefits_query = "SELECT * FROM benefits WHERE 1=1";

if (!empty($country_filter) && $country_filter != 'all_filter') {
    $benefits_query .= " AND (applicable_countries LIKE '%all%' OR applicable_countries LIKE '%$country_filter%')";
}

$benefits_query .= " ORDER BY created_at DESC";
$benefits_result = $conn->query($benefits_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Benefits - OFW Management System</title>
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
                    <i class="bi bi-cash-coin mr-2"></i>Manage Benefits
                </h1>
                <p class="text-gray-600">Create, edit, and manage benefit programs</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Country Filter -->
                <select onchange="window.location.href='update_benefits.php?country=' + this.value" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <option value="all_filter" <?= empty($country_filter) || $country_filter == 'all_filter' ? 'selected' : '' ?>>All Countries</option>
                    <option value="Philippines" <?= $country_filter == 'Philippines' ? 'selected' : '' ?>>Philippines</option>
                    <option value="Saudi Arabia" <?= $country_filter == 'Saudi Arabia' ? 'selected' : '' ?>>Saudi Arabia</option>
                    <option value="United Arab Emirates" <?= $country_filter == 'United Arab Emirates' ? 'selected' : '' ?>>UAE</option>
                    <option value="Kuwait" <?= $country_filter == 'Kuwait' ? 'selected' : '' ?>>Kuwait</option>
                    <option value="Qatar" <?= $country_filter == 'Qatar' ? 'selected' : '' ?>>Qatar</option>
                    <option value="Hong Kong" <?= $country_filter == 'Hong Kong' ? 'selected' : '' ?>>Hong Kong</option>
                    <option value="Singapore" <?= $country_filter == 'Singapore' ? 'selected' : '' ?>>Singapore</option>
                    <option value="Taiwan" <?= $country_filter == 'Taiwan' ? 'selected' : '' ?>>Taiwan</option>
                    <option value="Malaysia" <?= $country_filter == 'Malaysia' ? 'selected' : '' ?>>Malaysia</option>
                    <option value="Japan" <?= $country_filter == 'Japan' ? 'selected' : '' ?>>Japan</option>
                    <option value="South Korea" <?= $country_filter == 'South Korea' ? 'selected' : '' ?>>South Korea</option>
                    <option value="United States" <?= $country_filter == 'United States' ? 'selected' : '' ?>>United States</option>
                    <option value="Canada" <?= $country_filter == 'Canada' ? 'selected' : '' ?>>Canada</option>
                    <option value="United Kingdom" <?= $country_filter == 'United Kingdom' ? 'selected' : '' ?>>United Kingdom</option>
                    <option value="Australia" <?= $country_filter == 'Australia' ? 'selected' : '' ?>>Australia</option>
                    <option value="Italy" <?= $country_filter == 'Italy' ? 'selected' : '' ?>>Italy</option>
                </select>
                <a href="add_benefits.php" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                    <i class="bi bi-plus-circle mr-2"></i> Add Benefits
                </a>
            </div>
        </div>
    </header>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow-md flex items-center">
            <i class="bi bi-check-circle-fill text-2xl mr-3"></i>
            <span><?= $_SESSION['message']; unset($_SESSION['message']); ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 shadow-md flex items-center">
            <i class="bi bi-exclamation-triangle-fill text-2xl mr-3"></i>
            <span><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
        </div>
    <?php endif; ?>

    <!-- Benefits Grid -->
    <section class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php 
        $count = 0;
        while ($benefit = $benefits_result->fetch_assoc()) { 
            $count++;
            $expiration_date = $benefit["expiration_date"];
            $current_date = date("Y-m-d");
            $is_expired = $expiration_date && $expiration_date < $current_date;
            $description_preview = substr(strip_tags($benefit["description"]), 0, 120) . '...';
        ?>
            <article class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl flex flex-col <?= $is_expired ? 'opacity-75' : '' ?>">
                <!-- Header with Status Badge -->
                <div class="relative bg-gradient-to-r <?= $is_expired ? 'from-gray-500 to-gray-600' : 'from-purple-600 to-pink-600' ?> p-6 text-white">
                    <div class="absolute top-4 right-4">
                        <?php if ($is_expired) { ?>
                            <span class="px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded-full">
                                <i class="bi bi-x-circle-fill mr-1"></i>Expired
                            </span>
                        <?php } else { ?>
                            <span class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">
                                <i class="bi bi-check-circle-fill mr-1"></i>Active
                            </span>
                        <?php } ?>
                    </div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="bi bi-gift text-2xl"></i>
                        </div>
                        <h2 class="text-xl font-bold flex-1"><?= htmlspecialchars($benefit["title"]) ?></h2>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-6 flex flex-col flex-1">
                    <p class="text-sm text-gray-600 mb-4 flex-1 line-clamp-3">
                        <?= htmlspecialchars($description_preview) ?>
                    </p>

                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4 pb-4 border-b border-gray-200">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">
                                <i class="bi bi-calendar-event mr-1"></i>Expiration Date
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                <?= date("M d, Y", strtotime($benefit["expiration_date"])) ?>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">
                                <i class="bi bi-calendar-plus mr-1"></i>Published
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                <?= date("M d, Y", strtotime($benefit["created_at"])) ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Applicable Countries -->
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <div class="text-xs text-gray-500 mb-2">
                            <i class="bi bi-globe mr-1"></i>Applicable Countries
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <?php 
                            $countries = !empty($benefit['applicable_countries']) ? explode(',', $benefit['applicable_countries']) : ['all'];
                            if (in_array('all', $countries)) {
                                echo '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">All Countries</span>';
                            } else {
                                foreach (array_slice($countries, 0, 3) as $country) {
                                    echo '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">'.htmlspecialchars($country).'</span>';
                                }
                                if (count($countries) > 3) {
                                    echo '<span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">+'.(count($countries)-3).' more</span>';
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="edit_benefits.php?id=<?= $benefit['id']; ?>" 
                           class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md">
                            <i class="bi bi-pencil-square mr-2"></i> Edit
                        </a>
                        <button onclick="confirmDelete(<?= $benefit['id']; ?>)" 
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md">
                            <i class="bi bi-trash mr-2"></i> Delete
                        </button>
                    </div>
                </div>
            </article>
        <?php } ?>

        <?php if($count == 0): ?>
            <div class="col-span-full">
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                    <i class="bi bi-inbox text-gray-400 text-6xl mb-4 block"></i>
                    <h3 class="text-2xl font-bold text-gray-600 mb-2">No benefits yet</h3>
                    <p class="text-gray-500 mb-6">Start by creating your first benefit program</p>
                    <a href="add_benefits.php" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                        <i class="bi bi-plus-circle mr-2"></i> Add Benefits
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Footer Info -->
    <?php if($count > 0): ?>
    <footer class="mt-8 text-center">
        <div class="inline-flex items-center px-6 py-3 bg-white/90 rounded-full shadow-lg">
            <i class="bi bi-gift text-purple-600 text-xl mr-2"></i>
            <span class="text-gray-700 font-semibold">Total Benefits: <span class="text-purple-600"><?= $count ?></span></span>
        </div>
    </footer>
    <?php endif; ?>
</main>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-fade-in">
        <header class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4">
            <h2 class="text-xl font-bold">
                <i class="bi bi-exclamation-triangle-fill mr-2"></i>Confirm Delete
            </h2>
        </header>
        <div class="p-6">
            <p class="text-gray-700 text-lg">Are you sure you want to delete this benefit? This action cannot be undone and will affect all related applications.</p>
        </div>
        <footer class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition duration-200">
                Cancel
            </button>
            <a id="confirmDeleteBtn" href="#" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-200">
                <i class="bi bi-trash mr-1"></i>Delete
            </a>
        </footer>
    </div>
</div>

<script>
function confirmDelete(benefitId) {
    document.getElementById('confirmDeleteBtn').href = 'update_benefits.php?delete=' + benefitId;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});

// Close modal when clicking outside
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>

<style>
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

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

</body>
</html>
