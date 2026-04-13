<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

// Check if 'requirements' column exists, if not, add it to prevent errors
$check_column = $conn->query("SHOW COLUMNS FROM benefits LIKE 'requirements'");
if ($check_column && $check_column->num_rows == 0) {
    $conn->query("ALTER TABLE benefits ADD COLUMN requirements TEXT DEFAULT NULL AFTER description");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    
    $req_options = isset($_POST['req_options']) ? $_POST['req_options'] : [];
    $req_custom = isset($_POST['req_custom']) ? trim($_POST['req_custom']) : '';
    if (!empty($req_custom)) {
        $req_options = array_merge($req_options, array_map('trim', explode("\n", $req_custom)));
    }
    // Don't escape requirements - we need actual line breaks, not escaped ones
    $requirements = implode("\n", array_filter($req_options));
    
    // Handle country selection
    $countries = isset($_POST['countries']) ? $_POST['countries'] : [];
    $applicable_countries = implode(',', $countries);
    
    $expiration_date = mysqli_real_escape_string($conn, $_POST["expiration_date"]);
    $created_by = $_SESSION["user_id"];

    $query = "INSERT INTO benefits (title, description, requirements, applicable_countries, expiration_date, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $title, $description, $requirements, $applicable_countries, $expiration_date, $created_by);

    if ($stmt->execute()) {
        // Include notification helper
        include "create_notification.php";
        
        // Create notification for all OFW users
        $notification_title = "New Benefit Available: " . $title;
        $notification_message = "A new benefit program is now available. Check it out and apply!";
        $notification_link = "benefits.php";
        
        notifyAllOFWs($conn, 'benefit', $notification_title, $notification_message, $notification_link);
        
        $_SESSION['message'] = "Benefit added successfully!";
        header("Location: update_benefits.php");
        exit();
    } else {
        $error = "Failed to add benefit.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Benefit - OFW Management</title>
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
                    <h1 class="text-3xl font-bold text-white drop-shadow-lg">Add New Benefit</h1>
                    <p class="text-gray-200 text-sm mt-1">Create a new benefit program for OFWs</p>
                </div>
            </div>
            <a href="update_benefits.php" class="inline-flex items-center px-4 py-2 bg-white/90 hover:bg-white text-gray-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <i class="bi bi-arrow-left mr-2"></i>
                Back to Benefits
            </a>
        </div>
    </header>

    <?php if (!empty($error)): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md">
            <div class="flex items-center">
                <i class="bi bi-exclamation-triangle-fill text-xl mr-3"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Form Card -->
    <section class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="bi bi-gift-fill mr-3"></i>
                    Benefit Information
                </h2>
            </div>

            <!-- Card Body -->
            <div class="p-8">
                <form method="POST" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-card-heading text-blue-600 mr-2"></i>
                            Benefit Title
                        </label>
                        <input type="text" name="title" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                               placeholder="Enter benefit title" required>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-file-text text-blue-600 mr-2"></i>
                            Benefit Description
                        </label>
                        <textarea name="description" rows="5" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                  placeholder="Describe the benefit program in detail..." required></textarea>
                    </div>

                    <!-- Requirements -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="bi bi-list-check text-blue-600 mr-2"></i>
                            Requirements
                        </label>
                        <div class="bg-gray-50 rounded-lg p-5 space-y-3 mb-4 max-h-96 overflow-y-auto">
                            <?php 
                            $common_reqs = [
                                '1. Death Certificate – Local or Foreign (original – NSO/PSA Copy)',
                                '2. Burial Permit and Official receipt of Funeral Expenses (original)',
                                '3. Photocopy of 2 valid IDs and 2 pcs 2x2 ID pictures of Claimant',
                                '4. Certificate of No Marriage (CENOMAR) of OFW from NSO/PSA (Original)',
                                '5. Applicable Document (Original – NSO/PSA Issued):',
                                '   • Marriage Certificate – If claimant is spouse/husband of OFW',
                                '   • Birth Certificate of OFW – if claimant is a Parent of OFW',
                                '   • Birth Certificate of Child and Death Certificate of spouse – If claimant is a child of OFW',
                                '6. Passport/Seaman\'s Book – 1st Page, Latest departure/arrival date, Embarkation and Disembarkation if applicable',
                                '7. OFW Membership Verification Sheet/OWWA Official Receipt',
                                '8. Police Accident Report (if death is due to accident)',
                                '9. In the absence of Birth/Marriage Certificate:',
                                '   • Baptismal/Marriage Certificate certified by the Parish Priest/Office',
                                '   • Certificate from LCR that fact of birth/marriage is not recorded in Civil Registry'
                            ];
                            foreach ($common_reqs as $req) {
                                $id = 'req_'.md5($req);
                                $isSubItem = strpos($req, '   •') === 0;
                                $isHeader = strpos($req, ':') !== false && !$isSubItem;
                                
                                if ($isSubItem) {
                                    // Sub-items with bullet points
                                    echo '<div class="flex items-start ml-8">
                                            <input type="checkbox" name="req_options[]" value="'.htmlspecialchars(trim($req)).'" id="'.$id.'" 
                                                   class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-2 focus:ring-blue-500 mt-1 flex-shrink-0">
                                            <label for="'.$id.'" class="ml-3 text-sm text-gray-600 cursor-pointer leading-relaxed">'.htmlspecialchars($req).'</label>
                                          </div>';
                                } elseif ($isHeader) {
                                    // Headers (items ending with colon)
                                    echo '<div class="flex items-start mt-2">
                                            <input type="checkbox" name="req_options[]" value="'.htmlspecialchars($req).'" id="'.$id.'" 
                                                   class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-2 focus:ring-blue-500 mt-1 flex-shrink-0">
                                            <label for="'.$id.'" class="ml-3 text-sm font-semibold text-gray-700 cursor-pointer leading-relaxed">'.htmlspecialchars($req).'</label>
                                          </div>';
                                } else {
                                    // Regular numbered items
                                    echo '<div class="flex items-start">
                                            <input type="checkbox" name="req_options[]" value="'.htmlspecialchars($req).'" id="'.$id.'" 
                                                   class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-2 focus:ring-blue-500 mt-1 flex-shrink-0">
                                            <label for="'.$id.'" class="ml-3 text-sm text-gray-700 cursor-pointer leading-relaxed">'.htmlspecialchars($req).'</label>
                                          </div>';
                                }
                            }
                            ?>
                        </div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Other Requirements (One per line)</label>
                        <textarea name="req_custom" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                  placeholder="Enter additional requirements..."></textarea>
                    </div>

                    <!-- Applicable Countries -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="bi bi-globe text-blue-600 mr-2"></i>
                            Applicable Countries
                        </label>
                        <div class="bg-gray-50 rounded-lg p-5 space-y-2 mb-2 max-h-64 overflow-y-auto">
                            <?php 
                            $countries = [
                                'All Countries' => 'all',
                                'Philippines' => 'Philippines',
                                'Saudi Arabia' => 'Saudi Arabia',
                                'United Arab Emirates' => 'United Arab Emirates',
                                'Kuwait' => 'Kuwait',
                                'Qatar' => 'Qatar',
                                'Hong Kong' => 'Hong Kong',
                                'Singapore' => 'Singapore',
                                'Taiwan' => 'Taiwan',
                                'Malaysia' => 'Malaysia',
                                'Japan' => 'Japan',
                                'South Korea' => 'South Korea',
                                'United States' => 'United States',
                                'Canada' => 'Canada',
                                'United Kingdom' => 'United Kingdom',
                                'Australia' => 'Australia',
                                'Italy' => 'Italy'
                            ];
                            
                            foreach ($countries as $label => $value) {
                                $id = 'country_'.str_replace(' ', '_', $value);
                                echo '<div class="flex items-center">
                                        <input type="checkbox" name="countries[]" value="'.$value.'" id="'.$id.'" 
                                               class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                               '.($value == 'all' ? 'onchange="toggleAllCountries(this)"' : '').'>
                                        <label for="'.$id.'" class="ml-3 text-sm text-gray-700 cursor-pointer '.($value == 'all' ? 'font-semibold' : '').'">'.$label.'</label>
                                      </div>';
                            }
                            ?>
                        </div>
                        <small class="text-gray-600 text-xs block">
                            <i class="bi bi-info-circle mr-1"></i>Select "All Countries" or choose specific countries where this benefit applies
                        </small>
                    </div>

                    <!-- Expiration Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-calendar-event text-blue-600 mr-2"></i>
                            Expiration Date
                        </label>
                        <input type="date" name="expiration_date" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center pt-4">
                        <button type="submit" name="add_benefits" 
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                            <i class="bi bi-plus-circle-fill mr-2"></i>
                            Add Benefit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
// Toggle all countries checkbox
function toggleAllCountries(checkbox) {
    const countryCheckboxes = document.querySelectorAll('input[name="countries[]"]:not(#country_all)');
    countryCheckboxes.forEach(cb => {
        cb.checked = checkbox.checked;
        cb.disabled = checkbox.checked;
    });
}
</script>

</body>
</html>
