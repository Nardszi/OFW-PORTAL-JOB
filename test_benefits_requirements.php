<?php
// Test script to verify benefits requirements system
include "config/database.php";

echo "<h2>Testing Benefits Requirements System</h2>";

// 1. Check if requirements column exists
echo "<h3>1. Checking if 'requirements' column exists in benefits table...</h3>";
$check_column = $conn->query("SHOW COLUMNS FROM benefits LIKE 'requirements'");
if ($check_column && $check_column->num_rows > 0) {
    echo "✓ Column 'requirements' exists<br>";
} else {
    echo "✗ Column 'requirements' does NOT exist. Adding it now...<br>";
    $conn->query("ALTER TABLE benefits ADD COLUMN requirements TEXT DEFAULT NULL AFTER description");
    echo "✓ Column added successfully<br>";
}

// 2. Fetch all benefits and show their requirements
echo "<h3>2. Current Benefits and Their Requirements:</h3>";
$result = $conn->query("SELECT id, title, requirements FROM benefits");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<strong>Benefit ID {$row['id']}: {$row['title']}</strong><br>";
        
        if (empty($row['requirements'])) {
            echo "<em style='color: red;'>No requirements set</em><br>";
        } else {
            echo "Requirements (raw): <pre>" . htmlspecialchars($row['requirements']) . "</pre>";
            
            // Parse requirements like apply_benefit.php does
            $requirements_list = [];
            $raw_requirements = preg_split('/\r\n|\r|\n/', $row['requirements']);
            foreach ($raw_requirements as $req) {
                $req = trim($req);
                if (!empty($req)) {
                    $requirements_list[] = $req;
                }
            }
            
            echo "Parsed requirements (" . count($requirements_list) . " items):<br>";
            echo "<ol>";
            foreach ($requirements_list as $req) {
                echo "<li>" . htmlspecialchars($req) . "</li>";
            }
            echo "</ol>";
        }
        echo "</div>";
    }
} else {
    echo "<em>No benefits found in database</em><br>";
}

echo "<h3>3. Test Complete!</h3>";
echo "<p><a href='add_benefits.php'>Go to Add Benefits</a> | <a href='update_benefits.php'>Go to Manage Benefits</a></p>";
?>
