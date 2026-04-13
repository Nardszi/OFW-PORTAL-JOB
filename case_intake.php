<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "ofw") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

$user_id = $_SESSION["user_id"];
$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date_filed      = !empty($_POST['date_filed'])       ? $_POST['date_filed']       : null;
    $ofw_birthdate   = !empty($_POST['ofw_birthdate'])    ? $_POST['ofw_birthdate']    : null;
    $date_departure  = !empty($_POST['date_of_departure'])? $_POST['date_of_departure']: null;
    $date_arrival    = !empty($_POST['date_of_arrival'])  ? $_POST['date_of_arrival']  : null;
    $ofw_age         = !empty($_POST['ofw_age'])          ? intval($_POST['ofw_age'])  : null;

    $nature_raw = isset($_POST['nature_of_case']) && is_array($_POST['nature_of_case'])
        ? $_POST['nature_of_case'] : [];
    $nature_of_case = mysqli_real_escape_string($conn, json_encode($nature_raw));

    // Map raw POST to escaped vars
    $data = [];
    foreach ([
        'welfare_case_no','requesting_party','relationship_to_ofw','requesting_contact',
        'requesting_address','ofw_last_name','ofw_first_name','ofw_middle_name',
        'ofw_contact','ofw_fb_account','ofw_address','ofw_sex','ofw_civil_status',
        'ofw_passport_no','ofw_nature_of_work','ofw_length_of_service','ofw_jobsite_abroad',
        'employer_name','employer_address','agency_name','agency_address',
        'owwa_membership_payment','other_concerns','intake_facts','assistance_needed'
    ] as $f) {
        $data[$f] = isset($_POST[$f]) ? trim($_POST[$f]) : '';
    }

    // Use simple query to avoid bind_param type mismatch issues
    $welfare_case_no        = mysqli_real_escape_string($conn, $data['welfare_case_no']);
    $requesting_party       = mysqli_real_escape_string($conn, $data['requesting_party']);
    $relationship_to_ofw    = mysqli_real_escape_string($conn, $data['relationship_to_ofw']);
    $requesting_contact     = mysqli_real_escape_string($conn, $data['requesting_contact']);
    $requesting_address     = mysqli_real_escape_string($conn, $data['requesting_address']);
    $ofw_last_name          = mysqli_real_escape_string($conn, $data['ofw_last_name']);
    $ofw_first_name         = mysqli_real_escape_string($conn, $data['ofw_first_name']);
    $ofw_middle_name        = mysqli_real_escape_string($conn, $data['ofw_middle_name']);
    $ofw_contact            = mysqli_real_escape_string($conn, $data['ofw_contact']);
    $ofw_fb_account         = mysqli_real_escape_string($conn, $data['ofw_fb_account']);
    $ofw_address            = mysqli_real_escape_string($conn, $data['ofw_address']);
    $ofw_sex                = mysqli_real_escape_string($conn, $data['ofw_sex']);
    $ofw_civil_status       = mysqli_real_escape_string($conn, $data['ofw_civil_status']);
    $ofw_passport_no        = mysqli_real_escape_string($conn, $data['ofw_passport_no']);
    $ofw_nature_of_work     = mysqli_real_escape_string($conn, $data['ofw_nature_of_work']);
    $ofw_length_of_service  = mysqli_real_escape_string($conn, $data['ofw_length_of_service']);
    $ofw_jobsite_abroad     = mysqli_real_escape_string($conn, $data['ofw_jobsite_abroad']);
    $employer_name          = mysqli_real_escape_string($conn, $data['employer_name']);
    $employer_address       = mysqli_real_escape_string($conn, $data['employer_address']);
    $agency_name            = mysqli_real_escape_string($conn, $data['agency_name']);
    $agency_address         = mysqli_real_escape_string($conn, $data['agency_address']);
    $owwa_payment           = mysqli_real_escape_string($conn, $data['owwa_membership_payment']);
    $other_concerns_val     = mysqli_real_escape_string($conn, $data['other_concerns']);
    $intake_facts_val       = mysqli_real_escape_string($conn, $data['intake_facts']);
    $assistance_needed_val  = mysqli_real_escape_string($conn, $data['assistance_needed']);

    $df  = $date_filed     ? "'{$date_filed}'"     : "NULL";
    $dob = $ofw_birthdate  ? "'{$ofw_birthdate}'"  : "NULL";
    $ddep = $date_departure ? "'{$date_departure}'" : "NULL";
    $darr = $date_arrival   ? "'{$date_arrival}'"   : "NULL";
    $age_val = ($ofw_age !== null) ? intval($ofw_age) : "NULL";

    $sql = "INSERT INTO case_intake (
        user_id, welfare_case_no, date_filed, requesting_party, relationship_to_ofw,
        requesting_contact, requesting_address, ofw_last_name, ofw_first_name, ofw_middle_name,
        ofw_contact, ofw_fb_account, ofw_address, ofw_sex, ofw_civil_status, ofw_birthdate,
        ofw_age, ofw_passport_no, ofw_nature_of_work, ofw_length_of_service, ofw_jobsite_abroad,
        employer_name, employer_address, agency_name, agency_address, date_of_departure,
        date_of_arrival, owwa_membership_payment, nature_of_case, other_concerns,
        intake_facts, assistance_needed
    ) VALUES (
        $user_id, '$welfare_case_no', $df, '$requesting_party', '$relationship_to_ofw',
        '$requesting_contact', '$requesting_address', '$ofw_last_name', '$ofw_first_name', '$ofw_middle_name',
        '$ofw_contact', '$ofw_fb_account', '$ofw_address', '$ofw_sex', '$ofw_civil_status', $dob,
        $age_val, '$ofw_passport_no', '$ofw_nature_of_work', '$ofw_length_of_service', '$ofw_jobsite_abroad',
        '$employer_name', '$employer_address', '$agency_name', '$agency_address', $ddep,
        $darr, '$owwa_payment', '$nature_of_case', '$other_concerns_val',
        '$intake_facts_val', '$assistance_needed_val'
    )";

    if ($conn->query($sql)) {
        echo "<script>alert('Case Intake Sheet submitted successfully!'); window.location.href='case_intake.php';</script>";
        exit();
    } else {
        $error = "Submission failed: " . $conn->error;
    }
}

