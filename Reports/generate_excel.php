<?php
$port = 3306;
$connection = new mysqli("localhost", "root", "", "attendance_schema", $port);

function alertBack($message) {
    echo "<script>alert('$message'); window.location.href='generate_pdf_combined_classlist.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['report_type'])) {
    $class_id = $_GET['class_id'];
    $report_type = $_GET['report_type'];
    $filename = "attendance_export";

    // Get class name for filename
    $classQuery = $connection->query("SELECT class_name FROM classes WHERE id = $class_id");
    $className = $classQuery ? $classQuery->fetch_assoc()['class_name'] : "Unknown_Class";
    $classNameSafe = str_replace(' ', '_', $className);

    switch ($report_type) {
        case "day":
            $date = $_GET['date'];
            if (empty($date)) alertBack("Please select a valid date.");
            $filename .= "_{$classNameSafe}_day_" . $date;
            $sql = "SELECT u.fname, u.lname, a.date, a.status 
                    FROM attendance a 
                    JOIN users u ON a.student_id = u.id 
                    WHERE a.class_id = $class_id AND a.date = '$date'";
            break;

        case "month":
            $month = $_GET['month'];
            if (empty($month)) alertBack("Please select a valid month.");
            $filename .= "_{$classNameSafe}_month_" . str_replace('-', '_', $month);
            $sql = "SELECT u.fname, u.lname, a.date, a.status 
                    FROM attendance a 
                    JOIN users u ON a.student_id = u.id 
                    WHERE a.class_id = $class_id AND DATE_FORMAT(a.date, '%Y-%m') = '$month'";
            break;

        case "student":
            $student_id = $_GET['student_id'];
            if (empty($student_id)) alertBack("Please select a student.");
            $filename .= "_{$classNameSafe}_student_" . $student_id;
            $sql = "SELECT u.fname, u.lname, a.date, a.status 
                    FROM attendance a 
                    JOIN users u ON a.student_id = u.id 
                    WHERE a.class_id = $class_id AND a.student_id = $student_id";
            break;

        case "class_only":
            $filename .= "_{$classNameSafe}";
            $sql = "SELECT u.fname, u.lname, a.date, a.status 
                    FROM attendance a 
                    JOIN users u ON a.student_id = u.id 
                    WHERE a.class_id = $class_id
                    ORDER BY a.date ASC";
            break;

        default:
            alertBack("Invalid report type.");
    }

    $result = $connection->query($sql);
    if ($result->num_rows === 0) {
        alertBack("No attendance was recorded for this selection.");
    }

    // Output headers for Excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename={$filename}.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Output the data as tab-delimited rows
    echo "Name	Date	Status
";
    while ($row = $result->fetch_assoc()) {
        $fullName = $row['fname'] . ' ' . $row['lname'];
        echo "{$fullName}	{$row['date']}	{$row['status']}
";
    }
    exit;
} else {
    alertBack("Form not submitted properly.");
}
?>
