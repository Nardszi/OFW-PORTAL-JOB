<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Please login first");
}

include "config/database.php";

echo "<!DOCTYPE html>
<html>
<head>
    <title>Debug File Paths</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        h1 { color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #f0f0f0; font-weight: bold; }
        .success { color: green; }
        .error { color: red; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Debug File Paths</h1>";

// Check benefit applications
echo "<h2>Benefit Applications (NOT WORKING):</h2>";
$query1 = "SELECT id, user_id, benefit_id, documents FROM benefit_applications WHERE documents IS NOT NULL AND documents != '' LIMIT 5";
$result1 = $conn->query($query1);

if ($result1 && $result1->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Raw Documents Value</th><th>File Exists?</th><th>Test URL</th></tr>";
    
    while ($row = $result1->fetch_assoc()) {
        $files = explode(',', $row['documents']);
        foreach ($files as $file) {
            $file = trim($file);
            if (!empty($file)) {
                $clean_file = str_replace('uploads/', '', $file);
                $clean_file = ltrim($clean_file, '/');
                
                $server_path = __DIR__ . '/uploads/' . $clean_file;
                $exists = file_exists($server_path);
                $test_url = 'https://' . $_SERVER['HTTP_HOST'] . '/uploads/' . urlencode($clean_file);
                
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td><code>" . htmlspecialchars($file) . "</code></td>";
                echo "<td>" . ($exists ? "<span class='success'>✓ YES</span>" : "<span class='error'>✗ NO</span>") . "</td>";
                echo "<td><a href='$test_url' target='_blank'>Test Link</a></td>";
                echo "</tr>";
            }
        }
    }
    echo "</table>";
} else {
    echo "<p>No benefit applications found</p>";
}

// Check job applications
echo "<h2>Job Applications (WORKING):</h2>";
$query2 = "SELECT id, ofw_id, job_id, documents FROM job_applications WHERE documents IS NOT NULL AND documents != '' LIMIT 5";
$result2 = $conn->query($query2);

if ($result2 && $result2->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Raw Documents Value</th><th>File Exists?</th><th>Test URL</th></tr>";
    
    while ($row = $result2->fetch_assoc()) {
        $files = explode(',', $row['documents']);
        foreach ($files as $file) {
            $file = trim($file);
            if (!empty($file)) {
                $clean_file = str_replace('uploads/', '', $file);
                $clean_file = ltrim($clean_file, '/');
                
                $server_path = __DIR__ . '/uploads/' . $clean_file;
                $exists = file_exists($server_path);
                $test_url = 'https://' . $_SERVER['HTTP_HOST'] . '/uploads/' . urlencode($clean_file);
                
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td><code>" . htmlspecialchars($file) . "</code></td>";
                echo "<td>" . ($exists ? "<span class='success'>✓ YES</span>" : "<span class='error'>✗ NO</span>") . "</td>";
                echo "<td><a href='$test_url' target='_blank'>Test Link</a></td>";
                echo "</tr>";
            }
        }
    }
    echo "</table>";
} else {
    echo "<p>No job applications found</p>";
}

echo "<h2>📋 Analysis:</h2>";
echo "<p>Compare the 'Raw Documents Value' between benefits and jobs. They should be stored the same way.</p>";
echo "<p>If benefits show files that don't exist, those files were never uploaded to the server.</p>";

echo "<div style='margin-top: 30px;'>
    <a href='view_benefit_applications.php' style='display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>Back to Benefits</a>
    <a href='manage_applications.php' style='display: inline-block; padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Back to Jobs</a>
</div>";

echo "</div></body></html>";
?>
