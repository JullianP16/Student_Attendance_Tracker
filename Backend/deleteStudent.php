<?php
session_start();
require_once '../Includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Teacher') {
    exit("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];

    try {
        // Delete attendance records for this student in this course
        $stmt = $pdo->prepare("DELETE FROM attendance WHERE student_id = ? AND class_id = ?");
        $stmt->execute([$student_id, $course_id]);

        // Delete enrollment record
        $stmt = $pdo->prepare("DELETE FROM enrollments WHERE student_id = ? AND class_id = ?");
        $stmt->execute([$student_id, $course_id]);

        // Redirect back to the course view
        header("Location: ../Backend/getStudent.php?course_id=" . urlencode($course_id));
        exit;

    } catch (Exception $e) {
        echo "Error deleting student: " . $e->getMessage();
    }
}
?>
