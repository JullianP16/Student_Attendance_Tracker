<body>
    <link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/second_styles.css">
</body>
<div><h1 class ="titleHeader">TrackMate</h1></div>



<?php
    // For teacher dashboard use
    require_once '../Includes/db_connect.php';

    if (!isset($_SESSION['user_id'])) {
        echo "<p>You must be logged in to view course summaries.</p>";
        exit;
    }

    $teacher_id = $_SESSION['user_id'];

    // Default to current month/year if not selected
    $currentMonth = isset($_GET['summary_month']) ? (int)$_GET['summary_month'] : date('n');
    $currentYear = isset($_GET['summary_year']) ? (int)$_GET['summary_year'] : date('Y');

    // Get first and last date of the selected month
    $start_date = "$currentYear-" . str_pad($currentMonth, 2, "0", STR_PAD_LEFT) . "-01";
    $end_date = date("Y-m-t", strtotime($start_date));

?>

<form method="GET" style="margin-bottom: 15px;">
    <label for="summary_month">View summary for:</label>
    <select name="summary_month" id="summary_month">
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= $m == $currentMonth ? 'selected' : '' ?>>
                <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
            </option>
        <?php endfor; ?>
    </select>

    <select name="summary_year" id="summary_year">
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

<?php
    try {
        // Get all classes for this teacher
        $stmt = $pdo->prepare("SELECT id, class_name FROM classes WHERE teacher_id = ?");
        $stmt->execute([$teacher_id]);
        $courses = $stmt->fetchAll();

        if (count($courses) === 0) {
            echo "<p>No courses found to summarize.</p>";
            return;
        }

        echo "<h3>Attendance Summary for " . date('F Y', strtotime($start_date)) . "</h3>";
        echo "<table border='1' cellpadding='8'>";
        echo "<thead>
                <tr>
                    <th>Course</th>
                    <th>Present %</th>
                    <th>Absent %</th>
                    <th>Late %</th>
                </tr>
            </thead><tbody>";

        foreach ($courses as $course) {
            $course_id = $course['id'];
            $course_name = htmlspecialchars($course['class_name']);

            // Count total attendance by status for selected month
            $stmt = $pdo->prepare("
                SELECT status, COUNT(*) as count
                FROM attendance
                WHERE class_id = ? AND date BETWEEN ? AND ?
                GROUP BY status
            ");
            $stmt->execute([$course_id, $start_date, $end_date]);
            $totals = $stmt->fetchAll();

            $statusCounts = ['Present' => 0, 'Absent' => 0, 'Late' => 0];
            $total = 0;

            foreach ($totals as $row) {
                $status = $row['status'];
                $count = $row['count'];
                $statusCounts[$status] = $count;
                $total += $count;
            }

            if ($total > 0) {
                $presentPct = round($statusCounts['Present'] / $total * 100);
                $absentPct  = round($statusCounts['Absent'] / $total * 100);
                $latePct    = round($statusCounts['Late'] / $total * 100);
            } else {
                $presentPct = $absentPct = $latePct = 0;
            }

            echo "<tr>
                    <td>{$course_name}</td>
                    <td>{$presentPct}%</td>
                    <td>{$absentPct}%</td>
                    <td>{$latePct}%</td>
                </tr>";
        }

        echo "</tbody></table>";

    } catch (PDOException $e) {
        echo "<p>Error loading summary: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
?>
