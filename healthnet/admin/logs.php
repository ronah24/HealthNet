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

// Fetch system logs based on filters
function fetchSystemLogs($conn, $user_type = "", $activity = "")
{
    $sql = "SELECT * FROM system_logs WHERE 1=1";

    if ($user_type) {
        $sql .= " AND user_type = '$user_type'";
    }
    if ($activity) {
        $sql .= " AND activity LIKE '%$activity%'";
    }
    $sql .= " ORDER BY timestamp DESC";

    return $conn->query($sql);
}

// Handle filter form submission
$user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';
$activity = isset($_POST['activity']) ? $_POST['activity'] : '';
$logs = fetchSystemLogs($conn, $user_type, $activity);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="healthcarepro.css">
    <link rel="stylesheet" href="logs.css">
    <link rel="icon" href="../icons/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <div class="container">
        <!-- Sidebar -->
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
                <h1>System Logs</h1>
                <form method="POST">
                    <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </header>

            <!-- System Logs Table Section -->
            <section class="table-section">
                <h2 style="text-align: center;">Activity Logs</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Log ID</th>
                            <th>User Type</th>
                            <th>Activity</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($logs->num_rows > 0): ?>
                            <?php while ($log = $logs->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $log['log_id']; ?></td>
                                    <td><?php echo $log['user_type']; ?></td>
                                    <td><?php echo $log['activity']; ?></td>
                                    <td><?php echo $log['timestamp']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No logs found for the selected filters.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>

</html>