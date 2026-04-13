<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

$user_role = $_SESSION["role"]; // Get user role from session
$user_id = $_SESSION["user_id"];

// Get user's country
$user_country = '';
if ($user_role == 'ofw') {
    $user_stmt = $conn->prepare("SELECT country FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    if ($user_row = $user_result->fetch_assoc()) {
        $user_country = $user_row['country'];
    }
    $user_stmt->close();
}

// Handle Delete Application (For OFW)
if (isset($_GET['delete_application_id']) && $user_role == 'ofw') {
    $app_id = intval($_GET['delete_application_id']);
    $user_id = $_SESSION['user_id'];

    // Fetch documents to delete files, and confirm ownership/status
    $stmt = $conn->prepare("SELECT documents FROM benefit_applications WHERE id = ? AND user_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $app_id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $files = explode(',', $row['documents']);
        foreach ($files as $file) {
            $file = trim($file);
            if (!empty($file) && file_exists("uploads/" . $file)) {
                unlink("uploads/" . $file);
            }
        }
        // Use prepared statement for deletion
        $delete_stmt = $conn->prepare("DELETE FROM benefit_applications WHERE id = ? AND user_id = ?");
        $delete_stmt->bind_param("ii", $app_id, $user_id);
        $delete_stmt->execute();
        $delete_stmt->close();

        echo "<script>alert('Application deleted successfully!'); window.location.href='benefits.php';</script>";
    } else {
        echo "<script>alert('Application cannot be deleted. It may have already been processed.'); window.location.href='benefits.php';</script>";
    }
    $stmt->close();
    exit();
}

// Handle search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Fetch benefits from the database with search
$benefits_query = "SELECT id, title, description, applicable_countries, expiration_date, created_at FROM benefits WHERE 1=1";

if (!empty($search)) {
    $benefits_query .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
}

