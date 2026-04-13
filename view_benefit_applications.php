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
                ba.id,
                ba.user_id,
                ba.application_type,
                b.title AS benefit_title,
                u.name AS ofw_name,
                u.email AS ofw_email
            FROM benefit_applications ba
            INNER JOIN users u ON ba.user_id = u.id
            INNER JOIN benefits b ON ba.benefit_id = b.id
            WHERE ba.id = ?
        ";
        $details_stmt = $conn->prepare($details_query);
        $details_stmt->bind_param("i", $application_id);
        $details_stmt->execute();
        $details_result = $details_stmt->get_result();
        $app_details = $details_result->fetch_assoc();
        $details_stmt->close();
        
        // Update application status
        $stmt = $conn->prepare("UPDATE benefit_applications SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $application_id);
        $stmt->execute();
        $stmt->close();
        
        // Create notification for the user
        if ($app_details && $app_details['user_id']) {
            $notif_type = ($action === "approved") ? "success" : "error";
            
            if ($action === "approved") {
                $notif_title = "🎉 Congratulations! Benefit Application Approved";
                $notif_message = "Your benefit application has been APPROVED!\n\n" .
                                "Benefit: " . htmlspecialchars($app_details['benefit_title']) . "\n" .
                                "Type: " . htmlspecialchars($app_details['application_type']) . "\n" .
                                "Application ID: #" . $application_id . "\n" .
                                "Status: APPROVED\n\n" .
                                "Next Steps:\n" .
                                "• You will be contacted by our team regarding the next steps\n" .
                                "• Please keep your contact information updated\n" .
                                "• Check your dashboard regularly for updates\n" .
                                "• Prepare any additional documents that may be required";
            } else {
                $notif_title = "📋 Benefit Application Status Update";
                $notif_message = "Your benefit application status has changed.\n\n" .
                                "Benefit: " . htmlspecialchars($app_details['benefit_title']) . "\n" .
                                "Type: " . htmlspecialchars($app_details['application_type']) . "\n" .
                                "Application ID: #" . $application_id . "\n" .
                                "Status: REJECTED\n\n" .
                                "What's Next:\n" .
                                "• Review the requirements and ensure all documents are complete\n" .
                                "• You may reapply after addressing any issues\n" .
                                "• Contact our support team if you need clarification\n" .
                                "• Explore other available benefits and assistance programs\n\n" .
                                "💡 Note: Please ensure all required documents are properly submitted and meet the eligibility criteria before reapplying.";
            }
            
            createNotification($conn, $app_details['user_id'], $notif_type, $notif_title, $notif_message, "benefits.php");
        }
        
        // Send email notification for both approved and rejected
        if ($app_details) {
            $to = $app_details['ofw_email'];
            
            if ($action === "approved") {
                $subject = "Benefit Application Approved - OFW Management System";
                $headerColor = "#10b981";
                $statusColor = "#10b981";
                $icon = "🎉";
                $title = "Congratulations!";
                $subtitle = "Your Benefit Application Has Been Approved";
                $message = "We are pleased to inform you that your benefit application has been <strong style='color: #10b981;'>APPROVED</strong>!";
                $nextSteps = "
                    <h3 style='color: #2563eb;'>Next Steps:</h3>
                    <ol style='padding-left: 20px;'>
                        <li>You will be contacted by our team regarding the next steps</li>
                        <li>Please keep your contact information updated</li>
                        <li>Check your dashboard regularly for updates</li>
                        <li>Prepare any additional documents that may be required</li>
                    </ol>
                ";
            } else {
                $subject = "Benefit Application Update - OFW Management System";
                $headerColor = "#ef4444";
                $statusColor = "#ef4444";
                $icon = "📋";
                $title = "Application Status Update";
                $subtitle = "Your Benefit Application Status Has Changed";
                $message = "We regret to inform you that your benefit application has been <strong style='color: #ef4444;'>REJECTED</strong>.";
                $nextSteps = "
                    <h3 style='color: #2563eb;'>What's Next:</h3>
                    <ul style='padding-left: 20px;'>
                        <li>Review the requirements and ensure all documents are complete</li>
                        <li>You may reapply after addressing any issues</li>
                        <li>Contact our support team if you need clarification</li>
                        <li>Explore other available benefits and assistance programs</li>
                    </ul>
                    <p style='margin-top: 20px; padding: 15px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 5px;'>
                        <strong>💡 Note:</strong> Please ensure all required documents are properly submitted and meet the eligibility criteria before reapplying.
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
                        <p>Dear <strong>" . htmlspecialchars($app_details['ofw_name']) . "</strong>,</p>
                        <p>" . $message . "</p>
                        
                        <div class='info-box'>
                            <h3 style='margin-top: 0; color: #2563eb;'>Application Details:</h3>
                            <p><strong>Benefit Program:</strong> " . htmlspecialchars($app_details['benefit_title']) . "</p>
                            <p><strong>Assistance Type:</strong> " . htmlspecialchars($app_details['application_type']) . "</p>
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
                'to' => [['email' => $to, 'name' => $app_details['ofw_name']]],
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

    header("Location: view_benefit_applications.php");
    exit();
}

/* ===============================
   FETCH BENEFIT APPLICATIONS WITH FILTERS
   =============================== */

// Filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'pending';
$benefit_filter = isset($_GET['benefit']) ? intval($_GET['benefit']) : 0;

$where_conditions = [];
$params = [];
$types = "";

// Status filter
if (!empty($status_filter) && $status_filter !== 'all') {
    $where_conditions[] = "ba.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

// Search filter
if (!empty($search)) {
    $where_conditions[] = "(u.name LIKE ? OR u.email LIKE ? OR b.title LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

// Benefit filter
if ($benefit_filter > 0) {
    $where_conditions[] = "ba.benefit_id = ?";
    $params[] = $benefit_filter;
    $types .= "i";
}

$where_sql = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "
    SELECT 
        ba.id AS application_id,
        b.title AS benefit_title,
        ba.application_type,
        ba.documents,
        u.name AS ofw_name,
        u.email AS ofw_email,
        u.profile_picture,
        ba.applied_at,
        ba.status,
        ba.user_id,
        ba.benefit_id,
        (SELECT COUNT(*) FROM benefit_applications prev 
         WHERE prev.user_id = ba.user_id 
         AND prev.benefit_id = ba.benefit_id 
         AND prev.status = 'rejected' 
         AND prev.applied_at < ba.applied_at) AS previous_rejections
    FROM benefit_applications ba
    INNER JOIN users u ON ba.user_id = u.id
    INNER JOIN benefits b ON ba.benefit_id = b.id
    $where_sql
    ORDER BY ba.applied_at DESC
";

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

// Get all benefits for filter dropdown
$benefits_query = "SELECT id, title FROM benefits ORDER BY title ASC";
$benefits_result = $conn->query($benefits_query);

// Count applications by status (only for benefits that still exist)
$count_query = "
    SELECT ba.status, COUNT(*) as count 
    FROM benefit_applications ba
    INNER JOIN benefits b ON ba.benefit_id = b.id
    GROUP BY ba.status
";
$count_result = $conn->query($count_query);
$status_counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
while ($row = $count_result->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benefits Applicants - OFW Management System</title>
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
            .alert-message {
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
            }
            
            th {
                background-color: #f0f0f0 !important;
                font-weight: bold;
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
                    <i class="bi bi-heart-pulse-fill mr-2"></i>Benefits Applicants
                </h1>
                <p class="text-gray-600">Review and manage benefit applications</p>
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

    <!-- Filters Section -->
    <section class="bg-white/95 p-6 rounded-2xl mb-6 shadow-lg no-print">
        <form method="GET" class="flex flex-col lg:flex-row gap-4 items-start lg:items-center">
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

            <!-- Benefit Filter -->
            <div class="flex items-center gap-2">
                <span class="text-gray-700 font-medium text-sm">Benefit:</span>
                <select name="benefit" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                        onchange="this.form.submit()">
                    <option value="0">All Benefits</option>
                    <?php 
                    $benefits_result->data_seek(0); // Reset pointer
                    while ($benefit = $benefits_result->fetch_assoc()): 
                    ?>
                        <option value="<?= $benefit['id'] ?>" <?= ($benefit_filter == $benefit['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($benefit['title']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Search -->
            <div class="relative flex-1 lg:min-w-[300px]">
                <input type="text" 
                       name="search" 
                       class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Search by name, email, or benefit..." 
                       value="<?= htmlspecialchars($search) ?>">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md">
                    <i class="bi bi-search mr-1"></i> Search
                </button>
                <?php if(!empty($search) || $status_filter != 'pending' || $benefit_filter > 0): ?>
                    <a href="view_benefit_applications.php" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200">
                        Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow-md flex items-center no-print">
            <i class="bi bi-check-circle-fill text-2xl mr-3"></i>
            <span><?= $_SESSION['message']; unset($_SESSION['message']); ?></span>
        </div>
    <?php endif; ?>

    <!-- Print Header (only visible when printing) -->
    <div class="print-header">
        <h1 style="font-size: 24pt; font-weight: bold; margin: 0;">OFW Management System</h1>
        <h2 style="font-size: 18pt; margin: 10px 0;">Benefits Applicants Report</h2>
        <p style="font-size: 10pt; margin: 5px 0;">
            Generated on: <?= date('F d, Y h:i A') ?> | 
            Status: <?= ucfirst($status_filter) ?> | 
            Total Records: <?= $result->num_rows ?>
        </p>
    </div>

    <?php if ($result->num_rows > 0) { ?>
        <!-- Applications Table -->
        <section class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Benefit</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Applicant</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Date Applied</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Documents</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr class="hover:bg-blue-50 transition duration-150">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                <i class="bi bi-gift text-purple-600 mr-1"></i>
                                <?= htmlspecialchars($row['benefit_title']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center">
                                    <?php 
                                    // Check if user has profile picture
                                    if (!empty($row['profile_picture']) && file_exists($row['profile_picture'])) {
                                        echo '<img src="' . htmlspecialchars($row['profile_picture']) . '" 
                                              class="w-10 h-10 rounded-full object-cover mr-3 border-2 border-purple-200" 
                                              alt="' . htmlspecialchars($row['ofw_name']) . '"
                                              onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
                                        echo '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-bold mr-3 hidden">
                                              ' . strtoupper(substr($row['ofw_name'], 0, 1)) . '
                                              </div>';
                                    } else {
                                        echo '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-bold mr-3 shadow-md">
                                              ' . strtoupper(substr($row['ofw_name'], 0, 1)) . '
                                              </div>';
                                    }
                                    ?>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-900"><?= htmlspecialchars($row['ofw_name']) ?></span>
                                            <?php if ($row['previous_rejections'] > 0): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800 border border-orange-300" title="This applicant was previously rejected <?= $row['previous_rejections'] ?> time(s) for this benefit">
                                                    <i class="bi bi-arrow-repeat mr-1"></i>Re-Application
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <i class="bi bi-envelope mr-1"></i><?= htmlspecialchars($row['ofw_email']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div><i class="bi bi-calendar3 mr-1 text-gray-400"></i><?= date("M d, Y", strtotime($row['applied_at'])) ?></div>
                                <div class="text-xs text-gray-500"><i class="bi bi-clock mr-1"></i><?= date("h:i A", strtotime($row['applied_at'])) ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php if (!empty($row['documents'])): ?>
                                    <?php 
                                    $file_list = [];
                                    $files_arr = explode(',', $row['documents']);
                                    $has_valid_files = false;
                                    
                                    foreach ($files_arr as $file) {
                                        $file = trim($file);
                                        if(empty($file)) continue;
                                        
                                        // Check if file actually exists
                                        $clean_file = str_replace('uploads/', '', $file);
                                        $clean_file = ltrim($clean_file, '/');
                                        $file_path = 'uploads/' . $clean_file;
                                        
                                        if (file_exists($file_path)) {
                                            $file_list[] = ['file' => $file, 'label' => basename($file)];
                                            $has_valid_files = true;
                                        }
                                    }
                                    
                                    if ($has_valid_files):
                                        $files_json = htmlspecialchars(json_encode($file_list), ENT_QUOTES, 'UTF-8');
                                    ?>
                                        <button class="inline-flex items-center px-3 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-semibold rounded-lg transition duration-200" 
                                                data-files="<?= $files_json ?>" 
                                                onclick="viewDocuments(this)">
                                            <i class="bi bi-folder2-open mr-1"></i> View Files
                                        </button>
                                    <?php else: ?>
                                        <span class="text-orange-600 text-xs flex items-center">
                                            <i class="bi bi-exclamation-triangle mr-1"></i>Files Missing
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs">No Documents</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php if ($row['status'] == 'pending') { ?>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="bi bi-clock-history mr-1"></i>Pending
                                    </span>
                                <?php } elseif ($row['status'] == 'approved') { ?>
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
                                <?php if ($row['status'] == 'pending') { ?>
                                    <div class="flex gap-2">
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                                            <input type="hidden" name="action" value="approved">
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md" title="Approve">
                                                <i class="bi bi-check-lg mr-1"></i> Approve
                                            </button>
                                        </form>

                                        <form method="POST" class="inline">
                                            <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
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
                    </tbody>
                </table>
            </div>
        </section>
    <?php } else { ?>
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="bi bi-inbox text-gray-400 text-6xl mb-4 block"></i>
            <h3 class="text-2xl font-bold text-gray-600 mb-2">No benefit applications yet</h3>
            <p class="text-gray-500">Applications will appear here when OFWs apply for benefits</p>
        </div>
    <?php } ?>
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
                <i class="bi bi-file-earmark-check text-green-600 text-2xl"></i>
                <span class="text-sm font-semibold text-gray-700">${file.label}</span>
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

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDocumentsModal();
    }
});
</script>

</body>
</html>
