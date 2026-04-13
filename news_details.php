<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

if (!isset($_GET['id'])) {
    header("Location: news.php");
    exit();
}

$news_id = intval($_GET['id']);
$query = "SELECT * FROM news WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: news.php");
    exit();
}

$news = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['title']) ?> - OFW Management System</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="alternate icon" href="images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: url('images/wall234.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animation-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .animation-slide-in {
            animation: slideIn 0.5s ease-out;
        }
        .article-content {
            font-size: 1.125rem;
            line-height: 1.8;
            color: #374151;
        }
        .article-content p {
            margin-bottom: 1.25rem;
        }
        .featured-image {
            position: relative;
            overflow: hidden;
        }
        .featured-image::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to top, rgba(255,255,255,0.9), transparent);
        }
        .share-button {
            transition: all 0.3s ease;
        }
        .share-button:hover {
            transform: translateY(-2px);
        }
        /* Carousel Styles */
        .carousel-container {
            position: relative;
            overflow: hidden;
            background: #000;
            width: 100%;
        }
        .carousel-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
            height: 100%;
            width: 100%;
        }
        .carousel-slide {
            min-width: 100%;
            max-width: 100%;
            width: 100%;
            flex-shrink: 0;
            flex-grow: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            position: relative;
        }
        .carousel-slide img,
        .carousel-slide video {
            width: 100%;
            height: 100%;
            object-fit: contain;
            max-height: 600px;
            display: block;
        }
        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .carousel-slide img,
            .carousel-slide video {
                max-height: 400px;
                object-fit: contain;
            }
        }
        @media (max-width: 480px) {
            .carousel-slide img,
            .carousel-slide video {
                max-height: 300px;
                object-fit: contain;
            }
        }
        .carousel-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.3s ease;
            z-index: 10;
            backdrop-filter: blur(5px);
            flex-shrink: 0;
        }
        .carousel-button:hover {
            background: rgba(0, 0, 0, 0.8);
            transform: translateY(-50%) scale(1.1);
        }
        .carousel-button:active {
            transform: translateY(-50%) scale(0.95);
        }
        .carousel-button.prev {
            left: 20px;
        }
        .carousel-button.next {
            right: 20px;
        }
        .carousel-indicators {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }
        .carousel-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.8);
        }
        .carousel-indicator.active {
            background: white;
            width: 40px;
            border-radius: 6px;
        }
        .carousel-counter {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            z-index: 10;
            backdrop-filter: blur(5px);
        }
        @media (max-width: 768px) {
            .carousel-button {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }
            .carousel-button.prev {
                left: 10px;
            }
            .carousel-button.next {
                right: 10px;
            }
            .carousel-counter {
                top: 10px;
                right: 10px;
                padding: 6px 12px;
                font-size: 12px;
            }
            .carousel-indicators {
                bottom: 10px;
                gap: 8px;
            }
            .carousel-indicator {
                width: 10px;
                height: 10px;
            }
            .carousel-indicator.active {
                width: 30px;
            }
        }
        @media (max-width: 480px) {
            .carousel-button {
                width: 35px;
                height: 35px;
                font-size: 18px;
            }
            .carousel-button.prev {
                left: 5px;
            }
            .carousel-button.next {
                right: 5px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">

<?php include 'sidebar.php'; ?>

<main class="lg:ml-64 p-4 lg:p-8 min-h-screen pt-20 lg:pt-8">
    <!-- Breadcrumb Navigation -->
    <nav class="mb-6 animation-slide-in">
        <div class="flex items-center space-x-2 text-sm">
            <a href="dashboard.php" class="text-blue-600 hover:text-blue-700 font-medium transition">
                <i class="bi bi-house-door-fill"></i> Home
            </a>
            <i class="bi bi-chevron-right text-gray-400 text-xs"></i>
            <a href="news.php" class="text-blue-600 hover:text-blue-700 font-medium transition">News</a>
            <i class="bi bi-chevron-right text-gray-400 text-xs"></i>
            <span class="text-gray-500">Article</span>
        </div>
    </nav>

    <!-- Back Button -->
    <div class="mb-6 animation-slide-in flex justify-end">
        <a href="news.php" class="inline-flex items-center px-4 py-2 bg-white/90 hover:bg-white text-gray-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
            <i class="bi bi-arrow-left mr-2"></i>
            Back to News
        </a>
    </div>
    
    <!-- News Article -->
    <article class="max-w-5xl mx-auto animation-fade-in">
        <!-- Article Header -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-6">
            <div class="px-6 py-8 lg:px-12 lg:py-10">
                <!-- Category Badge -->
                <div class="mb-6">
                    <span class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-semibold rounded-full shadow-md">
                        <i class="bi bi-newspaper mr-2"></i>
                        News & Updates
                    </span>
                </div>

                <!-- Title -->
                <h1 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                    <?= htmlspecialchars($news['title']) ?>
                </h1>

                <!-- Meta Information -->
                <div class="flex flex-wrap items-center gap-6 text-gray-600 pb-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold mr-3 shadow-md">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Published by</p>
                            <p class="font-semibold text-gray-900">OFW Admin</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white shadow-md mr-3">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Published on</p>
                            <p class="font-semibold text-gray-900"><?= date("F j, Y", strtotime($news['created_at'])) ?></p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white shadow-md mr-3">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Time</p>
                            <p class="font-semibold text-gray-900"><?= date("g:i A", strtotime($news['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media Carousel (Image & Video) -->
            <?php 
            $hasImage = !empty($news['image']);
            $hasVideo = isset($news['video']) && !empty($news['video']);
            $mediaCount = ($hasImage ? 1 : 0) + ($hasVideo ? 1 : 0);
            ?>
            
            <?php if ($hasImage || $hasVideo): ?>
            <div class="carousel-container relative" id="mediaCarousel" style="min-height: 300px;">
                <!-- Counter -->
                <?php if ($mediaCount > 1): ?>
                <div class="carousel-counter">
                    <span id="currentSlide">1</span> / <span id="totalSlides"><?= $mediaCount ?></span>
                </div>
                <?php endif; ?>

                <!-- Carousel Track -->
                <div class="carousel-track" id="carouselTrack">
                    <!-- Image Slide -->
                    <?php if ($hasImage): ?>
                    <div class="carousel-slide">
                        <img src="uploads/<?= htmlspecialchars($news['image']) ?>" 
                             alt="<?= htmlspecialchars($news['title']) ?>" 
                             loading="lazy">
                    </div>
                    <?php endif; ?>

                    <!-- Video Slide -->
                    <?php if ($hasVideo): ?>
                    <div class="carousel-slide">
                        <video class="w-full h-full" 
                               controls 
                               preload="auto" 
                               playsinline
                               id="newsVideo"
                               poster="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 600'%3E%3Crect fill='%23000' width='800' height='600'/%3E%3Ctext fill='%23fff' font-family='Arial' font-size='48' x='50%25' y='50%25' text-anchor='middle' dominant-baseline='middle'%3ELoading Video...%3C/text%3E%3C/svg%3E">
                            <source src="uploads/<?= htmlspecialchars($news['video']) ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Navigation Buttons (only show if multiple media) -->
                <?php if ($mediaCount > 1): ?>
                <button class="carousel-button prev" id="prevBtn" aria-label="Previous slide">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="carousel-button next" id="nextBtn" aria-label="Next slide">
                    <i class="bi bi-chevron-right"></i>
                </button>

                <!-- Indicators -->
                <div class="carousel-indicators" id="carouselIndicators">
                    <?php for ($i = 0; $i < $mediaCount; $i++): ?>
                    <div class="carousel-indicator <?= $i === 0 ? 'active' : '' ?>" data-slide="<?= $i ?>"></div>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Article Content -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="px-6 py-8 lg:px-12 lg:py-10">
                <div class="article-content max-w-none">
                    <?= nl2br(htmlspecialchars($news['content'])) ?>
                </div>

                <!-- Article Footer -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-lg">
                                <i class="bi bi-shield-check text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Official News from</p>
                                <p class="text-lg font-bold text-gray-900">OFW Management System</p>
                            </div>
                        </div>
                        <a href="news.php" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                            <i class="bi bi-grid-3x3-gap-fill mr-2"></i>
                            View All News
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </article>
</main>

<script>
// Carousel functionality
const mediaCount = <?= $mediaCount ?>;

if (mediaCount > 1) {
    let currentSlide = 0;
    const carousel = document.getElementById('mediaCarousel');
    const track = document.getElementById('carouselTrack');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const indicators = document.querySelectorAll('.carousel-indicator');
    const currentSlideSpan = document.getElementById('currentSlide');
    const video = document.getElementById('newsVideo');

    // Preload video when page loads for faster playback
    if (video) {
        video.load();
        
        // Add event listener to show when video is ready
        video.addEventListener('loadeddata', function() {
            console.log('Video loaded and ready to play');
        });
        
        // Handle video loading errors
        video.addEventListener('error', function(e) {
            console.error('Video loading error:', e);
        });
    }

    // Get the actual width of the carousel container
    function getSlideWidth() {
        return carousel.offsetWidth;
    }

    function updateCarousel() {
        const slideWidth = getSlideWidth();
        track.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
        
        // Update indicators
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentSlide);
        });
        
        // Update counter
        currentSlideSpan.textContent = currentSlide + 1;

        // Handle video playback based on slide
        if (video) {
            const videoSlideIndex = <?= $hasImage ? 1 : 0 ?>;
            if (currentSlide === videoSlideIndex) {
                // On video slide - ensure video is loaded and ready
                if (video.readyState < 2) {
                    video.load(); // Force load if not ready
                }
            } else {
                // Not on video slide - pause it to save resources
                video.pause();
            }
        }
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % mediaCount;
        updateCarousel();
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + mediaCount) % mediaCount;
        updateCarousel();
    }

    function goToSlide(index) {
        currentSlide = index;
        updateCarousel();
    }

    // Event listeners
    prevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        prevSlide();
    });

    nextBtn.addEventListener('click', (e) => {
        e.preventDefault();
        nextSlide();
    });

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => goToSlide(index));
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') prevSlide();
        if (e.key === 'ArrowRight') nextSlide();
    });

    // Touch/swipe support with improved mobile handling
    let touchStartX = 0;
    let touchEndX = 0;
    let touchStartY = 0;
    let touchEndY = 0;
    let isSwiping = false;

    carousel.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].clientX;
        touchStartY = e.changedTouches[0].clientY;
        isSwiping = false;
    }, { passive: true });

    carousel.addEventListener('touchmove', (e) => {
        if (!isSwiping) {
            const touchMoveX = e.changedTouches[0].clientX;
            const touchMoveY = e.changedTouches[0].clientY;
            const diffX = Math.abs(touchMoveX - touchStartX);
            const diffY = Math.abs(touchMoveY - touchStartY);
            
            // If horizontal swipe is more significant than vertical
            if (diffX > diffY && diffX > 10) {
                isSwiping = true;
            }
        }
        
        // Prevent default only if we're swiping horizontally
        if (isSwiping) {
            e.preventDefault();
        }
    }, { passive: false });

    carousel.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].clientX;
        touchEndY = e.changedTouches[0].clientY;
        
        if (isSwiping) {
            handleSwipe();
        }
        isSwiping = false;
    }, { passive: true });

    function handleSwipe() {
        const diffX = touchStartX - touchEndX;
        const diffY = Math.abs(touchStartY - touchEndY);
        
        // Only trigger swipe if horizontal movement is greater than vertical
        // and movement is significant (more than 50px)
        if (Math.abs(diffX) > 50 && Math.abs(diffX) > diffY) {
            if (diffX > 0) {
                // Swipe left - next slide
                nextSlide();
            } else {
                // Swipe right - previous slide
                prevSlide();
            }
        }
    }

    // Recalculate on window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            updateCarousel();
        }, 250);
    });

    // Initial setup
    updateCarousel();
} else if (mediaCount === 1) {
    // If only video exists (no carousel), still optimize loading
    const video = document.getElementById('newsVideo');
    if (video) {
        // Preload video immediately
        video.load();
        
        video.addEventListener('loadeddata', function() {
            console.log('Video loaded and ready to play');
        });
        
        video.addEventListener('error', function(e) {
            console.error('Video loading error:', e);
        });
    }
}
</script>

</body>
</html>