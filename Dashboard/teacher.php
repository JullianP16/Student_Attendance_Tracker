
<head>
<link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/second_styles.css">
</head>

<?php
session_start();
require_once '../Includes/db_connect.php';
include_once '../Includes/header.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Teacher') {
    echo "<p>You must be logged in as a teacher to view this page.</p>";
    exit;
}

$teacher_id = $_SESSION['user_id'];

// Fetch teacher name
$stmt = $pdo->prepare("SELECT fname FROM users WHERE id = ?");
$stmt->execute([$teacher_id]);
$user = $stmt->fetch();
$fname = $user ? htmlspecialchars($user['fname']) : 'Teacher';

// Handle class creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_class'])) {
    $newClassName = trim($_POST['class_name']);
    if (!empty($newClassName)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO classes (class_name, teacher_id) VALUES (?, ?)");
            $stmt->execute([$newClassName, $teacher_id]);
            header("Location: teacher.php?created=1");
            exit;
        } catch (PDOException $e) {
            $createError = "Error: " . $e->getMessage();
        }
    } else {
        $createError = "Please enter a class name.";
    }
}

// Get selected month and year for summary (optional)
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$start_date = "$selectedYear-" . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) . "-01";
$end_date = date("Y-m-t", strtotime($start_date));

// Get all classes for this teacher
try {
    $stmt = $pdo->prepare("SELECT id, class_name FROM classes WHERE teacher_id = ?");
    $stmt->execute([$teacher_id]);
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<p>Error loading courses: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>

<h2>Teacher Dashboard</h2>
<h3><i>Welcome, <?= $fname ?>!</i></h3>

<?php
if (isset($_GET['created'])) {
    echo "<p style='color: green;'>Class created successfully!</p>";
}
if (!empty($createError)) {
    echo "<p style='color: red;'>$createError</p>";
}
?>

<h3>Create a New Class</h3>
<form method="POST" action="">
    <input type="text" name="class_name" placeholder="Class Name" required>
    <button type="submit" name="create_class">Create Class</button>
</form>

<br>

<?php if (count($courses) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Course ID</th>
                <th>Course Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?= $course['id'] ?></td>
                    <td><?= htmlspecialchars($course['class_name']) ?></td>
                    <td>
                        <a href="../Backend/getStudent.php?course_id=<?= $course['id'] ?>">View Students</a> |
                        <a href="../Backend/markAttendance.php?course_id=<?= $course['id'] ?>">Mark Attendance</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>

    <form action="/Student-Attendance-Tracker/Reports/generate_pdf_combined.php" method="get" target="_self">
    <button type="submit">
        Download Attendance PDF / Excel
    </button>
    </form>

    <!-- Month selector -->
    <form method="GET">
        <label for="month">View summary for:</label>
        <select name="month" id="month">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= $m == $selectedMonth ? 'selected' : '' ?>>
                    <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                </option>
            <?php endfor; ?>
        </select>
        <select name="year">
            <?php
            $thisYear = date('Y');
            for ($y = $thisYear; $y >= $thisYear - 3; $y--): ?>
                <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit">Go</button>
    </form>

    <h3>Attendance Summary for <?= date('F Y', strtotime($start_date)) ?></h3>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Course</th>
                <th>Present %</th>
                <th>Absent %</th>
                <th>Late %</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <?php
                $course_id = $course['id'];
                $stmt = $pdo->prepare("
                    SELECT status, COUNT(*) as count
                    FROM attendance
                    WHERE class_id = ? AND date BETWEEN ? AND ?
                    GROUP BY status
                ");
                $stmt->execute([$course_id, $start_date, $end_date]);
                $totals = $stmt->fetchAll();

                $counts = ['Present' => 0, 'Absent' => 0, 'Late' => 0];
                $total = 0;
                foreach ($totals as $row) {
                    $counts[$row['status']] = $row['count'];
                    $total += $row['count'];
                }

                $presentPct = $total ? round(($counts['Present'] / $total) * 100) : 0;
                $absentPct  = $total ? round(($counts['Absent'] / $total) * 100) : 0;
                $latePct    = $total ? round(($counts['Late'] / $total) * 100) : 0;
                ?>
                <tr>
                    <td><?= htmlspecialchars($course['class_name']) ?></td>
                    <td><?= $presentPct ?>%</td>
                    <td><?= $absentPct ?>%</td>
                    <td><?= $latePct ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>You haven’t created any classes yet.</p>
<?php endif; ?>
