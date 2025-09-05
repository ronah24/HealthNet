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

// Delete record logic
if (isset($_GET['delete'])) {
    $record_id = $_GET['delete'];
    $sql = "DELETE FROM health_records_management WHERE record_id='$record_id'";
    if ($conn->query($sql) === TRUE) {
        $message = "Record deleted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch health records
function fetchHealthRecords($conn, $search = null)
{
    $sql = "SELECT * FROM health_records_management";
    if ($search) {
        $sql .= " WHERE patient_id LIKE '%$search%' OR description LIKE '%$search%' OR date_uploaded LIKE '%$search%'";
    }
    return $conn->query($sql);
}

// Handle search functionality
$search = isset($_POST['search']) ? $_POST['search'] : '';
$records = fetchHealthRecords($conn, $search);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Records Management</title>
    <link rel="icon" href="../icons/logo.png">
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="healthcarepro.css">
    <link rel="stylesheet" href="records.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Centered message display */
        .message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }
    </style>
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
                <h1>Manage Health Records</h1>
                <form method="POST">
                    <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </header>

            <!-- Search and Message Section -->
            <?php if (isset($message)) echo "<div class='message'>$message</div>"; ?>
            <form method="POST" style="text-align: center;" class="recordsfrm">
                <input type="text" name="search" placeholder="Search records by patient ID ..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>

            <!-- Health Records Table -->
            <section class="table-section">
                <h2>List of Health Records</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient ID</th>
                            <th>Description</th>
                            <th>File</th>
                            <th>Date Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($record = $records->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $record['record_id']; ?></td>
                                <td><?php echo $record['patient_id']; ?></td>
                                <td><?php echo $record['description']; ?></td>
                                <td><a href="<?php echo $record['file_path']; ?>" target="_blank">View File</a></td>
                                <td><?php echo $record['date_uploaded']; ?></td>
                                <td>
                                    <a href="?delete=<?php echo $record['record_id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>

<script>
    // Display and auto-hide messages
    document.addEventListener('DOMContentLoaded', function() {
        let message = document.querySelector('.message');
        if (message) {
            message.style.display = 'block';
            setTimeout(() => message.style.display = 'none', 3000);
        }
    });
</script>

</html>