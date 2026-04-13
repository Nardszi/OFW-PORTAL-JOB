<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

// Handle Delete News
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    
    // Get media files to delete
    $get_media = "SELECT image, video FROM news WHERE id = ?";
    $stmt_get = $conn->prepare($get_media);
    $stmt_get->bind_param("i", $id);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    $media = $result->fetch_assoc();
    
    // Delete the news record
    $query = "DELETE FROM news WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Delete media files
        if (!empty($media['image']) && file_exists("uploads/" . $media['image'])) {
            unlink("uploads/" . $media['image']);
        }
        if (!empty($media['video']) && file_exists("uploads/" . $media['video'])) {
            unlink("uploads/" . $media['video']);
        }
        $_SESSION['message'] = "News deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete news.";
    }
    $stmt->close();
    header("Location: update_news.php");
    exit();
}

// Fetch all news
$news_query = "SELECT * FROM news ORDER BY created_at DESC";
$news_result = $conn->query($news_query);
$current_date = date("Y-m-d");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News - OFW Management System</title>
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
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-1">
                    <i class="bi bi-newspaper mr-2"></i>Manage News
                </h1>
                <p class="text-gray-600">Create, edit, and manage news articles and updates</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="add_news.php" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                    <i class="bi bi-plus-circle mr-2"></i> Add News
                </a>
            </div>
        </div>
    </header>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow-md flex items-center">
            <i class="bi bi-check-circle-fill text-2xl mr-3"></i>
            <span><?= $_SESSION['message']; unset($_SESSION['message']); ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 shadow-md flex items-center">
            <i class="bi bi-exclamation-triangle-fill text-2xl mr-3"></i>
            <span><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
        </div>
    <?php endif; ?>

    <!-- News Grid -->
    <section class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php 
        $count = 0;
        while ($news = $news_result->fetch_assoc()) { 
            $count++;
            $content_preview = substr(strip_tags($news["content"]), 0, 120) . '...';
            $expiration_date = !empty($news["expiration_date"]) ? $news["expiration_date"] : null;
            $is_expired = $expiration_date && $expiration_date < $current_date;
        ?>
            <article class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl flex flex-col <?= $is_expired ? 'opacity-75' : '' ?>">
                <!-- Gradient Header with Status Badge -->
                <div class="relative bg-gradient-to-r <?= $is_expired ? 'from-gray-500 to-gray-600' : 'from-blue-600 to-cyan-600' ?> p-6 text-white">
                    <div class="absolute top-4 right-4">
                        <?php if ($is_expired) { ?>
                            <span class="px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded-full">
                                <i class="bi bi-x-circle-fill mr-1"></i>Expired
                            </span>
                        <?php } else { ?>
                            <span class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">
                                <i class="bi bi-check-circle-fill mr-1"></i>Active
                            </span>
                        <?php } ?>
                    </div>
                    <div class="flex items-center gap-3 mb-2 pr-20">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <?php if (!empty($news["image"])): ?>
                                <i class="bi bi-image text-2xl"></i>
                            <?php elseif (!empty($news["video"])): ?>
                                <i class="bi bi-play-circle text-2xl"></i>
                            <?php else: ?>
                                <i class="bi bi-newspaper text-2xl"></i>
                            <?php endif; ?>
                        </div>
                        <h2 class="text-lg font-bold line-clamp-2"><?= htmlspecialchars($news["title"]); ?></h2>
                    </div>
                    <!-- Media type badges -->
                    <div class="flex gap-2 mt-1">
                        <?php if (!empty($news["image"])): ?>
                            <span class="px-2 py-0.5 bg-white/20 text-white text-xs rounded-full"><i class="bi bi-image-fill mr-1"></i>Image</span>
                        <?php endif; ?>
                        <?php if (!empty($news["video"])): ?>
                            <span class="px-2 py-0.5 bg-white/20 text-white text-xs rounded-full"><i class="bi bi-play-circle-fill mr-1"></i>Video</span>
                        <?php endif; ?>
                        <?php if (empty($news["image"]) && empty($news["video"])): ?>
                            <span class="px-2 py-0.5 bg-white/20 text-white text-xs rounded-full"><i class="bi bi-file-text mr-1"></i>Text Only</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-6 flex flex-col flex-1">
                    <p class="text-sm text-gray-600 mb-4 flex-1 line-clamp-3">
                        <?= htmlspecialchars($content_preview); ?>
                    </p>

                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4 pb-4 border-b border-gray-200">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">
                                <i class="bi bi-calendar-event mr-1"></i>Expiration Date
                            </div>
                            <div class="text-sm font-semibold <?= $is_expired ? 'text-red-600' : 'text-gray-900' ?>">
                                <?= $expiration_date ? date("M d, Y", strtotime($expiration_date)) : 'No expiry' ?>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">
                                <i class="bi bi-calendar-plus mr-1"></i>Published
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                <?= date("M d, Y", strtotime($news["created_at"])); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="edit_news.php?id=<?= $news['id']; ?>" 
                           class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md">
                            <i class="bi bi-pencil-square mr-2"></i> Edit
                        </a>
                        <button onclick="confirmDelete(<?= $news['id']; ?>)" 
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition duration-200 shadow-sm hover:shadow-md">
                            <i class="bi bi-trash mr-2"></i> Delete
                        </button>
                    </div>
                </div>
            </article>
        <?php } ?>

        <?php if($count == 0): ?>
            <div class="col-span-full">
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                    <i class="bi bi-inbox text-gray-400 text-6xl mb-4 block"></i>
                    <h3 class="text-2xl font-bold text-gray-600 mb-2">No news articles yet</h3>
                    <p class="text-gray-500 mb-6">Start by creating your first news article</p>
                    <a href="add_news.php" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                        <i class="bi bi-plus-circle mr-2"></i> Add News
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Footer Info -->
    <?php if($count > 0): ?>
    <footer class="mt-8 text-center">
        <div class="inline-flex items-center px-6 py-3 bg-white/90 rounded-full shadow-lg">
            <i class="bi bi-newspaper text-blue-600 text-xl mr-2"></i>
            <span class="text-gray-700 font-semibold">Total News Articles: <span class="text-blue-600"><?= $count ?></span></span>
        </div>
    </footer>
    <?php endif; ?>
</main>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-fade-in">
        <header class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4">
            <h2 class="text-xl font-bold">
                <i class="bi bi-exclamation-triangle-fill mr-2"></i>Confirm Delete
            </h2>
        </header>
        <div class="p-6">
            <p class="text-gray-700 text-lg">Are you sure you want to delete this news article? This action cannot be undone.</p>
        </div>
        <footer class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-lg transition duration-200">
                Cancel
            </button>
            <a id="confirmDeleteBtn" href="#" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-200">
                <i class="bi bi-trash mr-1"></i>Delete
            </a>
        </footer>
    </div>
</div>

<script>
function confirmDelete(newsId) {
    document.getElementById('confirmDeleteBtn').href = 'update_news.php?delete=' + newsId;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});

// Close modal when clicking outside
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fadeIn 0.2s ease-out;
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

</body>
</html>
