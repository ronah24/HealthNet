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

// Fetch health records
$provider_id = $_SESSION['user_id'];
$records = $conn->query("SELECT hr.*, p.first_name, p.last_name FROM health_records_management hr
                         JOIN patients p ON hr.patient_id = p.patient_id
                         WHERE hr.uploaded_by = '$provider_id'");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Records</title>
    <link rel="icon" href="../icons/logo.png">
    <link rel="stylesheet" href="provider_dashboard.css">
    <!-- <link rel="stylesheet" href="health_records.php"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .records-section {
            margin-top: 20px;
        }

        .records-section h2 {
            color: #1e3d59;
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .records-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .records-table th,
        .records-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .records-table th {
            background-color: #1e3d59;
            color: #fff;
            font-weight: normal;
        }

        .records-table tr:hover {
            background-color: #f1f1f1;
        }

        .download-link {
            color: #1e3d59;
            text-decoration: none;
            font-weight: bold;
        }

        .download-link:hover {
            color: #345f77;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2><i class="fas fa-stethoscope"></i> HealthNet</h2>
            <nav>
                <ul>
                    <li><a href="providers_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                    <li><a href="health_records.php" class="active"><i class="fas fa-file-medical"></i> Health Records</a></li>
                    <li><a href="resources.php"><i class="fas fa-book-medical"></i> Resources</a></li>
                    <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main>
            <header>
                <h1>Health Records</h1>
                <form method="POST">
                    <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </header>

            <section class="records-section">
                <h2>Patient Health Records</h2>
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Description</th>
                            <th>Date Uploaded</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($record = $records->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $record['first_name'] . ' ' . $record['last_name']; ?></td>
                                <td><?php echo $record['description']; ?></td>
                                <td><?php echo $record['date_uploaded']; ?></td>
                                <td><a href="<?php echo $record['file_path']; ?>" target="_blank" class="download-link">View</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>

</html>