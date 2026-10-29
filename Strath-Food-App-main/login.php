<?php include "header.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Strathmore University</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="main-header">
        <h1>Strathmore University Cafeteria Ordering System</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="registration-container">
        <form class="registration-form" method="POST" action="process_login.php">
            <h2>Login to Your Account</h2>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            
            <button type="submit" class="submit-btn">Login</button>
            
            <p class="login-link">Don't have an account? <a href="registration.php">Register here</a></p>
        </form>
    </main>
    
    <footer class="main-footer">
        <p>&copy; 2025 Strathmore University Cafeteria Ordering System. All rights reserved.</p>
    </footer>
</body>
</html>