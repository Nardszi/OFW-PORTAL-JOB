<?php
session_start();
include "../config/database.php";

// Check for Remember Me cookie
if (!isset($_SESSION["user_id"]) && isset($_COOKIE["remember_me"])) {
    list($user_id, $token) = explode(":", base64_decode($_COOKIE["remember_me"]));
    
    $query = "SELECT * FROM users WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && hash('sha256', $user["password"]) === $token) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["full_name"] = isset($user["name"]) ? $user["name"] : $user["full_name"];
        header("Location: ../dashboard.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user["password"])) {
        if ($user["role"] == "ofw" && $user["status"] != "approved") {
            $_SESSION["error_message"] = "Your account is pending approval. Please wait for admin approval.";
            header("Location: login.php");
            exit();
        }

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["full_name"] = $user["full_name"];

        // Log Login Activity
        $log_action = "Login";
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $log_stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)");
        $log_stmt->bind_param("isss", $user["id"], $log_action, $ip_address, $user_agent);
        $log_stmt->execute();

        // Handle Remember Me
        if (isset($_POST['remember_me'])) {
            $token = hash('sha256', $user["password"]);
            $cookie_value = base64_encode($user["id"] . ":" . $token);
            setcookie("remember_me", $cookie_value, time() + (86400 * 30), "/");
        }

        // Handle redirect parameter
        $redirect_page = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard';
        
        // Map redirect names to actual pages
        $redirect_map = [
            'view_jobs' => '../view_jobs.php',
            'jobs' => '../view_jobs.php',
            'benefits' => '../benefits.php',
            'news' => '../news.php',
            'dashboard' => '../dashboard.php'
        ];
        
        $redirect_url = isset($redirect_map[$redirect_page]) ? $redirect_map[$redirect_page] : '../dashboard.php';
        
        header("Location: " . $redirect_url);
        exit();
    } else {
        $_SESSION["error_message"] = "Invalid email or password!";
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OFW Management System</title>
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
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
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

<!-- Login Form -->
<main class="flex-1 flex items-center justify-center px-4 py-8 md:py-12 relative z-10 mt-16 md:mt-20">
    <div class="w-full max-w-md animate-slide-in">
        <div class="glass-effect rounded-2xl md:rounded-3xl p-6 md:p-10 shadow-2xl">
            <!-- Header -->
            <div class="text-center mb-6 md:mb-8">
                <div class="flex justify-center mb-3 md:mb-4 animate-float">
                    <img src="../images/logo5.png" alt="OFW Management" class="h-16 md:h-20 w-auto">
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                <p class="text-sm md:text-base text-gray-600">Sign in to your OFW account</p>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_SESSION["error_message"])) { ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 md:p-4 rounded-lg mb-4 md:mb-6 flex items-start text-sm md:text-base">
                    <i class="bi bi-exclamation-triangle-fill text-lg md:text-xl mr-2 md:mr-3 mt-0.5"></i>
                    <span><?php echo $_SESSION["error_message"]; unset($_SESSION["error_message"]); ?></span>
                </div>
            <?php } ?>

            <form method="POST" class="space-y-4 md:space-y-6">
                <!-- Email Field -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                        <i class="bi bi-envelope-fill text-blue-600 mr-2"></i>Email Address
                    </label>
                    <input type="email" name="email" 
                           class="input-field w-full px-3 md:px-4 py-2.5 md:py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-all text-sm md:text-base" 
                           placeholder="your.email@example.com" required>
                </div>

                <!-- Password Field -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                        <i class="bi bi-lock-fill text-blue-600 mr-2"></i>Password
                    </label>
                    <input type="password" name="password" 
                           class="input-field w-full px-3 md:px-4 py-2.5 md:py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-all text-sm md:text-base" 
                           placeholder="Enter your password" required>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="rememberMe" name="remember_me" 
                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="rememberMe" class="ml-2 text-xs md:text-sm text-gray-700">Remember me for 30 days</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 md:py-4 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 text-sm md:text-base">
                    <i class="bi bi-box-arrow-in-right mr-2"></i>
                    Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-4 md:my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-xs md:text-sm">
                    <span class="px-4 bg-white text-gray-500">New to OFW Management?</span>
                </div>
            </div>

            <!-- Register Link -->
            <div class="text-center">
                <a href="register.php" 
                   class="inline-flex items-center justify-center w-full px-4 md:px-6 py-2.5 md:py-3 border-2 border-blue-600 text-blue-600 hover:bg-blue-50 font-semibold rounded-xl transition-all duration-200 text-sm md:text-base">
                    <i class="bi bi-person-plus-fill mr-2"></i>
                    Create New Account
                </a>
            </div>

            <!-- Additional Info -->
            <div class="mt-4 md:mt-6 text-center">
                <p class="text-xs text-gray-500">
                    By signing in, you agree to our 
                    <a href="#" class="text-blue-600 hover:text-blue-700 underline">Terms of Service</a> 
                    and 
                    <a href="#" class="text-blue-600 hover:text-blue-700 underline">Privacy Policy</a>
                </p>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-4 md:mt-6 text-center">
            <div class="bg-white/10 backdrop-blur-md rounded-xl md:rounded-2xl p-3 md:p-4 border border-white/20">
                <p class="text-white text-xs md:text-sm mb-2">
                    <i class="bi bi-info-circle-fill mr-2"></i>
                    Need help accessing your account?
                </p>
                <a href="../index.php" class="text-blue-300 hover:text-blue-200 text-xs md:text-sm font-semibold">
                    Contact Support <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="relative z-10 py-3 md:py-4 text-center text-white/70 text-xs md:text-sm">
    <p>&copy; <?php echo date('Y'); ?> OFW Management System. All rights reserved.</p>
</footer>

<script>
// Mobile menu toggle
document.getElementById('mobile-menu-btn').addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
});
</script>

</body>
</html>
