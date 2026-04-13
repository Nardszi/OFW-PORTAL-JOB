<?php
session_start();

// Simple authentication
if (!isset($_SESSION['user_id'])) {
    die("Please login first");
}

$step = isset($_GET['step']) ? $_GET['step'] : 1;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnose & Fix Upload Issues</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2563eb; margin-bottom: 10px; }
        .step { background: #e0f2fe; padding: 20px; margin: 20px 0; border-left: 4px solid #2563eb; border-radius: 5px; }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; font-weight: 600; }
        .btn { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; font-weight: 600; }
        .btn:hover { background: #1d4ed8; }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        .btn-danger { background: #ef4444; }
        .btn-danger:hover { background: #dc2626; }
        .info-box { background: #fef3c7; padding: 15px; border-left: 4px solid #f59e0b; border-radius: 5px; margin: 15px 0; }
        .code { background: #1f2937; color: #10b981; padding: 15px; border-radius: 5px; font-family: monospace; margin: 10px 0; overflow-x: auto; }
        .test-link { color: #2563eb; text-decoration: underline; }
        .test-link:hover { color: #1d4ed8; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Upload Issues Diagnostic & Fix Tool</h1>
        <p style="color: #6b7280;">This tool will help diagnose and fix 404 errors when viewing/downloading documents</p>

<?php

$uploads_dir = __DIR__ . '/uploads';
include "config/database.php";

// STEP 1: Check folder and files
if ($step == 1) {
    echo "<div class='step'>";
    echo "<h2>Step 1: Check Uploads Folder</h2>";
    
    // Check folder exists
    if (!file_exists($uploads_dir)) {
        echo "<p class='error'>✗ Uploads folder does NOT exist!</p>";
        echo "<p>Creating uploads folder...</p>";
        if (mkdir($uploads_dir, 0755, true)) {
            echo "<p class='success'>✓ Uploads folder created successfully</p>";
        } else {
            echo "<p class='error'>✗ Failed to create uploads folder. Please create it manually.</p>";
        }
    } else {
        echo "<p class='success'>✓ Uploads folder exists</p>";
    }
    
    // Check folder permissions
    $folder_perms = substr(sprintf('%o', fileperms($uploads_dir)), -4);
    echo "<p><strong>Folder Permissions:</strong> $folder_perms ";
    if ($folder_perms == '0755' || $folder_perms == '0777') {
        echo "<span class='success'>✓ OK</span></p>";
    } else {
        echo "<span class='warning'>⚠ Should be 0755</span></p>";
    }
    
    // Check if writable
    echo "<p><strong>Writable:</strong> ";
    if (is_writable($uploads_dir)) {
        echo "<span class='success'>✓ Yes</span></p>";
    } else {
        echo "<span class='error'>✗ No - This will prevent file uploads!</span></p>";
    }
    
    // List files
    echo "<h3>Files in Uploads Folder:</h3>";
    $files = scandir($uploads_dir);
    $file_count = 0;
    
    echo "<table>";
    echo "<tr><th>Filename</th><th>Size</th><th>Permissions</th><th>Status</th></tr>";
    
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'index.php') {
            $filepath = $uploads_dir . '/' . $file;
            if (is_file($filepath)) {
                $file_count++;
                $filesize = filesize($filepath);
                $perms = substr(sprintf('%o', fileperms($filepath)), -4);
                
                $status = '';
                if ($perms == '0644' || $perms == '0666') {
                    $status = "<span class='success'>✓ OK</span>";
                } else {
                    $status = "<span class='warning'>⚠ Should be 0644</span>";
                }
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($file) . "</td>";
                echo "<td>" . number_format($filesize / 1024, 2) . " KB</td>";
                echo "<td>$perms</td>";
                echo "<td>$status</td>";
                echo "</tr>";
            }
        }
    }
    echo "</table>";
    
    if ($file_count == 0) {
        echo "<p class='warning'>⚠ No files found in uploads folder</p>";
    } else {
        echo "<p>Total files: <strong>$file_count</strong></p>";
    }
    
    echo "<a href='?step=2' class='btn'>Next: Check Database →</a>";
    echo "</div>";
}

// STEP 2: Check database
if ($step == 2) {
    echo "<div class='step'>";
    echo "<h2>Step 2: Check Database Records</h2>";
    
    // Check benefit_applications
    echo "<h3>Benefit Applications:</h3>";
    $query = "SELECT id, user_id, benefit_id, documents FROM benefit_applications WHERE documents IS NOT NULL AND documents != '' LIMIT 10";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Documents Path</th><th>Files Exist?</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['documents']) . "</td>";
            echo "<td>";
            
            $files_arr = explode(',', $row['documents']);
            foreach ($files_arr as $file) {
                $file = trim($file);
                if (!empty($file)) {
                    // Try different path variations
                    $file_clean = str_replace('uploads/', '', $file);
                    $file_clean = ltrim($file_clean, '/');
                    
                    $test_path = $uploads_dir . '/' . $file_clean;
                    $file_exists = file_exists($test_path);
                    
                    if ($file_exists) {
                        echo "<span class='success'>✓ " . htmlspecialchars($file_clean) . "</span><br>";
                    } else {
                        echo "<span class='error'>✗ " . htmlspecialchars($file_clean) . " (NOT FOUND)</span><br>";
                    }
                }
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>No benefit applications with documents found</p>";
    }
    
    // Check job_applications
    echo "<h3>Job Applications:</h3>";
    $query2 = "SELECT id, ofw_id, job_id, documents FROM job_applications WHERE documents IS NOT NULL AND documents != '' LIMIT 10";
    $result2 = $conn->query($query2);
    
    if ($result2 && $result2->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Documents Path</th><th>Files Exist?</th></tr>";
        
        while ($row = $result2->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['documents']) . "</td>";
            echo "<td>";
            
            $files_arr = explode(',', $row['documents']);
            foreach ($files_arr as $file) {
                $file = trim($file);
                if (!empty($file)) {
                    $file_clean = str_replace('uploads/', '', $file);
                    $file_clean = ltrim($file_clean, '/');
                    
                    $test_path = $uploads_dir . '/' . $file_clean;
                    $file_exists = file_exists($test_path);
                    
                    if ($file_exists) {
                        echo "<span class='success'>✓ " . htmlspecialchars($file_clean) . "</span><br>";
                    } else {
                        echo "<span class='error'>✗ " . htmlspecialchars($file_clean) . " (NOT FOUND)</span><br>";
                    }
                }
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>No job applications with documents found</p>";
    }
    
    echo "<a href='?step=1' class='btn'>← Previous</a>";
    echo "<a href='?step=3' class='btn'>Next: Test Direct Access →</a>";
    echo "</div>";
}

// STEP 3: Test direct file access
if ($step == 3) {
    echo "<div class='step'>";
    echo "<h2>Step 3: Test Direct File Access</h2>";
    echo "<p>Click the links below to test if files can be accessed directly:</p>";
    
    $files = scandir($uploads_dir);
    $test_count = 0;
    
    echo "<table>";
    echo "<tr><th>Filename</th><th>Test Link</th><th>Full URL</th></tr>";
    
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'index.php') {
            $filepath = $uploads_dir . '/' . $file;
            if (is_file($filepath) && $test_count < 10) {
                $test_count++;
                $url = 'uploads/' . urlencode($file);
                $full_url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $url;
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($file) . "</td>";
                echo "<td><a href='$url' target='_blank' class='test-link'>Test Link</a></td>";
                echo "<td style='font-size: 11px; color: #6b7280;'>" . htmlspecialchars($full_url) . "</td>";
                echo "</tr>";
            }
        }
    }
    echo "</table>";
    
    if ($test_count == 0) {
        echo "<p class='warning'>No files available to test</p>";
    }
    
    echo "<div class='info-box'>";
    echo "<strong>📝 Instructions:</strong><br>";
    echo "1. Click on 'Test Link' for each file<br>";
    echo "2. If the file opens/downloads → File access is working ✓<br>";
    echo "3. If you get 404 error → There's a server configuration issue<br>";
    echo "4. If you get 403 error → Permission issue<br>";
    echo "</div>";
    
    echo "<a href='?step=2' class='btn'>← Previous</a>";
    echo "<a href='?step=4' class='btn'>Next: Fix Permissions →</a>";
    echo "</div>";
}

// STEP 4: Fix permissions
if ($step == 4) {
    echo "<div class='step'>";
    echo "<h2>Step 4: Fix File Permissions</h2>";
    
    if (isset($_POST['fix_permissions'])) {
        echo "<h3>Fixing Permissions...</h3>";
        
        $fixed = 0;
        $errors = 0;
        
        // Fix folder permission
        if (@chmod($uploads_dir, 0755)) {
            echo "<p class='success'>✓ Fixed uploads folder permission to 0755</p>";
        } else {
            echo "<p class='warning'>⚠ Could not change folder permission (may not be allowed on this server)</p>";
        }
        
        // Fix file permissions
        $files = scandir($uploads_dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filepath = $uploads_dir . '/' . $file;
                if (is_file($filepath)) {
                    if (@chmod($filepath, 0644)) {
                        $fixed++;
                        echo "<p class='success'>✓ Fixed: $file (now 0644)</p>";
                    } else {
                        $errors++;
                        echo "<p class='warning'>⚠ Could not fix: $file</p>";
                    }
                }
            }
        }
        
        echo "<div class='info-box'>";
        echo "<strong>Summary:</strong><br>";
        echo "Fixed: $fixed files<br>";
        if ($errors > 0) {
            echo "Could not fix: $errors files<br>";
            echo "<br><strong>Note:</strong> Some hosting providers (like InfinityFree) don't allow changing permissions via PHP. This is normal.";
        }
        echo "</div>";
        
        echo "<a href='?step=5' class='btn btn-success'>Next: Final Check →</a>";
    } else {
        echo "<p>This will attempt to fix file permissions:</p>";
        echo "<ul>";
        echo "<li>Folder: 0755 (rwxr-xr-x)</li>";
        echo "<li>Files: 0644 (rw-r--r--)</li>";
        echo "</ul>";
        
        echo "<div class='info-box'>";
        echo "<strong>⚠ Note:</strong> On some hosting providers (like InfinityFree), you cannot change permissions via PHP. ";
        echo "If this fails, you'll need to use FTP/File Manager to change permissions manually.";
        echo "</div>";
        
        echo "<form method='POST'>";
        echo "<button type='submit' name='fix_permissions' class='btn btn-success'>Fix Permissions Now</button>";
        echo "</form>";
        
        echo "<a href='?step=3' class='btn'>← Previous</a>";
        echo "<a href='?step=5' class='btn'>Skip to Final Check →</a>";
    }
    
    echo "</div>";
}

// STEP 5: Final check and recommendations
if ($step == 5) {
    echo "<div class='step'>";
    echo "<h2>Step 5: Final Check & Recommendations</h2>";
    
    $issues = [];
    $all_good = true;
    
    // Check 1: Folder exists
    if (!file_exists($uploads_dir)) {
        $issues[] = "❌ Uploads folder does not exist";
        $all_good = false;
    }
    
    // Check 2: Folder writable
    if (!is_writable($uploads_dir)) {
        $issues[] = "❌ Uploads folder is not writable";
        $all_good = false;
    }
    
    // Check 3: .htaccess exists
    if (!file_exists($uploads_dir . '/.htaccess')) {
        $issues[] = "⚠ .htaccess file missing in uploads folder";
        $all_good = false;
    }
    
    // Check 4: Files exist
    $files = scandir($uploads_dir);
    $file_count = count($files) - 2; // Exclude . and ..
    if ($file_count == 0) {
        $issues[] = "⚠ No files in uploads folder (upload some documents first)";
    }
    
    if ($all_good && $file_count > 0) {
        echo "<div style='background: #d1fae5; padding: 20px; border-left: 4px solid #10b981; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3 style='color: #10b981; margin-top: 0;'>✓ All Checks Passed!</h3>";
        echo "<p>Your uploads folder appears to be configured correctly.</p>";
        echo "</div>";
    } else {
        echo "<h3>Issues Found:</h3>";
        echo "<ul>";
        foreach ($issues as $issue) {
            echo "<li>$issue</li>";
        }
        echo "</ul>";
    }
    
    echo "<h3>📋 Troubleshooting Guide:</h3>";
    echo "<div class='info-box'>";
    echo "<strong>If you're still getting 404 errors:</strong><br><br>";
    echo "<strong>1. Check .htaccess in root folder:</strong><br>";
    echo "Make sure it's not blocking access to uploads folder<br><br>";
    echo "<strong>2. Check .htaccess in uploads folder:</strong><br>";
    echo "Should allow file access but prevent PHP execution<br><br>";
    echo "<strong>3. Test direct URL:</strong><br>";
    echo "Try accessing: <code>http://yourdomain.com/uploads/filename.pdf</code><br><br>";
    echo "<strong>4. Check browser console:</strong><br>";
    echo "Open browser DevTools (F12) → Console tab → Look for actual URL being accessed<br><br>";
    echo "<strong>5. InfinityFree specific:</strong><br>";
    echo "- Files must be in <code>htdocs/uploads/</code><br>";
    echo "- Check File Manager to verify files are actually uploaded<br>";
    echo "- Some file types may be blocked by hosting provider<br>";
    echo "</div>";
    
    echo "<h3>🔍 Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Go to your application page (Benefits or Jobs)</li>";
    echo "<li>Click 'View Files' button on any application</li>";
    echo "<li>Open browser console (F12 → Console tab)</li>";
    echo "<li>Click 'View' or 'Download' button</li>";
    echo "<li>Check console for the actual URL being accessed</li>";
    echo "<li>Compare with the file paths shown in Step 2</li>";
    echo "</ol>";
    
    echo "<div style='margin-top: 30px; padding: 20px; background: #fef3c7; border-radius: 5px;'>";
    echo "<strong>⚠ IMPORTANT:</strong> After fixing the issue, delete these diagnostic files for security:<br>";
    echo "<code>check_uploads.php</code>, <code>fix_permissions.php</code>, <code>diagnose_and_fix.php</code>";
    echo "</div>";
    
    echo "<a href='?step=1' class='btn'>← Start Over</a>";
    echo "<a href='view_benefit_applications.php' class='btn btn-success'>Go to Benefits Applications</a>";
    echo "<a href='manage_applications.php' class='btn btn-success'>Go to Job Applications</a>";
    echo "</div>";
}

?>

    </div>
</body>
</html>
