<?php
$host = "localhost";
$user = "root";
$password = ""; 
$database = "lib_db";

$mysqli = new mysqli($host, $user, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

return $mysqli;
?>
