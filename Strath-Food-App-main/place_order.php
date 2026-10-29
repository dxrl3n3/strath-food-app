<?php
include "header.php";
session_start();
require_once "connection.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmed'])) {
    $menu_id   = $_POST['menu_id'];
    $quantity  = intval($_POST['quantity']);
    $user_id   = $_SESSION['user_id'];

    // Fetch item's data
    $stmt = $conn->prepare("SELECT name, price, available FROM menu WHERE id = ?");
    $stmt->bind_param("i", $menu_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    
    $stmt->close();

    if ($item && $item['available']) {
        $total_price = $item['price'] * $quantity;

        // Insert order
        $stmt = $conn->prepare("
            INSERT INTO orders (user_id, menu_id, quantity, total_price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiid", $user_id, $menu_id, $quantity, $total_price);
        $stmt->execute();
        $stmt->close();

        $message = "✅ Order placed for <strong>{$item['name']}</strong>.<br>"
                 . "Please pay <strong>Ksh " . number_format($total_price, 2) . "</strong>.";
    } else {
        $message = "❌ The selected item is not available.";
    }
}
?>
<link rel="stylesheet" href="styles.css">
<div class="container centered-form">
    <h2>🛒 Place an Order</h2>

    <?php if ($message): ?>
        <div class="success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" id="orderForm">
        <div class="form-group">
            <label for="menu_id">Choose Item:</label>
            <select name="menu_id" required>
                <option value="">-- Select an item --</option>
                <?php
                $result = $conn->query("
                    SELECT id, name, price, available
                    FROM menu
                    ORDER BY name ASC
                ");
                while ($row = $result->fetch_assoc()) {
                    $status   = $row['available'] ? "Available ✅" : "Unavailable ❌";
                    $disabled = $row['available'] ? "" : "disabled";
                    echo "<option value='{$row['id']}' $disabled>"
                       . "{$row['name']} - Ksh {$row['price']} ({$status})"
                       . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" value="1" min="1" required>
        </div>

        <button class="submit-btn" onclick="confirmPayment(event)">
            Place Order
        </button>
    </form>

    <p><a href="dashboard.php" class="back-button">← Back to Dashboard</a></p>
</div>
<footer class="main-footer">
    <p>&copy; 2025 Strathmore University Cafeteria Ordering System. All rights reserved.</p>
</footer>

<script>
function confirmPayment(event) {
    event.preventDefault();
    const menu     = document.querySelector('select[name="menu_id"]');
    const option   = menu.options[menu.selectedIndex];
    const quantity = parseInt(document.querySelector('input[name="quantity"]').value);

    if (!menu.value) {
        alert("Please select a menu item.");
        return;
    }
    if (quantity <= 0) {
        alert("Quantity must be at least 1.");
        return;
    }
    const match = option.text.match(/Ksh (\d+(\.\d+)?)/);
    if (!match) {
        alert("Could not find price.");
        return;
    }
    const price = parseFloat(match[1]);
    const total = price * quantity;
    if (confirm(`You are about to place an order worth Ksh ${total.toFixed(2)}.\nContinue and pay now?`)) {
        const form = document.getElementById('orderForm');
        const hidden = document.createElement('input');
        hidden.type  = 'hidden';
        hidden.name  = 'confirmed';
        hidden.value = '1';
        form.appendChild(hidden);
        form.submit();
    }
}
</script>
