<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

// Database connection
$servername = "localhost";
$username = "root";      // Adjust these settings as per your database configuration
$password = "";
$dbname = "healthnet";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


// Logout logic
if (isset($_POST['logout'])) {
  session_destroy();
  header('Location: ../index.php');
  exit();
}

// Fetch admin data for display in the Admin Management section
function fetchAdmins($conn)
{
  if ($conn) {
    $sql = "SELECT * FROM admins";
    $result = $conn->query($sql);
    return $result;
  } else {
    return false;
  }
}

// Fetch counts for dashboard display
function getCounts($conn)
{
  $counts = ['appointments' => 0, 'patients' => 0, 'providers' => 0, 'notifications' => 0];

  if ($conn) {
    $queries = [
      'appointments' => "SELECT COUNT(*) AS count FROM appointments",
      'patients' => "SELECT COUNT(*) AS count FROM patients",
      'providers' => "SELECT COUNT(*) AS count FROM healthcare_providers",
      'notifications' => "SELECT COUNT(*) AS count FROM notifications WHERE status = 'Unread'"
    ];

    foreach ($queries as $key => $query) {
      $result = $conn->query($query);
      if ($result && $row = $result->fetch_assoc()) {
        $counts[$key] = $row['count'];
      }
    }
  }

  return $counts;
}

$counts = getCounts($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="admin_dashboard.css">
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

    <!-- Main Content -->
    <main>
      <!-- Header -->
      <header>
        <h1>HealthNet Admin Dashboard</h1>
        <form method="POST">
          <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
      </header>

      <!-- Dashboard Content Containers -->
      <br><br><br><br><br>
      <section class="dashboard-stats">
        <div class="stat-container">
          <h3>Appointments <i class="fas fa-calendar-check"></i></h3>
          <p><?php echo $counts['appointments']; ?></p>
        </div>
        <div class="stat-container">
          <h3>Patients <i class="fas fa-users"></i></h3>
          <p><?php echo $counts['patients']; ?></p>
        </div>
        <div class="stat-container">
          <h3>Healthcare Providers <i class="fas fa-user-md"></i></h3>
          <p><?php echo $counts['providers']; ?></p>
        </div>
        <div class="stat-container">
          <h3>Unread Notifications <i class="fas fa-bell"></i></h3>
          <p><?php echo $counts['notifications']; ?></p>
        </div>
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