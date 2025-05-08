<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Anime Action Blog</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
  <div class="container">
    <h1>Anime Action Blog</h1>
    <nav>
      <a href="index.php">Home</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<div class="container content">
  <h2>Welcome to the Anime Action Blog</h2>
  <p>Your go-to place for all things anime!</p>
  <p>Explore our latest posts and join the community.</p>