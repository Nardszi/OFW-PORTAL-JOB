<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "ofw") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

if (!isset($_GET['id'])) {
    header("Location: benefits.php");
    exit();
}

$app_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch application details
$stmt = $conn->prepare("SELECT ba.*, b.title, b.description, b.requirements FROM benefit_applications ba JOIN benefits b ON ba.benefit_id = b.id WHERE ba.id = ? AND ba.user_id = ?");
$stmt->bind_param("ii", $app_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();

if (!$application || $application['status'] != 'pending') {
    echo "<script>alert('Application not found or cannot be edited.'); window.location.href='benefits.php';</script>";
    exit();
}

// Parse requirements
$requirements_list = [];
if (!empty($application['requirements'])) {
    $requirements_list = array_filter(array_map('trim', explode("\n", $application['requirements'])));
} else {
    $requirements_list = ['Death Certificate', 'Burial Permit', 'Valid IDs', 'CENOMAR', 'Proof of Relationship', 'Passport', 'Police Report'];
}

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
    $application_type = mysqli_real_escape_string($conn, $_POST["application_type"]);
    $target_dir = "uploads/";

    foreach ($requirements_list as $index => $req_name) {
        $key = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $req_name));
        $input_name = "req_" . $index;
        if (!empty($_FILES[$input_name]["name"])) {
            $filename = time() . "_" . $key . "_" . basename($_FILES[$input_name]["name"]);
            if (move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_dir . $filename)) {
                // Delete old file if exists
                if (isset($doc_map[$key]) && file_exists($target_dir . $doc_map[$key])) {
                    unlink($target_dir . $doc_map[$key]);
                }
                // Update map
                $doc_map[$key] = $filename;
            }
        }
    }

    $new_documents = implode(",", array_values($doc_map));

    $update_stmt = $conn->prepare("UPDATE benefit_applications SET application_type = ?, documents = ? WHERE id = ?");
    $update_stmt->bind_param("ssi", $application_type, $new_documents, $app_id);
    
    if ($update_stmt->execute()) {
        echo "<script>alert('Application updated successfully!'); window.location.href='benefits.php';</script>";
    } else {
        echo "<script>alert('Error updating application.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Benefit Application</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="alternate icon" href="images/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
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
        .content { margin-left: 256px; padding: 20px; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-3 position-relative">
        <h2 class="w-100 text-center m-3 text-white fw-bold">Edit Benefit Application</h2>
        <a href="view_benefit_applications.php" class="btn btn-secondary btn-sm position-absolute end-0">
            Back to Benefit Applications
        </a>
    </div>
    
    <div class="d-flex justify-content-center">
        <div class="card shadow-lg border-0" style="max-width: 1000px; width: 100%;">
            <div class="card-body p-5">
                <h5 class="text-center text-primary"><?= htmlspecialchars($application['title']) ?></h5>
                
                <form method="POST" enctype="multipart/form-data">
                    
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white fw-bold">
                            <i class="bi bi-check-circle-fill me-2"></i>Assistance Type
                        </div>
                        <div class="card-body bg-light">
                            <div class="form-group">
                                <label class="form-label fw-bold">Type of Assistance:</label>
                                <select name="application_type" class="form-select form-select-lg" required>
                                    <option value="Death Assistance" <?= $application['application_type'] == 'Death Assistance' ? 'selected' : '' ?>>Death Assistance</option>
                                    <option value="Burial Assistance" <?= $application['application_type'] == 'Burial Assistance' ? 'selected' : '' ?>>Burial Assistance</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white fw-bold">
                            <i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Update Documents
                        </div>
                        <div class="card-body bg-light">
                            <div class="alert alert-info small">
                                <i class="bi bi-info-circle-fill me-2"></i> Upload a new file only if you wish to replace the existing one.
                            </div>
                            <div class="row g-3">
                                <?php 
                                foreach ($requirements_list as $index => $req_name) {
                                    $key = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $req_name));
                                    $label = ($index + 1) . ". " . $req_name;
                                    $has_file = isset($doc_map[$key]);
                                    $required = $has_file ? '' : 'required';
                                ?>
                                <div class="col-12">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body">
                                            <label class="form-label fw-bold small text-primary"><?= $label ?></label>
                                            
                                            <?php if ($has_file): ?>
                                                <div class="mb-2">
                                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Uploaded</span>
                                                    <a href="uploads/<?= $doc_map[$key] ?>" target="_blank" class="small ms-2">View Current</a>
                                                </div>
                                            <?php else: ?>
                                                <div class="mb-2"><span class="badge bg-secondary">Not Uploaded</span></div>
                                            <?php endif; ?>

                                            <input type="file" name="req_<?= $index ?>" class="form-control form-control-sm" <?= $required ?>>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="benefits.php" class="btn btn-secondary btn-lg px-4 me-2">Cancel</a>
                        <button type="submit" class="btn btn-success btn-lg px-5">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>