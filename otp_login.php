<?php include_once 'Includes/header.php'; ?>
<link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/login.css">

<?php
if(session_status()===PHP_SESSION_NONE)
{
	session_start();
}
require_once 'includes/db_connect.php';  // Uses $pdo
require_once 'includes/otp_mailer.php';  // OTP mailer

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = strtolower(trim($_POST['email']));
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = (string)$otp;
            $_SESSION['otp_created_at'] = time();
            $_SESSION['otp_email'] = $email;

            if (sendOTP($email, $otp)) {
                echo "<script>alert('OTP sent to your email. Please verify.'); window.location.href='includes/otp_validator.php';</script>";
                exit;
            } else {
                echo "<script>alert('Failed to send OTP. Please try again later.'); window.location.href='otp_login.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Incorrect password.'); window.location.href='otp_login.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('No user found with this email.'); window.location.href='otp_login.php';</script>";
        exit;
    }
}
?>

<br><br><br><br>
<div class="login">
   <h2>Login</h2>
   <form method="POST" action="otp_login.php">
      <input type="email" name="email" placeholder="Email Address" required>
      <br><br>
      <input type="password" name="password" placeholder="Password" required>
      <br><br>
    <button type="submit">Login</button>
   </form>
   <br>
   <p style="color:purple"> Don't have an account? </p>
   <a id="createUser" href="register.php" >Create User</a>
</div>
