<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/pharmalink_client.php';

if (!isset($pl_title)) {
    $pl_title = 'PharmaLink';
}

$pl_logged_in = false;
if (!empty($_SESSION) && is_array($_SESSION)) {
    foreach (array_keys($_SESSION) as $pl_key) {
        if (strpos((string)$pl_key, 'pl_') !== 0) {
            $pl_logged_in = true;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= pl_h($pl_title) ?> - Strathmore University</title>
    <link rel="stylesheet" href="styles.css">
    <?php if (!empty($pl_head_extra)) { echo $pl_head_extra; } ?>
    <style>
        .pl-page { max-width: 1200px; margin: 40px auto; padding: 0 16px; }
        .pl-main { background: #fff; border-radius: 12px; padding: 24px 32px; margin: 0; color: #333; }
        .pl-main h2 { margin-top: 0; color: #333; }
        .pl-main h3 { color: #1caeb7; }
        .pl-main a { color: #1caeb7; }
        .pl-main input[type="text"], .pl-main input[type="number"] { padding: 8px 10px; border: 1px solid #bbb; border-radius: 6px; font-size: 15px; }
        .pl-table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .pl-table th, .pl-table td { border-bottom: 1px solid #e3e3e3; padding: 10px; text-align: left; }
        .pl-table th { color: #1caeb7; }
        .pl-msg { padding: 10px 14px; border-radius: 6px; margin: 12px 0; }
        .pl-success { background: #e6f4ea; color: #1e4620; }
        .pl-error { background: #fdecea; color: #611a15; }
        .pl-map { height: 450px; width: 100%; margin-top: 12px; border-radius: 8px; }
        .pl-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
        .pl-btn { display: inline-block; padding: 9px 16px; border: none; border-radius: 6px; text-decoration: none; cursor: pointer; background: #1caeb7; color: #fff !important; font-weight: bold; font-size: 15px; }
        .pl-btn:hover { background: #17939a; }
        .pl-qty { width: 60px; }
        .pl-footer { background: rgba(0, 0, 0, 0.8); color: #fff; text-align: center; padding: 18px; margin-top: 0; }
        .pl-footer p { margin: 0; }
    </style>
</head>
<body>
<div class="pl-page">
<header class="main-header">
    <h1>Strathmore University Cafeteria Ordering System</h1>
    <nav>
        <ul>
<?php if ($pl_logged_in): ?>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="place_order.php">Order Now</a></li>
            <li><a href="vieworders.php">Order History</a></li>
            <li><a href="find_medicine.php">Find Medicine</a></li>
            <li><a href="pharmacies_map.php">Pharmacy Map</a></li>
            <li><a href="logout.php">Logout</a></li>
<?php else: ?>
            <li><a href="index.php">Home</a></li>
            <li><a href="find_medicine.php">Find Medicine</a></li>
            <li><a href="pharmacies_map.php">Pharmacy Map</a></li>
            <li><a href="login.php">Login</a></li>
<?php endif; ?>
        </ul>
    </nav>
</header>
<main class="pl-main">
<?php
if (!empty($_SESSION['pl_flash'])) {
    $pl_flash = $_SESSION['pl_flash'];
    unset($_SESSION['pl_flash']);
    $pl_flash_class = (isset($pl_flash['type']) && $pl_flash['type'] === 'success') ? 'pl-success' : 'pl-error';
    echo '<div class="pl-msg ' . $pl_flash_class . '">' . pl_h(isset($pl_flash['msg']) ? $pl_flash['msg'] : '') . '</div>';
}
?>