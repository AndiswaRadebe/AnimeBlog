<?php
// admin.php
session_start();
include 'includes/db.php';

// Check if user is admin (you'll need to add an 'is_admin' column to your users table)
if (!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}


// Check if user is admin
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || !$user['is_admin']) {
    header("Location: index.php");
    exit();
}

// Handle post deletion
if (isset($_GET['delete_post'])) {
    $post_id = $_GET['delete_post'];
    $conn->query("DELETE FROM posts WHERE id = $post_id");
    header("Location: admin.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    // First delete user's posts to maintain referential integrity
    $conn->query("DELETE FROM posts WHERE user_id = $user_id");
    $conn->query("DELETE FROM users WHERE id = $user_id AND id != 1"); // Prevent deleting the first admin
    header("Location: admin.php");
    exit();
}


// Get all users and posts
$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
$posts = $conn->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Anime Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard-container admin-container">
        <h1>Admin Dashboard</h1>
        <div class="admin-nav">
            <a href="dashboard.php" class="btn">User Dashboard</a>
            <a href="index.php" class="btn">View Blog</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>

        <div class="admin-sections">
            <!-- Users Section -->
            <section class="admin-section">
                <h2>Manage Users</h2>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Admin</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= $user['is_admin'] ? 'Yes' : 'No' ?></td>
                                <td>
                                    <?php if ($user['id'] != 1): // Don't allow deleting the first admin ?>
                                    <a href="admin.php?delete_user=<?= $user['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user and all their posts?')">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Posts Section -->
            <section class="admin-section">
                <h2>Manage Posts</h2>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($post = $posts->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= htmlspecialchars($post['id']) ?></td>
                                <td><?= htmlspecialchars($post['title']) ?></td>
                                <td><?= htmlspecialchars($post['username']) ?></td>
                                <td>
                                    <a href="view_post.php?id=<?= $post['id'] ?>" class="btn">View</a>
                                    <a href="admin.php?delete_post=<?= $post['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</body>
</html>