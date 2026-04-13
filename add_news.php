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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_news"])) {
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $content = mysqli_real_escape_string($conn, $_POST["content"]);
    $expiration_date = !empty($_POST["expiration_date"]) ? $_POST["expiration_date"] : NULL;
    $created_by = $_SESSION["user_id"];
    $image = "";
    $video = "";
    $media_type = "image";

    // Handle Image Upload
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image);
        $media_type = "image";
    }

    // Handle Video Upload
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
            
            if (move_uploaded_file($video_file["tmp_name"], $target_dir . $video)) {
                // If both image and video are uploaded, set media_type to 'both'
                if (!empty($image)) {
                    $media_type = "both";
                } else {
                    $media_type = "video";
                }
            } else {
                $error = "Failed to upload video file.";
            }
        }
    }

    if (!isset($error)) {
        $query = "INSERT INTO news (title, content, image, video, media_type, created_by, expiration_date, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssss", $title, $content, $image, $video, $media_type, $created_by, $expiration_date);

        if ($stmt->execute()) {
            // Include notification helper
            include "create_notification.php";
            
            // Create notification for all OFW users
            $notification_title = "New News: " . $title;
            $notification_message = "Check out the latest news and updates!";
            $notification_link = "news.php";
            
            notifyAllOFWs($conn, 'news', $notification_title, $notification_message, $notification_link);
            
            $_SESSION['message'] = "News added successfully!";
            header("Location: update_news.php");
            exit();
        } else {
            $error = "Failed to add news.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add News - OFW Management</title>
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
                    <h1 class="text-3xl font-bold text-white drop-shadow-lg">Add News</h1>
                    <p class="text-gray-200 text-sm mt-1">Create a new news article or announcement</p>
                </div>
            </div>
            <a href="update_news.php" class="inline-flex items-center px-4 py-2 bg-white/90 hover:bg-white text-gray-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                <i class="bi bi-arrow-left mr-2"></i>
                Back to News
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
                    <i class="bi bi-newspaper mr-3"></i>
                    News Information
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
                                  placeholder="Write the news content here..." required></textarea>
                    </div>

                    <!-- Expiration Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-calendar-x text-blue-600 mr-2"></i>
                            Expiration Date (Optional)
                        </label>
                        <input type="date" name="expiration_date" 
                               min="<?= date('Y-m-d') ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="bi bi-info-circle mr-1"></i>Leave empty if this news should never expire. News will be hidden after this date.
                        </p>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-image text-blue-600 mr-2"></i>
                            Upload Image (Optional)
                        </label>
                        <input type="file" name="image" id="imageInput" accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-2">You can upload both image and video for the same news.</p>
                    </div>

                    <!-- Video Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-camera-video text-blue-600 mr-2"></i>
                            Upload Video (Optional)
                        </label>
                        <input type="file" name="video" id="videoInput" accept="video/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-100">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="bi bi-info-circle mr-1"></i>Supported formats: MP4, WebM, AVI, MOV | Max size: 100MB
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center pt-4">
                        <button type="submit" name="add_news" 
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                            <i class="bi bi-plus-circle-fill mr-2"></i>
                            Add News
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

</body>
</html>
