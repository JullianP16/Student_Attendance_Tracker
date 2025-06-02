<link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/OTP_validator.css">

<?php
session_start();
require_once 'db_connect.php';  // Uses $pdo

// Redirect if no OTP is set
if (!isset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['otp_created_at'])) {
    echo "<script>alert('Session expired or invalid access. Please login again.'); window.location.href='../otp_login.php';</script>";
    exit;
}

// Auto-expire OTP after 5 minutes
if (time() - $_SESSION['otp_created_at'] > 300) {
    unset($_SESSION['otp'], $_SESSION['otp_created_at']);
    echo "<script>alert('OTP expired. Please log in again.'); window.location.href='../otp_login.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['entered_otp'])) {
        $entered_otp = trim($_POST['entered_otp']);

        if ($entered_otp === $_SESSION['otp']) {
            // OTP is correct, fetch user info
            $email = $_SESSION['otp_email'];
            $stmt = $pdo->prepare("SELECT id, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Clean up OTP session
                unset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['otp_created_at']);

                // Redirect to appropriate dashboard
                if ($user['role'] === 'Teacher') {
                    header("Location: ../Dashboard/teacher.php");
                } elseif ($user['role'] === 'Student') {
                    header("Location: ../Dashboard/student.php");
                } else {
                    header("Location: ../index.php");
                }
                exit;
            } else {
                echo "<script>alert('User not found.'); window.location.href='../otp_login.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Incorrect OTP.');</script>";
        }
    }

    // Handle resend
    if (isset($_POST['resend_otp'])) {
        require_once 'otp_mailer.php';

        $otp = rand(100000, 999999);
        $_SESSION['otp'] = (string)$otp;
        $_SESSION['otp_created_at'] = time();

        if (sendOTP($_SESSION['otp_email'], $otp)) {
            echo "<script>alert('A new OTP has been sent to your email.');</script>";
        } else {
            echo "<script>alert('Failed to resend OTP. Try again later.');</script>";
        }
    }
}

// Dark mode persistence
$darkMode = isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'true';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../Assets/styles.css">
</head>
<body class="<?= $darkMode ? 'dark-mode' : '' ?>">

<?php include_once 'header.php'; ?>

<div class="OTP_Val">
<div class="otp-container">
    <h2>Enter the OTP sent to your email</h2>
    <form method="POST">
        <input type="text" name="entered_otp" placeholder="Enter OTP" required>
        <br><br>
        <button type="submit">Verify OTP</button>
    </form>

    <form method="POST" style="margin-top: 10px;">
        <button type="submit" name="resend_otp">Resend OTP</button>
    </form>
</div>
</div>

<script src="../Assets/scripts.js"></script>
</body>
</html>
