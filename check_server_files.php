<?php
session_start();

// Simple authentication
if (!isset($_SESSION['user_id'])) {
    die("Please login first");
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Check Server Files</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        h1 { color: #2563eb; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .test-link { color: #2563eb; text-decoration: underline; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Server File Checker</h1>";

// Get absolute paths
$script_path = __FILE__;
$script_dir = __DIR__;
$uploads_dir = $script_dir . '/uploads';

echo "<div class='info'>";
echo "<strong>Server Information:</strong><br>";
echo "<strong>Script Location:</strong> <code>$script_path</code><br>";
echo "<strong>Script Directory:</strong> <code>$script_dir</code><br>";
echo "<strong>Uploads Directory:</strong> <code>$uploads_dir</code><br>";
echo "<strong>Server Name:</strong> <code>" . $_SERVER['SERVER_NAME'] . "</code><br>";
echo "<strong>Document Root:</strong> <code>" . $_SERVER['DOCUMENT_ROOT'] . "</code><br>";
echo "</div>";

// Check if uploads folder exists
echo "<h2>Uploads Folder Check:</h2>";
if (file_exists($uploads_dir)) {
    echo "<p class='success'>✓ Uploads folder EXISTS</p>";
    
    if (is_dir($uploads_dir)) {
        echo "<p class='success'>✓ It is a DIRECTORY</p>";
    } else {
        echo "<p class='error'>✗ It is NOT a directory!</p>";
    }
    
    if (is_readable($uploads_dir)) {
        echo "<p class='success'>✓ Folder is READABLE</p>";
    } else {
        echo "<p class='error'>✗ Folder is NOT readable!</p>";
    }
    
    // List all files
    echo "<h2>Files in Uploads Folder:</h2>";
    $files = @scandir($uploads_dir);
    
    if ($files === false) {
        echo "<p class='error'>✗ Cannot read folder contents!</p>";
    } else {
        $file_count = 0;
        echo "<table>";
        echo "<tr><th>Filename</th><th>Size</th><th>Type</th><th>Readable?</th><th>Test URL</th></tr>";
        
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filepath = $uploads_dir . '/' . $file;
                $file_count++;
                
                $size = @filesize($filepath);
                $is_file = is_file($filepath);
                $is_readable = is_readable($filepath);
                
                $type = $is_file ? 'File' : (is_dir($filepath) ? 'Directory' : 'Unknown');
                $readable = $is_readable ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>";
                
                $test_url = 'https://' . $_SERVER['SERVER_NAME'] . '/uploads/' . urlencode($file);
                
                echo "<tr>";
                echo "<td><code>" . htmlspecialchars($file) . "</code></td>";
                echo "<td>" . ($size !== false ? number_format($size / 1024, 2) . " KB" : "N/A") . "</td>";
                echo "<td>$type</td>";
                echo "<td>$readable</td>";
                echo "<td><a href='$test_url' target='_blank' class='test-link'>Test</a></td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        
        if ($file_count == 0) {
            echo "<p class='warning'>⚠ No files found in uploads folder!</p>";
        } else {
            echo "<p>Total items: <strong>$file_count</strong></p>";
        }
    }
    
} else {
    echo "<p class='error'>✗ Uploads folder DOES NOT EXIST!</p>";
    echo "<p>Expected location: <code>$uploads_dir</code></p>";
    
    // Try to create it
    echo "<p>Attempting to create uploads folder...</p>";
    if (@mkdir($uploads_dir, 0755, true)) {
        echo "<p class='success'>✓ Uploads folder created successfully!</p>";
        echo "<p>Please upload your files to this folder and refresh this page.</p>";
    } else {
        echo "<p class='error'>✗ Failed to create uploads folder. You may need to create it manually via FTP/File Manager.</p>";
    }
}

// Check database for expected files
echo "<h2>Files Expected (from Database):</h2>";
include "config/database.php";

$query = "SELECT id, documents FROM benefit_applications WHERE documents IS NOT NULL AND documents != '' LIMIT 10";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>App ID</th><th>Filename in DB</th><th>File Exists?</th><th>Full Path</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $files_arr = explode(',', $row['documents']);
        foreach ($files_arr as $file) {
            $file = trim($file);
            if (!empty($file)) {
                // Clean filename
                $clean_file = str_replace('uploads/', '', $file);
                $clean_file = ltrim($clean_file, '/');
                
                $full_path = $uploads_dir . '/' . $clean_file;
                $exists = file_exists($full_path);
                
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td><code>" . htmlspecialchars($clean_file) . "</code></td>";
                echo "<td>" . ($exists ? "<span class='success'>✓ YES</span>" : "<span class='error'>✗ NO</span>") . "</td>";
                echo "<td style='font-size: 11px;'><code>" . htmlspecialchars($full_path) . "</code></td>";
                echo "</tr>";
            }
        }
    }
    echo "</table>";
} else {
    echo "<p>No benefit applications with documents found in database.</p>";
}

echo "<div class='info' style='margin-top: 30px;'>
    <h3>📋 What to do if files are missing:</h3>
    <ol>
        <li>Check your InfinityFree File Manager</li>
        <li>Navigate to <code>htdocs/uploads/</code></li>
        <li>Verify files are actually uploaded there</li>
        <li>Check if filenames match exactly (case-sensitive)</li>
        <li>If files are missing, re-upload documents through the application form</li>
    </ol>
    <p><strong>Note:</strong> On InfinityFree, files must be in <code>/htdocs/uploads/</code> folder.</p>
</div>";

echo "<div style='margin-top: 20px;'>
    <a href='diagnose_and_fix.php' style='display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>Run Full Diagnostic</a>
    <a href='view_benefit_applications.php' style='display: inline-block; padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Back to Applications</a>
</div>";

echo "</div></body></html>";
?>
