<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

if (!isset($_GET['id'])) {
    header("Location: manage_jobs.php");
    exit();
}

$job_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();

if (!$job) {
    header("Location: manage_jobs.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $preferred_sex = mysqli_real_escape_string($conn, $_POST['preferred_sex']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $max_applicants = !empty($_POST["max_applicants"]) ? intval($_POST["max_applicants"]) : NULL;
    $years_of_experience = !empty($_POST["years_of_experience"]) ? mysqli_real_escape_string($conn, $_POST["years_of_experience"]) : NULL;
    
    $req_options = isset($_POST['req_options']) ? $_POST['req_options'] : [];
    $req_custom = isset($_POST['req_custom']) ? trim($_POST['req_custom']) : '';
    if (!empty($req_custom)) {
        $req_options = array_merge($req_options, array_map('trim', explode("\n", $req_custom)));
    }
    $requirements = implode("\n", array_filter($req_options));
    
    $image = $job['image'];

    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image);
    }

    $stmt = $conn->prepare("UPDATE jobs SET job_title=?, company_name=?, location=?, preferred_sex=?, salary=?, requirements=?, max_applicants=?, years_of_experience=?, image=? WHERE id=?");
    $stmt->bind_param("ssssssissi", $job_title, $company_name, $location, $preferred_sex, $salary, $requirements, $max_applicants, $years_of_experience, $image, $job_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Job updated successfully!";
        header("Location: manage_jobs.php");
        exit();
    } else {
        $error = "Failed to update job.";
    }
    $stmt->close();
}