$benefits_query .= " ORDER BY created_at DESC";
$benefits_result = $conn->query($benefits_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benefits - OFW Management System</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="alternate icon" href="images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            background: url('images/wall234.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Inter', sans-serif;
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
        .benefit-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .benefit-card:hover {
            transform: translateY(-8px);
        }
        @media print {
            body { background: none; }
            .print-hide {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50">

<?php include 'sidebar.php'; ?>

<main class="lg:ml-64 p-4 lg:p-8 min-h-screen pt-20 lg:pt-8">
    <!-- Header Section -->
    <div class="mb-8 md:mb-12">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="bi bi-gift-fill text-4xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-white text-3xl md:text-4xl lg:text-5xl font-bold drop-shadow-lg">OFW Benefits & Assistance</h1>
                    <p class="text-white/90 text-sm md:text-base lg:text-lg mt-2 drop-shadow">Explore available benefits and assistance programs for OFWs</p>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="max-w-2xl">
            <form method="GET" action="benefits.php" class="relative">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="<?php echo htmlspecialchars($search); ?>"
                           placeholder="Search benefits by title or description..." 
                           class="w-full px-6 py-4 pl-14 pr-32 rounded-2xl border-2 border-white/30 bg-white/95 backdrop-blur-sm focus:outline-none focus:ring-4 focus:ring-blue-500/50 focus:border-blue-500 transition-all shadow-lg text-gray-800 placeholder-gray-500">
                    <i class="bi bi-search absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl"></i>
                    <div class="absolute right-2 top-1/2 transform -translate-y-1/2 flex gap-2">
                        <?php if (!empty($search)): ?>
                            <a href="benefits.php" 
                               class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-xl text-sm font-medium transition-all">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                        <button type="submit" 
                                class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl text-sm font-semibold transition-all shadow-lg hover:shadow-xl">
                            <i class="bi bi-search mr-2"></i>Search
                        </button>
                    </div>
                </div>
            </form>
            <?php if (!empty($search)): ?>
                <p class="text-white/90 text-sm mt-3 ml-2">
                    <i class="bi bi-info-circle mr-1"></i>
                    Showing results for: <span class="font-semibold">"<?php echo htmlspecialchars($search); ?>"</span>
                    <?php if ($benefits_result->num_rows == 0): ?>
                        <span class="text-yellow-300">- No benefits found</span>
                    <?php else: ?>
                        <span class="text-green-300">- <?php echo $benefits_result->num_rows; ?> benefit(s) found</span>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($benefits_result->num_rows > 0) { ?>
        <!-- Benefits Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <?php while ($benefit = $benefits_result->fetch_assoc()) { 
                $expiration_date = $benefit["expiration_date"];
                $current_date = date("Y-m-d");
                $is_expired = $expiration_date && $expiration_date < $current_date;
                
                // Check if benefit is applicable to user's country
                $applicable_countries = $benefit["applicable_countries"];
                $is_country_eligible = false;
                
                if (!empty($applicable_countries)) {
                    $countries_array = array_map('trim', explode(',', $applicable_countries));
                    $is_country_eligible = in_array('all', $countries_array) || in_array($user_country, $countries_array);
                } else {
                    $is_country_eligible = true; // If no countries specified, available to all
                }
            ?>
                <article class="benefit-card bg-white rounded-2xl shadow-xl overflow-hidden group">
                    <!-- Header -->
                    <div class="relative bg-gradient-to-br from-blue-600 to-blue-800 p-6">
                        <div class="absolute top-4 right-4">
                            <?php if ($is_expired) { ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500 text-white shadow-lg">
                                    <i class="bi bi-x-circle-fill mr-1"></i>Expired
                                </span>
                            <?php } else { ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500 text-white shadow-lg">
                                    <i class="bi bi-check-circle-fill mr-1"></i>Active
                                </span>
                            <?php } ?>
                        </div>
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-4">
                            <i class="bi bi-gift text-3xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2 pr-20">
                            <?php echo htmlspecialchars($benefit["title"]); ?>
                        </h3>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6">
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 leading-relaxed line-clamp-4">
                                <?php echo nl2br(htmlspecialchars($benefit["description"])); ?>
                            </p>
                        </div>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="bi bi-globe text-blue-600 mr-2"></i>
                                <span class="font-medium">Applicable Countries:</span>
                                <span class="ml-2">
                                    <?php 
                                    if (!empty($applicable_countries)) {
                                        $countries_array = array_map('trim', explode(',', $applicable_countries));
                                        if (in_array('all', $countries_array)) {
                                            echo '<span class="text-green-600 font-semibold">All Countries</span>';
                                        } else {
                                            echo htmlspecialchars(implode(', ', array_slice($countries_array, 0, 3)));
                                            if (count($countries_array) > 3) {
                                                echo ' <span class="text-gray-500">+' . (count($countries_array) - 3) . ' more</span>';
                                            }
                                        }
                                    } else {
                                        echo '<span class="text-green-600 font-semibold">All Countries</span>';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="bi bi-calendar-event text-blue-600 mr-2"></i>
                                <span class="font-medium">Expires:</span>
                                <span class="ml-2"><?php echo date("M d, Y", strtotime($benefit["expiration_date"])); ?></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="bi bi-calendar-plus text-blue-600 mr-2"></i>
                                <span class="font-medium">Posted:</span>
                                <span class="ml-2"><?php echo date("M d, Y", strtotime($benefit["created_at"])); ?></span>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="pt-4 border-t border-gray-200">
                            <?php if ($user_role == 'ofw'): ?>
                                <?php
                                $b_id = $benefit["id"];
                                $u_id = $_SESSION["user_id"];

                                if ($is_expired) {
                                    echo '<button disabled class="w-full inline-flex items-center justify-center px-6 py-3 bg-gray-300 text-gray-500 rounded-xl text-sm font-semibold cursor-not-allowed">
                                            <i class="bi bi-x-circle mr-2"></i>Benefit Expired
                                          </button>';
                                } elseif (!$is_country_eligible) {
                                    echo '<div class="space-y-2">';
                                    echo '<button disabled class="w-full inline-flex items-center justify-center px-6 py-3 bg-gray-300 text-gray-500 rounded-xl text-sm font-semibold cursor-not-allowed">
                                            <i class="bi bi-geo-alt-fill mr-2"></i>Not Available in Your Country
                                          </button>';
                                    echo '<p class="text-xs text-center text-gray-600">
                                            <i class="bi bi-info-circle mr-1"></i>Your country: <span class="font-semibold">' . htmlspecialchars($user_country) . '</span>
                                          </p>';
                                    echo '</div>';
                                } else {
                                    // Check for any application (including rejected)
                                    $check_stmt = $conn->prepare("SELECT id, status FROM benefit_applications WHERE user_id = ? AND benefit_id = ? ORDER BY applied_at DESC LIMIT 1");
                                    $check_stmt->bind_param("ii", $u_id, $b_id);
                                    $check_stmt->execute();
                                    $check_result = $check_stmt->get_result();
                                    
                                    // Count total applications for this benefit
                                    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM benefit_applications WHERE user_id = ? AND benefit_id = ?");
                                    $count_stmt->bind_param("ii", $u_id, $b_id);
                                    $count_stmt->execute();
                                    $count_result = $count_stmt->get_result();
                                    $count_row = $count_result->fetch_assoc();
                                    $total_applications = $count_row['total'];

                                    if ($check_result->num_rows > 0) {
                                        $row = $check_result->fetch_assoc();

                                        if ($row["status"] == "pending") {
                                            echo '<div class="space-y-2">';
                                            echo '<div class="w-full inline-flex items-center justify-center px-6 py-3 bg-yellow-100 text-yellow-800 rounded-xl text-sm font-semibold border-2 border-yellow-300">
                                                    <i class="bi bi-clock-fill mr-2"></i>Application Pending
                                                  </div>';
                                            echo '<div class="grid grid-cols-2 gap-2">';
                                            echo '<a href="edit_benefit_application.php?id='.$row['id'].'" class="inline-flex items-center justify-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium transition-all">
                                                    <i class="bi bi-pencil-square mr-2"></i>Edit
                                                  </a>';
                                            echo '<a href="benefits.php?delete_application_id='.$row['id'].'" class="inline-flex items-center justify-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-all" onclick="return confirm(\'Are you sure you want to delete this application?\');">
                                                    <i class="bi bi-trash mr-2"></i>Delete
                                                  </a>';
                                            echo '</div>';
                                            echo '</div>';
                                        } elseif ($row["status"] == "approved") {
                                            echo '<div class="w-full inline-flex items-center justify-center px-6 py-3 bg-green-100 text-green-800 rounded-xl text-sm font-semibold border-2 border-green-300">
                                                    <i class="bi bi-check-circle-fill mr-2"></i>Application Approved
                                                  </div>';
                                        } elseif ($row["status"] == "rejected") {
                                            if ($total_applications >= 3) {
                                                echo '<div class="w-full py-3 bg-gray-100 text-gray-600 rounded-xl font-semibold text-center text-sm border-2 border-gray-300 cursor-not-allowed">
                                                        <i class="bi bi-exclamation-circle-fill mr-2"></i>Maximum Applications Reached (2 Re-applications)
                                                      </div>';
                                            } else {
                                                echo '<div class="space-y-2">';
                                                echo '<div class="w-full py-2 bg-red-100 text-red-800 rounded-lg font-semibold text-center text-sm border border-red-300">
                                                        <i class="bi bi-x-circle-fill mr-1"></i>Previous Application Rejected
                                                        <span class="text-xs ml-2">('.(3 - $total_applications).' re-application'.((3 - $total_applications) > 1 ? 's' : '').' left)</span>
                                                      </div>';
                                                echo '<a href="apply_benefit.php?id='.$b_id.'" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl text-sm font-semibold transition-all shadow-lg hover:shadow-xl group">
                                                        <i class="bi bi-arrow-repeat mr-2"></i>Re-Apply Now
                                                        <i class="bi bi-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                                      </a>';
                                                echo '</div>';
                                            }
                                        }
                                    } else {
                                        echo '<a href="apply_benefit.php?id='.$b_id.'" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl text-sm font-semibold transition-all shadow-lg hover:shadow-xl group">
                                                <i class="bi bi-file-earmark-plus mr-2"></i>Apply Now
                                                <i class="bi bi-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                              </a>';
                                    }
                                    $check_stmt->close();
                                }
                                ?>
                            <?php elseif ($user_role == 'admin'): ?>
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="edit_benefits.php?id=<?= $benefit['id']; ?>" class="inline-flex items-center justify-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium transition-all">
                                        <i class="bi bi-pencil-square mr-2"></i>Edit
                                    </a>
                                    <a href="update_benefits.php?delete=<?= $benefit['id']; ?>" 
                                       class="inline-flex items-center justify-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-all"
                                       onclick="return confirm('Are you sure you want to delete this benefit?');">
                                       <i class="bi bi-trash mr-2"></i>Delete
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-16 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="bi bi-inbox text-gray-400 text-5xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Benefits Available</h3>
            <p class="text-gray-600">Check back later for new benefits and assistance programs.</p>
        </div>
    <?php } ?>
</main>

</body>
</html>
