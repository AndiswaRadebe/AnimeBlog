<?php 
include 'includes/db.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Anime Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Anime Blog</h1>
        <div class="nav">
            <a href="index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <?php 
                $user = query("SELECT is_admin FROM users WHERE id = ?", [$_SESSION['user_id']])->fetch();
                if ($user && $user['is_admin']): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to Anime Blog</h1>
        <p>Discover the latest anime reviews, news, and community stories.</p>
        <a href="#posts" class="btn btn-primary">Explore Posts</a>
    </section>

    <main>
        <h2 id="posts">Latest Posts</h2>
        <?php
        $posts = query("
            SELECT posts.*, users.username 
            FROM posts 
            JOIN users ON posts.user_id = users.id 
            ORDER BY posts.created_at DESC
        ")->fetchAll();

        if (count($posts) > 0): ?>
            <div class="posts-grid">
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                        <p class="post-excerpt">
                            <?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>
                            <?= strlen($post['content']) > 200 ? '...' : '' ?>
                        </p>
                        <div class="post-meta">
                            <small>By <?= htmlspecialchars($post['username']) ?></small>
                            <small><?= date('M j, Y', strtotime($post['created_at'])) ?></small>
                        </div>
                        <div class="post-actions">
                            <a href="view_post.php?id=<?= $post['id'] ?>" class="btn btn-view">Read More</a>
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                                <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-edit">Edit</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No posts found. Be the first to create one!</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Anime Blog. All rights reserved.</p>
    </footer>
</body>
</html>