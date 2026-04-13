<?php
session_start();

// Increase PHP limits for video uploads
@ini_set('upload_max_filesize', '100M');
@ini_set('post_max_size', '110M');
@ini_set('memory_limit', '256M');
@ini_set('max_execution_time', '300');
@ini_set('max_input_time', '300');

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

if (!isset($_GET["id"])) {
    header("Location: update_news.php");
    exit();
}

$id = intval($_GET["id"]);

// Fetch existing news
$query = "SELECT * FROM news WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$news = $result->fetch_assoc();
$stmt->close();

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_news"])) {
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $content = mysqli_real_escape_string($conn, $_POST["content"]);
    $expiration_date = !empty($_POST["expiration_date"]) ? $_POST["expiration_date"] : NULL;
    $image = $news['image']; // Keep existing image by default
    $video = isset($news['video']) ? $news['video'] : ""; // Keep existing video
    $media_type = isset($news['media_type']) ? $news['media_type'] : "image";

    // Handle New Image Upload
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image);
    }

    // Handle New Video Upload
    if (!empty($_FILES["video"]["name"])) {
        $target_dir = "uploads/";
        $video_file = $_FILES["video"];
        $file_size = $video_file["size"];
        $max_size = 100 * 1024 * 1024; // 100MB in bytes
        
        // Check file size
        if ($file_size > $max_size) {
            $error = "Video file size must be less than 100MB. Your file is " . round($file_size / (1024 * 1024), 2) . "MB.";
        } else {
            $video = time() . "_video_" . basename($video_file["name"]);
            
            if (!move_uploaded_file($video_file["tmp_name"], $target_dir . $video)) {
                $error = "Failed to upload video file.";
            }
        }
    }

    if (!isset($error)) {
        // Determine media type based on what's available
        if (!empty($image) && !empty($video)) {
            $media_type = "both";
        } elseif (!empty($video)) {
            $media_type = "video";
        } elseif (!empty($image)) {
            $media_type = "image";
        }

        $update_query = "UPDATE news SET title = ?, content = ?, image = ?, video = ?, media_type = ?, expiration_date = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssssssi", $title, $content, $image, $video, $media_type, $expiration_date, $id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "News updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update news.";
        }

        $stmt->close();
        header("Location: update_news.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit News - OFW Management</title>
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
                    <h1 class="text-3xl font-bold text-white drop-shadow-lg">Edit News</h1>
                    <p class="text-gray-200 text-sm mt-1">Update news article details</p>
                </div>
            </div>
            <a href="update_news.php" class="inline-flex items-center px-4 py-2 bg-white/90 hover:bg-white text-gray-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <i class="bi bi-arrow-left mr-2"></i>
                Back to News
            </a>
        </div>
    </header>

    <!-- Form Card -->
    <section class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="bi bi-pencil-square mr-3"></i>
                    Update News Information
                </h2>
            </div>

            <!-- Card Body -->
            <div class="p-8">
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-card-heading text-blue-600 mr-2"></i>
                            News Title
                        </label>
                        <input type="text" name="title" 
                               value="<?php echo htmlspecialchars($news['title']); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                               placeholder="Enter news title" required>
                    </div>

                    <!-- Content -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-file-text text-blue-600 mr-2"></i>
                            News Content
                        </label>
                        <textarea name="content" rows="6" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                                  placeholder="Write the news content here..." required><?= htmlspecialchars($news['content']); ?></textarea>
                    </div>

                    <!-- Expiration Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-calendar-x text-blue-600 mr-2"></i>
                            Expiration Date (Optional)
                        </label>
                        <input type="date" name="expiration_date" 
                               value="<?= !empty($news['expiration_date']) ? htmlspecialchars($news['expiration_date']) : '' ?>"
                               min="<?= date('Y-m-d') ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="bi bi-info-circle mr-1"></i>Leave empty if this news should never expire. News will be hidden after this date.
                            <?php if (!empty($news['expiration_date'])): ?>
                                <span class="text-orange-600 font-semibold">Current: <?= date('M d, Y', strtotime($news['expiration_date'])) ?></span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Current Media -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="bi bi-collection text-blue-600 mr-2"></i>
                            Current Media
                        </label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <?php if (!empty($news['image'])) { ?>
                                <div class="mb-4">
                                    <img src="uploads/<?= htmlspecialchars($news['image']); ?>" 
                                         class="w-48 h-32 object-cover rounded-lg shadow-md mb-2">
                                    <p class="text-xs text-gray-600">Current Image</p>
                                </div>
                            <?php } ?>
                            <?php if (isset($news['video']) && !empty($news['video'])) { ?>
                                <div class="mb-4">
                                    <video class="w-80 rounded-lg shadow-md mb-2" controls>
                                        <source src="uploads/<?= htmlspecialchars($news['video']); ?>" type="video/mp4">
                                    </video>
                                    <p class="text-xs text-gray-600">Current Video</p>
                                </div>
                            <?php } ?>
                            <?php if (empty($news['image']) && (empty($news['video']) || !isset($news['video']))) { ?>
                                <p class="text-sm text-gray-500 italic">No media uploaded</p>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- New Image Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-image text-blue-600 mr-2"></i>
                            Upload New Image (Optional)
                        </label>
                        <input type="file" name="image" id="imageInput" accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-2">Upload a new image or keep the existing one.</p>
                    </div>

                    <!-- New Video Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-camera-video text-blue-600 mr-2"></i>
                            Upload New Video (Optional)
                        </label>
                        <input type="file" name="video" id="videoInput" accept="video/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="bi bi-info-circle mr-1"></i>Upload a new video or keep the existing one | Max size: 100MB
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center pt-4">
                        <button type="submit" name="update_news" 
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                            <i class="bi bi-check-circle-fill mr-2"></i>
                            Update News
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

</body>
</html>
