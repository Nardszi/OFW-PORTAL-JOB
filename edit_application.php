<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

if (!isset($_GET['id'])) {
    header("Location: manage_applications.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch application details
$stmt = $conn->prepare("SELECT a.*, j.requirements FROM job_applications a JOIN jobs j ON a.job_id = j.id WHERE a.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();
$stmt->close();

if (!$application) {
    echo "<script>alert('Application not found!'); window.location.href='manage_applications.php';</script>";
    exit();
}

// Parse requirements
$requirements_list = [];
if (!empty($application['requirements'])) {
    $requirements_list = array_filter(array_map('trim', explode("\n", $application['requirements'])));
} else {
    $requirements_list = ['Resume'];
}

// Map existing documents
$doc_map = [];
$current_files = explode(',', $application['documents']);
foreach ($current_files as $file) {
    $file = trim($file);
    if (empty($file)) continue;
    foreach ($requirements_list as $req_name) {
        $key = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $req_name));
        if (strpos($file, "_".$key."_") !== false) {
            $doc_map[$key] = $file;
            break;
        }
    }
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $target_dir = "uploads/";

    foreach ($requirements_list as $index => $req_name) {
        $key = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $req_name));
        $input_name = "req_" . $index;
        if (!empty($_FILES[$input_name]["name"])) {
            $filename = time() . "_" . $key . "_" . basename($_FILES[$input_name]["name"]);
            if (move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_dir . $filename)) {
                if (isset($doc_map[$key]) && file_exists($target_dir . $doc_map[$key])) {
                    unlink($target_dir . $doc_map[$key]);
                }
                $doc_map[$key] = $filename;
            }
        }
    }
    $documents = implode(",", array_values($doc_map));

    $update_stmt = $conn->prepare("UPDATE job_applications SET full_name=?, email=?, phone=?, documents=? WHERE id=?");
    $update_stmt->bind_param("ssssi", $full_name, $email, $phone, $documents, $id);
    
    if ($update_stmt->execute()) {
        echo "<script>alert('Application updated successfully!'); window.location.href='manage_applications.php';</script>";
    } else {
        echo "<script>alert('Error updating application.');</script>";
    }
    $update_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Application</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="alternate icon" href="images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .content { margin-left: 256px; padding: 20px; min-height: 100vh; }
        body { background: url('images/wall234.jpg') no-repeat center center fixed; background-size: cover; position: relative; }
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
        .card-custom { background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-3 position-relative">
        <h2 class="w-100 text-center m-3 text-white fw-bold">Edit Application</h2>
        <a href="manage_applications.php" class="btn btn-secondary btn-sm position-absolute end-0">
            Back to Job Applications
        </a>
    </div>

    <div class="d-flex justify-content-center">
        <div class="card shadow-sm" style="max-width: 600px; width: 100%;">
            <div class="card-custom">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($application['full_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($application['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($application['phone']) ?>" required>
                    </div>

                    <h5 class="mt-4 mb-3 text-primary fw-bold"><i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Update Requirements</h5>
                    <div class="card border-0 shadow-sm mb-4 bg-light">
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush rounded-3">
                                <?php 
                                foreach ($requirements_list as $index => $req_name) {
                                    $key = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $req_name));
                                    $label = ($index + 1) . ". " . $req_name;
                                    $has_file = isset($doc_map[$key]);
                                ?>
                                    <li class="list-group-item py-3 bg-transparent">
                                        <div class="mb-2">
                                            <label class="form-label fw-bold text-dark mb-0" for="req_<?= $index ?>">
                                                <?= $label ?>
                                            </label>
                                            <?php if ($has_file): ?>
                                                <div class="mt-1">
                                                    <span class="badge bg-success" style="font-size: 0.7em;">Uploaded</span>
                                                    <a href="uploads/<?= $doc_map[$key] ?>" target="_blank" class="small ms-1 text-decoration-none"><i class="bi bi-eye"></i> View Current</a>
                                                </div>
                                            <?php else: ?>
                                                <div class="mt-1">
                                                    <span class="badge bg-secondary" style="font-size: 0.7em;">Not Uploaded</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <input type="file" name="req_<?= $index ?>" id="req_<?= $index ?>" class="form-control form-control-sm" onchange="previewImage(this)">
                                            <img id="preview-req_<?= $index ?>" class="ms-3 rounded border bg-white" style="width: 50px; height: 50px; object-fit: cover; display: none;">
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Update Application</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    var preview = document.getElementById('preview-' + input.id);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        // Only preview if it is an image
        if (input.files[0].type.startsWith('image/')) {
             reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    } else {
        preview.style.display = 'none';
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
