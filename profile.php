<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

$user_id = $_SESSION["user_id"];
$user_role = $_SESSION["role"];

// Fetch user details
$query = "SELECT name, email, contact_number, address, country, profile_picture, birth_date, civil_status FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission for updating profile
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = $_POST["name"];
    $contact_number = $_POST["contact_number"];
    $address = $_POST["address"];
    $country = $_POST["country"];
    $birth_date = $_POST["birth_date"];
    $civil_status = $_POST["civil_status"];

    $params = [$name, $contact_number, $address, $country, $birth_date, $civil_status];
    $types = "ssssss";
    $update_query_sql = "UPDATE users SET name = ?, contact_number = ?, address = ?, country = ?, birth_date = ?, civil_status = ?";

    // Handle profile picture upload
    if (!empty($_FILES["profile_picture"]["name"])) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES["profile_picture"]["type"];
        $file_size = $_FILES["profile_picture"]["size"];
        
        // Validate file type
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION["error_message"] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            header("Location: profile.php");
            exit();
        }
        
        // Validate file size (5MB max)
        if ($file_size > 5 * 1024 * 1024) {
            $_SESSION["error_message"] = "File size must be less than 5MB.";
            header("Location: profile.php");
            exit();
        }
        
        $target_dir = "uploads/";
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
        $file_name = "profile_" . $user_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Delete old profile picture if it exists and is not the default
            if (!empty($user['profile_picture']) && $user['profile_picture'] != 'uploads/default.png' && file_exists($user['profile_picture'])) {
                unlink($user['profile_picture']);
            }
            
            $update_query_sql .= ", profile_picture = ?";
            $types .= "s";
            $params[] = $target_file;
        } else {
            $_SESSION["error_message"] = "Error uploading profile picture.";
            header("Location: profile.php");
            exit();
        }
    }

    $update_query_sql .= " WHERE id = ?";
    $types .= "i";
    $params[] = $user_id;

    $update_stmt = $conn->prepare($update_query_sql);
    $update_stmt->bind_param($types, ...$params);

    if ($update_stmt->execute()) {
        $_SESSION["success_message"] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
    } else {
        $_SESSION["error_message"] = "Error updating profile.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if (password_verify($current_password, $row['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $new_hash, $user_id);
                if ($update_stmt->execute()) {
                    $_SESSION["success_message"] = "Password updated successfully!";
                } else {
                    $_SESSION["error_message"] = "Error updating password.";
                }
            } else {
                $_SESSION["error_message"] = "New password must be at least 6 characters.";
            }
        } else {
            $_SESSION["error_message"] = "New passwords do not match.";
        }
    } else {
        $_SESSION["error_message"] = "Incorrect current password.";
    }
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - OFW Management</title>
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
        .profile-cover {
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        .profile-cover::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        .profile-picture {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border: 6px solid white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }
        .profile-picture:hover {
            transform: scale(1.05);
        }
        .upload-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: 4px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-overlay:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-gray-50">

<?php include 'sidebar.php'; ?>

<main class="lg:ml-64 p-4 lg:p-8 min-h-screen pt-20 lg:pt-8">
    <!-- Page Header -->
    <header class="mb-8">
        <div class="flex items-center space-x-4">
            <div class="h-12 w-1 bg-blue-600 rounded-full"></div>
            <div>
                <h1 class="text-3xl font-bold text-white drop-shadow-lg">
                    <i class="bi bi-person-circle mr-2"></i>Profile Settings
                </h1>
                <p class="text-gray-200 text-sm mt-1">Manage your account information and security settings</p>
            </div>
        </div>
    </header>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION["success_message"])): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md animate-fade-in">
            <div class="flex items-center">
                <i class="bi bi-check-circle-fill text-xl mr-3"></i>
                <span><?php echo $_SESSION["success_message"]; unset($_SESSION["success_message"]); ?></span>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION["error_message"])): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md animate-fade-in">
            <div class="flex items-center">
                <i class="bi bi-exclamation-triangle-fill text-xl mr-3"></i>
                <span><?php echo $_SESSION["error_message"]; unset($_SESSION["error_message"]); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="max-w-6xl mx-auto">
        <!-- Profile Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden mb-8">
            <!-- Cover Photo -->
            <div class="profile-cover"></div>
            
            <!-- Profile Info Section -->
            <div class="px-8 pb-8">
                <div class="flex flex-col md:flex-row items-center md:items-end md:space-x-6 -mt-20">
                    <!-- Profile Picture -->
                    <div class="relative mb-4 md:mb-0">
                        <?php 
                        $profile_pic = 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&size=160&background=667eea&color=fff&bold=true';
                        if (!empty($user['profile_picture']) && file_exists($user['profile_picture'])) {
                            $profile_pic = $user['profile_picture'];
                        }
                        ?>
                        <img src="<?php echo $profile_pic; ?>" 
                             class="rounded-full profile-picture bg-white" id="profile-preview" alt="Profile Picture"
                             onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&size=160&background=667eea&color=fff&bold=true'">
                        <label for="profile_picture_input" class="upload-overlay rounded-full cursor-pointer" title="Change profile picture">
                            <i class="bi bi-camera-fill text-white text-xl"></i>
                        </label>
                        <input type="file" id="profile_picture_input" name="profile_picture" class="hidden" accept="image/jpeg,image/jpg,image/png,image/gif" onchange="previewFile()">
                    </div>
                    
                    <!-- User Info -->
                    <div class="flex-1 text-center md:text-left mb-4 md:mb-0">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($user["name"]); ?></h2>
                        <p class="text-gray-600 mb-3 flex items-center justify-center md:justify-start">
                            <i class="bi bi-envelope mr-2"></i>
                            <?php echo htmlspecialchars($user["email"]); ?>
                        </p>
                        <span class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full text-sm font-semibold shadow-lg">
                            <i class="bi bi-shield-check mr-2"></i>
                            <?php echo strtoupper(htmlspecialchars($user_role)); ?>
                        </span>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                    <div class="stat-card rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm mb-1">Account Status</p>
                                <p class="text-2xl font-bold">Active</p>
                            </div>
                            <i class="bi bi-check-circle-fill text-4xl opacity-50"></i>
                        </div>
                    </div>
                    <div class="stat-card rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm mb-1">Member Since</p>
                                <p class="text-2xl font-bold">2024</p>
                            </div>
                            <i class="bi bi-calendar-check-fill text-4xl opacity-50"></i>
                        </div>
                    </div>
                    <div class="stat-card rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/80 text-sm mb-1">Profile Completion</p>
                                <p class="text-2xl font-bold">100%</p>
                            </div>
                            <i class="bi bi-graph-up-arrow text-4xl opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forms Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Edit Profile Form -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="bi bi-person-gear mr-3"></i>
                        Personal Information
                    </h3>
                </div>
                <div class="p-6">
                    <form action="profile.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-person text-blue-600 mr-2"></i>Full Name
                            </label>
                            <input type="text" name="name" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                   value="<?php echo htmlspecialchars($user["name"]); ?>" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-envelope text-blue-600 mr-2"></i>Email Address
                            </label>
                            <input type="email" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" 
                                   value="<?php echo htmlspecialchars($user["email"]); ?>" disabled>
                            <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-phone text-blue-600 mr-2"></i>Contact Number
                            </label>
                            <input type="tel" name="contact_number" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                   value="<?php echo htmlspecialchars($user["contact_number"]); ?>" 
                                   placeholder="+63 912 345 6789" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-house text-blue-600 mr-2"></i>Address
                            </label>
                            <input type="text" name="address" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                   value="<?php echo htmlspecialchars($user["address"]); ?>" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-globe text-blue-600 mr-2"></i>Country
                            </label>
                            <select name="country" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                    required>
                                <option value="">Select Country</option>
                                <option value="Philippines" <?= (isset($user["country"]) && $user["country"] == "Philippines") ? 'selected' : '' ?>>Philippines</option>
                                <option value="Saudi Arabia" <?= (isset($user["country"]) && $user["country"] == "Saudi Arabia") ? 'selected' : '' ?>>Saudi Arabia</option>
                                <option value="United Arab Emirates" <?= (isset($user["country"]) && $user["country"] == "United Arab Emirates") ? 'selected' : '' ?>>United Arab Emirates</option>
                                <option value="Kuwait" <?= (isset($user["country"]) && $user["country"] == "Kuwait") ? 'selected' : '' ?>>Kuwait</option>
                                <option value="Qatar" <?= (isset($user["country"]) && $user["country"] == "Qatar") ? 'selected' : '' ?>>Qatar</option>
                                <option value="Hong Kong" <?= (isset($user["country"]) && $user["country"] == "Hong Kong") ? 'selected' : '' ?>>Hong Kong</option>
                                <option value="Singapore" <?= (isset($user["country"]) && $user["country"] == "Singapore") ? 'selected' : '' ?>>Singapore</option>
                                <option value="Taiwan" <?= (isset($user["country"]) && $user["country"] == "Taiwan") ? 'selected' : '' ?>>Taiwan</option>
                                <option value="Malaysia" <?= (isset($user["country"]) && $user["country"] == "Malaysia") ? 'selected' : '' ?>>Malaysia</option>
                                <option value="Japan" <?= (isset($user["country"]) && $user["country"] == "Japan") ? 'selected' : '' ?>>Japan</option>
                                <option value="South Korea" <?= (isset($user["country"]) && $user["country"] == "South Korea") ? 'selected' : '' ?>>South Korea</option>
                                <option value="United States" <?= (isset($user["country"]) && $user["country"] == "United States") ? 'selected' : '' ?>>United States</option>
                                <option value="Canada" <?= (isset($user["country"]) && $user["country"] == "Canada") ? 'selected' : '' ?>>Canada</option>
                                <option value="United Kingdom" <?= (isset($user["country"]) && $user["country"] == "United Kingdom") ? 'selected' : '' ?>>United Kingdom</option>
                                <option value="Australia" <?= (isset($user["country"]) && $user["country"] == "Australia") ? 'selected' : '' ?>>Australia</option>
                                <option value="Italy" <?= (isset($user["country"]) && $user["country"] == "Italy") ? 'selected' : '' ?>>Italy</option>
                                <option value="Other" <?= (isset($user["country"]) && $user["country"] == "Other") ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-calendar text-blue-600 mr-2"></i>Birth Date
                            </label>
                            <input type="date" name="birth_date" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                   value="<?php echo htmlspecialchars($user["birth_date"] ?? ''); ?>">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-heart text-blue-600 mr-2"></i>Civil Status
                            </label>
                            <select name="civil_status" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                <option value="">Select Civil Status</option>
                                <option value="Single" <?php echo (isset($user["civil_status"]) && $user["civil_status"] == "Single") ? "selected" : ""; ?>>Single</option>
                                <option value="Married" <?php echo (isset($user["civil_status"]) && $user["civil_status"] == "Married") ? "selected" : ""; ?>>Married</option>
                                <option value="Widowed" <?php echo (isset($user["civil_status"]) && $user["civil_status"] == "Widowed") ? "selected" : ""; ?>>Widowed</option>
                                <option value="Divorced" <?php echo (isset($user["civil_status"]) && $user["civil_status"] == "Divorced") ? "selected" : ""; ?>>Divorced</option>
                                <option value="Separated" <?php echo (isset($user["civil_status"]) && $user["civil_status"] == "Separated") ? "selected" : ""; ?>>Separated</option>
                            </select>
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                                <i class="bi bi-save mr-2"></i>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-5">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="bi bi-shield-lock mr-3"></i>
                        Security Settings
                    </h3>
                </div>
                <div class="p-6">
                    <form action="profile.php" method="POST" class="space-y-4">
                        <input type="hidden" name="change_password" value="1">
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-key text-orange-600 mr-2"></i>Current Password
                            </label>
                            <input type="password" name="current_password" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all" 
                                   placeholder="Enter current password" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-lock text-orange-600 mr-2"></i>New Password
                            </label>
                            <input type="password" name="new_password" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all" 
                                   placeholder="Enter new password" required>
                            <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-lock-fill text-orange-600 mr-2"></i>Confirm New Password
                            </label>
                            <input type="password" name="confirm_password" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all" 
                                   placeholder="Confirm new password" required>
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                                <i class="bi bi-arrow-repeat mr-2"></i>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
let isUploading = false;

