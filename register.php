<?php
session_start();
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        query("INSERT INTO users (username, password) VALUES (?, ?)", [$username, $password]);
        $user_id = query("SELECT id FROM users WHERE username = ?", [$username])->fetchColumn();
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        $error = "Username already exists or registration failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register | Anime Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <h1>Register</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Register</button>
            <p class="auth-link">Already have an account? <a href="login.php">Login here</a></p>
            <a href="index.php" class="btn">Return to Homepage</a>
        </form>
    </div>
</body>
</html>