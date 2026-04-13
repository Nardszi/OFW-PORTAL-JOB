<?php
/**
 * Upload Test Script
 * Use this to test if file uploads are working correctly
 * Delete this file after testing for security
 */

session_start();

// Simple password protection
$test_password = "test123";
$is_authenticated = isset($_SESSION['test_auth']) && $_SESSION['test_auth'] === true;

if (isset($_POST['password'])) {
    if ($_POST['password'] === $test_password) {
        $_SESSION['test_auth'] = true;
        $is_authenticated = true;
    } else {
        $error = "Wrong password!";
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['test_auth']);
    header("Location: test_upload.php");
    exit();
}

$message = "";
$upload_info = [];

// Check uploads folder
$uploads_dir = "uploads/";
$uploads_exists = file_exists($uploads_dir);
$uploads_writable = is_writable($uploads_dir);

// Get folder permissions
if ($uploads_exists) {
    $perms = fileperms($uploads_dir);
    $upload_info['permissions'] = substr(sprintf('%o', $perms), -4);
}

// Handle file upload
if ($is_authenticated && isset($_POST['upload']) && isset($_FILES['test_file'])) {
    if (!$uploads_exists) {
        mkdir($uploads_dir, 0755, true);
    }
    
    $file = $_FILES['test_file'];
    $filename = time() . "_" . basename($file['name']);
    $target_file = $uploads_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        chmod($target_file, 0644);
        $message = "<div class='success'>✓ File uploaded successfully: <a href='$target_file' target='_blank'>$filename</a></div>";
    } else {
        $message = "<div class='error'>✗ Failed to upload file. Check permissions.</div>";
    }
}

// Get list of uploaded files
$uploaded_files = [];
if ($uploads_exists) {
    $files = glob($uploads_dir . "*");
    foreach ($files as $file) {
        if (is_file($file) && !in_array(basename($file), ['.htaccess', 'index.php'])) {
            $uploaded_files[] = [
                'name' => basename($file),
                'size' => filesize($file),
                'date' => date("Y-m-d H:i:s", filemtime($file)),
                'permissions' => substr(sprintf('%o', fileperms($file)), -4)
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Test - OFW Management System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #856404;
        }
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #721c24;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-box h3 {
            margin-bottom: 10px;
            color: #004085;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .info-item:last-child { border-bottom: none; }
        .status-ok { color: #28a745; font-weight: bold; }
        .status-error { color: #dc3545; font-weight: bold; }
        .form-group {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        input[type="file"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        .logout-btn {
            background: #dc3545;
            padding: 8px 20px;
            font-size: 14px;
            float: right;
        }
        a { color: #667eea; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$is_authenticated): ?>
            <h1>🔒 Upload Test - Authentication Required</h1>
            <p style="color: #666; margin: 10px 0 20px;">Enter password to access upload test</p>
            
            <?php if (isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required autofocus>
                    <p style="color: #999; font-size: 12px; margin-top: 5px;">Default password: test123</p>
                </div>
                <button type="submit">Login</button>
            </form>
        <?php else: ?>
            <h1>📤 Upload Test Tool</h1>
            <a href="?logout" class="logout-btn">Logout</a>
            <p style="color: #666; margin: 10px 0 20px; clear: both;">Test file uploads and check folder permissions</p>
            
            <div class="warning">
                <strong>⚠️ Security Warning:</strong> Delete this file (test_upload.php) after testing!
            </div>
            
            <?= $message ?>
            
            <!-- System Information -->
            <div class="info-box">
                <h3>📊 System Information</h3>
                <div class="info-item">
                    <span>Uploads Folder Exists:</span>
                    <span class="<?= $uploads_exists ? 'status-ok' : 'status-error' ?>">
                        <?= $uploads_exists ? '✓ Yes' : '✗ No' ?>
                    </span>
                </div>
                <div class="info-item">
                    <span>Folder Writable:</span>
                    <span class="<?= $uploads_writable ? 'status-ok' : 'status-error' ?>">
                        <?= $uploads_writable ? '✓ Yes' : '✗ No' ?>
                    </span>
                </div>
                <?php if (isset($upload_info['permissions'])): ?>
                <div class="info-item">
                    <span>Folder Permissions:</span>
                    <span><?= $upload_info['permissions'] ?> (Recommended: 0755)</span>
                </div>
                <?php endif; ?>
                <div class="info-item">
                    <span>PHP Upload Max Size:</span>
                    <span><?= ini_get('upload_max_filesize') ?></span>
                </div>
                <div class="info-item">
                    <span>PHP Post Max Size:</span>
                    <span><?= ini_get('post_max_size') ?></span>
                </div>
                <div class="info-item">
                    <span>PHP Max Execution Time:</span>
                    <span><?= ini_get('max_execution_time') ?>s</span>
                </div>
            </div>
            
            <!-- Upload Form -->
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Select Image to Upload:</label>
                    <input type="file" name="test_file" accept="image/*" required>
                </div>
                <button type="submit" name="upload">Upload Test File</button>
            </form>
            
            <!-- Uploaded Files List -->
            <?php if (!empty($uploaded_files)): ?>
            <div class="info-box">
                <h3>📁 Uploaded Files (<?= count($uploaded_files) ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Date</th>
                            <th>Permissions</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($uploaded_files as $file): ?>
                        <tr>
                            <td><?= htmlspecialchars($file['name']) ?></td>
                            <td><?= number_format($file['size'] / 1024, 2) ?> KB</td>
                            <td><?= $file['date'] ?></td>
                            <td><?= $file['permissions'] ?></td>
                            <td><a href="uploads/<?= htmlspecialchars($file['name']) ?>" target="_blank">View</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <!-- Instructions -->
            <div class="info-box" style="margin-top: 30px;">
                <h3>📝 Quick Fix Instructions</h3>
                <p style="margin: 10px 0;"><strong>If uploads are not working:</strong></p>
                <ol style="margin-left: 20px; line-height: 1.8;">
                    <li>Create <code>uploads/</code> folder if it doesn't exist</li>
                    <li>Set folder permissions to <strong>755</strong></li>
                    <li>Set file permissions to <strong>644</strong></li>
                    <li>Ensure .htaccess file exists in uploads folder</li>
                    <li>Check PHP upload settings in php.ini</li>
                </ol>
                <p style="margin-top: 15px; color: #dc3545;"><strong>Remember:</strong> Delete this test file after verification!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
