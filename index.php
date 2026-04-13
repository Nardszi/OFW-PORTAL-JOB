<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OFW Management System - Empowering Filipino Workers Worldwide</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="alternate icon" href="images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        .gradient-text {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }
        .slider-container {
            position: relative;
            width: 100%;
        }
        .slider-image {
            display: none;
            width: 100%;
            height: auto;
        }
        .slider-image.active {
            display: block;
        }
        .slider-dot.active {
            background: white;
            width: 2rem;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Navigation -->
<nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-md shadow-md z-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-4">
            <!-- Logo -->
            <a href="index.php" class="flex items-center space-x-3 group">
                <img src="images/logo5.png" alt="OFW Management" class="h-12 w-auto group-hover:scale-110 transition-transform">
                <span class="text-xl font-bold text-gray-900">OFW Management</span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-gray-700 hover:text-blue-600 font-medium transition">Home</a>
                <a href="#features" class="text-gray-700 hover:text-blue-600 font-medium transition">Features</a>
                <a href="#about" class="text-gray-700 hover:text-blue-600 font-medium transition">About</a>
                
                <?php if (isset($_SESSION["user_id"])) { ?>
                    <a href="dashboard.php" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full font-semibold transition shadow-lg">
                        Dashboard
                    </a>
                <?php } else { ?>
                    <a href="auth/login.php" class="text-gray-700 hover:text-blue-600 font-medium transition">Login</a>
                    <a href="auth/register.php" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full font-semibold transition shadow-lg">
                        Get Started
                    </a>
                <?php } ?>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden text-gray-700">
                <i class="bi bi-list text-3xl"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden pb-4">
            <div class="flex flex-col space-y-3">
                <a href="index.php" class="text-gray-700 hover:text-blue-600 font-medium transition">Home</a>
                <a href="#features" class="text-gray-700 hover:text-blue-600 font-medium transition">Features</a>
                <a href="#about" class="text-gray-700 hover:text-blue-600 font-medium transition">About</a>
                <?php if (isset($_SESSION["user_id"])) { ?>
                    <a href="dashboard.php" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-full font-semibold text-center">Dashboard</a>
                <?php } else { ?>
                    <a href="auth/login.php" class="text-gray-700 hover:text-blue-600 font-medium transition">Login</a>
                    <a href="auth/register.php" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-full font-semibold text-center">Get Started</a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="home" class="pt-24 md:pt-32 pb-12 md:pb-20 hero-gradient relative overflow-hidden">
    <!-- Background Pattern Overlay -->
    <div class="absolute inset-0 opacity-10">
        <img src="images/wall4.jpg" alt="" class="w-full h-full object-cover">
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
            <!-- Left Content -->
            <div class="flex-1 text-white animate-fade-in-up text-center lg:text-left">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight">
                    Empowering Filipino Workers <span class="text-blue-200">Worldwide</span>
                </h1>
                <p class="text-base sm:text-lg md:text-xl mb-6 md:mb-8 text-white/90 leading-relaxed">
                    Your comprehensive platform for job opportunities, benefits, and support services designed specifically for Overseas Filipino Workers.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center lg:justify-start">
                    <a href="auth/register.php" class="inline-flex items-center justify-center px-6 md:px-8 py-3 md:py-4 bg-white text-blue-600 rounded-full font-bold text-base md:text-lg shadow-xl hover:shadow-2xl hover:scale-105 transition-all">
                        <i class="bi bi-person-plus-fill mr-2"></i>
                        Create Account
                    </a>
                    <a href="#features" class="inline-flex items-center justify-center px-6 md:px-8 py-3 md:py-4 bg-white/10 backdrop-blur-md text-white border-2 border-white rounded-full font-bold text-base md:text-lg hover:bg-white/20 transition-all">
                        <i class="bi bi-arrow-down-circle mr-2"></i>
                        Learn More
                    </a>
                </div>
            </div>

            <!-- Right Content - Image Slider -->
            <div class="flex-1 w-full animate-float">
                <div class="relative max-w-lg mx-auto">
                    <div id="hero-slider" class="rounded-xl md:rounded-2xl overflow-hidden shadow-2xl">
                        <div class="slider-container">
                            <img src="images/OFW HOTLINE FINAL 2.png" alt="OFW Services" class="w-full h-auto slider-image active">
                            <img src="images/OFW HOTLINE FINAL.png" alt="OFW Hotline" class="w-full h-auto slider-image">
                        </div>
                    </div>
                    <!-- Slider Controls -->
                    <button onclick="prevSlide()" class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center shadow-lg transition-all">
                        <i class="bi bi-chevron-left text-sm md:text-base"></i>
                    </button>
                    <button onclick="nextSlide()" class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center shadow-lg transition-all">
                        <i class="bi bi-chevron-right text-sm md:text-base"></i>
                    </button>
                    <!-- Slider Indicators -->
                    <div class="absolute bottom-3 md:bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                        <button onclick="goToSlide(0)" class="slider-dot active w-2 h-2 md:w-3 md:h-3 rounded-full bg-white transition-all"></button>
                        <button onclick="goToSlide(1)" class="slider-dot w-2 h-2 md:w-3 md:h-3 rounded-full bg-white/50 transition-all"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-12 md:py-16 lg:py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10 md:mb-16">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-3 md:mb-4">
                Powerful Features for <span class="gradient-text">OFWs</span>
            </h2>
            <p class="text-base md:text-lg lg:text-xl text-gray-600 max-w-2xl mx-auto px-4">
                Everything you need to manage your overseas employment journey in one place
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <!-- Feature 1 - Job Opportunities -->
            <div onclick="navigateToFeature('view_jobs')" class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl md:rounded-2xl p-6 md:p-8 hover:shadow-xl transition-all hover:-translate-y-2 cursor-pointer">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl md:rounded-2xl flex items-center justify-center mb-4 md:mb-6 shadow-lg">
                    <i class="bi bi-briefcase-fill text-2xl md:text-3xl text-white"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 md:mb-3">Job Opportunities</h3>
                <p class="text-sm md:text-base text-gray-600 leading-relaxed">
                    Browse and apply for verified job listings from trusted employers worldwide. Find your perfect opportunity abroad.
                </p>
            </div>

            <!-- Feature 2 - Benefits & Assistance -->
            <div onclick="navigateToFeature('benefits')" class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl md:rounded-2xl p-6 md:p-8 hover:shadow-xl transition-all hover:-translate-y-2 cursor-pointer">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl md:rounded-2xl flex items-center justify-center mb-4 md:mb-6 shadow-lg">
                    <i class="bi bi-gift-fill text-2xl md:text-3xl text-white"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 md:mb-3">Benefits & Assistance</h3>
                <p class="text-sm md:text-base text-gray-600 leading-relaxed">
                    Access government benefits, financial assistance, and support programs designed for OFWs and their families.
                </p>
            </div>

            <!-- Feature 3 - News & Updates -->
            <div onclick="navigateToFeature('news')" class="bg-gradient-to-br from-green-50 to-teal-50 rounded-xl md:rounded-2xl p-6 md:p-8 hover:shadow-xl transition-all hover:-translate-y-2 cursor-pointer">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-green-600 to-green-700 rounded-xl md:rounded-2xl flex items-center justify-center mb-4 md:mb-6 shadow-lg">
                    <i class="bi bi-newspaper text-2xl md:text-3xl text-white"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 md:mb-3">News & Updates</h3>
                <p class="text-sm md:text-base text-gray-600 leading-relaxed">
                    Stay informed with the latest news, policy updates, and important announcements for Filipino workers abroad.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-12 md:py-16 lg:py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4 md:mb-6">
                About <span class="gradient-text">OFW Management System</span>
            </h2>
            <p class="text-base md:text-lg lg:text-xl text-gray-600 leading-relaxed mb-4 md:mb-8 px-4">
                The OFW Management System is a comprehensive digital platform dedicated to supporting Overseas Filipino Workers throughout their journey abroad. We provide a centralized hub for job opportunities, government benefits, and essential services.
            </p>
            <p class="text-sm md:text-base lg:text-lg text-gray-600 leading-relaxed mb-8 md:mb-12 px-4">
                Our mission is to empower Filipino workers by providing them with the tools, resources, and support they need to succeed in their overseas careers while ensuring their welfare and that of their families back home.
            </p>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-12 md:py-16 lg:py-20 hero-gradient">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 md:mb-6">
            Ready to Get Started?
        </h2>
        <p class="text-base md:text-lg lg:text-xl text-white/90 mb-6 md:mb-8 max-w-2xl mx-auto px-4">
            Join thousands of OFWs who are already benefiting from our platform. Create your account today and take control of your overseas career.
        </p>
        <a href="auth/register.php" class="inline-flex items-center px-8 md:px-10 py-3 md:py-4 bg-white text-blue-600 rounded-full font-bold text-base md:text-lg shadow-2xl hover:shadow-3xl hover:scale-105 transition-all">
            <i class="bi bi-rocket-takeoff-fill mr-2"></i>
            Create Free Account
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-8 md:py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8 mb-6 md:mb-8">
            <div>
                <div class="flex items-center space-x-3 mb-3 md:mb-4">
                    <img src="images/logo5.png" alt="OFW Management" class="h-8 md:h-10 w-auto">
                    <span class="text-base md:text-lg font-bold">OFW Management</span>
                </div>
                <p class="text-gray-400 text-xs md:text-sm">
                    Empowering Filipino workers worldwide with opportunities and support.
                </p>
            </div>

            <div>
                <h4 class="font-bold mb-3 md:mb-4 text-sm md:text-base">Quick Links</h4>
                <ul class="space-y-2 text-gray-400 text-xs md:text-sm">
                    <li><a href="#home" class="hover:text-white transition">Home</a></li>
                    <li><a href="#features" class="hover:text-white transition">Features</a></li>
                    <li><a href="#benefits" class="hover:text-white transition">Benefits</a></li>
                    <li><a href="#about" class="hover:text-white transition">About</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold mb-3 md:mb-4 text-sm md:text-base">Services</h4>
                <ul class="space-y-2 text-gray-400 text-xs md:text-sm">
                    <li><a href="auth/register.php" class="hover:text-white transition">Job Listings</a></li>
                    <li><a href="auth/register.php" class="hover:text-white transition">Benefits</a></li>
                    <li><a href="auth/register.php" class="hover:text-white transition">News & Updates</a></li>
                    <li><a href="auth/register.php" class="hover:text-white transition">Support</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold mb-3 md:mb-4 text-sm md:text-base">Contact</h4>
                <ul class="space-y-2 text-gray-400 text-xs md:text-sm">
                    <li class="flex items-center">
                        <i class="bi bi-envelope mr-2"></i>
                        support@ofwmanagement.com
                    </li>
                    <li class="flex items-center">
                        <i class="bi bi-telephone mr-2"></i>
                        +63 XXX XXX XXXX
                    </li>
                    <li class="flex items-center">
                        <i class="bi bi-clock mr-2"></i>
                        24/7 Support
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-6 md:pt-8 text-center text-gray-400 text-xs md:text-sm">
            <p>&copy; <?php echo date('Y'); ?> OFW Management System. All rights reserved.</p>
        </div>
    </div>
</footer>

<script>
// Mobile menu toggle
document.getElementById('mobile-menu-btn').addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            // Close mobile menu if open
            document.getElementById('mobile-menu').classList.add('hidden');
        }
    });
});

// Image Slider
let currentSlide = 0;
const slides = document.querySelectorAll('.slider-image');
const dots = document.querySelectorAll('.slider-dot');

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.remove('active');
        dots[i].classList.remove('active');
    });
    
    if (index >= slides.length) {
        currentSlide = 0;
    } else if (index < 0) {
        currentSlide = slides.length - 1;
    } else {
        currentSlide = index;
    }
    
    slides[currentSlide].classList.add('active');
    dots[currentSlide].classList.add('active');
}

function nextSlide() {
    showSlide(currentSlide + 1);
}

function prevSlide() {
    showSlide(currentSlide - 1);
}

function goToSlide(index) {
    showSlide(index);
}

// Auto-advance slider every 5 seconds
setInterval(nextSlide, 5000);

// Navigate to feature sections
function navigateToFeature(feature) {
    <?php if (isset($_SESSION["user_id"])) { ?>
        // User is logged in, redirect directly to feature page
        window.location.href = feature + '.php';
    <?php } else { ?>
        // User is not logged in, redirect to login with redirect parameter
        window.location.href = 'auth/login.php?redirect=' + feature;
    <?php } ?>
}
</script>

</body>
</html>
