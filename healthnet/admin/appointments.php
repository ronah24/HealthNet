<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root"; // Adjust as per your database configuration
$password = "";
$dbname = "healthnet";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle cancellation of appointments
if (isset($_POST['cancel_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $sql = "UPDATE appointments SET status='Canceled' WHERE appointment_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $stmt->close();
}

// Handle rescheduling appointments
if (isset($_POST['reschedule_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_date = $_POST['new_date'];
    $new_time = $_POST['new_time'];

    $sql = "UPDATE appointments SET appointment_date=?, time_slot=? WHERE appointment_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $new_date, $new_time, $appointment_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all appointments
$sql = "SELECT a.appointment_id, a.appointment_date, a.time_slot, a.status, 
        CONCAT(p.first_name, ' ', p.last_name) AS patient_name, 
        CONCAT(h.first_name, ' ', h.last_name) AS provider_name 
        FROM appointments a 
        JOIN patients p ON a.patient_id = p.patient_id 
        JOIN healthcare_providers h ON a.provider_id = h.provider_id";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="appointments.css">
    <link rel="icon" href="../icons/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <h2><i class="fas fa-heartbeat"></i> HealthNet</h2>
            <nav>
                <ul>
                    <li><a href="admin_dashboard.php"><i class="fas fa-user-shield"></i> Admin Management</a></li>
                    <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                    <li><a href="healthcarepro.php"><i class="fas fa-user-md"></i> Healthcare Providers</a></li>
                    <li><a href="resources.php"><i class="fas fa-book-medical"></i> Health Resources</a></li>
                    <li><a href="records.php"><i class="fas fa-file-medical"></i> Health Records</a></li>
                    <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li><a href="patients.php"><i class="fas fa-users"></i> Patients</a></li>
                    <li><a href="logs.php"><i class="fas fa-history"></i> System Logs</a></li>
                    <li><a href="#system-settings"><i class="fas fa-cogs"></i> System Settings</a></li>
                </ul>
            </nav>
        </aside>

        <main>
            <header>
                <h1>Appointment Management</h1>
                <form method="POST">
                    <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </header>

            <section class="content-section">
                <h2>Appointments</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Appointment ID</th>
                            <th>Patient Name</th>
                            <th>Provider Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['appointment_id']; ?></td>
                                    <td><?php echo $row['patient_name']; ?></td>
                                    <td><?php echo $row['provider_name']; ?></td>
                                    <td><?php echo $row['appointment_date']; ?></td>
                                    <td><?php echo $row['time_slot']; ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                            <button type="submit" name="cancel_appointment" onclick="return confirm('Are you sure you want to cancel this appointment?');">Cancel</button>
                                        </form>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                            <input type="date" name="new_date" required>
                                            <input type="time" name="new_time" required>
                                            <button type="submit" name="reschedule_appointment">Reschedule</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No appointments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
    <div>
        <footer class="footer">
            <p>&copy; <?php echo date("Y"); ?> Health Net. All rights reserved.</p>
        </footer>
    </div>
</body>

</html>

<?php $conn->close(); ?>