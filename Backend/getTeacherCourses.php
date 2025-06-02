<body>
    <link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/second_styles.css">
</body>
<div><h1 class ="titleHeader">TrackMate</h1></div>



<?php
    // For teacher dashboard use
    session_start();
    require_once '../Includes/db_connect.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Teacher') {
        echo "<p>You must be logged in as a teacher to view this page.</p>";
        exit;
    }

    $teacher_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("SELECT id, class_name FROM classes WHERE teacher_id = ?");
        $stmt->execute([$teacher_id]);
        $courses = $stmt->fetchAll();

        if (count($courses) > 0) {
            echo "<table>";
            echo "<thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>";
            echo "<tbody>";
            foreach ($courses as $course) {
                $course_id = htmlspecialchars($course['id']);
                $course_name = htmlspecialchars($course['class_name']);
                echo "<tr>
                        <td>{$course_id}</td>
                        <td>{$course_name}</td>
                        <td>
                            <a href='../Backend/getStudent.php?course_id={$course_id}'>View Students</a> |
                            <a href='../Backend/markAttendance.php?course_id={$course_id}'>Mark Attendance</a>
                        </td>
                    </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No courses found for this teacher.</p>";
        }

    } catch (PDOException $e) {
        echo "<p>Error fetching courses: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
?>
