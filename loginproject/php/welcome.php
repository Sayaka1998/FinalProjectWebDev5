<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

// Display welcome message with user's email
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION["email"]; ?>!</h1>
    <p>This is your welcome page.</p>
    <p>Feel free to customize this page according to your needs.</p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
