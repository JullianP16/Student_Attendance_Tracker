<body>
    <link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/second_styles.css">
</body>


<?php
    // For teacher functionality
    session_start();
    require_once '../Includes/db_connect.php';
    include_once '../Includes/header.php'; 

    // Check login and role (optional if you want to restrict this to teachers)
    if (!isset($_SESSION['user_id'])) {
        echo "<p>You must be logged in to mark attendance.</p>";
        exit;
    }

    $teacher_id = $_SESSION['user_id'];
    $course_id = $_GET['course_id'] ?? null;

    // Get name of course
    $stmt = $pdo->prepare("SELECT class_name FROM classes WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();
    $course_name = $course ? htmlspecialchars($course['class_name']) : "Unknown Course";

    // If form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($_POST['attendance'] as $student_id => $status) {
            // Check if attendance was already marked for today
            $checkStmt = $pdo->prepare("
                SELECT COUNT(*) FROM attendance
                WHERE student_id = ? AND class_id = ? AND date = CURDATE()
            ");
            $checkStmt->execute([$student_id, $course_id]);
            $alreadyMarked = $checkStmt->fetchColumn();
        
            if ($alreadyMarked == 0) {
                $stmt = $pdo->prepare("
                    INSERT INTO attendance (student_id, class_id, date, status, recorded_by)
                    VALUES (?, ?, CURDATE(), ?, ?)
                ");
                $stmt->execute([$student_id, $course_id, $status, $teacher_id]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE attendance
                    SET status = ?, recorded_by = ?
                    WHERE student_id = ? AND class_id = ? AND date = CURDATE()
                ");
                $stmt->execute([$status, $teacher_id, $student_id, $course_id]);
            }
            
        }    

        // After saving attendance, redirect back to teacher dashboard
        header("Location: ../Dashboard/teacher.php?attendance=success");
        exit;
    }

    // Otherwise, display the form
    if (!$course_id) {
        echo "<p>Missing course ID.</p>";
        exit;
    }

    // Fetch students
    $stmt = $pdo->prepare("
        SELECT u.id, u.fname, u.lname
        FROM users u
        JOIN enrollments e ON u.id = e.student_id
        WHERE e.class_id = ? AND u.role = 'Student'
    ");
    $stmt->execute([$course_id]);
    $students = $stmt->fetchAll();
?>

<h3>Mark Attendance for <?= $course_name ?></h3>

<form method="POST">
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['fname'] . ' ' . $student['lname']) ?></td>
                    <td>
                        <select name="attendance[<?= $student['id'] ?>]">
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Late">Late</option>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <button type="submit">Submit Attendance</button>
</form>


