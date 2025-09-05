<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'provider') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $appointment_id = intval($_GET['id']);
    $new_status = $_GET['status'];

    // Validate the new status
    if (!in_array($new_status, ['Completed', 'Canceled'])) {
        die("Invalid status");
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

    // Update the appointment status
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
    $stmt->bind_param("si", $new_status, $appointment_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Appointment updated successfully.";
    } else {
        $_SESSION['message'] = "Failed to update appointment.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the appointments page
    header("Location: appointments.php");
    exit();
} else {
    die("Missing appointment ID or status.");
}
