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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthNet - Home</title>
    <link rel="icon" href="../icons/logo.png">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <!-- Navbar Section -->
    <div class="container">
        <header>

            <div class="logo">
                <h1><i class="fas fa-heartbeat"></i> Health Net</h1>
            </div>
            <nav class=header-nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="healthedu.php"><i class="fa-solid fa-bell-concierge"></i> Health-Edu</a></li>
                    <li><a href="appointments.php"><i class="fa-regular fa-calendar-check"></i> appointments</a></li>
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
                <li><a href="prescriptions.php"><i class="fa-regular fa-calendar-check"></i> appointments</a></li>
                <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><a href="account.php"><i class="fas fa-user"></i> Account</a></li>
                <li class="btn-logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content Section -->
        <main>
            <section class="welcome-section">
                <div class="contain">
                    <div class="welcome-text">
                        <h1>Welcome, <?php echo $patient['first_name']; ?>!</h1>
                        <p>Your health, our priority. Explore your medical records, prescriptions, and more.</p>
                    </div>
                    <div class="feature-cards">
                        <div class="card">
                            <img src="../images/image3.webp" alt="Telemedicine">
                            <h3><i class="fas fa-video"></i> Telemedicine</h3>
                            <p>Connect with your healthcare provider for remote consultations.</p>
                            <a href="telemedicine.php" class="btn">Start Telemedicine</a>
                        </div>
                        <div class="card">
                            <img src="../images/image4.jpg" alt="Medical History">
                            <h3><i class="fas fa-book-medical"></i> Medical History</h3>
                            <p>Review your previous appointments and health records.</p>
                            <a href="medical_history.php" class="btn">View History</a>
                        </div>
                        <div class="card">
                            <img src="../images/image5.jpg" alt="Prescriptions">
                            <h3><i class="fas fa-pills"></i> Prescriptions</h3>
                            <p>Check your prescriptions and medication details.</p>
                            <a href="prescriptions.php" class="btn">View Prescriptions</a>
                        </div>
                    </div>
                </div>
            </section>
            <br><br><br>
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