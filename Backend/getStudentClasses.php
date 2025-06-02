<body>
    <link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/getStudentClassesStyle.css">
</body>




<?php
    // For student dashboard use
    require_once '../Includes/db_connect.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        echo "<p>You must be logged in as a student to view this page.</p>";
        exit;
    }

    $student_id = $_SESSION['user_id'];
    $today = date('Y-m-d');

    try {
        // Get all classes the student is enrolled in
        $stmt = $pdo->prepare("
            SELECT c.id AS class_id, c.class_name
            FROM classes c
            JOIN enrollments e ON c.id = e.class_id
            WHERE e.student_id = ?
        ");
        $stmt->execute([$student_id]);
        $classes = $stmt->fetchAll();

        if (count($classes) === 0) {
            echo "<p>You are not enrolled in any classes.</p>";
            return;
        }

        echo "<div style='display: flex; flex-wrap: wrap; gap: 20px;'>";

        foreach ($classes as $class) {
            $class_id = $class['class_id'];
            $class_name = htmlspecialchars($class['class_name']);

            // Calculate total attendance for the class
            $stmt = $pdo->prepare("
                SELECT status FROM attendance 
                WHERE student_id = ? AND class_id = ?
            ");
            $stmt->execute([$student_id, $class_id]);
            $allRecords = $stmt->fetchAll();

            $presentCount = 0;
            $totalCount = count($allRecords);

            foreach ($allRecords as $record) {
                if ($record['status'] === 'Present') {
                    $presentCount++;
                }
            }

            $percentage = $totalCount > 0 ? round(($presentCount / $totalCount) * 100) : 0;

            // Fetch today's status
            $stmt = $pdo->prepare("
                SELECT status FROM attendance 
                WHERE student_id = ? AND class_id = ? AND date = ?
                LIMIT 1
            ");
            $stmt->execute([$student_id, $class_id, $today]);
            $todayRow = $stmt->fetch();
            $todayStatus = $todayRow ? $todayRow['status'] : 'Unmarked';

            // Render card
            echo '<div id="boxStyle">';
            echo "<div style='border: 1px solid #ccc; padding: 16px; width: 250px; border-radius: 8px;'>";
            echo "<h3 style='margin: 0 0 10px;'>$class_name</h3>";
            echo "<p><strong>Present:</strong> {$percentage}%</p>";
            echo "<p><strong>Today:</strong> " . date('F j, Y') . "</p>";
            echo "<p><strong>Status:</strong> {$todayStatus}</p>";
            echo "<a href='../Backend/studentClassAttendance.php?class_id={$class_id}' style='display:inline-block; margin-top:10px; background:#007BFF; color:white; padding:6px 10px; border-radius:5px; text-decoration:none;'>View History</a>";
            echo "</div>";
            echo '</div>';
        }

        echo "</div>";

    } catch (PDOException $e) {
        echo "<p>Error fetching classes: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
?>
