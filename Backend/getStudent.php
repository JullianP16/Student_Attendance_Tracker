
<body>
    <link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/second_styles.css">
</body>


<?php
    // For teacher functionality
    session_start();
    require_once '../Includes/db_connect.php';
    include_once '../Includes/header.php'; 

    // Show errors for debugging (optional, remove later)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Check if course_id is provided
    if (!isset($_GET['course_id'])) {
        echo "<p>Missing course ID.</p>";
        exit;
    }

    $course_id = $_GET['course_id'];

    // Get name of course
    $stmt = $pdo->prepare("SELECT class_name FROM classes WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();
    $course_name = $course ? htmlspecialchars($course['class_name']) : "Unknown Course";

    // Handle adding a new student
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];

        // Check if student already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $existingStudent = $stmt->fetch();

        if ($existingStudent) {
            $student_id = $existingStudent['id'];
        } else {
            // Insert new student
            $stmt = $pdo->prepare("
                INSERT INTO users (fname, lname, email, password, role)
                VALUES (?, ?, ?, ?, 'Student')
            ");
            $stmt->execute([$fname, $lname, $email, password_hash('default123', PASSWORD_DEFAULT)]);
            $student_id = $pdo->lastInsertId();
        }

        // Enroll the student in the course
        $stmt = $pdo->prepare("INSERT IGNORE INTO enrollments (student_id, class_id) VALUES (?, ?)");
        $stmt->execute([$student_id, $course_id]);

        echo "<p style='color: green;'>Student added and enrolled successfully!</p>";
    }

    // Fetch students and latest attendance
    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.fname, u.lname, u.email,
                a.status AS latest_status,
                a.date AS latest_date
            FROM users u
            JOIN enrollments e ON u.id = e.student_id
            LEFT JOIN (
                SELECT student_id, class_id, status, date
                FROM attendance
                WHERE class_id = ?
                AND date = (
                    SELECT MAX(date)
                    FROM attendance a2
                    WHERE a2.student_id = attendance.student_id
                    AND a2.class_id = attendance.class_id
                )
            ) a ON a.student_id = u.id AND a.class_id = e.class_id
            WHERE e.class_id = ? AND u.role = 'Student'
        ");
        $stmt->execute([$course_id, $course_id]);
        $students = $stmt->fetchAll();

        if (count($students) > 0) {
            echo "<h3>Students Enrolled in " . $course_name . "</h3>";
            echo "<table border='1' cellpadding='8'>";
            echo "<thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Last Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>";
            echo "<tbody>";
            foreach ($students as $student) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($student['id']) . "</td>";
                echo "<td>
                        <a href='studentAttendance.php?student_id=" . htmlspecialchars($student['id']) . "&course_id=" . htmlspecialchars($course_id) . "'>
                            " . htmlspecialchars($student['fname'] . ' ' . $student['lname']) . "
                        </a>
                    </td>";
                echo "<td>" . htmlspecialchars($student['email']) . "</td>";
                echo "<td>" . htmlspecialchars($student['latest_status'] ?? '—') . " (" . htmlspecialchars($student['latest_date'] ?? '—') . ")</td>";
                echo "<td>
                    <form method='POST' action='../Backend/deleteStudent.php' onsubmit=\"return confirm('Are you sure you want to delete this student and their attendance?');\">
                        <input type='hidden' name='student_id' value='" . htmlspecialchars($student['id']) . "'>
                        <input type='hidden' name='course_id' value='" . htmlspecialchars($course_id) . "'>
                        <button type='submit'>Delete</button>
                    </form>
                </td>";

            echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No students are currently enrolled in this course.</p>";
        }

    } catch (PDOException $e) {
        echo "<p>Error retrieving students: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
?>

<br>

<h3>Add New Student to This Course</h3>
<form method="POST" action="">
    <input type="text" name="fname" placeholder="First Name" required>
    <input type="text" name="lname" placeholder="Last Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <button type="submit" name="add_student">Add Student</button>
</form>
