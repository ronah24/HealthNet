<?php
// Start the session to track user login
session_start();

// Check if the user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
   header("Location: login.php");
   exit();
}

// Fetch user information from the database (for dynamic content)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthnet";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}

$patient_id = $_SESSION['user_id'];
$sql = "SELECT * FROM patients WHERE patient_id = $patient_id";
$result = $conn->query($sql);
$patient = $result->fetch_assoc();

// Handle form submission for updating patient details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $first_name = $_POST['first_name'];
   $middle_name = $_POST['middle_name'];
   $last_name = $_POST['last_name'];
   $email = $_POST['email'];
   $phone = $_POST['phone'];
   $gender = $_POST['gender'];
   $dob = $_POST['dob'];
   $password = $_POST['password'];

   // Update the database with the new information
   $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Secure password hashing
   $update_sql = "UPDATE patients SET first_name = '$first_name', middle_name = '$middle_name', last_name = '$last_name', email = '$email', phone = '$phone', gender = '$gender', dob = '$dob', password = '$hashed_password' WHERE patient_id = $patient_id";

   if ($conn->query($update_sql) === TRUE) {
      echo "<script>alert('Account details updated successfully.'); window.location.href='account.php';</script>";
   } else {
      echo "<script>alert('Error updating account details.');</script>";
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>HealthNet - Account</title>
   <link rel="icon" href="../icons/logo.png">
   <link rel="stylesheet" href="index.css">
   <link rel="stylesheet" href="account.css">
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
               <li><a href="appointments.php"><i class="fa-regular fa-calendar-check"></i> Appointments</a></li>
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
            <li><a href="prescriptions.php"><i class="fa-regular fa-calendar-check"></i> Appointments</a></li>
            <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><a href="account.php"><i class="fas fa-user"></i> Account</a></li>
            <li class="btn-logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
         </ul>
      </nav>

      <!-- Main Content Section -->
      <main>
         <section class="account-section">
            <div class="contain">
               <form method="POST" action="account.php">
                  <div class="form-group">
                     <label for="first_name">First Name</label>
                     <input type="text" id="first_name" name="first_name" value="<?php echo $patient['first_name']; ?>" required>
                  </div>
                  <div class="form-group">
                     <label for="middle_name">Middle Name</label>
                     <input type="text" id="middle_name" name="middle_name" value="<?php echo $patient['middle_name']; ?>">
                  </div>
                  <div class="form-group">
                     <label for="last_name">Last Name</label>
                     <input type="text" id="last_name" name="last_name" value="<?php echo $patient['last_name']; ?>" required>
                  </div>
                  <div class="form-group">
                     <label for="email">Email</label>
                     <input type="email" id="email" name="email" value="<?php echo $patient['email']; ?>" required>
                  </div>
                  <div class="form-group">
                     <label for="phone">Phone</label>
                     <input type="tel" id="phone" name="phone" value="<?php echo $patient['phone']; ?>" required>
                  </div>
                  <div class="form-group">
                     <label for="gender">Gender</label>
                     <select id="gender" name="gender" required>
                        <option value="Male" <?php echo ($patient['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($patient['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($patient['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="dob">Date of Birth</label>
                     <input type="date" id="dob" name="dob" value="<?php echo $patient['dob']; ?>" required>
                  </div>
                  <div class="form-group">
                     <label for="password">New Password</label>
                     <input type="password" id="password" name="password" placeholder="Enter new password">
                  </div>
                  <button type="submit" class="btn">Update Details</button>
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