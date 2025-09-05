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

// Fetch all patients or search if query is present
function fetchPatients($conn, $searchQuery = "")
{
    $sql = "SELECT * FROM patients WHERE first_name LIKE '%$searchQuery%' OR last_name LIKE '%$searchQuery%' OR email LIKE '%$searchQuery%' OR phone LIKE '%$searchQuery%'";
    return $conn->query($sql);
}

// Delete patient logic
if (isset($_GET['delete'])) {
    $patient_id = $_GET['delete'];
    $sql = "DELETE FROM patients WHERE patient_id='$patient_id'";
    if ($conn->query($sql) === TRUE) {
        $message = "Patient record deleted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch patient details for editing
if (isset($_GET['edit'])) {
    $patient_id = $_GET['edit'];
    $sql = "SELECT * FROM patients WHERE patient_id='$patient_id'";
    $result = $conn->query($sql);
    $patient = $result->fetch_assoc();
}

// Update patient details
if (isset($_POST['edit_patient'])) {
    $patient_id = $_POST['patient_id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];

    $sql = "UPDATE patients SET first_name='$first_name', middle_name='$middle_name', last_name='$last_name',
            email='$email', phone='$phone', gender='$gender', dob='$dob' WHERE patient_id='$patient_id'";

    if ($conn->query($sql) === TRUE) {
        $message = "Patient record updated successfully!";
        header("Location: patients.php");
        exit();
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle search form submission
$searchQuery = isset($_POST['search_query']) ? $_POST['search_query'] : '';
$patients = fetchPatients($conn, $searchQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management</title>
    <link rel="icon" href="../icons/logo.png">
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="healthcarepro.css">
    <link rel="stylesheet" href="patients.css">
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

        <main>
            <header>
                <h1>Manage Patients</h1>
                <form method="POST">
                    <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </header>

            <!-- Patient Search Section -->
            <section class="search-section">
                <form method="POST" class="srcfrm">
                    <input type="text" name="search_query" placeholder="Search by Name, Email, or Phone" value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                </form>
            </section>

            <!-- Patient List and Edit Section -->
            <section class="table-section">
                <h2>List of Patients</h2>
                <?php if (isset($message)) echo "<p>$message</p>"; ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Date of Birth</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($patient = $patients->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $patient['patient_id']; ?></td>
                                <td><?php echo $patient['first_name'] . " " . $patient['middle_name'] . " " . $patient['last_name']; ?></td>
                                <td><?php echo $patient['email']; ?></td>
                                <td><?php echo $patient['phone']; ?></td>
                                <td><?php echo $patient['gender']; ?></td>
                                <td><?php echo $patient['dob']; ?></td>
                                <td>
                                    <a href="?edit=<?php echo $patient['patient_id']; ?>">Edit</a>
                                    <a href="?delete=<?php echo $patient['patient_id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                            <?php if (isset($_GET['edit']) && $_GET['edit'] == $patient['patient_id']): ?>
                                <tr>
                                    <td colspan="7">
                                        <form method="POST" class="edit-form">
                                            <input type="hidden" name="patient_id" value="<?php echo $patient['patient_id']; ?>">
                                            <input type="text" name="first_name" value="<?php echo $patient['first_name']; ?>" required>
                                            <input type="text" name="middle_name" value="<?php echo $patient['middle_name']; ?>">
                                            <input type="text" name="last_name" value="<?php echo $patient['last_name']; ?>" required>
                                            <input type="email" name="email" value="<?php echo $patient['email']; ?>" required>
                                            <input type="text" name="phone" value="<?php echo $patient['phone']; ?>" required>
                                            <select name="gender" required>
                                                <option value="Male" <?php echo ($patient['gender'] == 'Male' ? 'selected' : ''); ?>>Male</option>
                                                <option value="Female" <?php echo ($patient['gender'] == 'Female' ? 'selected' : ''); ?>>Female</option>
                                                <option value="Other" <?php echo ($patient['gender'] == 'Other' ? 'selected' : ''); ?>>Other</option>
                                            </select>
                                            <input class="dob" type="date" name="dob" value="<?php echo $patient['dob']; ?>" required>
                                            <button type="submit" name="edit_patient">Update Patient</button>
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
</body>

</html>