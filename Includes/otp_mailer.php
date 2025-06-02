<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

function sendOTP($recipientEmail, $otp) {
    $mail = new PHPMailer(true);

    // Credentials for sending
    $your_email = "your_email@example.com";
    $your_password = "your_email_password"; // <-- This is your App Password (no spaces)

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $your_email;
        $mail->Password = $your_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom($your_email, 'Attendance Management System');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Login Verification';
        $mail->Body = "
            <h2>Attendance Management System - OTP Verification</h2>
            <p>Here is your OTP code:</p>
            <h1 style='color: #007BFF;'>$otp</h1>
            <p>Please enter this code to complete your login. This code will expire shortly.</p>
            <br>
            <small>If you did not request this, you can safely ignore this email.</small>
        ";

        $mail->AltBody = "Your OTP code is: $otp";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