// Fetch user's previous submissions
$my_cases = $conn->query("SELECT id, welfare_case_no, date_filed, status, submitted_at FROM case_intake WHERE user_id = $user_id ORDER BY submitted_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Intake Sheet - OFW Management</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
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
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: -1;
        }
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .form-input {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #111827;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .form-input::placeholder { color: #9ca3af; }
        select.form-input { cursor: pointer; }
        textarea.form-input { resize: vertical; min-height: 100px; }
        .section-divider {
            border: none;
            border-top: 1.5px solid #f3f4f6;
            margin: 20px 0;
        }
        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 16px;
        }
        .section-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px; height: 24px;
            background: #2563eb;
            color: #fff;
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .checkbox-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 16px;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 7px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .checkbox-item:hover { background: #eff6ff; }
        .checkbox-item input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: #2563eb;
            flex-shrink: 0;
            cursor: pointer;
        }
        .checkbox-item span {
            font-size: 0.85rem;
            color: #374151;
        }
        @media (max-width: 640px) {
            .checkbox-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<main class="lg:ml-64 p-4 lg:p-8 min-h-screen pt-20 lg:pt-8">
    <!-- Header -->
    <header class="mb-6">
        <div class="flex items-center space-x-4">
            <div class="h-12 w-1 bg-blue-600 rounded-full"></div>
            <div>
                <h1 class="text-3xl font-bold text-white drop-shadow-lg">Case Intake Sheet</h1>
                <p class="text-gray-200 text-sm mt-1">Submit a welfare case or complaint</p>
            </div>
        </div>
    </header>

    <?php if ($error): ?>
        <div class="max-w-5xl mx-auto mb-4 bg-blue-50 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg">
            <i class="bi bi-exclamation-triangle-fill mr-2"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Form Card -->
    <section class="max-w-5xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div style="background:linear-gradient(135deg,#1d4ed8,#2563eb);padding:20px 32px;">
                <h2 style="font-size:1.15rem;font-weight:800;color:#fff;display:flex;align-items:center;gap:10px;margin:0;">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    CASE INTAKE SHEET
                </h2>
                <p style="color:rgba(255,255,255,0.75);font-size:0.78rem;margin:4px 0 0;">Please fill in all information accurately</p>
            </div>

            <form method="POST" class="p-6 md:p-8 space-y-0">

                <!-- Top Info Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 pb-5">
                    <div>
                        <label class="form-label">Welfare Case No.</label>
                        <input type="text" name="welfare_case_no" class="form-input" placeholder="Case number (if any)">
                    </div>
                    <div>
                        <label class="form-label">Date</label>
                        <input type="date" name="date_filed" class="form-input" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div>
                        <label class="form-label">Requesting Party / NOK</label>
                        <input type="text" name="requesting_party" class="form-input" placeholder="Full name of requesting party" required>
                    </div>
                    <div>
                        <label class="form-label">Relationship to OFW</label>
                        <input type="text" name="relationship_to_ofw" class="form-input" placeholder="e.g. Spouse, Parent, Sibling">
                    </div>
                    <div>
                        <label class="form-label">Address</label>
                        <input type="text" name="requesting_address" class="form-input" placeholder="Complete address">
                    </div>
                    <div>
                        <label class="form-label">Contact Nos.</label>
                        <input type="text" name="requesting_contact" class="form-input" placeholder="+63 XXX XXX XXXX">
                    </div>
                </div>

                <hr class="section-divider">

                <!-- Section 1: OFW Info -->
                <div class="pb-5">
                    <div class="section-title">
                        <span class="section-num">1</span>
                        Name of OFW
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
                        <div>
                            <label class="form-label">Last Name</label>
                            <input type="text" name="ofw_last_name" class="form-input" placeholder="Last Name" required>
                        </div>
                        <div>
                            <label class="form-label">First Name</label>
                            <input type="text" name="ofw_first_name" class="form-input" placeholder="First Name" required>
                        </div>
                        <div>
                            <label class="form-label">Complete Middle Name</label>
                            <input type="text" name="ofw_middle_name" class="form-input" placeholder="Middle Name">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mt-4">
                        <div>
                            <label class="form-label">Contact Nos.</label>
                            <input type="text" name="ofw_contact" class="form-input" placeholder="+63 XXX XXX XXXX">
                        </div>
                        <div>
                            <label class="form-label">FB Account Name</label>
                            <input type="text" name="ofw_fb_account" class="form-input" placeholder="Facebook account name">
                        </div>
                        <div class="md:col-span-2">
                            <label class="form-label">Address</label>
                            <input type="text" name="ofw_address" class="form-input" placeholder="Complete address">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-x-4 gap-y-4 mt-4">
                        <div>
                            <label class="form-label">Sex</label>
                            <select name="ofw_sex" class="form-input">
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Civil Status</label>
                            <select name="ofw_civil_status" class="form-input">
                                <option value="">Select</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Birthdate</label>
                            <input type="date" name="ofw_birthdate" class="form-input" id="ofw_birthdate" onchange="calcAge()">
                        </div>
                        <div>
                            <label class="form-label">Age</label>
                            <input type="number" name="ofw_age" id="ofw_age" class="form-input" placeholder="Age" min="1" max="120">
                        </div>
                        <div>
                            <label class="form-label">Passport No.</label>
                            <input type="text" name="ofw_passport_no" class="form-input" placeholder="e.g. P1234567A">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 mt-4">
                        <div class="md:col-span-2">
                            <label class="form-label">Nature of Work Abroad / Position</label>
                            <input type="text" name="ofw_nature_of_work" class="form-input" placeholder="e.g. Domestic Worker, Engineer">
                        </div>
                        <div>
                            <label class="form-label">Length of Service</label>
                            <input type="text" name="ofw_length_of_service" class="form-input" placeholder="e.g. 2 years">
                        </div>
                        <div class="md:col-span-3">
                            <label class="form-label">Jobsite Abroad</label>
                            <input type="text" name="ofw_jobsite_abroad" class="form-input" placeholder="Country / City">
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                <!-- Section 2: Employer -->
                <div class="pb-5">
                    <div class="section-title">
                        <span class="section-num">2</span>
                        Name of OFW's Company / Foreign Employer
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <label class="form-label">Company / Employer Name</label>
                            <input type="text" name="employer_name" class="form-input" placeholder="Employer or company name">
                        </div>
                        <div>
                            <label class="form-label">Address / Contact Nos.</label>
                            <input type="text" name="employer_address" class="form-input" placeholder="Address and contact number">
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                <!-- Section 3: Recruitment Agency -->
                <div class="pb-5">
                    <div class="section-title">
                        <span class="section-num">3</span>
                        Name of OFW's Local Recruitment Agency
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <label class="form-label">Agency Name</label>
                            <input type="text" name="agency_name" class="form-input" placeholder="Recruitment agency name">
                        </div>
                        <div>
                            <label class="form-label">Address / Contact Nos.</label>
                            <input type="text" name="agency_address" class="form-input" placeholder="Address and contact number">
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                <!-- Section 4 & 5: Dates & OWWA -->
                <div class="pb-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
                        <div>
                            <label class="form-label">
                                <span class="section-num" style="width:18px;height:18px;font-size:0.65rem;display:inline-flex;margin-right:5px;">4</span>
                                Date of Departure <span style="font-weight:400;font-size:0.72rem;color:#6b7280;">(from Philippines)</span>
                            </label>
                            <input type="date" name="date_of_departure" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">
                                Date of Arrival <span style="font-weight:400;font-size:0.72rem;color:#6b7280;">(in Philippines)</span>
                            </label>
                            <input type="date" name="date_of_arrival" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">
                                <span class="section-num" style="width:18px;height:18px;font-size:0.65rem;display:inline-flex;margin-right:5px;">5</span>
                                Latest OWWA Membership Payment
                            </label>
                            <input type="text" name="owwa_membership_payment" class="form-input" placeholder="e.g. January 2024">
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                <!-- Nature of Case -->
                <div class="pb-5">
                    <div class="section-title" style="margin-bottom:12px;">
                        <i class="bi bi-exclamation-circle-fill" style="color:#2563eb;font-size:1rem;"></i>
                        NATURE OF CASE / COMPLAINT
                    </div>
                    <div class="checkbox-grid">
                        <?php
                        $cases = [
                            'Death', 'Disability / Physical / Mental Illness',
                            'Maltreatment', 'Abuses (Physical and Verbal)',
                            'Sexual Abuse / Harassment / Rape', 'Unpaid / Delayed Salary',
                            'Contract Violations', 'Non-Financial Support',
                            'Non-Communication', 'Deportation / Detention',
                            'Legal Assistance', 'Homesickness',
                            'Family Problems/s',
                        ];
                        foreach ($cases as $case): ?>
                        <label class="checkbox-item">
                            <input type="checkbox" name="nature_of_case[]" value="<?= htmlspecialchars($case) ?>">
                            <span><?= htmlspecialchars($case) ?></span>
                        </label>
                        <?php endforeach; ?>
                        <!-- Other Concerns spans full width -->
                        <div class="checkbox-item" style="grid-column: 1 / -1; align-items: center; gap: 8px;">
                            <input type="checkbox" name="nature_of_case[]" value="Other Concerns" id="other_check"
                                   style="width:16px;height:16px;accent-color:#2563eb;flex-shrink:0;cursor:pointer;"
                                   onchange="document.getElementById('other_input').disabled=!this.checked; if(this.checked) document.getElementById('other_input').focus();">
                            <label for="other_check" style="font-size:0.85rem;color:#374151;font-weight:600;white-space:nowrap;cursor:pointer;">Other Concerns:</label>
                            <input type="text" name="other_concerns" id="other_input" disabled
                                   class="form-input" style="flex:1;" placeholder="Please specify...">
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                <!-- Intake Facts -->
                <div class="pb-5">
                    <label class="form-label" style="font-size:0.9rem;margin-bottom:8px;">
                        <i class="bi bi-file-text" style="color:#2563eb;margin-right:6px;"></i>
                        INTAKE INFORMATION / FACTS OF THE CASE
                    </label>
                    <textarea name="intake_facts" rows="6" class="form-input"
                              placeholder="Describe the facts and details of the case in full..."></textarea>
                </div>

                <hr class="section-divider">

                <!-- Assistance Needed -->
                <div class="pb-5">
                    <label class="form-label" style="font-size:0.9rem;margin-bottom:8px;">
                        <i class="bi bi-hand-index-thumb" style="color:#2563eb;margin-right:6px;"></i>
                        ASSISTANCE NEEDED
                    </label>
                    <textarea name="assistance_needed" rows="4" class="form-input"
                              placeholder="Describe the type of assistance needed..."></textarea>
                </div>

                <!-- Submit -->
                <div style="display:flex;justify-content:center;padding-top:8px;">
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:8px;padding:12px 36px;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#fff;font-weight:700;font-size:0.95rem;border:none;border-radius:10px;cursor:pointer;box-shadow:0 4px 14px rgba(37,99,235,0.35);transition:all 0.2s;"
                            onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 18px rgba(37,99,235,0.45)'"
                            onmouseout="this.style.transform='';this.style.boxShadow='0 4px 14px rgba(37,99,235,0.35)'">
                        <i class="bi bi-send-fill"></i>
                        Submit Case Intake Sheet
                    </button>
                </div>

            </form>
        </div>

        <!-- My Previous Submissions -->
        <?php if ($my_cases && $my_cases->num_rows > 0): ?>
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mt-6">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-8 py-4">
                <h2 class="text-lg font-bold text-white flex items-center">
                    <i class="bi bi-clock-history mr-2"></i>
                    My Previous Submissions
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600 font-semibold">#</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-semibold">Case No.</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-semibold">Date Filed</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-semibold">Status</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-semibold">Submitted</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $i = 1; while ($row = $my_cases->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-500"><?= $i++ ?></td>
                            <td class="px-4 py-3 font-medium text-gray-800"><?= htmlspecialchars($row['welfare_case_no'] ?: 'N/A') ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= $row['date_filed'] ? date('M d, Y', strtotime($row['date_filed'])) : 'N/A' ?></td>
                            <td class="px-4 py-3">
                                <?php
                                $badge = ['pending'=>'bg-yellow-100 text-yellow-700','reviewed'=>'bg-blue-100 text-blue-700','closed'=>'bg-green-100 text-green-700'];
                                $s = $row['status'];
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $badge[$s] ?? 'bg-gray-100 text-gray-600' ?>">
                                    <?= ucfirst($s) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500"><?= date('M d, Y h:i A', strtotime($row['submitted_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </section>
</main>

<script>
function calcAge() {
    const bd = document.getElementById('ofw_birthdate').value;
    if (!bd) return;
    const today = new Date();
    const birth = new Date(bd);
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
    document.getElementById('ofw_age').value = age > 0 ? age : '';
}
</script>
</body>
</html>
