<?php
require 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middle_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];

    // Insert the new patient into the database
    $query = "INSERT INTO Patients (first_name, middle_name, last_name, email, password, phone, gender, dob)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ssssssss", $firstName, $middleName, $lastName, $email, $password, $phone, $gender, $dob);

    if ($stmt->execute()) {
        header("Location: login.php?signup=success");
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="signup.css">
    <link rel="icon" href="icons/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Sign Up</title>
</head>

<body>
    <div class="signup-container">
        <div class="signup-card">
            <h2>Create Your Account</h2>
            <?php if (isset($error)) : ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="signup.php" method="POST">
                <div class="input-group">
                    <label for="first_name"><i class="fas fa-user"></i> First Name</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div class="input-group">
                    <label for="middle_name"><i class="fas fa-user"></i> Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name">
                </div>
                <div class="input-group">
                    <label for="last_name"><i class="fas fa-user"></i> Last Name</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                <div class="input-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone</label>
                    <input type="text" id="phone" name="phone">
                </div>
                <div class="input-group">
                    <label for="gender"><i class="fas fa-venus-mars"></i> Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="dob"><i class="fas fa-calendar"></i> Date of Birth</label>
                    <input type="date" id="dob" name="dob" required>
                </div>
                <button type="submit" class="btn-signup">Sign Up</button>
            </form>
            <p class="login-link">Already have an account? <a href="login.php">Log In</a></p>
        </div>
    </div>
</body>

</html>