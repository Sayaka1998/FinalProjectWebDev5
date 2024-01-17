<?php
$servername = "localhost";  // replace with your server address (if not using localhost)
$username = "root";  // replace with the MySQL/MariaDB username
$password = "";    // replace with the MySQL/MariaDB password
$database = "customer_tb";       // replace with the name of your database

// Attempt to establish the connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>