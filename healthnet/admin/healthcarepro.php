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

$message = "";

// Add healthcare provider logic
if (isset($_POST['add_provider'])) {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $specialization = $_POST['specialization'];

    $sql = "INSERT INTO healthcare_providers (first_name, middle_name, last_name, email, password, phone, gender, specialization) 
            VALUES ('$first_name', '$middle_name', '$last_name', '$email', '$password', '$phone', '$gender', '$specialization')";
    if ($conn->query($sql) === TRUE) {
        $message = "Provider added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Edit healthcare provider logic
if (isset($_POST['edit_provider'])) {
    $provider_id = $_POST['provider_id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $specialization = $_POST['specialization'];

    $sql = "UPDATE healthcare_providers SET 
            first_name='$first_name', middle_name='$middle_name', last_name='$last_name', email='$email',
            phone='$phone', gender='$gender', specialization='$specialization'
            WHERE provider_id='$provider_id'";

    if ($conn->query($sql) === TRUE) {
        $message = "Provider updated successfully!";
        header("Location: healthcarepro.php");
        exit();
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch all healthcare providers
function fetchProviders($conn)
{
    $sql = "SELECT * FROM healthcare_providers";
    return $conn->query($sql);
}

$providers = fetchProviders($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare Providers</title>
    <link rel="icon" href="../icons/logo.png">
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="healthcarepro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styling for the Edit Form */
        .edit-form input[type="text"],
        .edit-form input[type="email"],
        .edit-form select {
            width: 18%;
            margin: 5px;
            padding: 8px;
        }

        .edit-form button {
            margin: 5px;
            padding: 10px 15px;
            font-size: 14px;
            color: #fff;
            background-color: #3498DB;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-form button:hover {
            background-color: #2980B9;
        }

        /* Styling for the message popup */
        .message-container {
            display: <?php echo $message ? 'flex' : 'none'; ?>;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            transition: opacity 0.3s ease;
        }

        .message-box {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 18px;
            color: #333;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if ($message): ?>
            <div class="message-container" id="message-container">
                <div class="message-box">
                    <?php echo $message; ?>
                </div>
            </div>
        <?php endif; ?>

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
                <h1>Manage Healthcare Providers</h1>
                <form method="POST">
                    <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </header>

            <!-- Add Provider Form -->
            <section class="form-section">
                <h2>Add Healthcare Provider</h2>
                <form method="POST">
                    <input type="text" name="first_name" placeholder="First Name" required>
                    <input type="text" name="middle_name" placeholder="Middle Name">
                    <input type="text" name="last_name" placeholder="Last Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="text" name="phone" placeholder="Phone">
                    <select name="gender" required>
                        <option value="">Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" name="specialization" placeholder="Specialization">
                    <button type="submit" name="add_provider">Add Provider</button>
                </form>
            </section>

            <!-- Provider List and Edit Section -->
            <section class="table-section">
                <h2 style="text-align: center;">List of Healthcare Providers</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Specialization</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($provider = $providers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $provider['provider_id']; ?></td>
                                <td><?php echo $provider['first_name'] . ' ' . $provider['last_name']; ?></td>
                                <td><?php echo $provider['email']; ?></td>
                                <td><?php echo $provider['phone']; ?></td>
                                <td><?php echo $provider['gender']; ?></td>
                                <td><?php echo $provider['specialization']; ?></td>
                                <td><?php echo $provider['date_created']; ?></td>
                                <td>
                                    <a href="?edit=<?php echo $provider['provider_id']; ?>">Edit</a>
                                    <a href="delete_provider.php?id=<?php echo $provider['provider_id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                            <?php if (isset($_GET['edit']) && $_GET['edit'] == $provider['provider_id']): ?>
                                <tr class="edit-form">
                                    <td colspan="8">
                                        <form method="POST">
                                            <input type="hidden" name="provider_id" value="<?php echo $provider['provider_id']; ?>">
                                            <input type="text" name="first_name" value="<?php echo $provider['first_name']; ?>" required>
                                            <input type="text" name="middle_name" value="<?php echo $provider['middle_name']; ?>">
                                            <input type="text" name="last_name" value="<?php echo $provider['last_name']; ?>" required>
                                            <input type="email" name="email" value="<?php echo $provider['email']; ?>" required>
                                            <input type="text" name="phone" value="<?php echo $provider['phone']; ?>">
                                            <select name="gender" required>
                                                <option value="Male" <?php if ($provider['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                                <option value="Female" <?php if ($provider['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                                <option value="Other" <?php if ($provider['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                                            </select>
                                            <input type="text" name="specialization" value="<?php echo $provider['specialization']; ?>">
                                            <button type="submit" name="edit_provider">Save Changes</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <script>
        // Hide message container after 3 seconds
        setTimeout(function() {
            const messageContainer = document.getElementById('message-container');
            if (messageContainer) {
                messageContainer.style.opacity = '0';
                setTimeout(() => messageContainer.style.display = 'none', 500);
            }
        }, 3000);
    </script>
</body>

</html>