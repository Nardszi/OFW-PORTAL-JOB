<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

$user_role = $_SESSION["role"];

// Pagination logic
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total number of news items (excluding expired)
$total_news_query = "SELECT COUNT(*) as total FROM news WHERE expiration_date IS NULL OR expiration_date >= CURDATE()";
$total_news_result = $conn->query($total_news_query);
$total_news = $total_news_result->fetch_assoc()['total'];
$total_pages = ceil($total_news / $limit);

// Fetch news updates (excluding expired)
$news_query = "SELECT id, title, content, image, created_at, expiration_date FROM news 
               WHERE expiration_date IS NULL OR expiration_date >= CURDATE()
               ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$news_result = $conn->query($news_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News - OFW Management System</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="alternate icon" href="images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            background: url('images/wall234.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            font-family: 'Inter', sans-serif;
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
        .news-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .news-card:hover {
            transform: translateY(-8px);
        }
        .news-card img {
            transition: transform 0.5s ease;
        }
        .news-card:hover img {
            transform: scale(1.1);
        }
        .gradient-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);
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

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="lg:ml-64 p-4 lg:p-8 min-h-screen pt-20 lg:pt-8">
    <!-- Header Section -->
    <div class="mb-8 md:mb-12">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-3xl p-8 md:p-12 shadow-2xl">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="bi bi-newspaper text-4xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-white text-3xl md:text-4xl lg:text-5xl font-bold">Latest News & Updates</h1>
                    <p class="text-white/90 text-sm md:text-base lg:text-lg mt-2">Stay informed with the latest announcements and opportunities</p>
                </div>
            </div>
        </div>
    </div>

    <!-- News Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        <?php if ($news_result->num_rows > 0) { ?>
            <?php while ($news = $news_result->fetch_assoc()) { 
                $news_id = $news['id'];
                $content_preview = substr(strip_tags($news["content"]), 0, 150) . '...';
            ?>
                <article class="news-card bg-white rounded-2xl shadow-xl overflow-hidden group">
                    <!-- Image Section -->
                    <div class="relative h-64 overflow-hidden">
                        <?php if (!empty($news["image"])) { ?>
                            <img src="uploads/<?php echo htmlspecialchars($news["image"]); ?>" 
                                 alt="<?php echo htmlspecialchars($news["title"]); ?>" 
                                 class="w-full h-full object-cover">
                        <?php } elseif (isset($news["video"]) && !empty($news["video"])) { ?>
                            <video class="w-full h-full object-cover">
                                <source src="uploads/<?php echo htmlspecialchars($news["video"]); ?>" type="video/mp4">
                            </video>
                            <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                                <div class="w-16 h-16 bg-white/90 rounded-full flex items-center justify-center">
                                    <i class="bi bi-play-fill text-3xl text-blue-600"></i>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                <i class="bi bi-newspaper text-7xl text-blue-600"></i>
                            </div>
                        <?php } ?>
                        
                        <!-- Gradient Overlay -->
                        <div class="gradient-overlay absolute bottom-0 left-0 right-0 h-24"></div>
                        
                        <!-- Date Badge -->
                        <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg">
                            <span class="text-xs font-semibold text-blue-600 flex items-center gap-1">
                                <i class="bi bi-calendar3"></i>
                                <?php echo date("M d, Y", strtotime($news["created_at"])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Content Section -->
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-3 text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                            <?= htmlspecialchars($news["title"]) ?>
                        </h2>
                        <p class="text-gray-600 text-sm mb-6 line-clamp-3 leading-relaxed">
                            <?= htmlspecialchars($content_preview) ?>
                        </p>
                        <a href="news_details.php?id=<?= $news_id ?>" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full transition-all duration-300 font-semibold shadow-lg hover:shadow-xl group">
                            Read Full Story 
                            <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </article>
            <?php } ?>
        <?php } else { ?>
            <div class="col-span-full">
                <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-16 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="bi bi-inbox text-gray-400 text-5xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">No News Available</h3>
                    <p class="text-gray-600">Check back later for the latest updates and announcements.</p>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation" class="mt-12">
        <div class="flex justify-center items-center gap-2">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" 
                   class="w-12 h-12 flex items-center justify-center bg-white hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-lg hover:shadow-xl font-semibold">
                    <i class="bi bi-chevron-left"></i>
                </a>
            <?php endif; ?>

            <div class="flex gap-2">
                <?php 
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                
                if ($start > 1): ?>
                    <a href="?page=1" class="w-12 h-12 flex items-center justify-center bg-white hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-lg hover:shadow-xl font-semibold">1</a>
                    <?php if ($start > 2): ?>
                        <span class="w-12 h-12 flex items-center justify-center text-white">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?page=<?= $i ?>" 
                       class="w-12 h-12 flex items-center justify-center <?= ($i == $page) ? 'bg-blue-600 text-white shadow-xl' : 'bg-white hover:bg-blue-600 hover:text-white' ?> rounded-xl transition-all shadow-lg hover:shadow-xl font-semibold">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($end < $total_pages): ?>
                    <?php if ($end < $total_pages - 1): ?>
                        <span class="w-12 h-12 flex items-center justify-center text-white">...</span>
                    <?php endif; ?>
                    <a href="?page=<?= $total_pages ?>" class="w-12 h-12 flex items-center justify-center bg-white hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-lg hover:shadow-xl font-semibold"><?= $total_pages ?></a>
                <?php endif; ?>
            </div>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>" 
                   class="w-12 h-12 flex items-center justify-center bg-white hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-lg hover:shadow-xl font-semibold">
                    <i class="bi bi-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </nav>
    <?php endif; ?>
</main>

</body>
</html>
