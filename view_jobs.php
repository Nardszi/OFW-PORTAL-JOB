<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "ofw") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

$ofw_id = $_SESSION["user_id"];
$user_role = $_SESSION["role"];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch available jobs and check if the OFW has already applied (only count pending/approved)
$query = "SELECT jobs.*, 
                 (SELECT COUNT(*) FROM job_applications 
                  WHERE job_applications.ofw_id = ? 
                  AND job_applications.job_id = jobs.id 
                  AND job_applications.status IN ('pending', 'approved')) AS applied,
                 (SELECT status FROM job_applications 
                  WHERE job_applications.ofw_id = ? 
                  AND job_applications.job_id = jobs.id 
                  ORDER BY applied_at DESC LIMIT 1) AS application_status,
                 (SELECT COUNT(*) FROM job_applications 
                  WHERE job_applications.ofw_id = ? 
                  AND job_applications.job_id = jobs.id) AS total_applications,
                 (SELECT COUNT(*) FROM job_applications 
                  WHERE job_applications.job_id = jobs.id 
                  AND job_applications.status IN ('pending', 'approved')) AS current_applicants
          FROM jobs";

if (!empty($search)) {
    $query .= " WHERE (job_title LIKE ? OR location LIKE ?)";
}

$stmt = $conn->prepare($query);

if (!empty($search)) {
    $search_param = "%" . $search . "%";
    $stmt->bind_param("iiiss", $ofw_id, $ofw_id, $ofw_id, $search_param, $search_param);
} else {
    $stmt->bind_param("iii", $ofw_id, $ofw_id, $ofw_id);
}

$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Jobs - OFW Management</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="alternate icon" href="images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    body {
        background: url('images/wall234.jpg') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
    .content {
        margin-left: 256px;
        padding: 2rem;
        min-height: 100vh;
    }
    .job-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .job-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    @media print {
        .content {
            margin-left: 0;
            padding: 20px;
            background: none;
        }
        .job-card {
            page-break-inside: avoid;
            box-shadow: none !important;
        }
    }
</style>
</head>
<body class="bg-gray-50">

<?php include 'sidebar.php'; ?>

