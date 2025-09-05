<?php
session_start();

// Check if the user is logged in as a patient
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
   header("Location: login.php");
   exit();
}

// Check if patient_id is available in the session
if (!isset($_SESSION['patient_id'])) {
   die("Error: Patient ID not found. Please log in.");
}

$patient_id = $_SESSION['patient_id'];

// Database connection
$connection = new mysqli("localhost", "root", "", "healthnet"); // Update as necessary
if ($connection->connect_error) {
   die("Connection failed: " . $connection->connect_error);
}

// Fetch appointments for the logged-in patient
$sql = "SELECT a.*, p.first_name, p.last_name, p.specialization
        FROM appointments a
        LEFT JOIN healthcare_providers p ON a.provider_id = p.provider_id
        WHERE a.patient_id = ? 
        ORDER BY a.appointment_date DESC, a.time_slot DESC";

$stmt = $connection->prepare($sql);
if ($stmt === false) {
   die("Error preparing statement: " . $connection->error);
}

$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result_appointments = $stmt->get_result(); // Assign query result to variable

// Check if the query executed correctly
if ($result_appointments === false) {
   die("Error executing query: " . $stmt->error);
}

$appointments = $result_appointments->fetch_all(MYSQLI_ASSOC);

// Handle new appointment booking (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment'])) {
   $provider_id = $_POST['provider_id'];
   $appointment_date = $_POST['appointment_date'];
   $time_slot = $_POST['time_slot'];

   // Insert new appointment into the database
   $sql_insert = "INSERT INTO appointments (patient_id, provider_id, appointment_date, time_slot, status)
   VALUES (?, ?, ?, ?, 'Scheduled')";
   $stmt_insert = $connection->prepare($sql_insert);
   $stmt_insert->bind_param("iiss", $patient_id, $provider_id, $appointment_date, $time_slot);

   if ($stmt_insert->execute()) {
      $message = "Appointment booked successfully!";
   } else {
      $message = "Error booking appointment: " . $stmt_insert->error;
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Appointments</title>
   <link rel="icon" href="../icons/logo.png">
   <link rel="stylesheet" href="index.css">
   <link rel="stylesheet" href="appointments.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
   <!-- Navbar Section -->
   <div class="container">
      <header>
         <div class="logo">
            <h1><i class="fas fa-heartbeat"></i> Health Net</h1>
         </div>
         <nav class="header-nav">
            <ul>
               <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
               <li><a href="healthedu.php"><i class="fa-solid fa-bell-concierge"></i> Health-Edu</a></li>
               <li><a href="appointments.php" class="active"><i class="fa-regular fa-calendar-check"></i> Appointments</a></li>
               <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
               <li><a href="account.php"><i class="fas fa-user"></i> Account</a></li>
               <li class="btn-logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
         </nav>
         <div class="header-right">
            <button class="menu-toggle"><i class="fas fa-bars"></i></button>
         </div>
      </header>

      <nav class="sidebar" id="sidebar">
         <button class="close-btn"><i class="fas fa-times"></i></button>
         <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="healthedu.php"><i class="fa-solid fa-bell-concierge"></i> Health-Edu</a></li>
            <li><a href="appointments.php" class="active"><i class="fa-regular fa-calendar-check"></i> Appointments</a></li>
            <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><a href="account.php"><i class="fas fa-user"></i> Account</a></li>
            <li class="btn-logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
         </ul>
      </nav>

      <!-- Main Content Section -->
      <main>
         <section class="appointments-section">
            <div class="contain">
               <?php if (isset($message)) {
                  echo "<p class='message'>$message</p>";
               } ?>

               <!-- Appointment Listing -->
               <h2>Upcoming Appointments</h2>
               <div class="appointments-list">
                  <?php if ($appointments && count($appointments) > 0): ?>
                     <table>
                        <thead>
                           <tr>
                              <th>Provider</th>
                              <th>Date</th>
                              <th>Time</th>
                              <th>Status</th>
                              <th>Notes</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php foreach ($appointments as $row): ?>
                              <tr>
                                 <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                 <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                                 <td><?php echo htmlspecialchars($row['time_slot']); ?></td>
                                 <td><?php echo htmlspecialchars($row['status']); ?></td>
                                 <td><?php echo htmlspecialchars(substr($row['notes'], 0, 50)); ?>...</td>
                              </tr>
                           <?php endforeach; ?>
                        </tbody>
                     </table>
                  <?php else: ?>
                     <p>No upcoming appointments.</p>
                  <?php endif; ?>
               </div>


               <!-- Book a New Appointment -->
               <h2>Book a New Appointment</h2>
               <form method="POST" action="appointments.php">
                  <label for="provider_id">Choose a Provider</label>
                  <select id="provider_id" name="provider_id" required>
                     <option value="" disabled selected>Select a provider</option>
                     <?php
                     $sql_providers = "SELECT * FROM healthcare_providers";
                     $result_providers = $connection->query($sql_providers);

                     if ($result_providers && $result_providers->num_rows > 0) {
                        while ($provider = $result_providers->fetch_assoc()) {
                           echo "<option value='" . $provider['provider_id'] . "'>" . htmlspecialchars($provider['first_name'] . ' ' . $provider['last_name']) . "</option>";
                        }
                     } else {
                        echo "<option value=''>No providers available</option>";
                     }
                     ?>
                  </select>

                  <label for="appointment_date">Select Date</label>
                  <input type="date" id="appointment_date" name="appointment_date" required>

                  <label for="time_slot">Select Time</label>
                  <input type="time" id="time_slot" name="time_slot" required>

                  <button type="submit" name="book_appointment" class="btn">Book Appointment</button>
               </form>
            </div>
         </section>
      </main>

      <footer>
         <div class="footer-content">
            <p>&copy; 2024 HealthNet. All rights reserved.</p>
         </div>
      </footer>
   </div>

   <script src="script.js"></script>
</body>

</html>