function previewFile() {
    if (isUploading) return;
    
    const preview = document.getElementById('profile-preview');
    const fileInput = document.getElementById('profile_picture_input');
    const file = fileInput.files[0];
    
    if (!file) return;
    
    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!validTypes.includes(file.type)) {
        alert('Please select a valid image file (JPG, PNG, or GIF)');
        fileInput.value = '';
        return;
    }
    
    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        fileInput.value = '';
        return;
    }
    
    const reader = new FileReader();

    reader.addEventListener("load", function () {
        preview.src = reader.result;
        
        // Auto-submit the form to upload the new profile picture
        isUploading = true;
        
        const formData = new FormData();
        formData.append('update_profile', '1');
        formData.append('name', '<?php echo addslashes($user["name"]); ?>');
        formData.append('contact_number', '<?php echo addslashes($user["contact_number"]); ?>');
        formData.append('address', '<?php echo addslashes($user["address"]); ?>');
        formData.append('birth_date', '<?php echo addslashes($user["birth_date"] ?? ''); ?>');
        formData.append('civil_status', '<?php echo addslashes($user["civil_status"] ?? ''); ?>');
        formData.append('profile_picture', file);
        
        // Show loading indicator
        const overlay = document.querySelector('.upload-overlay');
        overlay.innerHTML = '<i class="bi bi-hourglass-split text-white text-xl animate-spin"></i>';
        
        fetch('profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                throw new Error('Upload failed');
            }
        })
        .catch(error => {
            alert('Error uploading profile picture. Please try again.');
            overlay.innerHTML = '<i class="bi bi-camera-fill text-white text-xl"></i>';
            isUploading = false;
        });
    }, false);

    reader.readAsDataURL(file);
}
</script>

</body>
</html>
