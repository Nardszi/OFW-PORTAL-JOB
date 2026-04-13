<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

// Handle status update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_status'])) {
    $case_id = intval($_POST['case_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $conn->query("UPDATE case_intake SET status='$new_status' WHERE id=$case_id");
    echo "<script>alert('Status updated.'); window.location.href='manage_case_intake.php';</script>";
    exit();
}

// Filters
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

$where = "WHERE 1=1";
if ($status_filter) $where .= " AND ci.status = '$status_filter'";
if ($search) $where .= " AND (ci.ofw_first_name LIKE '%$search%' OR ci.ofw_last_name LIKE '%$search%' OR ci.welfare_case_no LIKE '%$search%' OR ci.requesting_party LIKE '%$search%')";

$cases = $conn->query("
    SELECT ci.*, u.name as submitted_by_name, u.email as submitted_by_email
    FROM case_intake ci
    LEFT JOIN users u ON ci.user_id = u.id
    $where
    ORDER BY ci.submitted_at DESC
");

$total_pending  = $conn->query("SELECT COUNT(*) as c FROM case_intake WHERE status='pending'")->fetch_assoc()['c'];
$total_reviewed = $conn->query("SELECT COUNT(*) as c FROM case_intake WHERE status='reviewed'")->fetch_assoc()['c'];
$total_closed   = $conn->query("SELECT COUNT(*) as c FROM case_intake WHERE status='closed'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Intake - Admin | OFW Management</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
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
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: -1;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<main class="lg:ml-64 p-4 lg:p-8 min-h-screen pt-20 lg:pt-8">
    <!-- Header -->
    <header class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-1 bg-blue-600 rounded-full"></div>
                <div>
                    <h1 class="text-3xl font-bold text-white drop-shadow-lg">Case Intake Applicants</h1>
                    <p class="text-gray-200 text-sm mt-1">Review and manage submitted case intake sheets</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6 max-w-lg">
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600"><?= $total_pending ?></p>
            <p class="text-xs text-yellow-700 font-medium mt-1">Pending</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-blue-600"><?= $total_reviewed ?></p>
            <p class="text-xs text-blue-700 font-medium mt-1">Reviewed</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-green-600"><?= $total_closed ?></p>
            <p class="text-xs text-green-700 font-medium mt-1">Closed</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow p-4 mb-6 flex flex-col md:flex-row gap-3">
        <form method="GET" class="flex flex-col md:flex-row gap-3 w-full">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Search by name, case no., requesting party..."
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400">
                <option value="">All Status</option>
                <option value="pending"  <?= $status_filter==='pending'  ? 'selected':'' ?>>Pending</option>
                <option value="reviewed" <?= $status_filter==='reviewed' ? 'selected':'' ?>>Reviewed</option>
                <option value="closed"   <?= $status_filter==='closed'   ? 'selected':'' ?>>Closed</option>
            </select>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                <i class="bi bi-search mr-1"></i> Filter
            </button>
            <?php if ($search || $status_filter): ?>
            <a href="manage_case_intake.php" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-semibold transition text-center">
                Clear
            </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Case No.</th>
                        <th class="px-4 py-3 text-left">OFW Name</th>
                        <th class="px-4 py-3 text-left">Requesting Party</th>
                        <th class="px-4 py-3 text-left">Submitted By</th>
                        <th class="px-4 py-3 text-left">Date Filed</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if ($cases && $cases->num_rows > 0):
                        $i = 1;
                        while ($row = $cases->fetch_assoc()):
                            $badge = ['pending'=>'bg-yellow-100 text-yellow-700','reviewed'=>'bg-blue-100 text-blue-700','closed'=>'bg-green-100 text-green-700'];
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-500"><?= $i++ ?></td>
                        <td class="px-4 py-3 font-medium text-gray-800"><?= htmlspecialchars($row['welfare_case_no'] ?: 'N/A') ?></td>
                        <td class="px-4 py-3 text-gray-800">
                            <?= htmlspecialchars(trim($row['ofw_first_name'] . ' ' . $row['ofw_last_name'])) ?: 'N/A' ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($row['requesting_party'] ?: 'N/A') ?></td>
                        <td class="px-4 py-3 text-gray-600">
                            <div><?= htmlspecialchars($row['submitted_by_name'] ?? 'N/A') ?></div>
                            <div class="text-xs text-gray-400"><?= htmlspecialchars($row['submitted_by_email'] ?? '') ?></div>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?= $row['date_filed'] ? date('M d, Y', strtotime($row['date_filed'])) : 'N/A' ?></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $badge[$row['status']] ?? 'bg-gray-100 text-gray-600' ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="openModal(<?= htmlspecialchars(json_encode($row)) ?>)"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition mr-1">
                                <i class="bi bi-eye-fill mr-1"></i> View
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                            <i class="bi bi-inbox text-4xl block mb-2"></i>
                            No case intake submissions found.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Detail Modal -->
<div id="caseModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h2 class="text-lg font-bold text-white flex items-center">
                <i class="bi bi-file-earmark-text-fill mr-2"></i>
                Case Intake Details
            </h2>
            <div class="flex items-center gap-3">
                <a id="modal_print_link" href="#" target="_blank"
                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded-lg text-xs font-semibold transition">
                    <i class="bi bi-printer-fill"></i> Print
                </a>
                <button onclick="closeModal()" class="text-white/80 hover:text-white text-2xl leading-none">&times;</button>
            </div>
        </div>
        <div class="p-6 space-y-4 text-sm" id="modalContent"></div>
        <!-- Status Update -->
        <div class="px-6 pb-6">
            <form method="POST" class="flex items-center gap-3">
                <input type="hidden" name="case_id" id="modal_case_id">
                <input type="hidden" name="update_status" value="1">
                <label class="font-semibold text-gray-700">Update Status:</label>
                <select name="new_status" id="modal_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400">
                    <option value="pending">Pending</option>
                    <option value="reviewed">Reviewed</option>
                    <option value="closed">Closed</option>
                </select>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                    Save
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(data) {
    document.getElementById('modal_case_id').value = data.id;
    document.getElementById('modal_status').value = data.status;
    document.getElementById('modal_print_link').href = 'print_case_intake.php?id=' + data.id;

    const nature = (() => {
        try { return JSON.parse(data.nature_of_case || '[]'); } catch(e) { return []; }
    })();

    const row = (label, val) => val
        ? `<div class="flex gap-2"><span class="font-semibold text-gray-600 w-44 shrink-0">${label}:</span><span class="text-gray-800">${val}</span></div>`
        : '';

    document.getElementById('modalContent').innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 border-b pb-4">
            ${row('Welfare Case No.', data.welfare_case_no)}
            ${row('Date Filed', data.date_filed)}
            ${row('Requesting Party', data.requesting_party)}
            ${row('Relationship to OFW', data.relationship_to_ofw)}
            ${row('Requesting Contact', data.requesting_contact)}
            ${row('Requesting Address', data.requesting_address)}
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 border-b pb-4">
            <div class="md:col-span-2 font-bold text-gray-800">OFW Information</div>
            ${row('OFW Name', [data.ofw_first_name, data.ofw_middle_name, data.ofw_last_name].filter(Boolean).join(' '))}
            ${row('Contact', data.ofw_contact)}
            ${row('FB Account', data.ofw_fb_account)}
            ${row('Address', data.ofw_address)}
            ${row('Sex', data.ofw_sex)}
            ${row('Civil Status', data.ofw_civil_status)}
            ${row('Birthdate', data.ofw_birthdate)}
            ${row('Age', data.ofw_age)}
            ${row('Passport No.', data.ofw_passport_no)}
            ${row('Nature of Work', data.ofw_nature_of_work)}
            ${row('Length of Service', data.ofw_length_of_service)}
            ${row('Jobsite Abroad', data.ofw_jobsite_abroad)}
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 border-b pb-4">
            <div class="md:col-span-2 font-bold text-gray-800">Employer / Agency</div>
            ${row('Employer Name', data.employer_name)}
            ${row('Employer Address', data.employer_address)}
            ${row('Agency Name', data.agency_name)}
            ${row('Agency Address', data.agency_address)}
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 border-b pb-4">
            ${row('Date of Departure', data.date_of_departure)}
            ${row('Date of Arrival', data.date_of_arrival)}
            ${row('OWWA Membership Payment', data.owwa_membership_payment)}
        </div>
        <div class="border-b pb-4">
            <p class="font-bold text-gray-800 mb-2">Nature of Case / Complaint</p>
            ${nature.length ? '<div class="flex flex-wrap gap-2">' + nature.map(n => `<span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">${n}</span>`).join('') + '</div>' : '<span class="text-gray-400 text-xs">None selected</span>'}
            ${data.other_concerns ? `<p class="mt-2 text-sm text-gray-700"><span class="font-semibold">Other:</span> ${data.other_concerns}</p>` : ''}
        </div>
        <div class="border-b pb-4">
            <p class="font-bold text-gray-800 mb-1">Intake Information / Facts of the Case</p>
            <p class="text-gray-700 whitespace-pre-wrap">${data.intake_facts || '<span class="text-gray-400">N/A</span>'}</p>
        </div>
        <div>
            <p class="font-bold text-gray-800 mb-1">Assistance Needed</p>
            <p class="text-gray-700 whitespace-pre-wrap">${data.assistance_needed || '<span class="text-gray-400">N/A</span>'}</p>
        </div>
    `;

    document.getElementById('caseModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('caseModal').classList.add('hidden');
}

document.getElementById('caseModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
</body>
</html>
