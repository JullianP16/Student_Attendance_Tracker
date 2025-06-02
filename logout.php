<link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/headerTitle.css">

<div><h1 class ="titleHeader">TrackMate</h1></div>


<?php
    session_start();
    session_destroy();
    header("Location: /Student-Attendance-Tracker/otp_login.php");
    exit;