<main class="lg:ml-64 p-4 lg:p-8 min-h-screen pt-20 lg:pt-8">
    <!-- Page Header -->
    <header class="mb-6 md:mb-8 print:hidden">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center space-x-3 md:space-x-4">
                <div class="h-10 md:h-12 w-1 bg-blue-600 rounded-full"></div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white drop-shadow-lg">
                        <i class="bi bi-briefcase-fill mr-2"></i>Available Jobs
                    </h1>
                    <p class="text-gray-200 text-xs md:text-sm mt-1">Find your next opportunity</p>
                </div>
            </div>
            <form method="GET" class="flex gap-2 w-full md:w-auto">
                <input class="flex-1 md:flex-none px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm md:text-base" 
                       type="search" name="search" placeholder="Search jobs..." value="<?= htmlspecialchars($search) ?>">
                <button class="px-4 md:px-6 py-2 bg-white hover:bg-gray-50 text-blue-600 font-semibold rounded-lg shadow-md transition-all text-sm md:text-base" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <?php while ($job = $result->fetch_assoc()): ?>
            <article class="bg-white rounded-2xl shadow-lg overflow-hidden job-card">
                <div class="relative">
                    <?php if (!empty($job["image"])) { ?>
                        <img src="uploads/<?= htmlspecialchars($job['image']); ?>" 
                             class="w-full h-48 object-cover cursor-pointer" 
                             alt="Job Image"
                             onclick="openImageModal('uploads/<?= htmlspecialchars($job['image']); ?>')">
                    <?php } else { ?>
                        <div class="w-full h-48 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                            <i class="bi bi-briefcase text-6xl text-blue-400"></i>
                        </div>
                    <?php } ?>
                    <span class="absolute top-3 right-3 px-3 py-1 bg-gray-900/80 text-white text-xs font-semibold rounded-full shadow-lg">
                        <?= htmlspecialchars($job["preferred_sex"]); ?>
                    </span>
                </div>
                
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($job["job_title"]); ?></h2>
                    <p class="text-gray-600 text-sm mb-4">
                        <i class="bi bi-building mr-1"></i><?= htmlspecialchars($job["company_name"]); ?>
                    </p>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center text-gray-700">
                            <i class="bi bi-geo-alt-fill text-red-500 mr-2"></i>
                            <span class="text-sm"><?= htmlspecialchars($job["location"]); ?></span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="bi bi-cash-stack text-green-500 mr-2"></i>
                            <span class="text-sm font-semibold"><?= htmlspecialchars($job["salary"]); ?></span>
                        </div>
                        <?php if (!empty($job["years_of_experience"])): ?>
                        <div class="flex items-center text-gray-700">
                            <i class="bi bi-calendar-check text-blue-500 mr-2"></i>
                            <span class="text-sm"><?= htmlspecialchars($job["years_of_experience"]); ?> experience</span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($job["max_applicants"])): ?>
                        <div class="flex items-center text-gray-700">
                            <i class="bi bi-people text-purple-500 mr-2"></i>
                            <span class="text-sm">
                                <?= $job["current_applicants"] ?> / <?= $job["max_applicants"] ?> applicants
                                <?php if ($job["current_applicants"] >= $job["max_applicants"]): ?>
                                    <span class="ml-1 px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full font-semibold">FULL</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php 
                    // Check if job is full (reached max applicants)
                    $is_job_full = !empty($job["max_applicants"]) && $job["current_applicants"] >= $job["max_applicants"];
                    ?>

                    <?php if ($is_job_full): ?>
                        <div class="w-full py-3 bg-red-100 text-red-800 rounded-lg font-semibold text-center border-2 border-red-300 cursor-not-allowed">
                            <i class="bi bi-x-circle-fill mr-2"></i>Position Filled (Max Applicants Reached)
                        </div>
                    <?php elseif ($job["applied"] > 0): ?>
                        <?php if ($job["application_status"] == "pending"): ?>
                            <button class="w-full py-3 bg-yellow-100 text-yellow-800 rounded-lg font-semibold cursor-not-allowed border-2 border-yellow-300" disabled>
                                <i class="bi bi-clock-history mr-2"></i>Application Pending
                            </button>
                        <?php elseif ($job["application_status"] == "approved"): ?>
                            <button class="w-full py-3 bg-green-100 text-green-800 rounded-lg font-semibold cursor-not-allowed border-2 border-green-300" disabled>
                                <i class="bi bi-check-circle-fill mr-2"></i>Application Approved
                            </button>
                        <?php endif; ?>
                    <?php elseif (!empty($job["application_status"]) && $job["application_status"] == "rejected"): ?>
                        <?php if ($job["total_applications"] >= 3): ?>
                            <div class="w-full py-3 bg-gray-100 text-gray-600 rounded-lg font-semibold text-center border-2 border-gray-300 cursor-not-allowed">
                                <i class="bi bi-exclamation-circle-fill mr-2"></i>Maximum Applications Reached (2 Re-applications)
                            </div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <div class="w-full py-2 bg-red-100 text-red-800 rounded-lg font-semibold text-center text-sm border border-red-300">
                                    <i class="bi bi-x-circle-fill mr-1"></i>Previous Application Rejected
                                    <span class="text-xs ml-2">(<?= (3 - $job["total_applications"]) ?> re-application<?= (3 - $job["total_applications"]) > 1 ? 's' : '' ?> left)</span>
                                </div>
                                <a href="apply_job.php?job_id=<?= $job["id"]; ?>" 
                                   class="block w-full py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-center rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
                                    <i class="bi bi-arrow-repeat mr-2"></i>Re-Apply Now
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="apply_job.php?job_id=<?= $job["id"]; ?>" 
                           class="block w-full py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-center rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
                            <i class="bi bi-send-fill mr-2"></i>Apply Now
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>

    <?php if ($result->num_rows == 0): ?>
        <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
            <i class="bi bi-inbox text-gray-400 text-6xl mb-4 block"></i>
            <h3 class="text-2xl text-gray-600">No jobs found.</h3>
        </div>
    <?php endif; ?>
</main>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white text-3xl hover:text-gray-300">
            <i class="bi bi-x-circle-fill"></i>
        </button>
        <img id="modalImage" src="" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl" alt="Full Size Image">
    </div>
</div>

<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
