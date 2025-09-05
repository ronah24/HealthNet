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

// Add notification logic
if (isset($_POST['send_notification'])) {
    $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : NULL;
    $user_type = $_POST['user_type'];
    $message = $_POST['message'];
    $status = "Unread";

    $sql = "INSERT INTO notifications (user_id, user_type, message, status) 
            VALUES ('$user_id', '$user_type', '$message', '$status')";
    if ($conn->query($sql) === TRUE) {
        $message = "Notification sent successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Update notification status
if (isset($_GET['mark_as_read'])) {
    $notification_id = $_GET['mark_as_read'];
    $sql = "UPDATE notifications SET status='Read' WHERE notification_id='$notification_id'";
    $conn->query($sql);
}
if (isset($_GET['mark_as_unread'])) {
    $notification_id = $_GET['mark_as_unread'];
    $sql = "UPDATE notifications SET status='Unread' WHERE notification_id='$notification_id'";
    $conn->query($sql);
}

// Delete notification
if (isset($_GET['delete'])) {
    $notification_id = $_GET['delete'];
    $sql = "DELETE FROM notifications WHERE notification_id='$notification_id'";
    $conn->query($sql);
}

// Fetch notifications
function fetchNotifications($conn)
{
    $sql = "SELECT * FROM notifications ORDER BY notification_date DESC";
    return $conn->query($sql);
}

$notifications = fetchNotifications($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications Management</title>
    <link rel="icon" href="../icons/logo.png">
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="notifications.css">
    <link rel="stylesheet" href="healthcarepro.css">
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
                <h1>Manage Notifications</h1>
                <form method="POST">
                    <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </header>

            <!-- Send Notification Form -->
            <section class="form-section">
                <h2>Send Notification</h2>
                <?php if (isset($message)) echo "<p>$message</p>"; ?>
                <form method="POST">
                    <input type="number" name="user_id" placeholder="User ID (optional)">
                    <select name="user_type" required>
                        <option value="Patient">Patient</option>
                        <option value="Provider">Provider</option>
                        <option value="Admin">Admin</option>
                    </select>
                    <textarea name="message" class="formtxt" placeholder="Enter your message" required></textarea>
                    <button type="submit" name="send_notification">Send Notification</button>
                </form>
            </section>

            <!-- Notifications Table -->
            <section class="table-section">
                <h2 style="text-align: center;">Notifications</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>User Type</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($notification = $notifications->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $notification['notification_id']; ?></td>
                                <td><?php echo $notification['user_id'] ?? 'All'; ?></td>
                                <td><?php echo $notification['user_type']; ?></td>
                                <td><?php echo substr($notification['message'], 0, 50); ?>...</td>
                                <td><?php echo $notification['notification_date']; ?></td>
                                <td><?php echo $notification['status']; ?></td>
                                <td>
                                    <?php if ($notification['status'] === 'Unread'): ?>
                                        <a href="?mark_as_read=<?php echo $notification['notification_id']; ?>">Mark as Read</a>
                                    <?php else: ?>
                                        <a href="?mark_as_unread=<?php echo $notification['notification_id']; ?>">Mark as Unread</a>
                                    <?php endif; ?>
                                    <a href="?delete=<?php echo $notification['notification_id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>

</html>