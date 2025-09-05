<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'provider') {
    header("Location: login.php");
    exit();
}

$provider_id = $_SESSION['user_id']; // Assuming the provider's ID is stored in session

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

// Fetch appointments for this provider
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'Scheduled';
$query = "SELECT a.appointment_id, a.appointment_date, a.time_slot, a.status, a.notes, 
          p.first_name, p.last_name 
          FROM appointments AS a
          JOIN patients AS p ON a.patient_id = p.patient_id
          WHERE a.provider_id = '$provider_id' AND a.status = '$statusFilter'
          ORDER BY a.appointment_date ASC, a.time_slot ASC";
$appointments = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link rel="stylesheet" href="appointments.css">
    <link rel="icon" href="../icons/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <h2><i class="fas fa-stethoscope"></i> HealthNet</h2>
            <nav>
                <ul>
                    <li><a href="provider_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="appointments.php" class="active"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                    <li><a href="health_records.php"><i class="fas fa-file-medical"></i> Health Records</a></li>
                    <li><a href="resources.php"><i class="fas fa-book-medical"></i> Resources</a></li>
                    <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                </ul>
            </nav>
        </aside>

        <main>
            <header>
                <h1>Your Appointments</h1>
                <div class="filter">
                    <form method="GET" action="appointments.php">
                        <select name="status" onchange="this.form.submit()">
                            <option value="Scheduled" <?php echo $statusFilter === 'Scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                            <option value="Completed" <?php echo $statusFilter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Canceled" <?php echo $statusFilter === 'Canceled' ? 'selected' : ''; ?>>Canceled</option>
                        </select>
                    </form>
                </div>
                <form method="POST">
                    <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </header>

            <section class="appointments-list">
                <?php if ($appointments->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($appointment = $appointments->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $appointment['appointment_date']; ?></td>
                                    <td><?php echo $appointment['time_slot']; ?></td>
                                    <td><?php echo $appointment['first_name'] . " " . $appointment['last_name']; ?></td>
                                    <td><?php echo $appointment['status']; ?></td>
                                    <td><?php echo $appointment['notes'] ?: 'No notes'; ?></td>
                                    <td>
                                        <?php if ($appointment['status'] == 'Scheduled'): ?>
                                            <a href="update_status.php?id=<?php echo $appointment['appointment_id']; ?>&status=Completed" class="complete-btn">Complete</a>
                                            <a href="update_status.php?id=<?php echo $appointment['appointment_id']; ?>&status=Canceled" class="cancel-btn">Cancel</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No appointments found for the selected status.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>

</html>