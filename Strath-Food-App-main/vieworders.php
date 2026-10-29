<?php
include "header.php";
session_start();
require_once "connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['user_role'];

if ($user_role === 'student') {
    $title = "📋 My Orders";
    $stmt = $conn->prepare("
        SELECT o.id, m.name AS item, o.quantity, o.total_price, o.status, o.order_time
        FROM orders o
        JOIN menu m ON o.menu_id = m.id
        WHERE o.user_id = ?
        ORDER BY o.order_time DESC
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
}
elseif ($user_role === 'kitchen') {
    $title = "👩‍🍳 Kitchen: All Orders";
    $stmt = $conn->prepare("
        SELECT o.id,
               u.fullname AS student,
               m.name     AS item,
               o.quantity,
               o.total_price,
               o.status,
               o.order_time
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN menu  m ON o.menu_id = m.id
        ORDER BY o.order_time DESC
    ");
}
else {
    header("Location: dashboard.php");
    exit();
}

$stmt->execute();
$result = $stmt->get_result();
?>

<link rel="stylesheet" href="styles.css">
<div class="container centered-form">
  <h2><?= $title ?></h2>

  <?php if ($result->num_rows === 0): ?>
    <p class="error">
      <?= $user_role === 'student'
           ? "You haven’t placed any orders yet."
           : "No orders found." ?>
    </p>
  <?php else: ?>
    <table class="orders-table">
      <thead>
        <tr>
          <th>#</th>
          <?php if ($user_role === 'kitchen'): ?>
            <th>Student</th>
          <?php endif; ?>
          <th>Item</th>
          <th>Qty</th>
          <th>Total (Ksh)</th>
          <th>Status</th>
          <?php if ($user_role === 'kitchen'): ?>
            <th>Action</th>
          <?php endif; ?>
          <th>Ordered At</th>
        </tr>
      </thead>
      <tbody>
      <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <?php if ($user_role === 'kitchen'): ?>
            <td><?= htmlspecialchars($row['student']) ?></td>
          <?php endif; ?>
          <td><?= htmlspecialchars($row['item']) ?></td>
          <td><?= $row['quantity'] ?></td>
          <td><?= number_format($row['total_price'],2) ?></td>
          <td class="status-<?= $row['status'] ?>">
            <?= ucfirst($row['status']) ?>
          </td>
          <?php if ($user_role === 'kitchen'): ?>
            <td>
              <?php if ($row['status'] === 'pending'): ?>
                <form method="POST" action="update_order_status.php">
                  <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                  <button type="submit" class="back-button">Mark Delivered</button>
                </form>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
          <?php endif; ?>
          <td><?= $row['order_time'] ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <p><a href="dashboard.php" class="back-button">← Back to Dashboard</a></p>
</div>
<footer class="main-footer">
    <p>&copy; 2025 Strathmore University Cafeteria Ordering System. All rights reserved.</p>
</footer>
