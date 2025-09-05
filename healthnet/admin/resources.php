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

// Add health resource logic
if (isset($_POST['add_resource'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $file_type = $_POST['file_type'];
    $file_url = $_POST['file_url'];
    $status = $_POST['status'];

    $sql = "INSERT INTO health_education_resources (title, content, category_id, file_type, file_url, status) 
            VALUES ('$title', '$content', '$category_id', '$file_type', '$file_url', '$status')";
    if ($conn->query($sql) === TRUE) {
        $message = "Resource added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Edit health resource logic
if (isset($_POST['edit_resource'])) {
    $resource_id = $_POST['resource_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $file_type = $_POST['file_type'];
    $file_url = $_POST['file_url'];
    $status = $_POST['status'];

    $sql = "UPDATE health_education_resources SET 
            title='$title', content='$content', category_id='$category_id', file_type='$file_type', 
            file_url='$file_url', status='$status' WHERE resource_id='$resource_id'";

    if ($conn->query($sql) === TRUE) {
        $message = "Resource updated successfully!";
        header("Location: health_resources.php");
        exit();
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Delete health resource logic
if (isset($_GET['delete'])) {
    $resource_id = $_GET['delete'];
    $sql = "DELETE FROM health_education_resources WHERE resource_id='$resource_id'";
    if ($conn->query($sql) === TRUE) {
        $message = "Resource deleted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch categories for dropdown
function fetchCategories($conn)
{
    $sql = "SELECT * FROM categories";
    return $conn->query($sql);
}

// Fetch all health resources
function fetchResources($conn)
{
    $sql = "SELECT * FROM health_education_resources";
    return $conn->query($sql);
}

$categories = fetchCategories($conn);
$resources = fetchResources($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Resources</title>
    <link rel="icon" href="../icons/logo.png">
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="healthcarepro.css">
    <link rel="stylesheet" href="resources.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Message display style */
        .message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            font-size: 16px;
            z-index: 1000;
            display: none;
        }

        .message.error {
            background-color: #f44336;
        }

        /* Timeout to hide message after 3 seconds */
        @keyframes fadeOut {
            0% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }
    </style>
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
                    <li><a href="health_resources.php"><i class="fas fa-book-medical"></i> Health Resources</a></li>
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
                <h1>Manage Health Resources</h1>
                <form method="POST">
                    <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </header>

            <!-- Add Resource Form -->
            <section class="form-section">
                <h2 style="text-align: center;">Add Health Resource</h2>
                <?php if (isset($message)) echo "<p id='message' class='message " . (strpos($message, 'Error') === false ? '' : 'error') . "'>$message</p>"; ?>
                <form method="POST">
                    <input type="text" name="title" placeholder="Title" required>
                    <textarea class="contents" name="content" placeholder="Content" required></textarea>
                    <select name="category_id" required>
                        <option value="">Select Category</option>
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <select name="file_type" required>
                        <option value="article">Article</option>
                        <option value="video">Video</option>
                        <option value="pdf">PDF</option>
                    </select>
                    <input type="url" name="file_url" placeholder="File URL" required>
                    <select name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <button type="submit" name="add_resource">Add Resource</button>
                </form>
            </section>

            <!-- Resource List and Edit Section -->
            <section class="table-section">
                <h2>List of Health Resources</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>File Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($resource = $resources->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $resource['resource_id']; ?></td>
                                <td><?php echo $resource['title']; ?></td>
                                <td><?php echo $resource['category_id']; // Category Name fetched separately 
                                    ?></td>
                                <td><?php echo $resource['file_type']; ?></td>
                                <td><?php echo $resource['status']; ?></td>
                                <td>
                                    <a href="?edit=<?php echo $resource['resource_id']; ?>">Edit</a>
                                    <a href="?delete=<?php echo $resource['resource_id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                            <?php if (isset($_GET['edit']) && $_GET['edit'] == $resource['resource_id']): ?>
                                <tr>
                                    <td colspan="6">
                                        <form method="POST" class="edit-form">
                                            <input type="hidden" name="resource_id" value="<?php echo $resource['resource_id']; ?>">
                                            <input type="text" name="title" value="<?php echo $resource['title']; ?>" required>
                                            <textarea name="content"><?php echo $resource['content']; ?></textarea>
                                            <select name="category_id" required>
                                                <option value="<?php echo $resource['category_id']; ?>"><?php echo $resource['category_id']; ?></option>
                                            </select>
                                            <input type="url" name="file_url" value="<?php echo $resource['file_url']; ?>" required>
                                            <button type="submit" name="edit_resource">Save Changes</button>
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
        // Show message for 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const message = document.getElementById('message');
            if (message) {
                message.style.display = 'block';
                setTimeout(function() {
                    message.style.animation = 'fadeOut 1s forwards';
                    setTimeout(function() {
                        message.style.display = 'none';
                    }, 1000);
                }, 3000);
            }
        });
    </script>
</body>

</html>