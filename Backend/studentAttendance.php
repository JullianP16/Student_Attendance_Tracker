<body>
    <link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/second_styles.css">
</body>



<?php
    // For teacher dashboard use
    session_start();
    require_once '../Includes/db_connect.php';
    include_once '../Includes/header.php'; 

    // Show errors while developing (optional)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Validate input
    if (!isset($_GET['student_id']) || !isset($_GET['course_id'])) {
        echo "<p>Missing student or course ID.</p>";
        exit;
    }

    $student_id = $_GET['student_id'];
    $course_id = $_GET['course_id'];

    // Fetch student name
    $stmt = $pdo->prepare("SELECT fname, lname FROM users WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
    $student_name = $student ? htmlspecialchars($student['fname'] . ' ' . $student['lname']) : "Student";


    // Get current month and year from URL or default to today
    $month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
    $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_manual_attendance'])) {
        $manualDate = $_POST['manual_date'];
        $manualStatus = $_POST['manual_status'];

        // Insert the new attendance record
        $stmt = $pdo->prepare("
            INSERT INTO attendance (student_id, class_id, date, status, recorded_by)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$student_id, $course_id, $manualDate, $manualStatus, $_SESSION['user_id'] ?? 0]);

        echo "<p style='color: green;'>Attendance successfully added for {$manualDate}.</p>";
    }


    // Handle attendance edit
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_attendance'])) {
        $newStatus = $_POST['status'];
        $editDate = $_POST['edit_date'];
        $stmt = $pdo->prepare("
            UPDATE attendance
            SET status = ?
            WHERE student_id = ? AND class_id = ? AND date = ?
        ");
        $stmt->execute([$newStatus, $student_id, $course_id, $editDate]);
        echo "<p style='color: green;'>Attendance updated for {$editDate}.</p>";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_attendance'])) {
        $deleteDate = $_POST['edit_date'];

        $stmt = $pdo->prepare("
            DELETE FROM attendance
            WHERE student_id = ? AND class_id = ? AND date = ?
        ");
        $stmt->execute([$student_id, $course_id, $deleteDate]);

        // Redirect after delete to avoid resubmission
        header("Location: studentAttendance.php?student_id=$student_id&course_id=$course_id&month=$month&year=$year");
        exit;
    }

    // Handle month navigation
    $prev_month = $month - 1;
    $prev_year = $year;
    if ($prev_month < 1) {
        $prev_month = 12;
        $prev_year--;
    }

    $next_month = $month + 1;
    $next_year = $year;
    if ($next_month > 12) {
        $next_month = 1;
        $next_year++;
    }

    // Search date logic
    $search_date = isset($_GET['search_date']) ? $_GET['search_date'] : null;

    // Fetch attendance records
    try {
        if ($search_date) {
            $stmt = $pdo->prepare("
                SELECT date, status
                FROM attendance
                WHERE student_id = ? AND class_id = ? AND date = ?
                ORDER BY date ASC
            ");
            $stmt->execute([$student_id, $course_id, $search_date]);
        } else {
            $start_date = "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01";
            $end_date = date("Y-m-t", strtotime($start_date));
            $stmt = $pdo->prepare("
                SELECT date, status
                FROM attendance
                WHERE student_id = ? AND class_id = ? AND date BETWEEN ? AND ?
                ORDER BY date ASC
            ");
            $stmt->execute([$student_id, $course_id, $start_date, $end_date]);
        }

        $attendance = $stmt->fetchAll();

    } catch (PDOException $e) {
        echo "<p>Error retrieving attendance: " . htmlspecialchars($e->getMessage()) . "</p>";
        exit;
    }
?>

<h2>Attendance History</h2>
<h3>For <i> <?= $student_name ?></i></h3>

<!-- Month navigation -->
<div style="margin-bottom: 15px;">
    <a href="?student_id=<?= $student_id ?>&course_id=<?= $course_id ?>&month=<?= $prev_month ?>&year=<?= $prev_year ?>" style="margin-right: 10px;">← Previous Month</a>
    <strong><?= date('F Y', strtotime("$year-$month-01")) ?></strong>
    <a href="?student_id=<?= $student_id ?>&course_id=<?= $course_id ?>&month=<?= $next_month ?>&year=<?= $next_year ?>" style="margin-left: 10px;">Next Month →</a>
</div>

<!-- Search by date -->
<form method="GET" style="margin-bottom: 15px;">
    <input type="hidden" name="student_id" value="<?= $student_id ?>">
    <input type="hidden" name="course_id" value="<?= $course_id ?>">
    <label for="search_date">Search by Date:</label>
    <input type="date" id="search_date" name="search_date" required>
    <button type="submit">Search</button>
</form>

<!-- Attendance table with ability to edit -->
<?php if (count($attendance) > 0): ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendance as $record): ?>
                <tr>
                <form method="POST">
                    <td><?= htmlspecialchars($record['date']) ?></td>
                    <td>
                        <select name="status">
                            <option value="Present" <?= $record['status'] === 'Present' ? 'selected' : '' ?>>Present</option>
                            <option value="Absent" <?= $record['status'] === 'Absent' ? 'selected' : '' ?>>Absent</option>
                            <option value="Late" <?= $record['status'] === 'Late' ? 'selected' : '' ?>>Late</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="edit_date" value="<?= htmlspecialchars($record['date']) ?>">
                        <input type="hidden" name="month" value="<?= $month ?>">
                        <input type="hidden" name="year" value="<?= $year ?>">
                        <button type="submit" name="edit_attendance">Save</button>
                        <button type="submit" name="delete_attendance" onclick="return confirm('Are you sure you want to delete this attendance record?');">Delete</button>
                    </td>
                    <input type="hidden" name="student_id" value="<?= $student_id ?>">
                    <input type="hidden" name="course_id" value="<?= $course_id ?>">
                </form>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No attendance records found for this <?= $search_date ? "date" : "month" ?>.</p>

    <h4>Manually Add Attendance for This Month</h4>
    <form method="POST">
        <input type="date" name="manual_date" required>
        <select name="manual_status" required>
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
            <option value="Late">Late</option>
        </select>
        <input type="submit" name="add_manual_attendance" value="Add Attendance">
    </form>

<?php endif; ?>

<br>