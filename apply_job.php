<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "ofw") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

if (!isset($_GET["job_id"])) {
    echo "<script>alert('Invalid Job ID!'); window.location.href='view_jobs.php';</script>";
    exit();
}

$job_id = $_GET["job_id"];
$ofw_id = $_SESSION["user_id"];

// Fetch job details
$query = "SELECT * FROM jobs WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();

if (!$job) {
    echo "<script>alert('Job not found!'); window.location.href='view_jobs.php';</script>";
    exit();
}

// Parse requirements - properly handle line breaks
$requirements_list = [];
if (!empty($job['requirements'])) {
    $raw_requirements = preg_split('/\r\n|\r|\n/', $job['requirements']);
    foreach ($raw_requirements as $req) {
        $req = trim($req);
        if (!empty($req)) {
            $requirements_list[] = $req;
        }
    }
} else {
    $requirements_list = ['Resume'];
}

if (empty($requirements_list)) {
    $requirements_list = ['Resume'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST["full_name"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $phone = mysqli_real_escape_string($conn, $_POST["phone"]);
    
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $uploaded_files = [];
    $all_uploads_successful = true;

    foreach ($requirements_list as $index => $req_name) {
        $input_name = "req_" . $index;
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $req_name));
        if (empty($slug)) $slug = "req" . $index;

        if (!empty($_FILES[$input_name]["name"])) {
            $filename = time() . "_" . $slug . "_" . basename($_FILES[$input_name]["name"]);
            if (move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_dir . $filename)) {
                $uploaded_files[] = $filename;
            } else {
                $all_uploads_successful = false;
                break;
            }
        } else {
            $all_uploads_successful = false;
            break;
        }
    }

    if (!$all_uploads_successful) {
        echo "<script>alert('Please upload all required documents.'); window.history.back();</script>";
        exit();
    }

    $documents = implode(",", $uploaded_files);

    // Check if already applied (only block if pending or approved, allow if rejected)
    $check_query = "SELECT * FROM job_applications WHERE ofw_id = ? AND job_id = ? AND status IN ('pending', 'approved')";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $ofw_id, $job_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('You have already applied for this job!'); window.location.href='view_jobs.php';</script>";
    } else {
        // Check total number of applications (limit to 3 total = 1 original + 2 re-applications)
        $count_query = "SELECT COUNT(*) as total FROM job_applications WHERE ofw_id = ? AND job_id = ?";
        $count_stmt = $conn->prepare($count_query);
        $count_stmt->bind_param("ii", $ofw_id, $job_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_row = $count_result->fetch_assoc();
        $total_applications = $count_row['total'];
        
        if ($total_applications >= 3) {
            echo "<script>alert('You have reached the maximum number of applications (2 re-applications) for this job.'); window.location.href='view_jobs.php';</script>";
        } else {
            $apply_query = "INSERT INTO job_applications (ofw_id, job_id, full_name, email, phone, documents) 
                            VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($apply_query);
            $stmt->bind_param("iissss", $ofw_id, $job_id, $full_name, $email, $phone, $documents);
            $stmt->execute();
            echo "<script>alert('Application submitted successfully!'); window.location.href='view_jobs.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job - OFW Management</title>
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
        }
        .content {
            margin-left: 256px;
            padding: 2rem;
            min-height: 100vh;
        }
    </style>
</head>
<body class="bg-gray-50">

<?php include 'sidebar.php'; ?>

<main class="lg:ml-64 p-4 lg:p-8 min-h-screen pt-20 lg:pt-8">
    <!-- Page Header -->
    <header class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-1 bg-blue-600 rounded-full"></div>
                <div>
                    <h1 class="text-3xl font-bold text-white drop-shadow-lg">Apply for Job</h1>
                    <p class="text-gray-200 text-sm mt-1"><?= htmlspecialchars($job["job_title"]); ?></p>
                </div>
            </div>
            <a href="view_jobs.php" class="inline-flex items-center px-4 py-2 bg-white/90 hover:bg-white text-gray-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <i class="bi bi-arrow-left mr-2"></i>
                Back to Jobs
            </a>
        </div>
    </header>

    <!-- Form Card -->
    <section class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="bi bi-file-earmark-person mr-3"></i>
                    Job Application Form
                </h2>
            </div>

            <!-- Card Body -->
            <div class="p-8">
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <!-- Personal Information -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="bi bi-person-lines-fill text-blue-600 mr-2"></i>
                            Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="full_name" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                       placeholder="Enter your full name" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                <input type="email" name="email" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                       placeholder="your@email.com" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                                <input type="text" name="phone" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                       placeholder="+63 XXX XXX XXXX" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upload Requirements -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                            <i class="bi bi-file-earmark-arrow-up-fill text-blue-600 mr-2"></i>
                            Upload Requirements
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Please upload each required document separately below.</p>
                        
                        <div class="space-y-4">
                            <?php foreach ($requirements_list as $index => $req_name): ?>
                                <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-200 hover:border-blue-400 transition-all">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-sm font-bold text-gray-900 flex items-center">
                                            <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-600 text-white rounded-full text-xs mr-2">
                                                <?= ($index + 1) ?>
                                            </span>
                                            <?= htmlspecialchars($req_name) ?>
                                        </label>
                                    </div>
                                    <input type="file" name="req_<?= $index ?>" id="req_<?= $index ?>" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
                                           required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                           onchange="previewFile(this)">
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="bi bi-info-circle mr-1"></i>
                                        Accepted: PDF, DOC, DOCX, JPG, PNG (Max 5MB)
                                    </p>
                                    <div id="preview-req_<?= $index ?>" class="mt-2 hidden">
                                        <div class="flex items-center text-green-600 text-sm">
                                            <i class="bi bi-check-circle-fill mr-2"></i>
                                            <span id="filename-req_<?= $index ?>"></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center pt-4">
                        <button type="submit" 
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                            <i class="bi bi-send-fill mr-2"></i>
                            Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
function previewFile(input) {
    var index = input.id;
    var preview = document.getElementById('preview-' + index);
    var filename = document.getElementById('filename-' + index);
    
    if (input.files && input.files[0]) {
        var file = input.files[0];
        filename.textContent = file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)';
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
}
</script>

</body>
</html>
