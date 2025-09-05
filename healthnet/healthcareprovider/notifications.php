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

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ../index.php');
    exit();
}

// Fetch notifications for the logged-in provider
$provider_id = $_SESSION['user_id'];
$notifications = $conn->query("SELECT * FROM notifications WHERE user_id = '$provider_id' AND user_type = 'Provider' ORDER BY notification_date DESC");

// Check for errors in the notifications query
if (!$notifications) {
    die("Error executing notifications query: " . $conn->error);
}

// Mark notifications as read
if (isset($_POST['mark_read'])) {
    $notification_id = $_POST['mark_read'];
    $conn->query("UPDATE notifications SET status = 'Read' WHERE notification_id = '$notification_id'");
    header("Location: notifications.php");
    exit();
}

// Fetch active patients for initiating chat
$patients_query = $conn->query("SELECT patient_id, first_name, last_name FROM patients");
if (!$patients_query) {
    die("Error executing patients query: " . $conn->error);
}

// Initialize chat variables
$selected_patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;
$chat_history = [];

// Fetch chat history if a patient is selected
if ($selected_patient_id) {
    $chat_history_query = $conn->query("SELECT * FROM messages WHERE 
        (sender_id = '$provider_id' AND receiver_id = '$selected_patient_id' AND user_type = 'Provider') 
        OR (sender_id = '$selected_patient_id' AND receiver_id = '$provider_id' AND user_type = 'Patient') 
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
if (isset($_POST['send_message']) && !empty($_POST['message_content']) && $selected_patient_id) {
    $message_content = $conn->real_escape_string($_POST['message_content']);
    $conn->query("INSERT INTO messages (sender_id, receiver_id, user_type, message_content, status) 
                  VALUES ('$provider_id', '$selected_patient_id', 'Provider', '$message_content', 'Unread')");
    header("Location: notifications.php?patient_id=$selected_patient_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications and Chat</title>
    <link rel="icon" href="../icons/logo.png">
    <link rel="stylesheet" href="provider_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styling adjustments for notifications and chat interface */
        .notifications-section,
        .chat-section {
            margin-top: 20px;
        }

        .notifications-list,
        .patients-list {
            list-style-type: none;
            padding: 0;
        }

        .notification-item,
        .patient-item {
            background-color: #fff;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mark-read-btn,
        .chat-btn,
        .send-message-btn {
            background-color: #1e3d59;
            color: #fff;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .chat-history {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-top: 15px;
            background-color: #f9f9f9;
        }

        .message {
            margin-bottom: 10px;
            font-size: 0.9em;
        }

        .message.provider {
            text-align: right;
            color: #1e3d59;
        }

        .message.patient {
            text-align: left;
            color: #e04a4a;
        }

        .send-message-form {
            margin-top: 10px;
            display: flex;
        }

        .send-message-form input[type="text"] {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .send-message-btn {
            margin-left: 10px;
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
                    <li><a href="provider_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                    <li><a href="health_records.php"><i class="fas fa-file-medical"></i> Health Records</a></li>
                    <li><a href="resources.php"><i class="fas fa-book-medical"></i> Resources</a></li>
                    <li><a href="notifications.php" class="active"><i class="fas fa-bell"></i> Notifications</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main>
            <header>
                <h1>Notifications and Chat</h1>
                <form method="POST">
                    <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </header>

            <section class="notifications-section">
                <h2>My Notifications</h2>
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
                    <p>No notifications available.</p>
                <?php endif; ?>
            </section>

            <!-- Chat Section -->
            <section class="chat-section">
                <h3>Chat with Patients</h3>
                <ul class="patients-list">
                    <?php while ($patient = $patients_query->fetch_assoc()): ?>
                        <li class="patient-item">
                            <span><?php echo $patient['first_name'] . ' ' . $patient['last_name']; ?></span>
                            <a href="notifications.php?patient_id=<?php echo $patient['patient_id']; ?>" class="chat-btn">Chat</a>
                        </li>
                    <?php endwhile; ?>
                </ul>

                <?php if ($selected_patient_id): ?>
                    <div class="chat-history">
                        <h4>Chat History</h4>
                        <?php foreach ($chat_history as $message): ?>
                            <div class="message <?php echo $message['user_type'] === 'Provider' ? 'provider' : 'patient'; ?>">
                                <p><?php echo $message['message_content']; ?></p>
                                <small><?php echo $message['timestamp']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <form method="POST" class="send-message-form">
                        <input type="text" name="message_content" placeholder="Type your message here" required>
                        <button type="submit" name="send_message" class="send-message-btn">Send</button>
                    </form>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>

</html>