<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (empty($type) || $id === 0) {
    header("Location: archived_applications.php");
    exit();
}

if ($type === 'job') {
    $query = "SELECT ja.*, j.job_title, j.company_name, j.location, j.salary, j.requirements,
              u.name, u.email, u.contact_number, u.profile_picture
              FROM job_applications ja
              JOIN jobs j ON ja.job_id = j.id
              JOIN users u ON ja.ofw_id = u.id
              WHERE ja.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $application = $result->fetch_assoc();
    
    if (!$application) {
        header("Location: archived_applications.php");
        exit();
    }
    
    $documents = !empty($application['documents']) ? explode(',', $application['documents']) : [];
    
} elseif ($type === 'benefit') {
    $query = "SELECT ba.*, b.title as benefit_title, b.description, b.requirements,
              u.name, u.email, u.contact_number, u.profile_picture
              FROM benefit_applications ba
              JOIN benefits b ON ba.benefit_id = b.id
              JOIN users u ON ba.user_id = u.id
              WHERE ba.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $application = $result->fetch_assoc();
    
    if (!$application) {
        header("Location: archived_applications.php");
        exit();
    }
    
    $documents = !empty($application['documents']) ? explode(',', $application['documents']) : [];
} else {
    header("Location: archived_applications.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Details - OFW Management</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="alternate icon" href="images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: url('images/wall234.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Inter', 'Segoe UI', sans-serif;
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
<body>

<?php include 'sidebar.php'; ?>

<main class="lg:ml-64 p-4 lg:p-8 min-h-screen pt-20 lg:pt-8">
    <!-- Page Header -->
    <header class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-1 bg-blue-600 rounded-full"></div>
                <div>
                    <h1 class="text-3xl font-bold text-white drop-shadow-lg">
                        <i class="bi bi-file-earmark-text mr-2"></i>Application Details
                    </h1>
                    <p class="text-gray-200 text-sm mt-1">
                        <?= ucfirst($type) ?> Application #<?= $id ?>
                    </p>
                </div>
            </div>
            <a href="archived_applications.php?filter=<?= $application['status'] ?>" 
               class="inline-flex items-center px-4 py-2 bg-white/90 hover:bg-white text-gray-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <i class="bi bi-arrow-left mr-2"></i>Back to Archive
            </a>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Application Status</h2>
                    <?php if ($application['status'] === 'approved'): ?>
                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            <i class="bi bi-check-circle-fill mr-1"></i>Approved
                        </span>
                    <?php else: ?>
                        <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                            <i class="bi bi-x-circle-fill mr-1"></i>Rejected
                        </span>
                    <?php endif; ?>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Applied Date:</span>
                        <p class="font-semibold text-gray-900"><?= date('F d, Y', strtotime($application['applied_at'])) ?></p>
                    </div>
                    <div>
                        <span class="text-gray-600">Application ID:</span>
                        <p class="font-semibold text-gray-900">#<?= $id ?></p>
                    </div>
                </div>
            </div>

            <!-- Job/Benefit Details -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    <?= $type === 'job' ? 'Job' : 'Benefit' ?> Details
                </h2>
                <?php if ($type === 'job'): ?>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600 text-sm">Job Title:</span>
                            <p class="font-semibold text-gray-900"><?= htmlspecialchars($application['job_title']) ?></p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Company:</span>
                            <p class="font-semibold text-gray-900"><?= htmlspecialchars($application['company_name']) ?></p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Location:</span>
                            <p class="font-semibold text-gray-900"><?= htmlspecialchars($application['location']) ?></p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Salary:</span>
                            <p class="font-semibold text-gray-900"><?= htmlspecialchars($application['salary']) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600 text-sm">Benefit:</span>
                            <p class="font-semibold text-gray-900"><?= htmlspecialchars($application['benefit_title']) ?></p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Application Type:</span>
                            <p class="font-semibold text-gray-900"><?= htmlspecialchars($application['application_type']) ?></p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Description:</span>
                            <p class="text-gray-700"><?= htmlspecialchars($application['description']) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    <i class="bi bi-file-earmark-arrow-down mr-2"></i>Submitted Documents
                </h2>
                <?php if (!empty($documents)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach ($documents as $index => $doc): ?>
                            <a href="uploads/<?= htmlspecialchars($doc) ?>" 
                               target="_blank"
                               class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-all group">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="bi bi-file-earmark-pdf text-blue-600 text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">Document <?= ($index + 1) ?></p>
                                    <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($doc) ?></p>
                                </div>
                                <i class="bi bi-download text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-center py-4">No documents submitted</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Applicant Info -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Applicant Information</h2>
                <div class="flex flex-col items-center mb-4">
                    <?php 
                    // Display profile picture or avatar
                    if (!empty($application['profile_picture']) && file_exists($application['profile_picture'])) {
                        echo '<img src="' . htmlspecialchars($application['profile_picture']) . '" 
                              class="w-20 h-20 rounded-full object-cover border-4 border-blue-400 shadow-lg mb-3" 
                              alt="' . htmlspecialchars($application['name']) . '"
                              onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
                        echo '<div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl mb-3 shadow-lg hidden">';
                        $name_parts = explode(' ', $application['name']);
                        $initials = strtoupper(substr($name_parts[0], 0, 1));
                        if (count($name_parts) > 1) {
                            $initials .= strtoupper(substr($name_parts[count($name_parts)-1], 0, 1));
                        }
                        echo $initials;
                        echo '</div>';
                    } else {
                        echo '<div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl mb-3 shadow-lg">';
                        $name_parts = explode(' ', $application['name']);
                        $initials = strtoupper(substr($name_parts[0], 0, 1));
                        if (count($name_parts) > 1) {
                            $initials .= strtoupper(substr($name_parts[count($name_parts)-1], 0, 1));
                        }
                        echo $initials;
                        echo '</div>';
                    }
                    ?>
                    <h3 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($application['name']) ?></h3>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center text-gray-700">
                        <i class="bi bi-envelope text-blue-600 mr-2"></i>
                        <span class="break-all"><?= htmlspecialchars($application['email']) ?></span>
                    </div>
                    <?php if (!empty($application['contact_number'])): ?>
                    <div class="flex items-center text-gray-700">
                        <i class="bi bi-telephone text-green-600 mr-2"></i>
                        <span><?= htmlspecialchars($application['contact_number']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($type === 'job' && !empty($application['phone'])): ?>
                    <div class="flex items-center text-gray-700">
                        <i class="bi bi-phone text-purple-600 mr-2"></i>
                        <span><?= htmlspecialchars($application['phone']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
                <div class="space-y-2">
                    <a href="mailto:<?= htmlspecialchars($application['email']) ?>" 
                       class="block w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-lg font-semibold transition-all">
                        <i class="bi bi-envelope mr-2"></i>Send Email
                    </a>
                    <button onclick="window.print()" 
                            class="block w-full px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-center rounded-lg font-semibold transition-all">
                        <i class="bi bi-printer mr-2"></i>Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>
