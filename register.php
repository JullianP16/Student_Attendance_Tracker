<?php include_once 'Includes/header.php'; ?>
<link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/register.css">


<?php
    if(session_status()===PHP_SESSION_NONE)
    {
	session_start();
    }
    require_once 'Includes/db_connect.php';

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fname = trim($_POST['fname']);
        $lname = trim($_POST['lname']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $teacher_code = trim($_POST['teacher_code']);

        // Check if teacher code is correct
        if ($teacher_code !== 'XinYang2025') {
            $error = "Only teachers can create an account. Invalid or missing teacher code.";
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = "An account with that email already exists.";
            } else {
                // Insert teacher account
                $stmt = $pdo->prepare("
                    INSERT INTO users (fname, lname, email, password, role)
                    VALUES (?, ?, ?, ?, 'Teacher')
                ");
                $stmt->execute([
                    $fname, 
                    $lname, 
                    $email, 
                    password_hash($password, PASSWORD_DEFAULT)
                ]);

                $success = "Teacher account created successfully! You can now log in.";
            }
        }
    }
?>

<div class="register-container">
    <div class = "register">
    <h2>Create Teacher Account</h2>

    <form method="POST">
        <label>First Name: <input type="text" name="fname" required></label><br><br>
        <label>Last Name: <input type="text" name="lname" required></label><br><br>
        <label>Email: <input type="email" name="email" required></label><br><br>
        <label>Password: <input type="password" name="password" required></label><br><br>
        <label>Teacher Code: <input type="text" name="teacher_code" required></label><br><br>
        
        <button type="submit">Register</button>
        <a href="otp_login.php">Go to Login</a>
    </form>
    </div>
</div>


<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php elseif ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>