$current_reqs = [];
if (!empty($job['requirements'])) {
    $raw_requirements = preg_split('/\r\n|\r|\n/', $job['requirements']);
    foreach ($raw_requirements as $req) {
        $req = trim($req);
        if (!empty($req)) {
            $current_reqs[] = $req;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job - OFW Management System</title>
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

<?php include 'sidebar.php'; ?>

<main class="lg:ml-64 p-4 lg:p-6 min-h-screen pt-20 lg:pt-6">
    <header class="bg-white/95 p-6 rounded-2xl mb-8 shadow-lg border-l-4 border-blue-600">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-1">
                    <i class="bi bi-pencil-square mr-2"></i>Edit Job
                </h1>
                <p class="text-gray-600">Update job posting information</p>
            </div>
            <a href="manage_jobs.php" class="inline-flex items-center px-4 py-2 bg-white/90 hover:bg-white text-gray-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <i class="bi bi-arrow-left mr-2"></i> Back
            </a>
        </div>
    </header>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 shadow-md flex items-center max-w-4xl mx-auto">
            <i class="bi bi-exclamation-triangle-fill text-2xl mr-3"></i>
            <span><?= $error ?></span>
        </div>
    <?php endif; ?>

    <section class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">
                    <i class="bi bi-info-circle mr-2"></i>Job Details
                </h2>
            </div>
            
            <form method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-briefcase mr-1"></i>Job Title
                        </label>
                        <input type="text" name="job_title" value="<?= htmlspecialchars($job['job_title']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-building mr-1"></i>Company Name
                        </label>
                        <input type="text" name="company_name" value="<?= htmlspecialchars($job['company_name']) ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-geo-alt mr-1"></i>Location
                        </label>
                        <select name="location" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                            <option value="">Select Location</option>
                            <option value="Saudi Arabia" <?= ($job['location']=='Saudi Arabia') ? 'selected' : '' ?>>Saudi Arabia</option>
                            <option value="United Arab Emirates" <?= ($job['location']=='United Arab Emirates') ? 'selected' : '' ?>>United Arab Emirates</option>
                            <option value="Qatar" <?= ($job['location']=='Qatar') ? 'selected' : '' ?>>Qatar</option>
                            <option value="Kuwait" <?= ($job['location']=='Kuwait') ? 'selected' : '' ?>>Kuwait</option>
                            <option value="Bahrain" <?= ($job['location']=='Bahrain') ? 'selected' : '' ?>>Bahrain</option>
                            <option value="Oman" <?= ($job['location']=='Oman') ? 'selected' : '' ?>>Oman</option>
                            <option value="Hong Kong" <?= ($job['location']=='Hong Kong') ? 'selected' : '' ?>>Hong Kong</option>
                            <option value="Singapore" <?= ($job['location']=='Singapore') ? 'selected' : '' ?>>Singapore</option>
                            <option value="Taiwan" <?= ($job['location']=='Taiwan') ? 'selected' : '' ?>>Taiwan</option>
                            <option value="Japan" <?= ($job['location']=='Japan') ? 'selected' : '' ?>>Japan</option>
                            <option value="South Korea" <?= ($job['location']=='South Korea') ? 'selected' : '' ?>>South Korea</option>
                            <option value="Malaysia" <?= ($job['location']=='Malaysia') ? 'selected' : '' ?>>Malaysia</option>
                            <option value="Canada" <?= ($job['location']=='Canada') ? 'selected' : '' ?>>Canada</option>
                            <option value="United States" <?= ($job['location']=='United States') ? 'selected' : '' ?>>United States</option>
                            <option value="United Kingdom" <?= ($job['location']=='United Kingdom') ? 'selected' : '' ?>>United Kingdom</option>
                            <option value="Australia" <?= ($job['location']=='Australia') ? 'selected' : '' ?>>Australia</option>
                            <option value="New Zealand" <?= ($job['location']=='New Zealand') ? 'selected' : '' ?>>New Zealand</option>
                            <option value="Italy" <?= ($job['location']=='Italy') ? 'selected' : '' ?>>Italy</option>
                            <option value="Spain" <?= ($job['location']=='Spain') ? 'selected' : '' ?>>Spain</option>
                            <option value="Germany" <?= ($job['location']=='Germany') ? 'selected' : '' ?>>Germany</option>
                            <option value="Other" <?= ($job['location']=='Other') ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-gender-ambiguous mr-1"></i>Preferred Sex
                        </label>
                        <select name="preferred_sex" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                            <option value="Any" <?= ($job['preferred_sex']=='Any') ? 'selected' : '' ?>>Any</option>
                            <option value="Male" <?= ($job['preferred_sex']=='Male') ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($job['preferred_sex']=='Female') ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-cash-coin mr-1"></i>Salary
                        </label>
                        <select name="salary" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                            <option value="">Select Salary Range</option>
                            <option value="Below 20k /month" <?= ($job['salary']=='Below 20k /month') ? 'selected' : '' ?>>Below 20k /month</option>
                            <option value="20k-40k /month" <?= ($job['salary']=='20k-40k /month') ? 'selected' : '' ?>>20k-40k /month</option>
                            <option value="40k-60k /month" <?= ($job['salary']=='40k-60k /month') ? 'selected' : '' ?>>40k-60k /month</option>
                            <option value="60k-80k /month" <?= ($job['salary']=='60k-80k /month') ? 'selected' : '' ?>>60k-80k /month</option>
                            <option value="80k-100k /month" <?= ($job['salary']=='80k-100k /month') ? 'selected' : '' ?>>80k-100k /month</option>
                            <option value="100k-150k /month" <?= ($job['salary']=='100k-150k /month') ? 'selected' : '' ?>>100k-150k /month</option>
                            <option value="150k-200k /month" <?= ($job['salary']=='150k-200k /month') ? 'selected' : '' ?>>150k-200k /month</option>
                            <option value="200k-300k /month" <?= ($job['salary']=='200k-300k /month') ? 'selected' : '' ?>>200k-300k /month</option>
                            <option value="Above 300k /month" <?= ($job['salary']=='Above 300k /month') ? 'selected' : '' ?>>Above 300k /month</option>
                            <option value="Negotiable" <?= ($job['salary']=='Negotiable') ? 'selected' : '' ?>>Negotiable</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-people mr-1"></i>Maximum Number of Applicants (Optional)
                        </label>
                        <input type="number" name="max_applicants" min="1" 
                               value="<?= !empty($job['max_applicants']) ? htmlspecialchars($job['max_applicants']) : '' ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" 
                               placeholder="e.g., 50">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="bi bi-info-circle mr-1"></i>Leave empty for unlimited applicants. Job will close when limit is reached.
                            <?php if (!empty($job['max_applicants'])): ?>
                                <span class="text-blue-600 font-semibold">Current: <?= $job['max_applicants'] ?> applicants</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-calendar-check mr-1"></i>Years of Experience Required (Optional)
                        </label>
                        <select name="years_of_experience" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">Not Specified</option>
                            <option value="No experience required" <?= ($job['years_of_experience']=='No experience required') ? 'selected' : '' ?>>No experience required</option>
                            <option value="Less than 1 year" <?= ($job['years_of_experience']=='Less than 1 year') ? 'selected' : '' ?>>Less than 1 year</option>
                            <option value="1-2 years" <?= ($job['years_of_experience']=='1-2 years') ? 'selected' : '' ?>>1-2 years</option>
                            <option value="2-3 years" <?= ($job['years_of_experience']=='2-3 years') ? 'selected' : '' ?>>2-3 years</option>
                            <option value="3-5 years" <?= ($job['years_of_experience']=='3-5 years') ? 'selected' : '' ?>>3-5 years</option>
                            <option value="5-7 years" <?= ($job['years_of_experience']=='5-7 years') ? 'selected' : '' ?>>5-7 years</option>
                            <option value="7-10 years" <?= ($job['years_of_experience']=='7-10 years') ? 'selected' : '' ?>>7-10 years</option>
                            <option value="10+ years" <?= ($job['years_of_experience']=='10+ years') ? 'selected' : '' ?>>10+ years</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="bi bi-info-circle mr-1"></i>Specify the minimum years of experience required for this position.
                            <?php if (!empty($job['years_of_experience'])): ?>
                                <span class="text-blue-600 font-semibold">Current: <?= htmlspecialchars($job['years_of_experience']) ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="bi bi-list-check mr-1"></i>Requirements
                    </label>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-2 mb-3">
                        <?php 
                        $common_reqs = [
                            'Passport: Valid for at least six months.',
                            'Employment Contract: Signed and verified by the Philippine embassy/labor office in the destination country.',
                            'Work Visa/Permit: Proper documentation to work legally.',
                            'Medical Certificate: Valid, DOH-accredited clinic clearance.',
                            'NBI Clearance: Valid for travel/work abroad.',
                            'PSA Birth Certificate: Valid for identification.',
                            'Transcript of Records/Diploma: Educational background.',
                            'Resume/CV: Updated work history.',
                            'Photos: 2x2 pictures.'
                        ];
                        foreach ($common_reqs as $req) {
                            $checked = in_array($req, $current_reqs) ? 'checked' : '';
                            $id = 'req_'.preg_replace('/[^a-zA-Z0-9]/', '', $req);
                            echo '<div class="flex items-start">
                                    <input type="checkbox" name="req_options[]" value="'.$req.'" id="'.$id.'" '.$checked.' class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="'.$id.'" class="ml-2 text-sm text-gray-700">'.$req.'</label>
                                  </div>';
                        }
                        $other_reqs = array_diff($current_reqs, $common_reqs);
                        ?>
                    </div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Other Requirements (One per line)</label>
                    <textarea name="req_custom" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" rows="3"><?= htmlspecialchars(implode("\n", $other_reqs)) ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="bi bi-image mr-1"></i>Job Image
                    </label>
                    <?php if (!empty($job['image'])): ?>
                        <div class="mb-3">
                            <img src="uploads/<?= htmlspecialchars($job['image']) ?>" class="w-48 h-48 object-cover rounded-lg shadow-md">
                            <p class="text-sm text-gray-500 mt-2">Current image</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-sm text-gray-500 mt-1">Upload a new image to replace the current one</p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                        <i class="bi bi-check-circle mr-2"></i>Update Job
                    </button>
                    <a href="manage_jobs.php" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 rounded-lg transition duration-300 shadow-md hover:shadow-lg text-center">
                        <i class="bi bi-x-circle mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
</main>

</body>
</html>
