<?php
session_start();

if (isset($_SESSION['user_id']) && isset($_GET['job_id'])) {
    $job_id = (int)$_GET['job_id'];

    if (!isset($_SESSION['viewed_jobs'])) {
        $_SESSION['viewed_jobs'] = [];
    }

    // Add job to the beginning of the array
array_unshift($_SESSION['viewed_jobs'], $job_id);

    // Remove duplicates
    $_SESSION['viewed_jobs'] = array_unique($_SESSION['viewed_jobs']);

    // Keep only the last 5 viewed jobs
    $_SESSION['viewed_jobs'] = array_slice($_SESSION['viewed_jobs'], 0, 5);
}

?>