<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "ofw") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

if (isset($_GET['id'])) {
    $benefit_id = intval($_GET['id']);
    $query = "SELECT * FROM benefits WHERE id = $benefit_id";
    $result = $conn->query($query);
    
    if ($result->num_rows == 0) {
        echo "<script>alert('Benefit not found!'); window.location.href='benefits.php';</script>";
        exit();
    }
    $benefit = $result->fetch_assoc();
} else {
    header("Location: benefits.php");
    exit();
}

// Parse requirements
$requirements_list = [];
if (!empty($benefit['requirements'])) {
    $raw_requirements = preg_split('/\r\n|\r|\n/', $benefit['requirements']);
    foreach ($raw_requirements as $req) {
        $req = trim($req);
        if (!empty($req)) {
            $requirements_list[] = $req;
        }
    }
} else {
    $requirements_list = ['Death Certificate', 'Burial Permit', 'Valid IDs', 'CENOMAR', 'Proof of Relationship', 'Passport', 'Police Report'];
}

if (empty($requirements_list)) {
    $requirements_list = ['Death Certificate', 'Burial Permit', 'Valid IDs'];
}

// Handle Application Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $benefit_id = intval($_POST["benefit_id"]);
    $application_type = mysqli_real_escape_string($conn, $_POST["application_type"]);
    $documents = "";

    $target_dir = "uploads/";
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $uploaded_files = [];
    $all_uploads_successful = true;

    foreach ($requirements_list as $index => $req_name) {
        $input_name = "req_" . $index;
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $req_name));
        if (empty($slug)) $slug = "req" . $index;

        if (!empty($_FILES[$input_name]["name"])) {
            if ($_FILES[$input_name]['error'] !== UPLOAD_ERR_OK) {
                $all_uploads_successful = false;
                break; 
            }

            $filename = time() . "_" . $slug . "_" . basename($_FILES[$input_name]["name"]);
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_file)) {
                // Set proper file permissions
                chmod($target_file, 0644);
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
        foreach ($uploaded_files as $file) {
            if (file_exists($target_dir . $file)) {
                unlink($target_dir . $file);
            }
        }
        echo "<script>alert('A required file upload failed. Please ensure all required documents are selected and try again.'); window.history.back();</script>";
        exit();
    }

    $documents = implode(",", $uploaded_files);

    // Check if already applied (only block if pending or approved, allow if rejected)
    $check_stmt = $conn->prepare("SELECT id FROM benefit_applications WHERE user_id = ? AND benefit_id = ? AND status IN ('pending', 'approved')");
    $check_stmt->bind_param("ii", $user_id, $benefit_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        echo "<script>alert('You have already applied for this benefit.'); window.location.href='benefits.php';</script>";
    } else {
        // Check total number of applications (limit to 3 total = 1 original + 2 re-applications)
        $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM benefit_applications WHERE user_id = ? AND benefit_id = ?");
        $count_stmt->bind_param("ii", $user_id, $benefit_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_row = $count_result->fetch_assoc();
        $total_applications = $count_row['total'];
        
        if ($total_applications >= 3) {
            echo "<script>alert('You have reached the maximum number of applications (2 re-applications) for this benefit.'); window.location.href='benefits.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO benefit_applications (user_id, benefit_id, application_type, documents, status, applied_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
            $stmt->bind_param("iiss", $user_id, $benefit_id, $application_type, $documents);
            
            if ($stmt->execute()) {
                echo "<script>alert('Application submitted successfully!'); window.location.href='benefits.php';</script>";
            } else {
                echo "<script>alert('Error submitting application.');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Benefit - OFW Management</title>
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
    <header class="mb-6 md:mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center space-x-3 md:space-x-4">
                <div class="h-10 md:h-12 w-1 bg-blue-600 rounded-full"></div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white drop-shadow-lg">Apply for Benefit</h1>
                    <p class="text-gray-200 text-xs md:text-sm mt-1"><?= htmlspecialchars($benefit['title']) ?></p>
                </div>
            </div>
            <a href="benefits.php" class="inline-flex items-center justify-center px-4 py-2 bg-white/90 hover:bg-white text-gray-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 text-sm md:text-base">
                <i class="bi bi-arrow-left mr-2"></i>
                Back to Benefits
            </a>
        </div>
    </header>
    
    <section class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl md:rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 md:px-8 py-4 md:py-6">
                <h2 class="text-xl md:text-2xl font-bold text-white flex items-center">
                    <i class="bi bi-file-earmark-medical mr-2 md:mr-3"></i>
                    Benefit Application Form
                </h2>
                <p class="text-blue-100 text-xs md:text-sm mt-2"><?= htmlspecialchars($benefit['description']) ?></p>
            </div>

            <div class="p-4 md:p-8">
                <form id="applicationForm" method="POST" enctype="multipart/form-data" class="space-y-4 md:space-y-6">
                    <input type="hidden" name="benefit_id" value="<?= $benefit['id'] ?>">
                    <input type="hidden" name="application_type" value="General Assistance">
                    
                    <div>
                        <h3 class="text-base md:text-lg font-bold text-gray-900 mb-2 flex items-center">
                            <i class="bi bi-1-circle-fill text-blue-600 mr-2 text-xl md:text-2xl"></i>
                            Upload Required Documents
                        </h3>
                        <p class="text-xs md:text-sm text-gray-600 mb-3 md:mb-4">Please upload each required document separately below.</p>
                        
                        <div class="space-y-3 md:space-y-4">
                            <?php foreach ($requirements_list as $index => $req_name): ?>
                                <div class="bg-gray-50 rounded-lg p-3 md:p-4 border-2 border-gray-200 hover:border-blue-400 transition-all">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-xs md:text-sm font-bold text-gray-900 flex items-center">
                                            <span class="inline-flex items-center justify-center w-5 h-5 md:w-6 md:h-6 bg-blue-600 text-white rounded-full text-xs mr-2">
                                                <?= ($index + 1) ?>
                                            </span>
                                            <span class="break-words"><?= htmlspecialchars($req_name) ?></span>
                                        </label>
                                    </div>
                                    <input type="file" name="req_<?= $index ?>" id="req_<?= $index ?>" 
                                           class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-xs md:text-sm file:mr-2 md:file:mr-4 file:py-1 md:file:py-2 file:px-2 md:file:px-4 file:rounded-lg file:border-0 file:text-xs md:file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
                                           required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                           onchange="previewFile(this)">
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="bi bi-info-circle mr-1"></i>
                                        Accepted: PDF, DOC, DOCX, JPG, PNG (Max 5MB)
                                    </p>
                                    <div id="preview-req_<?= $index ?>" class="mt-2 hidden">
                                        <div class="flex items-center text-green-600 text-xs md:text-sm">
                                            <i class="bi bi-check-circle-fill mr-2"></i>
                                            <span id="filename-req_<?= $index ?>" class="break-all"></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="flex justify-center pt-3 md:pt-4">
                        <button type="submit" id="submitBtn" 
                                class="w-full md:w-auto inline-flex items-center justify-center px-6 md:px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed text-sm md:text-base" 
                                disabled>
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
    checkAllInputs();
}

function checkAllInputs() {
    const submitBtn = document.getElementById("submitBtn");
    const requiredInputs = document.querySelectorAll("input[required], select[required]");
    let allFilled = true;
    
    requiredInputs.forEach(input => {
        if (!input.value) allFilled = false;
    });
    
    submitBtn.disabled = !allFilled;
}

document.addEventListener("DOMContentLoaded", function() {
    const requiredInputs = document.querySelectorAll("input[required], select[required]");
    requiredInputs.forEach(input => input.addEventListener("change", checkAllInputs));
    checkAllInputs();
});

document.getElementById("applicationForm").addEventListener("submit", function(event) {
    var confirmation = confirm("Are you sure you want to submit this application? Please review all your details and uploaded documents.");
    if (!confirmation) {
        event.preventDefault();
    }
});
</script>

</body>
</html>
