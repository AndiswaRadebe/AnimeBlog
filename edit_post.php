<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$post_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Verify post belongs to user (or user is admin)
$post = query("SELECT * FROM posts WHERE id = ?", [$post_id])->fetch();
if (!$post || ($post['user_id'] != $user_id && !isAdmin($user_id))) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    query("UPDATE posts SET title = ?, content = ? WHERE id = ?", [$title, $content, $post_id]);
    header("Location: dashboard.php");
    exit();
}

function isAdmin($user_id) {
    $user = query("SELECT is_admin FROM users WHERE id = ?", [$user_id])->fetch();
    return $user && $user['is_admin'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Post | Anime Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h1>Edit Post</h1>
        
        <form method="POST" class="post-form">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" value="<?= htmlspecialchars($post['title']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" id="content" rows="10" required><?= htmlspecialchars($post['content']) ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Post</button>
                <a href="dashboard.php" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>