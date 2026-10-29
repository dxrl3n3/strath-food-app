<?php
include "header.php";
include 'connection.php';

// Handle updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['items'] as $id => $details) {
        $price = (float)$details['price'];
        $availability = ($details['availability'] === 'available') ? 1 : 0;

        $stmt = $conn->prepare("UPDATE menu SET price = ?, available = ? WHERE id = ?");
        $stmt->bind_param("dii", $price, $availability, $id);
        $stmt->execute();
    }
    echo "<p class='success'>Menu updated successfully.</p>";
}

// Fetch menu items
$result = $conn->query("SELECT * FROM menu ORDER BY name ASC");
?>

<div class="container">
    <h2>Menu Management</h2>
    <form method="POST">
        <table>
            <tr>
                <th>Item</th>
                <th>Price (Ksh)</th>
                <th>Availability</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>
                        <input type="number" step="0.01" name="items[<?php echo $row['id']; ?>][price]" value="<?php echo $row['price']; ?>" required>
                    </td>
                    <td>
                        <select name="items[<?php echo $row['id']; ?>][availability]">
                            <option value="available" <?php if ($row['available']) echo 'selected'; ?>>Available</option>
                            <option value="unavailable" <?php if (!$row['available']) echo 'selected'; ?>>Unavailable</option>
                        </select>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <button type="submit" class="submit-btn">Update Menu</button>
    </form>
    <a href="dashboard.php" class="back-button">Back</a>
</div>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
    <footer class="main-footer">
        <p>&copy; 2025 Strathmore University Cafeteria Ordering System. All rights reserved.</p>
    </footer>
</html>