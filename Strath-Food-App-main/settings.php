<?php
require_once "connection.php";
session_start();

// Simulated settings in a settings table (optional enhancement: use DB table)
$settings = [
    'default_availability' => '1', // true
    'maintenance_mode' => '0',     // false
    
    'support_email' => 'cafeteria@strathmore.edu'
];

// Load from DB in future: SELECT * FROM settings

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $settings['default_availability'] = isset($_POST['default_availability']) ? '1' : '0';
    $settings['maintenance_mode'] = isset($_POST['maintenance_mode']) ? '1' : '0';
    $settings['support_email'] = trim($_POST['support_email']);

    // In production, update in DB here
    $message = "Settings updated successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="main-header">
    <h1>Strathmore Cafeteria Admin Settings</h1>
</header>
<div class="container">
    <h2>System Settings</h2>
    <?php if (!empty($message)): ?>
        <div class="success"> <?= htmlspecialchars($message) ?> </div>
    <?php endif; ?>

    <form method="POST" class="registration-form">
        <div class="form-group">
            <label>
                <input type="checkbox" name="default_availability" <?= $settings['default_availability'] === '1' ? 'checked' : '' ?>>
                Default availability ON for new menu items
            </label>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="maintenance_mode" <?= $settings['maintenance_mode'] === '1' ? 'checked' : '' ?>>
                Enable Maintenance Mode
            </label>
        </div>

        <div class="form-group">
            <label for="support_email">Support Email</label>
            <input type="email" name="support_email" id="support_email" value="<?= htmlspecialchars($settings['support_email']) ?>" required>
        </div>

        <button type="submit" class="submit-btn">Save Settings</button>
    </form>
    <div style="text-align:center; margin-top:2rem;">
        <a href="dashboard.php" class="back-button">Back to Dashboard</a>
    </div>
</div>
<footer class="main-footer">
    <p>&copy; 2025 Strathmore University Cafeteria Ordering System. All rights reserved.</p>
</footer>
</body>
</html>
