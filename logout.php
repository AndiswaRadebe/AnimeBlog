
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
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="../login.php">Login</a></li>
                <li><a href="../register.php">Register</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>You have been logged out.</h2>
        <p>Thank you for visiting our blog!</p>
    </main>
<?php
session_start();
session_destroy();
header("Location: ../Blog/index.php");
exit();
?>
</body>
</html>
