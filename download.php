<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("HTTP/1.0 403 Forbidden");
    exit("Access Denied");
}

// Get the file parameter
if (!isset($_GET['file']) || empty($_GET['file'])) {
    header("HTTP/1.0 400 Bad Request");
    exit("No file specified");
}

$filename = basename($_GET['file']); // Prevent directory traversal
$filepath = "uploads/" . $filename;

// Check if file exists
if (!file_exists($filepath)) {
    header("HTTP/1.0 404 Not Found");
    exit("File not found: " . htmlspecialchars($filename));
}

// Check if it's actually a file
if (!is_file($filepath)) {
    header("HTTP/1.0 403 Forbidden");
    exit("Invalid file");
}

// Get file info
$filesize = filesize($filepath);
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimetype = finfo_file($finfo, $filepath);
finfo_close($finfo);

// Check if action is view or download
$action = isset($_GET['action']) && $_GET['action'] === 'view' ? 'inline' : 'attachment';

// Set headers
header('Content-Description: File Transfer');
header('Content-Type: ' . $mimetype);
header('Content-Disposition: ' . $action . '; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . $filesize);

// Clear output buffer
if (ob_get_level()) {
    ob_end_clean();
}
flush();

// Read and output file
readfile($filepath);
exit();
?>
