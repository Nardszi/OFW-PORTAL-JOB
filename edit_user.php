<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

$user_id = intval($_GET["id"]);
$query = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: manage_users.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST["name"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $role = mysqli_real_escape_string($conn, $_POST["role"]);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);
    $contact_number = mysqli_real_escape_string($conn, $_POST["contact_number"]);
    $gender = mysqli_real_escape_string($conn, $_POST["gender"]);
    $birth_date = mysqli_real_escape_string($conn, $_POST["birth_date"]);
    $civil_status = mysqli_real_escape_string($conn, $_POST["civil_status"]);

    $update_query = "UPDATE users SET name=?, email=?, role=?, address=?, contact_number=?, gender=?, birth_date=?, civil_status=? WHERE id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssssssi", $full_name, $email, $role, $address, $contact_number, $gender, $birth_date, $civil_status, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "User updated successfully!";
        header("Location: manage_users.php");
        exit();
    } else {
        $error = "Failed to update user.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - OFW Management System</title>
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

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="lg:ml-64 p-4 lg:p-6 min-h-screen pt-20 lg:pt-6">
    <!-- Header Section -->
    <header class="bg-white/95 p-6 rounded-2xl mb-8 shadow-lg border-l-4 border-blue-600">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-1">
                    <i class="bi bi-pencil-square mr-2"></i>Edit User
                </h1>
                <p class="text-gray-600">Update user information</p>
            </div>
            <a href="manage_users.php" class="inline-flex items-center px-4 py-2 bg-white/90 hover:bg-white text-gray-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <i class="bi bi-arrow-left mr-2"></i> Back to Users
            </a>
        </div>
    </header>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 shadow-md flex items-center">
            <i class="bi bi-exclamation-triangle-fill text-2xl mr-3"></i>
            <span><?= $error ?></span>
        </div>
    <?php endif; ?>

    <!-- Form Section -->
    <section class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">
                    <i class="bi bi-person-fill mr-2"></i>User Information
                </h2>
            </div>
            
            <form method="POST" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-person mr-1"></i>Full Name
                        </label>
                        <input type="text" 
                               name="name" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                               value="<?= htmlspecialchars($user['name']) ?>" 
                               required>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-envelope mr-1"></i>Email Address
                        </label>
                        <input type="email" 
                               name="email" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-gray-50" 
                               value="<?= htmlspecialchars($user['email']) ?>" 
                               readonly>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="bi bi-info-circle mr-1"></i>Email cannot be changed
                        </p>
                    </div>

                    <!-- Contact Number -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-telephone mr-1"></i>Contact Number
                        </label>
                        <input type="tel" 
                               name="contact_number" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                               value="<?= htmlspecialchars($user['contact_number']) ?>" 
                               placeholder="+63 912 345 6789"
                               pattern="[0-9+\s\-()]+"
                               required>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-gender-ambiguous mr-1"></i>Gender
                        </label>
                        <select name="gender" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                required>
                            <option value="">Select Gender</option>
                            <option value="Male" <?= ($user["gender"] == "Male") ? "selected" : "" ?>>Male</option>
                            <option value="Female" <?= ($user["gender"] == "Female") ? "selected" : "" ?>>Female</option>
                        </select>
                    </div>

                    <!-- Birth Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-calendar mr-1"></i>Birth Date
                        </label>
                        <input type="date" 
                               name="birth_date" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                               value="<?= htmlspecialchars($user['birth_date']) ?>" 
                               required>
                    </div>

                    <!-- Civil Status -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-heart mr-1"></i>Civil Status
                        </label>
                        <select name="civil_status" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                required>
                            <option value="">Select Status</option>
                            <option value="Single" <?= ($user["civil_status"] == "Single") ? "selected" : "" ?>>Single</option>
                            <option value="Married" <?= ($user["civil_status"] == "Married") ? "selected" : "" ?>>Married</option>
                            <option value="Widowed" <?= ($user["civil_status"] == "Widowed") ? "selected" : "" ?>>Widowed</option>
                            <option value="Divorced" <?= ($user["civil_status"] == "Divorced") ? "selected" : "" ?>>Divorced</option>
                            <option value="Separated" <?= ($user["civil_status"] == "Separated") ? "selected" : "" ?>>Separated</option>
                        </select>
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-geo-alt mr-1"></i>Complete Address
                        </label>
                        <input type="text" 
                               name="address" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" 
                               value="<?= htmlspecialchars($user['address']) ?>" 
                               placeholder="Street, Barangay, City, Province"
                               required>
                    </div>

                    <!-- Role -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-person-badge mr-1"></i>Role
                        </label>
                        <select name="role" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="admin" <?= ($user["role"] == "admin") ? "selected" : "" ?>>Admin</option>
                            <option value="ofw" <?= ($user["role"] == "ofw") ? "selected" : "" ?>>OFW</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                        <i class="bi bi-check-circle mr-2"></i>Save Changes
                    </button>
                    <a href="manage_users.php" 
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 rounded-lg transition duration-300 shadow-md hover:shadow-lg text-center">
                        <i class="bi bi-x-circle mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
</main>

</body>
</html>
