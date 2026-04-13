<?php
include "config/database.php";

echo "<h2>Testing Benefit Requirements System</h2>";

// Create a test benefit with specific requirements
$title = "TEST BENEFIT - " . date('H:i:s');
$description = "Test benefit with separated requirements";
$expiration_date = date('Y-m-d', strtotime('+1 year'));
$created_by = 4; // Admin user ID from database

// Simulate admin checking only these requirements
$selected_requirements = [
    "Death Certificate",
    "Burial Permit",
    "Valid IDs",
    "CENOMAR",
    "Proof of Relationship",
    "Passport"
];

$requirements = implode("\n", $selected_requirements);

$stmt = $conn->prepare("INSERT INTO benefits (title, description, requirements, expiration_date, created_by, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("ssssi", $title, $description, $requirements, $expiration_date, $created_by);

if ($stmt->execute()) {
    $new_benefit_id = $stmt->insert_id;
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724;'>✅ Test Benefit Created!</h3>";
    echo "<p><strong>Benefit ID:</strong> $new_benefit_id</p>";
    echo "<p><strong>Title:</strong> $title</p>";
    echo "</div>";
    
    echo "<h3>Admin Selected These " . count($selected_requirements) . " Requirements:</h3>";
    echo "<ol>";
    foreach ($selected_requirements as $req) {
        echo "<li>☑️ " . htmlspecialchars($req) . "</li>";
    }
    echo "</ol>";
    
    echo "<hr>";
    echo "<h3>🎯 What Users Will See:</h3>";
    echo "<p>When users apply for this benefit, they will see " . count($selected_requirements) . " separate upload cards:</p>";
    
    foreach ($selected_requirements as $index => $req) {
        echo "<div style='border: 2px solid #0d6efd; background: white; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
        echo "<div style='background: #0d6efd; color: white; padding: 10px; margin: -15px -15px 10px -15px; border-radius: 6px 6px 0 0;'>";
        echo "<strong>" . ($index + 1) . "</strong> " . htmlspecialchars($req);
        echo "</div>";
        echo "<div style='padding: 10px;'>";
        echo "☁️ <strong>Upload Document:</strong><br>";
        echo "<input type='file' disabled style='width: 100%; padding: 8px; margin-top: 5px;'>";
        echo "<br><small style='color: #666;'>📎 Accepted: PDF, DOC, DOCX, JPG, PNG (Max 5MB)</small>";
        echo "</div>";
        echo "</div>";
    }
    
    echo "<hr>";
    echo "<div style='text-align: center; padding: 30px;'>";
    echo "<a href='apply_benefit.php?id=$new_benefit_id' target='_blank' style='display: inline-block; padding: 20px 40px; background: #28a745; color: white; text-decoration: none; border-radius: 10px; font-weight: bold; font-size: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.2);'>";
    echo "🚀 APPLY FOR THIS BENEFIT";
    echo "</a>";
    echo "<p style='margin-top: 15px; color: #666;'>Click to see the 6 separated upload fields in action!</p>";
    echo "</div>";
    
} else {
    echo "<p style='color: red;'>❌ Error: " . $conn->error . "</p>";
}

$conn->close();
?>