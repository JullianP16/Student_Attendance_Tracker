<?php
if(session_status()===PHP_SESSION_NONE)
{
	session_start();
}
$darkMode = isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'true';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome | Attendance Tracker</title>
    <link rel="stylesheet" href="Assets/styles.css">
    <style>
        body {
            margin: 0;
            font-family: Georgia, serif; 
            background-color: lightgreen;
        }
        .home-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 90vh;
            text-align: center;
            padding: 2rem;
        }
        .home-logo {
            width: 300px;
            margin-bottom: 1.5rem;
        }
        .home-title {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .home-subtitle {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            color: #888;
        }
        .home-buttons a {
            padding: 0.75rem 1.5rem;
            margin: 0 1rem;
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .home-buttons a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body class="<?= $darkMode ? 'dark-mode' : '' ?>">

<?php include_once 'Includes/header.php'; ?>

<div class="home-container">
    <img src="Assets/logo.png" alt="TrackMate Logo" class="home-logo">
    
    <h1 class="home-title">Student Attendance Tracker</h1>
    <p class="home-subtitle">Track. Manage. Succeed. Your one-stop portal for attendance management.</p>
    
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Replace 'user_id' with whatever session variable you use to track login
    if (!isset($_SESSION['user_id'])): ?>
        <div class="home-buttons">
            <a href="otp_login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    <?php endif; ?>
</div>

<script src="Assets/scripts.js"></script>
</body>
</html>
