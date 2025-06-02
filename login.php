<?php include_once 'Includes/header.php'; ?>
<link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/login.css">

<?php
if(session_status()===PHP_SESSION_NONE)
{
	session_start();
}
require_once 'Includes/db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user
    $stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect by role
            if ($user['role'] === 'Teacher') {
                header("Location: Dashboard/teacher.php");
                exit;
            } elseif ($user['role'] === 'Student') {
                header("Location: Dashboard/student.php");
                exit;
            } else {
                $error = "Unknown role.";
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<br><br><br><br>
<div class="login">
   <h2>Login</h2>
   <form method="POST">
       <label>Email:    <input type="email" name="email" required></label><br><br>
       <label>Password: <input type="password" name="password" required></label><br><br>
       <button type="submit">Login</button>
    </form>
	<br>
   <p style="color:purple"> Don't have an accout? </p>
   <a id="createUser" href="register.php" >Create User</a>
   <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
   <?php endif; ?>
</div>
