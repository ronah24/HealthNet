<?php
session_start();
require 'config.php'; // Ensure this file contains the connection to the database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['username'];
    $password = $_POST['password'];

    // Placeholder variable for role
    $role = '';

    // Function to verify user and return role
    function verifyUser($email, $password, $table, $roleName, $connection)
    {
        $query = "SELECT * FROM $table WHERE email = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row[$roleName . '_id'];
                $_SESSION['role'] = $roleName;
                return $roleName;
            }
        }
        return false;
    }

    // Check each table and role
    $connection = new mysqli("localhost", "root", "", "healthnet"); // Update as necessary
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Check Patients
    if (!$role) {
        $role = verifyUser($email, $password, 'Patients', 'patient', $connection);

        // Store patient_id if login is successful
        if ($role) {
            $query = "SELECT patient_id FROM Patients WHERE email = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($patient_id);
            $stmt->fetch();
            $_SESSION['patient_id'] = $patient_id; // Store patient_id in the session
        }
    }

    // Check Healthcare Providers
    if (!$role) {
        $role = verifyUser($email, $password, 'Healthcare_Providers', 'provider', $connection);
    }

    // Check Admins
    if (!$role) {
        $role = verifyUser($email, $password, 'Admins', 'admin', $connection);
    }

    if ($role) {
        // Redirect based on role
        switch ($role) {
            case 'admin':
                header("Location: admin/admin_dashboard.php");
                break;
            case 'provider':
                header("Location: healthcareprovider/provider_dashboard.php");
                break;
            case 'patient':
                header("Location: patient/index.php");
                break;
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="icons/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Health Net Login</title>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <h2>Login to Health Net</h2>
            <?php if (isset($error)) : ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="username"><i class="fas fa-user"></i> Email</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
            <p class="signup-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>
</body>

</html>