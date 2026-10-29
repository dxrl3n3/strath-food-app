<?php
include "header.php"; 
session_start();
require_once "connection.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $message = "Please fill in all fields";
        $messageType = "error";
    } else {
        // Check user credentials (removed status check)
        $stmt = $conn->prepare("SELECT id, fullname, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['fullname'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                $message = "Login successful! Redirecting to dashboard...";
                $messageType = "success";
            } else {
                $message = "Invalid email or password";
                $messageType = "error";
            }
        } else {
            $message = "Invalid email or password";
            $messageType = "error";
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Status - Strathmore University</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="main-header">
        <h1>Strathmore University Cafeteria Ordering System</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="registration.php">Register</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="registration-container">
        <div class="registration-form">
            <h2>Login Status</h2>
            
            <?php if (!empty($message)): ?>
                <div class="<?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div style="text-align: center; margin-top: 2rem;">
                <?php if ($messageType === "success"): ?>
                    <a href="dashboard.php" class="cta-button">Go to Dashboard</a>
                <?php else: ?>
                    <a href="login.php" class="cta-button">Back to Login</a>
                <?php endif; ?>
                <a href="index.php" class="cta-button secondary">Go to Home</a>
            </div>
        </div>
    </main>
    
    <footer class="main-footer">
        <p>&copy; 2025 Strathmore University Cafeteria Ordering System. All rights reserved.</p>
    </footer>

    <script>
        <?php if ($messageType === "success"): ?>
        setTimeout(function() {
            window.location.href = 'dashboard.php';
        }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>