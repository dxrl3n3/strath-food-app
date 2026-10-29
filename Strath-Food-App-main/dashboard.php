<?php
include "header.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "connection.php";

// Get user information
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Strathmore University</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="main-header">
        <h1>Strathmore University Cafeteria Ordering System</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="find_medicine.php">Find Medicine</a></li>
                <li><a href="pharmacies_map.php">Pharmacy Map</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="container">
        <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
        
        <div class="features-grid">
            <div class="feature">
                <h3>Your Profile</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
                <p><strong>Role:</strong> <?php echo ucfirst(htmlspecialchars($user_role)); ?></p>
            </div>
            
            <?php if ($user_role === 'student'): ?>
            <div class="feature">
                <h3>Place Order</h3>
                <p>Browse our menu and place your food orders.</p>
                <a href="place_order.php" class="cta-button">Order Now</a>

            </div>
            
            <div class="feature">
                <h3>Order History</h3>
                <p>View your previous orders and track current ones.</p>
                <a href="vieworders.php" class="cta-button">Order History</a>
            </div>
            <?php endif; ?>
            
            <?php if ($user_role === 'admin'): ?>
            <div class="feature">
                <h3>Manage Users</h3>
                <p>View and manage system users.</p>
                <a href="manage_users.php" class="cta-button">Manage Users</a>
            </div>
            
            <div class="feature">
                <h3>System Settings</h3>
                <p>Configure system settings and preferences.</p>
                <a href="settings.php" class="cta-button">Settings</a>
            </div>
            <?php endif; ?>
            
            <?php if ($user_role === 'kitchen'): ?>
            <div class="feature">
                <h3>Pending Orders</h3>
                <p>View and process incoming food orders.</p>
                <a href="vieworders.php" class="cta-button">View Orders</a>
            </div>
            
            <div class="feature">
                <h3>Menu Management</h3>
                <p>Update menu items and availability.</p>
                <a href="menumgmt.php" class="cta-button">Manage Menu</a>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <footer class="main-footer">
        <p>&copy; 2025 Strathmore University Cafeteria Ordering System. All rights reserved.</p>
    </footer>
</body>
</html>