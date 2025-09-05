<?php
// Start a session if needed for login or other features.
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <link rel="icon" href="icons/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Health Net</title>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <header>
            <div class="logo">
                <h1><i class="fas fa-heartbeat"></i> Health Net</h1>
            </div>
            <nav class="header-nav">
                <ul>
                    <li><a href="#services"><i class="fas fa-stethoscope"></i> Services</a></li>
                    <li><a href="#about"><i class="fas fa-info-circle"></i> About Us</a></li>
                    <li><a href="#contact"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="login.php" class="btn-login"><i class="fas fa-user"></i> Login</a></li>
                </ul>
            </nav>
            <div class="header-right">
                <button class="menu-toggle"><i class="fas fa-bars"></i></button>
            </div>
        </header>

        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <button class="close-btn"><i class="fas fa-times"></i></button>
            <ul>
                <li><a href="#services"><i class="fas fa-stethoscope"></i> Services</a></li>
                <li><a href="#about"><i class="fas fa-info-circle"></i> About Us</a></li>
                <li><a href="#contact"><i class="fas fa-envelope"></i> Contact</a></li>
                <li><a href="#login"><i class="fas fa-user"></i> Login</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main>
            <!-- Hero Section -->
            <section class="hero">
                <h2>Your Health, Our Commitment</h2>
                <h3>
                    <p>Providing accessible health services tailored for you.</p>
                </h3>
            </section>

            <!-- Services Section -->
            <section id="services" class="section">
                <h3>Our Services</h3>
                <div class="service-cards">
                    <div class="card">
                        <i class="fas fa-calendar-alt"></i>
                        <h4>Appointment Scheduling</h4>
                        <p>Schedule your health appointments quickly and easily.</p>
                    </div>
                    <div class="card">
                        <i class="fas fa-notes-medical"></i>
                        <h4>Health Records Management</h4>
                        <p>Securely manage your health records in one place.</p>
                    </div>
                    <div class="card">
                        <i class="fas fa-comments"></i>
                        <h4>Telemedicine</h4>
                        <p>Consult with healthcare providers from the comfort of your home.</p>
                    </div>
                </div>
            </section>

            <!-- Other Sections (Testimonials, FAQ, Contact, etc.) -->
            <section id="testimonials" class="section">
                <h3>What Our Patients Say</h3>
                <blockquote>
                    <p>"Health Net made managing my appointments so easy!"</p>
                    <footer>- Jane Doe</footer>
                </blockquote>
            </section>

            <section id="faq" class="section">
                <h3>Frequently Asked Questions</h3>
                <ul>
                    <li><strong>How do I schedule an appointment?</strong> You can schedule an appointment through our appointment portal.</li>
                    <li><strong>What services do you offer?</strong> We offer a variety of services including telemedicine and health record management.</li>
                </ul>
            </section>

            <section id="contact" class="section">
                <h3>Contact Us</h3>
                <form>
                    <input type="text" placeholder="Your Name" required>
                    <input type="email" placeholder="Your Email" required>
                    <textarea placeholder="Your Message" required></textarea>
                    <button type="submit">Send Message</button>
                </form>
            </section>
        </main>
    </div>

    <!-- Sidebar Toggle Script -->
    <script>
        // Function to toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        // Event listeners for sidebar toggle
        document.querySelector('.menu-toggle').addEventListener('click', toggleSidebar);
        document.querySelector('.close-btn').addEventListener('click', toggleSidebar);
    </script>
</body>

</html>