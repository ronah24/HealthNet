<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f4f4f9;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
            color: #555;
        }
        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 15px;
        }
        button:hover {
            background-color: #218838;
        }
        .success, .error {
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            margin-bottom: 15px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Admin Registration Form</h2>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Database connection
        $conn = new mysqli("localhost", "root", "", "healthnet"); // replace with your DB credentials

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Sanitize and collect form data
        $first_name = $conn->real_escape_string($_POST['first_name']);
        $middle_name = $conn->real_escape_string($_POST['middle_name']);
        $last_name = $conn->real_escape_string($_POST['last_name']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $phone = $conn->real_escape_string($_POST['phone']);
        $gender = $conn->real_escape_string($_POST['gender']);

        // Insert data into the database
        $sql = "INSERT INTO Admins (first_name, middle_name, last_name, email, password, phone, gender) 
                VALUES ('$first_name', '$middle_name', '$last_name', '$email', '$password', '$phone', '$gender')";

        if ($conn->query($sql) === TRUE) {
            echo "<div class='success'>Admin registered successfully!</div>";
        } else {
            echo "<div class='error'>Error: " . $conn->error . "</div>";
        }

        $conn->close();
    }
    ?>

    <form action="" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required maxlength="50">

        <label for="middle_name">Middle Name:</label>
        <input type="text" id="middle_name" name="middle_name" maxlength="50">

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required maxlength="50">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required maxlength="100">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" maxlength="15">

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <button type="submit">Register Admin</button>
    </form>
</div>
</body>
</html>
