<?php
session_start();
include "../config/database.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = trim($_POST["role"]);
    $address = trim($_POST["address"]);
    $country = trim($_POST["country"]);
    $contact_number = trim($_POST["contact_number"]);
    $gender = trim($_POST["gender"]);
    $birth_date = trim($_POST["birth_date"]);
    $civil_status = trim($_POST["civil_status"]);
    $otp = isset($_POST["otp"]) ? trim($_POST["otp"]) : "";
    $terms = isset($_POST["terms"]) ? true : false;

    if (empty($name) || empty($email) || empty($password) || empty($role) || empty($address) || empty($country) || empty($contact_number) || empty($gender) || empty($birth_date) || empty($civil_status) || empty($otp)) {
        $error = "All fields are required!";
    } elseif (!$terms) {
        $error = "You must agree to the Terms and Conditions!";
    } else {
        $name = mysqli_real_escape_string($conn, $name);
        $email = mysqli_real_escape_string($conn, $email);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $role = mysqli_real_escape_string($conn, $role);
        $address = mysqli_real_escape_string($conn, $address);
        $country = mysqli_real_escape_string($conn, $country);
        $contact_number = mysqli_real_escape_string($conn, $contact_number);
        $gender = mysqli_real_escape_string($conn, $gender);
        $birth_date = mysqli_real_escape_string($conn, $birth_date);
        $civil_status = mysqli_real_escape_string($conn, $civil_status);

        if (!isset($_SESSION['otp']) || $otp != $_SESSION['otp']) {
            $error = "Invalid OTP. Please verify your email.";
        } elseif (!isset($_SESSION['otp_email']) || $email != $_SESSION['otp_email']) {
            $error = "Email does not match the verified email.";
        } else {
            $checkEmail = "SELECT id FROM users WHERE email='$email'";
            $result = mysqli_query($conn, $checkEmail);

            if (mysqli_num_rows($result) > 0) {
                $error = "Email is already registered!";
            } else {
                $query = "INSERT INTO users (name, email, password, role, address, country, contact_number, gender, birth_date, civil_status) 
                          VALUES ('$name', '$email', '$password', '$role', '$address', '$country', '$contact_number', '$gender', '$birth_date', '$civil_status')";

                if (mysqli_query($conn, $query)) {
                    $success = "Registration successful! Redirecting to login...";
                    unset($_SESSION['otp']);
                    unset($_SESSION['otp_email']);
                    header("refresh:2;url=login.php");
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
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
    <title>Create Account - OFW Management System</title>
    <link rel="icon" type="image/svg+xml" href="../images/favicon.svg">
    <link rel="alternate icon" href="../images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: url('../images/wall234.jpg') no-repeat center center fixed;
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
            z-index: 0;
        }
        body > * {
            position: relative;
            z-index: 1;
        }
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.5));
            z-index: 0;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-in {
            animation: slideIn 0.6s ease-out;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

<!-- Header Navigation -->
<nav class="fixed top-0 left-0 right-0 bg-white z-50 shadow-sm">
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
            <a href="../index.php" class="flex items-center hover:opacity-80 transition">
                <img src="../images/logo5.png" alt="OFW Management" class="h-10 w-auto mr-3">
                <span class="text-gray-900 font-bold text-lg">OFW Management</span>
            </a>
            <div class="hidden md:flex items-center gap-6">
                <a href="../index.php#home" class="text-gray-700 hover:text-blue-600 transition font-medium">Home</a>
                <a href="../index.php#features" class="text-gray-700 hover:text-blue-600 transition font-medium">Features</a>
                <a href="../index.php#benefits" class="text-gray-700 hover:text-blue-600 transition font-medium">Benefits</a>
                <a href="../index.php#about" class="text-gray-700 hover:text-blue-600 transition font-medium">About</a>
                <a href="login.php" class="text-gray-700 hover:text-blue-600 transition font-medium">Login</a>
                <a href="register.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full font-semibold transition shadow-md">Get Started</a>
            </div>
            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden text-gray-700">
                <i class="bi bi-list text-3xl"></i>
            </button>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden pt-4 pb-2">
            <div class="flex flex-col space-y-3">
                <a href="../index.php#home" class="text-gray-700 hover:text-blue-600 transition font-medium">Home</a>
                <a href="../index.php#features" class="text-gray-700 hover:text-blue-600 transition font-medium">Features</a>
                <a href="../index.php#benefits" class="text-gray-700 hover:text-blue-600 transition font-medium">Benefits</a>
                <a href="../index.php#about" class="text-gray-700 hover:text-blue-600 transition font-medium">About</a>
                <a href="login.php" class="text-gray-700 hover:text-blue-600 transition font-medium">Login</a>
                <a href="register.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full font-semibold text-center transition">Get Started</a>
            </div>
        </div>
    </div>
</nav>

<!-- Register Form -->
<main class="flex-1 flex items-center justify-center px-4 py-8 md:py-12 relative z-10 mt-16 md:mt-20">
    <div class="w-full max-w-4xl animate-slide-in">
        <div class="glass-effect rounded-2xl md:rounded-3xl p-6 md:p-12 shadow-2xl">
            <!-- Header -->
            <div class="text-center mb-6 md:mb-8">
                <div class="flex justify-center mb-3 md:mb-4">
                    <img src="../images/logo5.png" alt="OFW Management" class="h-16 md:h-20 w-auto">
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Create Your Account</h1>
                <p class="text-sm md:text-base text-gray-600">Join the OFW Management System today</p>
            </div>
            
            <!-- Alert Messages -->
            <?php if (!empty($error)) { ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 md:p-4 rounded-lg mb-4 md:mb-6 flex items-start text-sm md:text-base">
                    <i class="bi bi-exclamation-triangle-fill text-lg md:text-xl mr-2 md:mr-3 mt-0.5"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php } ?>
            
            <?php if (!empty($success)) { ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 md:p-4 rounded-lg mb-4 md:mb-6 flex items-start text-sm md:text-base">
                    <i class="bi bi-check-circle-fill text-lg md:text-xl mr-2 md:mr-3 mt-0.5"></i>
                    <span><?php echo $success; ?></span>
                </div>
            <?php } ?>

            <form method="POST" class="space-y-4 md:space-y-6">
                <!-- Step 1: Personal Information -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-blue-200">
                    <h3 class="text-base md:text-lg font-bold text-gray-900 mb-3 md:mb-4 flex items-center">
                        <span class="w-6 h-6 md:w-8 md:h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-2 md:mr-3 text-xs md:text-sm">1</span>
                        Personal Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-person-fill text-blue-600 mr-2"></i>Full Name
                            </label>
                            <input type="text" name="name" 
                                   class="input-field w-full px-3 md:px-4 py-2 md:py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-all text-sm md:text-base" 
                                   placeholder="Juan Dela Cruz" required 
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-calendar-fill text-blue-600 mr-2"></i>Birth Date
                            </label>
                            <input type="date" name="birth_date" 
                                   class="input-field w-full px-3 md:px-4 py-2 md:py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-all text-sm md:text-base" 
                                   required 
                                   value="<?php echo isset($_POST['birth_date']) ? htmlspecialchars($_POST['birth_date']) : ''; ?>">
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-gender-ambiguous text-blue-600 mr-2"></i>Gender
                            </label>
                            <select name="gender" 
                                    class="input-field w-full px-3 md:px-4 py-2 md:py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-all text-sm md:text-base" 
                                    required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-heart-fill text-blue-600 mr-2"></i>Civil Status
                            </label>
                            <select name="civil_status" 
                                    class="input-field w-full px-3 md:px-4 py-2 md:py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-all text-sm md:text-base" 
                                    required>
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-geo-alt-fill text-blue-600 mr-2"></i>Complete Address
                            </label>
                            <input type="text" name="address" 
                                   class="input-field w-full px-3 md:px-4 py-2 md:py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-all text-sm md:text-base" 
                                   placeholder="Street, Barangay, City, Province" required 
                                   value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-globe text-blue-600 mr-2"></i>Country
                            </label>
                            <select name="country" 
                                    class="input-field w-full px-3 md:px-4 py-2 md:py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-all text-sm md:text-base" 
                                    required>
                                <option value="">Select Country</option>
                                <option value="Philippines">Philippines</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="United Arab Emirates">United Arab Emirates</option>
                                <option value="Kuwait">Kuwait</option>
                                <option value="Qatar">Qatar</option>
                                <option value="Hong Kong">Hong Kong</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Taiwan">Taiwan</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Japan">Japan</option>
                                <option value="South Korea">South Korea</option>
                                <option value="United States">United States</option>
                                <option value="Canada">Canada</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Australia">Australia</option>
                                <option value="Italy">Italy</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-telephone-fill text-blue-600 mr-2"></i>Contact Number
                            </label>
                            <input type="tel" name="contact_number" 
                                   class="input-field w-full px-3 md:px-4 py-2 md:py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-all text-sm md:text-base" 
                                   placeholder="+63 912 345 6789" required 
                                   pattern="[0-9+\s\-()]+" 
                                   value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : ''; ?>">
                            <small class="text-gray-600 text-xs mt-1 block">
                                <i class="bi bi-info-circle mr-1"></i>Enter your mobile number (e.g., +63 912 345 6789)
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Account Details -->
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-indigo-200">
                    <h3 class="text-base md:text-lg font-bold text-gray-900 mb-3 md:mb-4 flex items-center">
                        <span class="w-6 h-6 md:w-8 md:h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mr-2 md:mr-3 text-xs md:text-sm">2</span>
                        Account Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-envelope-fill text-blue-600 mr-2"></i>Email Address
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="email" name="email" id="email" 
                                       class="input-field flex-1 px-3 md:px-4 py-2 md:py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-all text-sm md:text-base" 
                                       placeholder="your.email@example.com" required 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                <button type="button" 
                                        class="px-4 md:px-6 py-2 md:py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-xl text-sm md:text-base whitespace-nowrap" 
                                        id="sendOtpBtn">
                                    <i class="bi bi-send-fill mr-2"></i>Send OTP
                                </button>
                            </div>
                            <small id="otp-help" class="text-gray-600 text-xs mt-1 block"></small>
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-lock-fill text-blue-600 mr-2"></i>Password
                            </label>
                            <input type="password" name="password" id="password" 
                                   class="input-field w-full px-3 md:px-4 py-2 md:py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-all text-sm md:text-base" 
                                   placeholder="Create a strong password" required>
                            <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full transition-all duration-300" id="password-strength-bar" style="width: 0%"></div>
                            </div>
                            <small id="password-strength-text" class="text-gray-600 text-xs mt-1 block"></small>
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                                <i class="bi bi-person-badge-fill text-blue-600 mr-2"></i>Account Type
                            </label>
                            <select name="role" 
                                    class="input-field w-full px-3 md:px-4 py-2 md:py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-all bg-gray-50 text-sm md:text-base" 
                                    required>
                                <option value="ofw">OFW (Overseas Filipino Worker)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Verification -->
                <div id="otp-container" class="<?php echo isset($_POST['otp']) ? '' : 'hidden'; ?> bg-gradient-to-r from-green-50 to-teal-50 rounded-lg md:rounded-xl p-4 md:p-6 border border-green-200">
                    <h3 class="text-base md:text-lg font-bold text-gray-900 mb-3 md:mb-4 flex items-center">
                        <span class="w-6 h-6 md:w-8 md:h-8 bg-green-600 text-white rounded-full flex items-center justify-center mr-2 md:mr-3 text-xs md:text-sm">3</span>
                        Email Verification
                    </h3>
                    
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                        <i class="bi bi-key-fill text-green-600 mr-2"></i>Enter OTP Code
                    </label>
                    <input type="text" name="otp" id="otp" 
                           class="input-field w-full px-3 md:px-4 py-2 md:py-3 border-2 border-gray-200 rounded-lg focus:border-green-500 focus:outline-none transition-all text-center text-xl md:text-2xl tracking-widest font-bold" 
                           placeholder="000000" maxlength="6" 
                           value="<?php echo isset($_POST['otp']) ? htmlspecialchars($_POST['otp']) : ''; ?>">
                    <p class="text-xs md:text-sm text-gray-600 mt-2">
                        <i class="bi bi-info-circle mr-1"></i>
                        Check your email inbox for the 6-digit verification code
                    </p>
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-start bg-gray-50 rounded-lg p-3 md:p-4 border border-gray-200">
                    <input type="checkbox" id="terms" name="terms" 
                           class="w-4 h-4 md:w-5 md:h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 mt-0.5" required>
                    <label for="terms" class="ml-2 md:ml-3 text-xs md:text-sm text-gray-700">
                        I agree to the 
                        <button type="button" onclick="openTermsModal()" 
                                class="text-blue-600 hover:text-blue-700 font-semibold underline">
                            Terms and Conditions
                        </button> 
                        and 
                        <button type="button" onclick="openTermsModal()" 
                                class="text-blue-600 hover:text-blue-700 font-semibold underline">
                            Privacy Policy
                        </button>
                        of the OFW Management System
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 md:py-4 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 text-sm md:text-base">
                    <i class="bi bi-check-circle-fill mr-2"></i>
                    Create Account
                </button>
            </form>

            <!-- Login Link -->
            <p class="mt-4 md:mt-6 text-center text-gray-600 text-xs md:text-sm">
                Already have an account? 
                <a href="login.php" class="text-blue-600 hover:text-blue-700 font-semibold">
                    Login here <i class="bi bi-arrow-right"></i>
                </a>
            </p>
        </div>
    </div>
</main>

<!-- Terms Modal -->
<div id="termsModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col shadow-2xl">
        <header class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-5">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold">Terms and Conditions</h2>
                <button onclick="closeTermsModal()" class="text-white/80 hover:text-white text-3xl leading-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </header>
        <div class="p-6 overflow-y-auto flex-1 text-gray-700">
            <h3 class="font-bold text-lg mb-3 text-gray-900">1. Acceptance of Terms</h3>
            <p class="mb-4">By registering and using the OFW Management System, you agree to comply with and be bound by these Terms and Conditions.</p>

            <h3 class="font-bold text-lg mb-3 text-gray-900">2. User Registration</h3>
            <p class="mb-4">You must provide accurate, current, and complete information during the registration process. You are responsible for maintaining the confidentiality of your account credentials.</p>

            <h3 class="font-bold text-lg mb-3 text-gray-900">3. Use of Services</h3>
            <p class="mb-4">The OFW Management System is designed to assist Overseas Filipino Workers with job opportunities, benefits, and support services. You agree to use the system only for lawful purposes.</p>

            <h3 class="font-bold text-lg mb-3 text-gray-900">4. Privacy and Data Protection</h3>
            <p class="mb-4">We are committed to protecting your personal information. Your data will be handled in accordance with applicable data protection laws and our Privacy Policy.</p>

            <h3 class="font-bold text-lg mb-3 text-gray-900">5. User Responsibilities</h3>
            <p class="mb-4">You are responsible for all activities that occur under your account. You must notify us immediately of any unauthorized use of your account.</p>

            <p class="mt-6 font-semibold text-gray-900">Last Updated: <?php echo date('F Y'); ?></p>
        </div>
        <footer class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <button onclick="closeTermsModal()" 
                    class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg transition-all shadow-md">
                I Understand
            </button>
        </footer>
    </div>
</div>

<script>
function openTermsModal() {
    document.getElementById('termsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeTermsModal() {
    document.getElementById('termsModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Password Strength Indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    
    let strength = 0;
    if (password.length >= 8) strength += 1;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
    if (password.match(/\d/)) strength += 1;
    if (password.match(/[^a-zA-Z\d]/)) strength += 1;

    if (password.length === 0) {
        strengthBar.style.width = '0%';
        strengthBar.className = 'h-full transition-all duration-300';
        strengthText.textContent = '';
    } else if (strength < 2) {
        strengthBar.style.width = '33%';
        strengthBar.className = 'h-full transition-all duration-300 bg-red-500';
        strengthText.textContent = 'Weak password';
        strengthText.className = 'text-red-600 text-xs mt-1 block';
    } else if (strength < 4) {
        strengthBar.style.width = '66%';
        strengthBar.className = 'h-full transition-all duration-300 bg-yellow-500';
        strengthText.textContent = 'Medium strength';
        strengthText.className = 'text-yellow-600 text-xs mt-1 block';
    } else {
        strengthBar.style.width = '100%';
        strengthBar.className = 'h-full transition-all duration-300 bg-green-500';
        strengthText.textContent = 'Strong password';
        strengthText.className = 'text-green-600 text-xs mt-1 block';
    }
});

// Send OTP
document.getElementById('sendOtpBtn').addEventListener('click', function() {
    const email = document.getElementById('email').value;
    const btn = this;
    const helpText = document.getElementById('otp-help');
    
    if (!email) {
        helpText.className = 'text-red-600 text-xs mt-1 block';
        helpText.innerText = 'Please enter an email address first.';
        return;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        helpText.className = 'text-red-600 text-xs mt-1 block';
        helpText.innerText = 'Please enter a valid email address.';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-2"></i>Sending...';

    const formData = new FormData();
    formData.append('email', email);

    fetch('send_otp.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('otp-container').classList.remove('hidden');
            document.getElementById('otp').focus();
            helpText.className = 'text-green-600 text-xs mt-1 block';
            helpText.innerHTML = '<i class="bi bi-check-circle-fill mr-1"></i>OTP sent successfully! Check your email.';

            let countdown = 60;
            btn.disabled = true;
            const interval = setInterval(() => {
                countdown--;
                btn.innerHTML = `<i class="bi bi-clock mr-2"></i>Resend in ${countdown}s`;
                if (countdown <= 0) {
                    clearInterval(interval);
                    btn.innerHTML = '<i class="bi bi-send-fill mr-2"></i>Resend OTP';
                    btn.disabled = false;
                }
            }, 1000);
        } else {
            helpText.className = 'text-red-600 text-xs mt-1 block';
            helpText.innerHTML = '<i class="bi bi-exclamation-circle-fill mr-1"></i>' + data.message;
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill mr-2"></i>Send OTP';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill mr-2"></i>Send OTP';
        helpText.className = 'text-red-600 text-xs mt-1 block';
        helpText.innerHTML = '<i class="bi bi-exclamation-circle-fill mr-1"></i>An error occurred. Please try again.';
    });
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTermsModal();
    }
});

// Mobile menu toggle
document.getElementById('mobile-menu-btn').addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
});
</script>
</body>
</html>
