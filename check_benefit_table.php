<?php
include "config/database.php";

// Check benefit_applications table structure
echo "Checking benefit_applications table structure:\n\n";
$result = $conn->query("DESCRIBE benefit_applications");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "Column: " . $row['Field'] . " | Type: " . $row['Type'] . " | Null: " . $row['Null'] . "\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

// Check if the columns used in INSERT exist
echo "\n\nChecking if required columns exist:\n";
$required_columns = ['user_id', 'benefit_id', 'application_type', 'documents', 'status', 'applied_at'];
foreach ($required_columns as $col) {
    $check = $conn->query("SHOW COLUMNS FROM benefit_applications LIKE '$col'");
    if ($check && $check->num_rows > 0) {
        echo "✓ Column '$col' exists\n";
    } else {
        echo "✗ Column '$col' MISSING\n";
    }
}

$conn->close();
?>