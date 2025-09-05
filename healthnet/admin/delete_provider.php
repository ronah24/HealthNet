<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthnet";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get provider ID and delete
$provider_id = $_GET['id'];
$sql = "DELETE FROM healthcare_providers WHERE provider_id = $provider_id";

if ($conn->query($sql) === TRUE) {
    header("Location: healthcarepro.php?message=Provider+deleted+successfully");
} else {
    echo "Error deleting provider: " . $conn->error;
}
