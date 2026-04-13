<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";
include "includes/notifications.php";

/* ===============================
   HANDLE APPROVE / REJECT ACTION
   =============================== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["application_id"], $_POST["action"])) {
    $application_id = intval($_POST["application_id"]);
    $action = $_POST["action"];

    if (in_array($action, ["approved", "rejected"])) {
        // Get application details before updating
        $details_query = "
            SELECT 
                a.id,
                a.ofw_id,
                a.full_name AS applicant_name,
                a.email AS applicant_email,
                j.job_title,
                j.company_name
            FROM job_applications a
            INNER JOIN jobs j ON a.job_id = j.id
            WHERE a.id = ?
        ";
        $details_stmt = $conn->prepare($details_query);
        $details_stmt->bind_param("i", $application_id);
        $details_stmt->execute();
        $details_result = $details_stmt->get_result();
        $app_details = $details_result->fetch_assoc();
        $details_stmt->close();
        
        // Update application status
        $stmt = $conn->prepare("UPDATE job_applications SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $application_id);
        $stmt->execute();
        $stmt->close();
        
        // Create notification for the user
        if ($app_details && $app_details['ofw_id']) {
            $notif_type = ($action === "approved") ? "success" : "error";
            
            if ($action === "approved") {
                $notif_title = "🎉 Congratulations! Job Application Approved";
                $notif_message = "Your job application has been APPROVED!\n\n" .
                                "Job Position: " . htmlspecialchars($app_details['job_title']) . "\n" .
                                "Company: " . htmlspecialchars($app_details['company_name']) . "\n" .
                                "Application ID: #" . $application_id . "\n" .
                                "Status: APPROVED\n\n" .
                                "Next Steps:\n" .
                                "• The employer will contact you directly regarding the next steps\n" .
                                "• Please keep your contact information updated\n" .
                                "• Check your dashboard regularly for updates\n" .
                                "• Prepare necessary documents for the interview process";
            } else {
                $notif_title = "📋 Job Application Status Update";
                $notif_message = "Your job application status has changed.\n\n" .
                                "Job Position: " . htmlspecialchars($app_details['job_title']) . "\n" .
                                "Company: " . htmlspecialchars($app_details['company_name']) . "\n" .
                                "Application ID: #" . $application_id . "\n" .
                                "Status: REJECTED\n\n" .
                                "What's Next:\n" .
                                "• Don't be discouraged - keep applying to other opportunities\n" .
                                "• Review and update your profile to improve your chances\n" .
                                "• Browse other available job postings on our platform\n" .
                                "• Consider applying for benefits and assistance programs\n\n" .
                                "💡 Tip: Each application is a learning opportunity. Keep improving your skills and qualifications!";
            }
            
            createNotification($conn, $app_details['ofw_id'], $notif_type, $notif_title, $notif_message, "view_jobs.php");
        }
        
        // Send email notification for both approved and rejected
        if ($app_details) {
            $to = $app_details['applicant_email'];
            
            if ($action === "approved") {
                $subject = "Job Application Approved - OFW Management System";
                $headerColor = "#10b981";
                $statusColor = "#10b981";
                $icon = "🎉";
                $title = "Congratulations!";
                $subtitle = "Your Job Application Has Been Approved";
                $message = "We are pleased to inform you that your job application has been <strong style='color: #10b981;'>APPROVED</strong>!";
                $nextSteps = "
                    <h3 style='color: #2563eb;'>Next Steps:</h3>
                    <ol style='padding-left: 20px;'>
                        <li>The employer will contact you directly regarding the next steps</li>
                        <li>Please keep your contact information updated</li>
                        <li>Check your dashboard regularly for updates</li>
                        <li>Prepare necessary documents for the interview process</li>
                    </ol>
                ";
            } else {
                $subject = "Job Application Update - OFW Management System";
                $headerColor = "#ef4444";
                $statusColor = "#ef4444";
                $icon = "📋";
                $title = "Application Status Update";
                $subtitle = "Your Job Application Status Has Changed";
                $message = "We regret to inform you that your job application has been <strong style='color: #ef4444;'>REJECTED</strong>.";
                $nextSteps = "
                    <h3 style='color: #2563eb;'>What's Next:</h3>
                    <ul style='padding-left: 20px;'>
                        <li>Don't be discouraged - keep applying to other opportunities</li>
                        <li>Review and update your profile to improve your chances</li>
                        <li>Browse other available job postings on our platform</li>
                        <li>Consider applying for benefits and assistance programs</li>
                    </ul>
                    <p style='margin-top: 20px; padding: 15px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 5px;'>
                        <strong>💡 Tip:</strong> Each application is a learning opportunity. Keep improving your skills and qualifications!
                    </p>
                ";
            }
            
            $htmlContent = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: " . $headerColor . "; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
                    .info-box { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid " . $headerColor . "; border-radius: 5px; }
                    .footer { background: #1f2937; color: #9ca3af; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; font-size: 12px; }
                    .button { display: inline-block; padding: 12px 30px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                    .icon { font-size: 48px; text-align: center; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1 style='margin: 0; font-size: 28px;'>" . $icon . " " . $title . "</h1>
                        <p style='margin: 10px 0 0 0; font-size: 16px;'>" . $subtitle . "</p>
                    </div>
                    <div class='content'>
                        <p>Dear <strong>" . htmlspecialchars($app_details['applicant_name']) . "</strong>,</p>
                        <p>" . $message . "</p>
                        
                        <div class='info-box'>
                            <h3 style='margin-top: 0; color: #2563eb;'>Application Details:</h3>
                            <p><strong>Job Position:</strong> " . htmlspecialchars($app_details['job_title']) . "</p>
                            <p><strong>Company:</strong> " . htmlspecialchars($app_details['company_name']) . "</p>
                            <p><strong>Application ID:</strong> #" . $application_id . "</p>
                            <p><strong>Status:</strong> <span style='color: " . $statusColor . "; font-weight: bold;'>" . ucfirst($action) . "</span></p>
                        </div>
                        
                        " . $nextSteps . "
                        
                        <div style='text-align: center;'>
                            <a href='http://" . $_SERVER['HTTP_HOST'] . "/dashboard.php' class='button'>View Dashboard</a>
                        </div>
                        
                        <p style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;'>
                            If you have any questions, please don't hesitate to contact our support team.
                        </p>
                    </div>
                    <div class='footer'>
                        <p style='margin: 0;'>&copy; " . date('Y') . " OFW Management System. All rights reserved.</p>
                        <p style='margin: 10px 0 0 0;'>This is an automated message. Please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            // Send email using Brevo API
            $apiKey = 'YOUR_BREVO_API_KEY'; // TODO: Replace with your actual Brevo API Key
            $url = 'https://api.brevo.com/v3/smtp/email';

            $data = [
                'sender' => ['name' => 'OFW Management System', 'email' => 'ralphbelandres1@gmail.com'],
                'to' => [['email' => $to, 'name' => $app_details['applicant_name']]],
                'subject' => $subject,
                'htmlContent' => $htmlContent
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'api-key: ' . $apiKey,
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $emailStatus = ($httpCode == 201 || $httpCode == 200) ? " Email notification sent to applicant." : " (Email notification failed to send)";
        } else {
            $emailStatus = "";
        }
        
        $_SESSION['message'] = "Application " . $action . " successfully!" . $emailStatus;
    }

    header("Location: manage_applications.php");
    exit();
}

// Handle Delete Application
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Get documents path to delete file
    $stmt = $conn->prepare("SELECT documents FROM job_applications WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $files = explode(',', $row['documents']);
        foreach ($files as $file) {
            if (!empty($file) && file_exists("uploads/" . trim($file))) {
                unlink("uploads/" . trim($file));
            }
        }
    }
    $stmt->close();

    $conn->query("DELETE FROM job_applications WHERE id = $delete_id");
    $_SESSION['message'] = "Application deleted successfully!";
    header("Location: manage_applications.php");
    exit();
}

// Pagination
$limit_options = [10, 20, 50, 100];
$limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $limit_options) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filters
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'pending';
$job_filter = isset($_GET['job']) ? intval($_GET['job']) : 0;

// Base query and where clause
$base_query = "FROM job_applications a JOIN jobs j ON a.job_id = j.id LEFT JOIN users u ON a.ofw_id = u.id";
$where_conditions = [];
$params = [];
$types = "";

// Status filter
if (!empty($status_filter) && $status_filter !== 'all') {
    $where_conditions[] = "a.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

// Search filter (name, company, job title, email)
if (!empty($search_term)) {
    $where_conditions[] = "(a.full_name LIKE ? OR j.job_title LIKE ? OR j.company_name LIKE ? OR a.email LIKE ?)";
    $search_like = "%" . $search_term . "%";
    $params[] = $search_like;
    $params[] = $search_like;
    $params[] = $search_like;
    $params[] = $search_like;
    $types .= "ssss";
}

// Job filter
if ($job_filter > 0) {
    $where_conditions[] = "a.job_id = ?";
    $params[] = $job_filter;
    $types .= "i";
}

$where_clause = !empty($where_conditions) ? " WHERE " . implode(" AND ", $where_conditions) : "";

// Count total rows
$total_query = "SELECT COUNT(a.id) as total " . $base_query . $where_clause;
if (!empty($params)) {
    $stmt_total = $conn->prepare($total_query);
    $stmt_total->bind_param($types, ...$params);
    $stmt_total->execute();
    $total_result = $stmt_total->get_result();
} else {
    $total_result = $conn->query($total_query);
}
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
if (isset($stmt_total)) $stmt_total->close();

// Get all jobs for filter dropdown
$jobs_query = "SELECT id, job_title, company_name FROM jobs ORDER BY job_title ASC";
$jobs_result = $conn->query($jobs_query);

// Count applications by status
$count_query = "SELECT status, COUNT(*) as count FROM job_applications GROUP BY status";
$count_result = $conn->query($count_query);
$status_counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
while ($row = $count_result->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
}

// Sorting
$sort_mapping = [
    'job_title' => 'j.job_title',
    'company_name' => 'j.company_name',
    'full_name' => 'a.full_name',
    'applied_at' => 'a.applied_at'
];
$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $sort_mapping) ? $_GET['sort'] : 'applied_at';
$order = isset($_GET['order']) && strtoupper($_GET['order']) == 'ASC' ? 'ASC' : 'DESC';
$sort_sql = $sort_mapping[$sort];

// Fetch applications with pagination and check for re-applications
$applications_query = "SELECT a.id, j.job_title, j.company_name, a.full_name, a.email, a.phone, a.documents, a.status, a.applied_at, a.ofw_id, u.profile_picture,
                       (SELECT COUNT(*) FROM job_applications prev 
                        WHERE prev.ofw_id = a.ofw_id 
                        AND prev.job_id = a.job_id 
                        AND prev.status = 'rejected' 
                        AND prev.applied_at < a.applied_at) AS previous_rejections
                       " . $base_query . $where_clause . " ORDER BY $sort_sql $order LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($applications_query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$applications_result = $stmt->get_result();

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
    <title>Manage Applicants - OFW Management System</title>
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
            .no-print, 
            .sidebar, 
            header, 
            .filters-section,
            button,
            .alert-message,
            nav,
            section:not(.print-section),
            form,
            select {
                display: none !important;
            }
            
            /* Show only print section */
            .print-section {
                display: block !important;
                box-shadow: none !important;
                border-radius: 0 !important;
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
            }
            
            th {
                background-color: #f0f0f0 !important;
                font-weight: bold;
            }
            
            /* Hide specific columns - Contact Info, Documents, and Actions */
            table th:nth-child(5),  /* Contact Info */
            table td:nth-child(5),
            table th:nth-child(6),  /* Documents */
            table td:nth-child(6),
            table th:nth-child(9),  /* Actions */
            table td:nth-child(9) {
                display: none !important;
            }
            
            /* Hide action buttons in table */
            td form,
            td button,
            td .inline-flex {
                display: none !important;
            }
            
            /* Adjust status badges for print */
            .px-3 {
                padding: 2px 6px !important;
                border: 1px solid #000;
                border-radius: 3px;
            }
            
            /* Hide profile pictures for cleaner print */
            img, .w-10.h-10.rounded-full {
                display: none !important;
            }
            
            /* Adjust applicant name cell */
            td:nth-child(4) .flex {
                display: block !important;
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
                    <i class="bi bi-person-lines-fill mr-2"></i>Manage Job Applicants
                </h1>
                <p class="text-gray-600">Review and manage job applications</p>
                <div class="mt-2 flex gap-2">
                    <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                        <i class="bi bi-clock mr-1"></i>Pending: <?= $status_counts['pending'] ?>
                    </span>
                    <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                        <i class="bi bi-check-circle mr-1"></i>Approved: <?= $status_counts['approved'] ?>
                    </span>
                    <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                        <i class="bi bi-x-circle mr-1"></i>Rejected: <?= $status_counts['rejected'] ?>
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="no-print inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                    <i class="bi bi-printer mr-2"></i> Print
                </button>
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

    <!-- Filters Section -->
    <section class="bg-white/95 p-6 rounded-2xl mb-6 shadow-lg no-print">
        <form method="GET" class="flex flex-col lg:flex-row gap-4 items-center">
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
                </select>
            </div>

            <!-- Status Filter -->
            <div class="flex items-center gap-2">
                <span class="text-gray-700 font-medium text-sm">Status:</span>
                <select name="status" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        onchange="this.form.submit()">
                    <option value="all" <?= ($status_filter == 'all') ? 'selected' : '' ?>>All</option>
                    <option value="pending" <?= ($status_filter == 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= ($status_filter == 'approved') ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= ($status_filter == 'rejected') ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>

            <!-- Job Filter -->
            <div class="flex items-center gap-2">
                <span class="text-gray-700 font-medium text-sm">Job:</span>
                <select name="job" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        onchange="this.form.submit()">
                    <option value="0">All Jobs</option>
                    <?php 
                    $jobs_result->data_seek(0); // Reset pointer
                    while ($job = $jobs_result->fetch_assoc()): 
                    ?>
                        <option value="<?= $job['id'] ?>" <?= ($job_filter == $job['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($job['job_title']) ?> - <?= htmlspecialchars($job['company_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Search + Button -->
            <div class="flex flex-1 items-center gap-2 min-w-0">
                <div class="relative flex-1">
                    <input type="text" 
                           name="search" 
                           class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="Search by name, email, job, or company..." 
                           value="<?= htmlspecialchars($search_term) ?>">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md whitespace-nowrap">
                    <i class="bi bi-search mr-1"></i> Search
                </button>
                <?php if(!empty($search_term) || $status_filter != 'pending' || $job_filter > 0): ?>
                    <a href="manage_applications.php" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200 whitespace-nowrap">
                        Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
        <div class="mt-3 text-gray-600 text-sm no-print">
            <i class="bi bi-info-circle mr-1"></i>
            Showing <span class="font-semibold"><?= $offset + 1 ?></span> to
            <span class="font-semibold"><?= min($offset + $limit, $total_rows) ?></span> of
            <span class="font-semibold"><?= $total_rows ?></span> applications
        </div>
    </section>

    <!-- Print Header (only visible when printing) -->
    <div class="print-header">
        <h1 style="margin: 0; font-size: 24px; font-weight: bold;">OFW Management System</h1>
        <h2 style="margin: 10px 0; font-size: 18px;">Job Applications Report</h2>
        <p style="margin: 5px 0; font-size: 12px;">Generated on: <?= date('F d, Y h:i A') ?></p>
        <?php if (!empty($search_term) || $status_filter != 'all' || $job_filter > 0): ?>
            <p style="margin: 5px 0; font-size: 11px; color: #666;">
                Filters Applied: 
                <?php if (!empty($search_term)): ?>Search: "<?= htmlspecialchars($search_term) ?>" | <?php endif; ?>
                <?php if ($status_filter != 'all'): ?>Status: <?= ucfirst($status_filter) ?> | <?php endif; ?>
                <?php if ($job_filter > 0): ?>
                    <?php 
                    $jobs_result->data_seek(0);
                    while ($job = $jobs_result->fetch_assoc()) {
                        if ($job['id'] == $job_filter) {
                            echo 'Job: ' . htmlspecialchars($job['job_title']);
                            break;
                        }
                    }
                    ?>
                <?php endif; ?>
            </p>
        <?php endif; ?>
        <p style="margin: 5px 0; font-size: 11px;">Total Records: <?= $total_rows ?></p>
    </div>

    <!-- Applications Table -->
    <section class="bg-white rounded-2xl shadow-lg overflow-hidden print-section">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">#</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('job_title', 'Job Title', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('company_name', 'Company', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('full_name', 'Applicant Name', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Contact Info</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Documents</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold"><?= sortLink('applied_at', 'Applied At', $sort, $order) ?></th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php 
                    $count = $offset + 1;
                    while ($applicant = $applications_result->fetch_assoc()) { ?>
                        <tr class="hover:bg-blue-50 transition duration-150">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $count++; ?></td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                <i class="bi bi-briefcase text-blue-600 mr-1"></i>
                                <?= htmlspecialchars($applicant["job_title"]) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <i class="bi bi-building text-gray-400 mr-1"></i>
                                <?= htmlspecialchars($applicant["company_name"]) ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <div class="flex items-center">
                                    <?php 
                                    // Check if applicant has profile picture
                                    if (!empty($applicant['profile_picture']) && file_exists($applicant['profile_picture'])) {
                                        echo '<img src="' . htmlspecialchars($applicant['profile_picture']) . '" 
                                              class="w-10 h-10 rounded-full object-cover mr-3 border-2 border-green-200 shadow-md" 
                                              alt="' . htmlspecialchars($applicant['full_name']) . '"
                                              onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
                                        echo '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center text-white font-bold mr-3 shadow-md hidden">
                                              ' . strtoupper(substr($applicant['full_name'], 0, 1)) . '
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
                                        $colorIndex = ord(strtoupper(substr($applicant['full_name'], 0, 1))) % count($colors);
                                        $gradient = $colors[$colorIndex];
                                        
                                        echo '<div class="w-10 h-10 rounded-full bg-gradient-to-br ' . $gradient . ' flex items-center justify-center text-white font-bold mr-3 shadow-md">
                                              ' . strtoupper(substr($applicant['full_name'], 0, 1)) . '
                                              </div>';
                                    }
                                    ?>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span><?= htmlspecialchars($applicant["full_name"]) ?></span>
                                            <?php if ($applicant['previous_rejections'] > 0): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800 border border-orange-300" title="This applicant was previously rejected <?= $applicant['previous_rejections'] ?> time(s) for this job">
                                                    <i class="bi bi-arrow-repeat mr-1"></i>Re-Application
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="space-y-1">
                                    <div><i class="bi bi-envelope text-gray-400 mr-1"></i><?= htmlspecialchars($applicant["email"]) ?></div>
                                    <div><i class="bi bi-telephone text-gray-400 mr-1"></i><?= htmlspecialchars($applicant["phone"]) ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php if (!empty($applicant['documents'])): ?>
                                    <?php 
                                    $file_list = [];
                                    $files_arr = explode(',', $applicant['documents']);
                                    foreach ($files_arr as $file) {
                                        $file = trim($file);
                                        if(empty($file)) continue;
                                        $file_list[] = ['file' => $file, 'label' => basename($file)];
                                    }
                                    $files_json = htmlspecialchars(json_encode($file_list), ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <button class="inline-flex items-center px-3 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-semibold rounded-lg transition duration-200" 
                                            data-files="<?= $files_json ?>" 
                                            onclick="viewDocuments(this)">
                                        <i class="bi bi-folder2-open mr-1"></i> View Files
                                    </button>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs">No Documents</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <i class="bi bi-calendar3 mr-1 text-gray-400"></i>
                                <?= date("M d, Y", strtotime($applicant["applied_at"])) ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php if ($applicant['status'] == 'pending') { ?>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="bi bi-clock-history mr-1"></i>Pending
                                    </span>
                                <?php } elseif ($applicant['status'] == 'approved') { ?>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="bi bi-check-circle-fill mr-1"></i>Approved
                                    </span>
                                <?php } else { ?>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="bi bi-x-circle-fill mr-1"></i>Rejected
                                    </span>
                                <?php } ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php if ($applicant['status'] == 'pending') { ?>
                                    <div class="flex gap-2">
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="application_id" value="<?= $applicant['id'] ?>">
                                            <input type="hidden" name="action" value="approved">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md" title="Approve">
                                                <i class="bi bi-check-lg mr-1"></i> Approve
                                            </button>
                                        </form>

                                        <form method="POST" class="inline">
                                            <input type="hidden" name="application_id" value="<?= $applicant['id'] ?>">
                                            <input type="hidden" name="action" value="rejected">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md" title="Reject">
                                                <i class="bi bi-x-lg mr-1"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                <?php } else { ?>
                                    <span class="text-gray-400 text-xs italic">Processed</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if($applications_result->num_rows == 0): ?>
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="bi bi-inbox text-6xl mb-4"></i>
                                    <p class="text-lg font-semibold">No applications found</p>
                                    <p class="text-sm">Try adjusting your search criteria</p>
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

<!-- Documents Modal -->
<div id="documentsModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden">
        <header class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold">
                <i class="bi bi-file-earmark-text-fill mr-2"></i>Submitted Documents
            </h2>
            <button onclick="closeDocumentsModal()" class="text-white hover:text-gray-200 text-2xl">&times;</button>
        </header>
        <div class="p-6 max-h-96 overflow-y-auto">
            <ul class="space-y-2" id="fileList">
                <!-- Files will be loaded here via JS -->
            </ul>
        </div>
        <footer class="bg-gray-50 px-6 py-4 flex justify-end">
            <button onclick="closeDocumentsModal()" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">Close</button>
        </footer>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <header class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4">
            <h2 class="text-xl font-bold">
                <i class="bi bi-exclamation-triangle-fill mr-2"></i>Confirm Delete
            </h2>
        </header>
        <div class="p-6">
            <p class="text-gray-700 text-lg">Are you sure you want to delete this application? This action cannot be undone.</p>
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
function viewDocuments(btn) {
    const files = JSON.parse(btn.getAttribute('data-files'));
    const list = document.getElementById('fileList');
    list.innerHTML = '';

    if (files.length === 0) {
        list.innerHTML = '<li class="text-center text-gray-500 py-4">No documents found</li>';
        document.getElementById('documentsModal').classList.remove('hidden');
        return;
    }

    files.forEach(file => {
        const li = document.createElement('li');
        li.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition';
        
        // Clean the filename - remove any path prefixes
        let fileName = file.file.trim();
        fileName = fileName.replace(/^\/+/, ''); // Remove leading slashes
        fileName = fileName.replace(/^uploads\//, ''); // Remove 'uploads/' if present
        
        // Use absolute URL from root to avoid .htaccess rewrite issues
        const baseUrl = window.location.origin;
        const filePath = baseUrl + '/uploads/' + fileName;
        
        // Debug logging
        console.log('=== Document Debug ===');
        console.log('Original file:', file.file);
        console.log('Cleaned fileName:', fileName);
        console.log('Full URL:', filePath);
        
        li.innerHTML = `
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <i class="bi bi-file-earmark-pdf text-red-600 text-2xl"></i>
                <span class="text-sm text-gray-700 truncate">${file.label}</span>
            </div>
            <div class="flex gap-2">
                <a href="${filePath}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition" target="_blank" rel="noopener noreferrer" onclick="console.log('🔍 VIEW:', '${filePath}')">
                    <i class="bi bi-eye mr-1"></i> View
                </a>
                <a href="${filePath}" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition" download="${fileName}" onclick="console.log('⬇️ DOWNLOAD:', '${filePath}')">
                    <i class="bi bi-download mr-1"></i> Download
                </a>
            </div>
        `;
        list.appendChild(li);
    });

    document.getElementById('documentsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDocumentsModal() {
    document.getElementById('documentsModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function confirmDelete(appId) {
    document.getElementById('confirmDeleteBtn').href = 'manage_applications.php?delete_id=' + appId;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDocumentsModal();
        closeDeleteModal();
    }
});
</script>

</body>
</html>
