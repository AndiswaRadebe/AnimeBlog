<?php
session_start();
require_once 'includes/db.php'; // Fixed path - use relative to current file

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Get user info to check admin status
    $user_stmt = $conn->prepare("SELECT username, is_admin FROM users WHERE id = ?");
    $user_stmt->execute([$_SESSION['user_id']]);
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found");
    }

    // Get user's posts
    $post_stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
    $post_stmt->execute([$_SESSION['user_id']]);
    $posts = $post_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    die("An error occurred. Please try again later.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Anime Blog - Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Welcome, <?= htmlspecialchars($user['username'] ?? 'User', ENT_QUOTES, 'UTF-8') ?>!</h1>
        <div class="dashboard-actions">
            <?php if ($user['is_admin'] ?? false): ?>
                <a href="admin.php" class="btn btn-admin">Admin Panel</a>
            <?php endif; ?>
            <a href="new_post.php" class="btn btn-primary">New Post</a>
            <a href="logout.php" class="btn btn-logout">Logout</a>
        </div>
    </div>

    <div class="dashboard-content">
        <section class="user-posts">
            <h2>Your Anime Posts</h2>
            
            <?php if (count($posts) > 0): ?>
                <div class="posts-grid">
                    <?php foreach ($posts as $post): ?>
                        <div class="post-card">
                            <h3><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="post-excerpt">
                                <?= nl2br(htmlspecialchars(substr($post['content'], 0, 150), ENT_QUOTES, 'UTF-8')) ?>
                                <?= strlen($post['content']) > 150 ? '...' : '' ?>
                            </p>
                            <div class="post-meta">
                                <small>Created: <?= date('M j, Y', strtotime($post['created_at'])) ?></small>
                                <?php if ($post['updated_at'] && $post['updated_at'] != $post['created_at']): ?>
                                    <small>Updated: <?= date('M j, Y', strtotime($post['updated_at'])) ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="post-actions">
                                <a href="view_post.php?id=<?= $post['id'] ?>" class="btn btn-view">View</a>
                                <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-edit">Edit</a>
                                <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-posts">
                    <p>You haven't created any posts yet.</p>
                    <a href="new_post.php" class="btn btn-primary">Create Your First Post</a>
                </div>
            <?php endif; ?>
        </section>

        <section class="dashboard-sidebar">
            <div class="user-stats">
                <h3>Your Stats</h3>
                <div class="stat-item">
                    <span class="stat-label">Total Posts:</span>
                    <span class="stat-value"><?= count($posts) ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Account Type:</span>
                    <span class="stat-value"><?= ($user['is_admin'] ?? false) ? 'Admin' : 'Standard' ?></span>
                </div>
            </div>

            <div class="quick-actions">
                <h3>Quick Actions</h3>
                <a href="new_post.php" class="btn btn-sm">New Post</a>
                <a href="index.php" class="btn btn-sm">View Blog</a>
                <?php if ($user['is_admin'] ?? false): ?>
                    <a href="admin.php" class="btn btn-sm btn-admin">Admin Panel</a>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="dashboard-footer">
        <a href="index.php" class="btn">Return to Homepage</a>
    </div>
</div>
</body>
</html>