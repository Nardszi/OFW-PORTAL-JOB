<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

// Filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

$where_sql = "";
$params = [];
$types = "";

if (!empty($search)) {
    $where_sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($date)) {
    $where_sql .= " AND DATE(l.created_at) = ?";
    $params[] = $date;
    $types .= "s";
}

// Fetch logs (No pagination for export)
$query = "SELECT u.name, u.email, u.role, l.action, l.ip_address, l.created_at 
          FROM activity_logs l 
          JOIN users u ON l.user_id = u.id 
          WHERE 1=1 $where_sql
          ORDER BY l.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="activity_logs_' . date('Y-m-d') . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write column headers
fputcsv($output, ['User Name', 'Email', 'Role', 'Action', 'IP Address', 'Date/Time']);

// Write rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['name'],
        $row['email'],
        ucfirst($row['role']),
        $row['action'],
        $row['ip_address'],
        date("M d, Y h:i A", strtotime($row['created_at']))
    ]);
}

fclose($output);
exit();
?>