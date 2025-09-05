<?php
// Start the session to track user login
session_start();

// Check if the user is logged in as a patient
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
   header("Location: login.php");
   exit();
}

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthnet";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}

// Fetch categories for the dropdown
$categories = [];
$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);
if ($result_categories->num_rows > 0) {
   while ($row = $result_categories->fetch_assoc()) {
      $categories[] = $row;
   }
}

// Fetch resources based on selected category
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$sql_resources = "SELECT r.*, c.category_name 
                  FROM health_education_resources r
                  LEFT JOIN categories c ON r.category_id = c.category_id
                  WHERE r.status = 'active'";
if ($category_id) {
   $sql_resources .= " AND r.category_id = $category_id";
}
$sql_resources .= " ORDER BY r.date_published DESC";

$result_resources = $conn->query($sql_resources);
$resources = $result_resources->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Health-Edu</title>
   <link rel="icon" href="../icons/logo.png">
   <link rel="stylesheet" href="index.css">
   <link rel="stylesheet" href="healthedu.css">
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
               <li><a href="healthedu.php" class="active"><i class="fa-solid fa-bell-concierge"></i> Health-Edu</a></li>
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
            <li><a href="healthedu.php" class="active"><i class="fa-solid fa-bell-concierge"></i> Health-Edu</a></li>
            <li><a href="prescriptions.php"><i class="fa-regular fa-calendar-check"></i> Appointments</a></li>
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
                  <h1>Health-Edu Resources</h1>
                  <p>Explore educational resources to enhance your well-being.</p>
               </div>
               <div class="categories">
                  <h3>Categories</h3>
                  <form action="healthedu.php" method="GET">
                     <select name="category_id" id="category_id">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                           <option value="<?php echo $category['category_id']; ?>" <?php echo ($category['category_id'] == $category_id) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($category['category_name']); ?>
                           </option>
                        <?php endforeach; ?>
                     </select>
                     <button type="submit" class="btn-filter">Filter</button>
                  </form>
               </div>
               <div class="feature-cards">
                  <?php if (!empty($resources)): ?>
                     <?php foreach ($resources as $resource): ?>
                        <div class="card">
                           <h3>
                              <i class="<?php echo $resource['file_type'] === 'video' ? 'fas fa-video' : ($resource['file_type'] === 'pdf' ? 'fas fa-file-pdf' : 'fas fa-file-alt'); ?>"></i>
                              <?php echo htmlspecialchars($resource['title']); ?>
                           </h3>
                           <p><?php echo htmlspecialchars(substr($resource['content'], 0, 100)); ?>...</p>
                           <a href="<?php echo $resource['file_url']; ?>" class="btn" target="_blank">
                              <?php echo $resource['file_type'] === 'pdf' ? 'Download PDF' : 'View Resource'; ?>
                           </a>
                        </div>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <p>No resources available in this category.</p>
                  <?php endif; ?>
               </div>
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