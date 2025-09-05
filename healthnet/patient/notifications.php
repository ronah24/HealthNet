<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
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

// Logout functionality
if (isset($_POST['logout'])) {
   session_destroy();
   header('Location: index.php');
   exit();
}

// Fetch healthcare providers based on specialization
$specialization_filter = isset($_POST['specialization']) ? $_POST['specialization'] : '';

// Query to fetch providers based on specialization
$providers_query = $conn->query("SELECT * FROM healthcare_providers WHERE specialization LIKE '%$specialization_filter%'");

if (!$providers_query) {
   die("Error fetching providers: " . $conn->error);
}

// Fetch notifications for the logged-in patient
$patient_id = $_SESSION['user_id'];
$notifications = $conn->query("SELECT * FROM notifications WHERE user_id = '$patient_id' AND user_type = 'Patient' ORDER BY notification_date DESC");

// Fetch messages if a provider is selected
$selected_provider_id = isset($_GET['provider_id']) ? $_GET['provider_id'] : null;
$chat_history = [];

if ($selected_provider_id) {
   $chat_history_query = $conn->query("SELECT * FROM messages WHERE 
        (sender_id = '$patient_id' AND receiver_id = '$selected_provider_id' AND user_type = 'Patient') 
        OR (sender_id = '$selected_provider_id' AND receiver_id = '$patient_id' AND user_type = 'Provider') 
        ORDER BY timestamp ASC");

   if ($chat_history_query) {
      while ($message = $chat_history_query->fetch_assoc()) {
         $chat_history[] = $message;
      }
   } else {
      die("Error fetching chat history: " . $conn->error);
   }
}

// Sending a new message
if (isset($_POST['send_message']) && !empty($_POST['message_content']) && $selected_provider_id) {
   $message_content = $conn->real_escape_string($_POST['message_content']);
   $conn->query("INSERT INTO messages (sender_id, receiver_id, user_type, message_content, status) 
                  VALUES ('$patient_id', '$selected_provider_id', 'Patient', '$message_content', 'Unread')");
   header("Location: notifications.php?provider_id=$selected_provider_id");
   exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Notifications</title>
   <link rel="icon" href="../icons/logo.png">
   <link rel="stylesheet" href="index.css">
   <link rel="stylesheet" href="notifications.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
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

      <!-- Main Content -->
      <main class="thing">
         <!-- Specialization Filter Section -->
         <!-- <section class="specialization-filter">
            <form method="POST">
               abel for="specialization">Select Specialization: </label>
               <select name="specialization" id="specialization">
                  <option value="">Select...</option>
                  <option value="Cardiologist">Cardiologist</option>
                  <option value="Dermatologist">Dermatologist</option>
                  <option value="Pediatrician">Pediatrician</option>
                  <option value="General Practitioner">General Practitioner</option>
               </select>
               <button type="submit">Filter Providers</button>
            </form>
         </section> -->

         <!-- Providers Section -->
         <section class="providers-section">
            <h2 style="color:#fff;">Providers</h2>
            <?php if ($providers_query->num_rows > 0): ?>
               <ul class="providers-list">
                  <?php while ($provider = $providers_query->fetch_assoc()): ?>
                     <li class="provider-item">
                        <span><?php echo $provider['first_name'] . ' ' . $provider['last_name']; ?> (<?php echo $provider['specialization']; ?>)</span>
                        <a href="notifications.php?provider_id=<?php echo $provider['provider_id']; ?>" class="chat-btn">Chat</a>
                     </li>
                  <?php endwhile; ?>
               </ul>
            <?php else: ?>
               <p style="color: #fff;">No providers found for this specialization.</p>
            <?php endif; ?>
         </section>

         <!-- Notifications Section -->
         <section class="notifications-section">
            <h2 style="color:#fff;">My Notifications</h2>
            <?php if ($notifications->num_rows > 0): ?>
               <ul class="notifications-list">
                  <?php while ($notification = $notifications->fetch_assoc()): ?>
                     <li class="notification-item">
                        <p><?php echo $notification['message']; ?></p>
                        <small><?php echo $notification['notification_date']; ?></small>
                        <form method="POST" style="display: inline;">
                           <button type="submit" name="mark_read" value="<?php echo $notification['notification_id']; ?>" class="mark-read-btn">Mark as Read</button>
                        </form>
                     </li>
                  <?php endwhile; ?>
               </ul>
            <?php else: ?>
               <p style="color: #fff;">No notifications available.</p>
            <?php endif; ?>
         </section>

         <!-- Chat Section -->
         <section class="chat-section">
            <h3 style="color:#fff;">Chat with Provider</h3>
            <?php if ($selected_provider_id && !empty($chat_history)): ?>
               <div class="chat-history">
                  <?php foreach ($chat_history as $message): ?>
                     <div class="message <?php echo $message['user_type'] === 'Patient' ? 'patient' : 'provider'; ?>">
                        <p><?php echo nl2br($message['message_content']); ?></p>
                        <small><?php echo $message['timestamp']; ?></small>
                     </div>
                  <?php endforeach; ?>
               </div>

               <!-- Send Message Form -->
               <form method="POST" class="send-message-form">
                  <input type="text" name="message_content" placeholder="Type your message..." required>
                  <button type="submit" name="send_message" class="send-message-btn">Send</button>
               </form>
            <?php else: ?>
               <p style="color:#fff;">No conversation selected.</p>
            <?php endif; ?>
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