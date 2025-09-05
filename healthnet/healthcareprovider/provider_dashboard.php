<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'provider') {
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

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ../index.php');
    exit();
}

// Fetching provider-specific information like upcoming appointments and notifications
$provider_id = $_SESSION['user_id']; // Assuming provider's ID is stored in session
$appointments = $conn->query("SELECT * FROM appointments WHERE provider_id = '$provider_id' AND appointment_date >= CURDATE()");
$notifications = $conn->query("SELECT * FROM notifications WHERE user_id = '$provider_id' AND user_type = 'Provider' AND status = 'Unread'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" href="../icons/logo.png">
    <link rel="stylesheet" href="provider_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2><i class="fas fa-stethoscope"></i> HealthNet</h2>
            <nav>
                <ul>
                    <li><a href="provider_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                    <li><a href="health_records.php"><i class="fas fa-file-medical"></i> Health Records</a></li>
                    <li><a href="resources.php"><i class="fas fa-book-medical"></i> Resources</a></li>
                    <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main>
            <header>
                <h1>Welcome</h1>
                <form method="POST">
                    <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </header>

            <section class="dashboard-overview">
                <div class="dashboard-widget">
                    <h3>Upcoming Appointments</h3>
                    <ul>
                        <?php while ($appointment = $appointments->fetch_assoc()): ?>
                            <li><?php echo $appointment['appointment_date']; ?> - <?php echo $appointment['patient_name']; ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <div class="dashboard-widget">
                    <h3>Notifications</h3>
                    <ul>
                        <?php while ($notification = $notifications->fetch_assoc()): ?>
                            <li><?php echo $notification['message']; ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <div class="dashboard-widget">
                    <h3>Health Resources</h3>
                    <p>Access the latest health resources and training materials.</p>
                    <a href="resources.php" class="view-more">View Resources</a>
                </div>
            </section>
        </main>
    </div>
</body>

</html>