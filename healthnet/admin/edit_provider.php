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

// Get provider ID
$provider_id = $_GET['id'];

// Fetch provider details
$sql = "SELECT * FROM healthcare_providers WHERE provider_id = $provider_id";
$result = $conn->query($sql);
$provider = $result->fetch_assoc();

// Update provider logic
if (isset($_POST['update_provider'])) {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $specialization = $_POST['specialization'];

    $sql = "UPDATE healthcare_providers SET 
                first_name = '$first_name', 
                middle_name = '$middle_name', 
                last_name = '$last_name', 
                email = '$email', 
                phone = '$phone', 
                gender = '$gender', 
                specialization = '$specialization' 
            WHERE provider_id = $provider_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: admin_healthcare_providers.php?message=Provider+updated+successfully");
        exit();
    } else {
        $error = "Error updating provider: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Healthcare Provider</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>

<body>
    <div class="container">
        <h2>Edit Healthcare Provider</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="first_name" value="<?php echo $provider['first_name']; ?>" required>
            <input type="text" name="middle_name" value="<?php echo $provider['middle_name']; ?>">
            <input type="text" name="last_name" value="<?php echo $provider['last_name']; ?>" required>
            <input type="email" name="email" value="<?php echo $provider['email']; ?>" required>
            <input type="text" name="phone" value="<?php echo $provider['phone']; ?>">
            <select name="gender" required>
                <option value="Male" <?php if ($provider['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($provider['gender'] === 'Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if ($provider['gender'] === 'Other') echo 'selected'; ?>>Other</option>
            </select>
            <input type="text" name="specialization" value="<?php echo $provider['specialization']; ?>">
            <button type="submit" name="update_provider">Update Provider</button>
        </form>
    </div>
</body>

</html>