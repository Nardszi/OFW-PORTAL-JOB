<?php
session_start();

// Simple authentication
if (!isset($_SESSION['user_id'])) {
    die("Please login first");
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Check Uploads Folder</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        h1 { color: #333; }
        .success { color: green; }
        .error { color: red; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>📁 Uploads Folder Check</h1>";

// Check if uploads folder exists
$uploads_dir = __DIR__ . '/uploads';
echo "<div class='info'>";
echo "<strong>Uploads Directory:</strong> " . $uploads_dir . "<br>";
echo "<strong>Exists:</strong> " . (file_exists($uploads_dir) ? "<span class='success'>✓ Yes</span>" : "<span class='error'>✗ No</span>") . "<br>";

if (file_exists($uploads_dir)) {
    echo "<strong>Writable:</strong> " . (is_writable($uploads_dir) ? "<span class='success'>✓ Yes</span>" : "<span class='error'>✗ No</span>") . "<br>";
    echo "<strong>Permissions:</strong> " . substr(sprintf('%o', fileperms($uploads_dir)), -4) . "<br>";
}
echo "</div>";

// List all files in uploads folder
if (file_exists($uploads_dir)) {
    echo "<h2>Files in Uploads Folder:</h2>";
    $files = scandir($uploads_dir);
    
    if (count($files) > 2) { // More than . and ..
        echo "<table>";
        echo "<tr><th>Filename</th><th>Size</th><th>Permissions</th><th>Test Link</th></tr>";
        
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'index.php') {
                $filepath = $uploads_dir . '/' . $file;
                $filesize = filesize($filepath);
                $perms = substr(sprintf('%o', fileperms($filepath)), -4);
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($file) . "</td>";
                echo "<td>" . number_format($filesize / 1024, 2) . " KB</td>";
                echo "<td>" . $perms . "</td>";
                echo "<td><a href='uploads/" . htmlspecialchars($file) . "' target='_blank'>Test Link</a></td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    } else {
        echo "<p class='error'>No files found in uploads folder!</p>";
    }
}

// Check database for stored file paths
echo "<h2>Files in Database (benefit_applications):</h2>";
include "config/database.php";

$query = "SELECT id, ofw_id, benefit_id, documents FROM benefit_applications WHERE documents IS NOT NULL AND documents != '' LIMIT 10";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Documents (from DB)</th><th>Files</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['documents']) . "</td>";
        echo "<td>";
        
        $files_arr = explode(',', $row['documents']);
        foreach ($files_arr as $file) {
            $file = trim($file);
            if (!empty($file)) {
                $file_exists = file_exists($uploads_dir . '/' . $file);
                $color = $file_exists ? 'success' : 'error';
                $icon = $file_exists ? '✓' : '✗';
                echo "<span class='$color'>$icon " . htmlspecialchars($file) . "</span><br>";
            }
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No documents found in database</p>";
}

// Check job_applications too
echo "<h2>Files in Database (job_applications):</h2>";
$query2 = "SELECT id, ofw_id, job_id, documents FROM job_applications WHERE documents IS NOT NULL AND documents != '' LIMIT 10";
$result2 = $conn->query($query2);

if ($result2 && $result2->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Documents (from DB)</th><th>Files</th></tr>";
    
    while ($row = $result2->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['documents']) . "</td>";
        echo "<td>";
        
        $files_arr = explode(',', $row['documents']);
        foreach ($files_arr as $file) {
            $file = trim($file);
            if (!empty($file)) {
                $file_exists = file_exists($uploads_dir . '/' . $file);
                $color = $file_exists ? 'success' : 'error';
                $icon = $file_exists ? '✓' : '✗';
                echo "<span class='$color'>$icon " . htmlspecialchars($file) . "</span><br>";
            }
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No documents found in database</p>";
}

echo "<div class='info' style='margin-top: 30px;'>
    <h3>🔧 Troubleshooting Steps:</h3>
    <ol>
        <li>Make sure <code>uploads/</code> folder exists on your server</li>
        <li>Set folder permissions to <strong>755</strong></li>
        <li>Set file permissions to <strong>644</strong></li>
        <li>Upload files are actually in the uploads folder</li>
        <li>File names in database match actual file names (case-sensitive)</li>
        <li>Test direct access: <code>https://yourdomain.com/uploads/filename.pdf</code></li>
    </ol>
    <p><strong>Delete this file (check_uploads.php) after checking!</strong></p>
</div>";

echo "</div></body></html>";
?>
