<body>
    <link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/second_styles.css">
</body>


<?php
    // For student dashboard use
    session_start();
    require_once '../Includes/db_connect.php';
    include_once '../Includes/header.php';

    // Show errors (optional for debugging)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Validate input
    if (!isset($_GET['class_id'])) {
        echo "<p>Missing class ID.</p>";
        exit;
    }

    // Check login and role
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
        echo "<p>You must be logged in as a student to view attendance.</p>";
        exit;
    }

    $student_id = $_SESSION['user_id'];
    $class_id = $_GET['class_id'];

    // Get current month/year or default to today
    $currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
    $currentYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

    // Build date range for the selected month
    $start_date = "$currentYear-" . str_pad($currentMonth, 2, "0", STR_PAD_LEFT) . "-01";
    $end_date = date("Y-m-t", strtotime($start_date));

    // Calculate previous and next months
    $prevMonth = $currentMonth - 1;
    $prevYear = $currentYear;
    if ($prevMonth < 1) {
        $prevMonth = 12;
        $prevYear--;
    }

    $nextMonth = $currentMonth + 1;
    $nextYear = $currentYear;
    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear++;
    }


    // Fetch class name
    try {
        $stmt = $pdo->prepare("SELECT class_name FROM classes WHERE id = ?");
        $stmt->execute([$class_id]);
        $class = $stmt->fetch();
        $class_name = $class ? htmlspecialchars($class['class_name']) : "Unknown Class";
    } catch (PDOException $e) {
        echo "<p>Error fetching class name: " . htmlspecialchars($e->getMessage()) . "</p>";
        exit;
    }

    // Fetch attendance records for selected month
    try {
        $stmt = $pdo->prepare("
            SELECT date, status
            FROM attendance
            WHERE student_id = ? AND class_id = ?
            AND date BETWEEN ? AND ?
            ORDER BY date ASC
        ");
        $stmt->execute([$student_id, $class_id, $start_date, $end_date]);
        $attendanceRecords = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "<p>Error retrieving attendance records: " . htmlspecialchars($e->getMessage()) . "</p>";
        exit;
    }
?>

<h2>Attendance for <?= $class_name ?></h2>

<!-- Month/Year filter form -->
<form method="GET" style="margin-bottom: 15px;">
    <input type="hidden" name="class_id" value="<?= $class_id ?>">
    <label for="month">Month:</label>
    <select name="month" id="month">
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= $m == $currentMonth ? 'selected' : '' ?>>
                <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
            </option>
        <?php endfor; ?>
    </select>

    <label for="year">Year:</label>
    <select name="year" id="year">
        <?php
        $thisYear = date('Y');
        for ($y = $thisYear; $y >= $thisYear - 3; $y--): ?>
            <option value="<?= $y ?>" <?= $y == $currentYear ? 'selected' : '' ?>>
                <?= $y ?>
            </option>
        <?php endfor; ?>
    </select>

    <button type="submit">Go</button>
</form>

<!-- Month navigation arrows -->
<div style="margin-bottom: 10px;">
    <a href="?class_id=<?= $class_id ?>&month=<?= $prevMonth ?>&year=<?= $prevYear ?>" style="margin-right: 10px;">← Previous Month</a>
    <strong><?= date('F Y', strtotime($start_date)) ?></strong>
    <a href="?class_id=<?= $class_id ?>&month=<?= $nextMonth ?>&year=<?= $nextYear ?>" style="margin-left: 10px;">Next Month →</a>
</div>

<!-- Show current month/year -->
<h4><?= date('F Y', strtotime($start_date)) ?></h4>

<!-- Attendance table -->
<?php if (count($attendanceRecords) > 0): ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendanceRecords as $record): ?>
                <tr>
                    <td><?= htmlspecialchars($record['date']) ?></td>
                    <td><?= htmlspecialchars($record['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No attendance records found for <?= date('F Y', strtotime($start_date)) ?>.</p>
<?php endif; ?>

<br>
