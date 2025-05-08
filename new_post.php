<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    
    query("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)", [$user_id, $title, $content]);
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Post | Anime Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h1>Create New Post</h1>
        
        <form method="POST" class="post-form">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" required>
            </div>
            
            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" id="content" rows="10" required></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Post</button>
                <a href="dashboard.php" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>