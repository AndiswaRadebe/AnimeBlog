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
$post = query("SELECT user_id FROM posts WHERE id = ?", [$post_id])->fetch();
if (!$post || ($post['user_id'] != $user_id && !isAdmin($user_id))) {
    header("Location: dashboard.php");
    exit();
}

query("DELETE FROM posts WHERE id = ?", [$post_id]);
header("Location: dashboard.php");
exit();

function isAdmin($user_id) {
    $user = query("SELECT is_admin FROM users WHERE id = ?", [$user_id])->fetch();
    return $user && $user['is_admin'];
}
?>