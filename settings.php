<link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/settings.css">


<?php
    if(session_status()===PHP_SESSION_NONE)
    {
	session_start();
    }
    require_once 'Includes/db_connect.php';
    include 'Includes/header.php';

    if (!isset($_SESSION['user_id'])) {
        echo "<p>You must be logged in to view this page.</p>";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $success = '';
    $error = '';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_email = $_POST['email'];
        $new_password = $_POST['password'];

        // Validate email format
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt->execute([$new_email, $hashedPassword, $user_id]);
                $success = "Your account has been updated.";
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }

    // Fetch current user data
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    $current_email = $user['email'] ?? '';
?>

<div class="dashboard">
    <div class="settings"> <!-- from settings.css -->
    <h2>Update Account Settings</h2>

    <?php if ($success): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php elseif ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($current_email) ?>" required><br><br>

        <label>New Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Update</button>
    </form>
    </div>
</div>

