<!-- filepath: c:\xampp\htdocs\Blog\view_post.php -->
<?php
session_start();
require_once 'includes/db.php'; // Use require_once to ensure the file is included only once

// Validate and sanitize the post ID
$post_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$post_id) {
    header("Location: index.php");
    exit();
}

try {
    // Fetch the post using a prepared statement
    $post = query("
        SELECT posts.*, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.id = ?
    ", [$post_id])->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        header("Location: index.php");
        exit();
    }

    // Helper function to check if the user is an admin
    function isAdmin($user_id) {
        global $conn;
        $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['is_admin'] ?? false;
    }
} catch (Exception $e) {
    error_log("Error fetching post: " . $e->getMessage());
    die("An error occurred. Please try again later.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?> | Anime Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="post-view-container">
        <article class="full-post">
            <h1><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></h1>
            
            <div class="post-meta">
                <span>By <?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8') ?></span>
                <span>Posted on <?= date('F j, Y', strtotime($post['created_at'])) ?></span>
                <?php if ($post['updated_at'] && $post['updated_at'] != $post['created_at']): ?>
                    <span>Updated on <?= date('F j, Y', strtotime($post['updated_at'])) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="post-content">
                <?= nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')) ?>
            </div>
            
            <div class="post-actions">
                <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $post['user_id'] || isAdmin($_SESSION['user_id']))): ?>
                    <a href="edit_post.php?id=<?= urlencode($post['id']) ?>" class="btn btn-edit">Edit</a>
                    <a href="delete_post.php?id=<?= urlencode($post['id']) ?>" class="btn btn-delete" 
                       onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                <?php endif; ?>
                <a href="index.php" class="btn">Back to Home</a>
            </div>
        </article>
    </div>
</body>
</html>