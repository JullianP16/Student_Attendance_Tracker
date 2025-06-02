<?php
ob_start();
require_once('../Assets/tcpdf/tcpdf.php');

function alertBack($message) {
    ob_end_clean();
    echo "<script>alert('$message'); window.location.href='generate_pdf.php';</script>";
    exit;
}
$port = 3306;
$connection = new mysqli("localhost", "root", "", "attendance_schema", $port);

$class_id = $_GET['class_id'];
$report_type = $_GET['report_type'];
$title = "Attendance Report";

switch ($report_type) {
    case "day":
        $date = $_GET['date'];
        if (empty($date)) alertBack("Please select a valid date.");
        $title = "Class Attendance on $date";
        $sql = "SELECT u.fname, u.lname, a.date, a.status 
                FROM attendance a 
                JOIN users u ON a.student_id = u.id 
                WHERE a.class_id = $class_id AND a.date = '$date'";
        break;

    case "month":
        $month = $_GET['month'];
        if (empty($month)) alertBack("Please select a valid month.");
        $title = "Class Attendance for " . date("F Y", strtotime($month));
        $sql = "SELECT u.fname, u.lname, a.date, a.status 
                FROM attendance a 
                JOIN users u ON a.student_id = u.id 
                WHERE a.class_id = $class_id AND DATE_FORMAT(a.date, '%Y-%m') = '$month'";
        break;

    case "student":
        $student_id = $_GET['student_id'];
        if (empty($student_id)) alertBack("Please select a student.");
        $title = "Attendance for Student ID $student_id in Class ID $class_id";
        $sql = "SELECT u.fname, u.lname, a.date, a.status 
                FROM attendance a 
                JOIN users u ON a.student_id = u.id 
                WHERE a.class_id = $class_id AND a.student_id = $student_id";
        break;

    default:
        alertBack("Invalid report type.");
}

$result = $connection->query($sql);
if ($result->num_rows === 0) alertBack("No attendance was recorded for this selection.");

ob_end_clean(); // Clean buffer before sending PDF

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Write(0, $title, '', 0, 'L', true, 0, false, false, 0);
$pdf->Ln();

$html = '<table border="1" cellpadding="4"><thead><tr><th>Name</th><th>Date</th><th>Status</th></tr></thead><tbody>';
while ($row = $result->fetch_assoc()) {
    $fullName = $row['fname'] . ' ' . $row['lname'];
    $html .= "<tr><td>{$fullName}</td><td>{$row['date']}</td><td>{$row['status']}</td></tr>";
}
$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('attendance_report.pdf', 'I');
exit;
?>
