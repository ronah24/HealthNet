<?php
// Database connection variables
$servername = "localhost"; // Replace with your database server
$username = "root";        // Replace with your database username
$password = "";            // Replace with your database password
$dbname = "healthnet"; // Replace with your database name

// Create the connection
$connection = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
