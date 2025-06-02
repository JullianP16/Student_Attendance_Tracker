<body>
    <link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/second_styles.css">
</body>

<?php 
    session_start();
    include_once '../Includes/header.php'; 
    require_once '../Includes/db_connect.php';

    $student_id = $_SESSION['user_id'];

    // Fetch student first name
    $stmt = $pdo->prepare("SELECT fname FROM users WHERE id = ?");
    $stmt->execute([$student_id]);
    $user = $stmt->fetch();
    $fname = $user ? htmlspecialchars($user['fname']) : 'Student';
?>

<h2>Student Dashboard</h2>
<h3><i>Welcome, <?= $fname ?>!</i></h3>

<div class="course-table">
    <?php include_once '../Backend/getStudentClasses.php'; ?>
</div>

</body>
</html>