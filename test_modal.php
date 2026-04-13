<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}

// Get a sample file from uploads folder
$uploads_dir = __DIR__ . '/uploads';
$sample_files = [];

if (file_exists($uploads_dir)) {
    $files = scandir($uploads_dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'index.php') {
            $filepath = $uploads_dir . '/' . $file;
            if (is_file($filepath)) {
                $sample_files[] = [
                    'file' => $file,
                    'label' => 'Test Document - ' . $file
                ];
                if (count($sample_files) >= 3) break; // Only get 3 files for testing
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Modal - OFW Management System</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">
            <i class="bi bi-bug text-red-600 mr-2"></i>Modal Test Page
        </h1>
        
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <p class="text-blue-800">
                <strong>Purpose:</strong> This page tests if the document modal is working correctly.
                Open browser console (F12) to see debug information.
            </p>
        </div>

        <?php if (empty($sample_files)): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                <p class="text-yellow-800">
                    <strong>⚠ No files found!</strong> Upload some documents first to test the modal.
                </p>
            </div>
        <?php else: ?>
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-3">Sample Files Found:</h2>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    <?php foreach ($sample_files as $file): ?>
                        <li><?= htmlspecialchars($file['file']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-3">Test Button:</h2>
                <?php $files_json = htmlspecialchars(json_encode($sample_files), ENT_QUOTES, 'UTF-8'); ?>
                <button class="inline-flex items-center px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white font-semibold rounded-lg transition duration-200" 
                        data-files="<?= $files_json ?>" 
                        onclick="viewDocuments(this)">
                    <i class="bi bi-folder2-open mr-2"></i> Open Modal with Test Files
                </button>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold mb-2">What to check:</h3>
                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                    <li>Click the button above to open the modal</li>
                    <li>Open browser console (F12 → Console tab)</li>
                    <li>Click "View" or "Download" on any file</li>
                    <li>Check console for the file path being accessed</li>
                    <li>If you get 404, note the exact URL shown in console</li>
                </ol>
            </div>
        <?php endif; ?>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="font-semibold mb-3">Quick Links:</h3>
            <div class="flex gap-3">
                <a href="diagnose_and_fix.php" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="bi bi-tools mr-1"></i> Diagnostic Tool
                </a>
                <a href="check_uploads.php" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    <i class="bi bi-folder-check mr-1"></i> Check Uploads
                </a>
                <a href="view_benefit_applications.php" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                    <i class="bi bi-heart-pulse mr-1"></i> Benefits Applications
                </a>
                <a href="manage_applications.php" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                    <i class="bi bi-briefcase mr-1"></i> Job Applications
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Documents Modal (Same as in actual pages) -->
<div id="documentsModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden">
        <header class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold">
                <i class="bi bi-file-earmark-text-fill mr-2"></i>Test Documents Modal
            </h2>
            <button onclick="closeDocumentsModal()" class="text-white hover:text-gray-200 text-2xl">&times;</button>
        </header>
        <div class="p-6 max-h-96 overflow-y-auto">
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4 text-sm">
                <strong>Debug Mode:</strong> Check browser console for file path information
            </div>
            <ul class="space-y-2" id="fileList">
                <!-- Files will be loaded here via JS -->
            </ul>
        </div>
        <footer class="bg-gray-50 px-6 py-4 flex justify-end">
            <button onclick="closeDocumentsModal()" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">Close</button>
        </footer>
    </div>
</div>

<script>
console.log('=== MODAL TEST PAGE LOADED ===');
console.log('Current URL:', window.location.href);
console.log('Base URL:', window.location.origin);

function viewDocuments(btn) {
    console.log('=== VIEW DOCUMENTS CALLED ===');
    
    const filesJson = btn.getAttribute('data-files');
    console.log('Raw JSON:', filesJson);
    
    const files = JSON.parse(filesJson);
    console.log('Parsed files:', files);
    
    const list = document.getElementById('fileList');
    list.innerHTML = '';

    if (files.length === 0) {
        list.innerHTML = '<li class="text-center text-gray-500 py-4">No documents found</li>';
        document.getElementById('documentsModal').classList.remove('hidden');
        return;
    }

    files.forEach((file, index) => {
        console.log(`\n--- Processing file ${index + 1} ---`);
        console.log('Original file object:', file);
        
        const li = document.createElement('li');
        li.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-gray-200';
        
        // Ensure proper file path - remove any leading slashes or 'uploads/' if already present
        let fileName = file.file.trim();
        console.log('Step 1 - Original fileName:', fileName);
        
        fileName = fileName.replace(/^\/+/, ''); // Remove leading slashes
        console.log('Step 2 - After removing leading slashes:', fileName);
        
        fileName = fileName.replace(/^uploads\//, ''); // Remove 'uploads/' if present
        console.log('Step 3 - After removing uploads/ prefix:', fileName);
        
        // Construct the full path
        const filePath = 'uploads/' + fileName;
        console.log('Step 4 - Final filePath:', filePath);
        
        const fullUrl = window.location.origin + '/' + filePath;
        console.log('Step 5 - Full URL:', fullUrl);
        
        li.innerHTML = `
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <i class="bi bi-file-earmark-check text-green-600 text-2xl"></i>
                <div>
                    <div class="text-sm font-semibold text-gray-700">${file.label}</div>
                    <div class="text-xs text-gray-500 font-mono">${filePath}</div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="${filePath}" 
                   class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   onclick="console.log('🔍 VIEW clicked:', '${filePath}'); console.log('Full URL:', '${fullUrl}');">
                    <i class="bi bi-eye mr-1"></i> View
                </a>
                <a href="${filePath}" 
                   class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition" 
                   download="${fileName}" 
                   onclick="console.log('⬇️ DOWNLOAD clicked:', '${filePath}'); console.log('Download filename:', '${fileName}');">
                    <i class="bi bi-download mr-1"></i> Download
                </a>
            </div>
        `;
        list.appendChild(li);
    });

    console.log('=== OPENING MODAL ===');
    document.getElementById('documentsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDocumentsModal() {
    console.log('=== CLOSING MODAL ===');
    document.getElementById('documentsModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDocumentsModal();
    }
});

// Log any network errors
window.addEventListener('error', function(e) {
    if (e.target.tagName === 'IMG' || e.target.tagName === 'A') {
        console.error('❌ Resource failed to load:', e.target.href || e.target.src);
    }
}, true);
</script>

</body>
</html>
