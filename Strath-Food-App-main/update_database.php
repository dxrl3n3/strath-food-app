<?php
include "header.php";
require_once "connection.php";

echo "<h2>Database Update Script</h2>";

// Check if status column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'status'");

if ($result->num_rows == 0) {
    // Add status column
    $sql = "ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER role";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Status column added successfully!</p>";
        
        // Update existing users to active status
        $updateSql = "UPDATE users SET status = 'active' WHERE status IS NULL";
        if ($conn->query($updateSql) === TRUE) {
            echo "<p style='color: green;'>✓ Existing users updated to active status!</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Error adding status column: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ Status column already exists!</p>";
}

// Check if registration_date column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'registration_date'");

if ($result->num_rows == 0) {
    // Add registration_date column
    $sql = "ALTER TABLE users ADD COLUMN registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER status";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Registration_date column added successfully!</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding registration_date column: " . $conn->error . "</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ Registration_date column already exists!</p>";
}

echo "<p><a href='index.php'>← Back to Home</a></p>";

$conn->close();
?>