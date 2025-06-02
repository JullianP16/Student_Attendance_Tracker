<?php
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance System</title>
  <link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/styles.css">
</head>
<body>

<div class="topnav">
<div class="topnav-left">
    <h1 class="topnav-title">
    <a href="/Student-Attendance-Tracker/index.php">TrackMate</a>
    </h1>
  </div>
  <div class="topnav-right">
    <label class="dark-toggle">
      <input type="checkbox" id="darkModeToggle"> Dark Mode
    </label>
    <?php if (isset($_SESSION['role'])): ?>
      <a href="/Student-Attendance-Tracker/settings.php">Settings</a>
      <a href="/Student-Attendance-Tracker/logout.php">Logout</a>
      
    <?php if (isset($_SESSION['role'])): ?>
        <?php if ($_SESSION['role'] === 'Teacher'): ?>
          <a href="/Student-Attendance-Tracker/Dashboard/teacher.php">Dashboard</a>
        <?php elseif ($_SESSION['role'] === 'Student'): ?>
          <a href="/Student-Attendance-Tracker/Dashboard/student.php">Dashboard</a>
        <?php endif; ?>
    <?php else: ?>
        <a href="/Student-Attendance-Tracker/otp_login.php">Dashboard</a>
    <?php endif; ?>
    
    <?php else: ?>
      <a href="/Student-Attendance-Tracker/otp_login.php">Login</a>
    <?php endif; ?>
  </div>
</div>

<script src="/Student-Attendance-Tracker/Assets/scripts.js"></script>
