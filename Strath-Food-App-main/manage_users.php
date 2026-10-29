<?php 
require_once "connection.php";

// Handle user role update, disable, enable, or deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update']) && isset($_POST['role'], $_POST['user_id'])) {
        $role = $_POST['role'];
        $user_id = intval($_POST['user_id']);
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $role, $user_id);
        $stmt->execute();
    }

    if (isset($_POST['disable']) && isset($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }

    if (isset($_POST['enable']) && isset($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }

    if (isset($_POST['delete']) && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    // Check if user has orders
    $check = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $check->bind_result($order_count);
    $check->fetch();
    $check->close();

    if ($order_count > 0) {
        echo "<script>alert('Cannot delete user: this user has existing orders.');</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
}

}

// Filter and search functionality
$search = $_GET['search'] ?? '';
$filter_status = $_GET['status'] ?? '';

$sql = "SELECT id, fullname, email, role, status, registration_date FROM users WHERE 1";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (fullname LIKE ? OR email LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

if ($filter_status === 'active' || $filter_status === 'inactive') {
    $sql .= " AND status = ?";
    $params[] = $filter_status;
    $types .= "s";
}

$sql .= " ORDER BY id ASC";

if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Strathmore Ordering</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Manage Users</h1>

    <form method="GET" style="margin-bottom: 1rem;">
        <input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>" style="padding: 0.5rem; border-radius: 5px; width: 250px;">
        <select name="status" style="padding: 0.5rem;">
            <option value="">All</option>
            <option value="active" <?= $filter_status === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $filter_status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
        <button type="submit" class="submit-btn">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Registered On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <form method="POST">
                            <td><?= htmlspecialchars($row["id"]) ?></td>
                            <td><?= htmlspecialchars($row["fullname"]) ?></td>
                            <td><?= htmlspecialchars($row["email"]) ?></td>
                            <td>
                                <select name="role">
                                    <option value="student" <?= $row["role"] === 'student' ? 'selected' : '' ?>>Student</option>
                                    <option value="admin" <?= $row["role"] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="kitchen" <?= $row["role"] === 'kitchen' ? 'selected' : '' ?>>Kitchen</option>
                                </select>
                            </td>
                            <td><?= ucfirst($row["status"]) ?></td>
                            <td><?= date("Y-m-d H:i", strtotime($row["registration_date"])) ?></td>
                            <td>
                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="update" class="submit-btn">Update</button>
                                <?php if ($row['status'] === 'active'): ?>
                                    <button type="submit" name="disable" class="submit-btn" onclick="return confirm('Are you sure you want to disable this user?');">Disable</button>
                                <?php else: ?>
                                    <button type="submit" name="enable" class="submit-btn" onclick="return confirm('Are you sure you want to re-enable this user?');">Enable</button>
                                <?php endif; ?>
                                <button type="submit" name="delete" class="submit-btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="back-button">Back</a>
</div>
<footer class="main-footer">
    <p>&copy; 2025 Strathmore University Cafeteria Ordering System. All rights reserved.</p>
</footer>
</body>
</html>
