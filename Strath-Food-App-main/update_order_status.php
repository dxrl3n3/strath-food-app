<?php
include "header.php";
session_start();
require_once "connection.php";

// Only kitchen staff can update order status
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'kitchen') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    // Update status to 'delivered'
    $stmt = $conn->prepare("UPDATE orders SET status = 'delivered' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to the orders view
header("Location: vieworders.php");
exit();