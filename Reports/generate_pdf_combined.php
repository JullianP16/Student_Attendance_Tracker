<?php include_once '../Includes/header.php';?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Generate Attendance Reports</title>
  <link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/styles.css">
  <link rel="stylesheet" href="/Student-Attendance-Tracker/Assets/second_styles.css">
  <script src="/Student-Attendance-Tracker/Assets/scripts.js" defer></script>
</head>
<body>

  <?php
  $port = 3306;
  $connection = new mysqli("localhost", "root", "", "attendance_schema",$port);

  // Get the teacher ID from session
  $teacher_id = $_SESSION['user_id'];

  // Load classes into reusable array
  $class_list = [];
  $classes = $connection->prepare("
    SELECT id, class_name 
    FROM classes 
    WHERE teacher_id = ? 
    ORDER BY class_name
  ");
  $classes->bind_param("i", $teacher_id);
  $classes->execute();
  $result = $classes->get_result();

  // Debugging: Check if any classes were found
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $class_list[] = $row;
    }
  } else {
    echo "No classes found for this teacher.";  // Debugging message
  }

  // Load students for PDF filtering
  $students = $connection->prepare("
    SELECT DISTINCT u.id, u.fname, u.lname 
    FROM users u
    JOIN enrollments e ON u.id = e.student_id
    JOIN classes c ON e.class_id = c.id
    WHERE c.teacher_id = ? AND u.role = 'Student'
    ORDER BY u.fname
  ");
  $students->bind_param("i", $teacher_id);
  $students->execute();
  $students_result = $students->get_result();

  // Debugging: Check if students were found
  if ($students_result->num_rows > 0) {
    $students_list = [];
    while ($student = $students_result->fetch_assoc()) {
      $students_list[] = $student;
    }
  } else {
    echo "No students found for this teacher's classes.";  // Debugging message
  }
  ?>

  <div class="dashboard">
    <h2>Generate Attendance PDF</h2>

    <form action="pdf_generate_action.php" method="GET">
      <label>Class:</label>
      <select name="class_id" required>
        <option value="">Select Class</option>
        <?php foreach ($class_list as $row): ?>
          <option value="<?= $row['id'] ?>"><?= $row['class_name'] ?></option>
        <?php endforeach; ?>
      </select><br><br>

      <label><input type="radio" name="report_type" value="day" checked> Attendance for Specific Day</label><br>
      <input type="date" name="date"><br><br>

      <label><input type="radio" name="report_type" value="month"> Attendance for Selected Month</label><br>
      <input type="month" name="month"><br><br>

      <label><input type="radio" name="report_type" value="student"> Individual Student Attendance</label><br>
      <select name="student_id">
        <option value="">Select Student</option>
        <?php foreach ($students_list as $student): ?>
          <option value="<?= $student['id'] ?>"><?= $student['fname'] . " " . $student['lname'] ?></option>
        <?php endforeach; ?>
      </select><br><br>

      <button type="submit">Generate PDF</button>
    </form>
  </div>

  <hr>

  <div class="dashboard">
    <h2>Generate Excel for Entire Class</h2>

    <form action="generate_excel.php" method="GET">
      <label>Select Class:</label>
      <select name="class_id" required>
        <option value="">Select Class</option>
        <?php foreach ($class_list as $row): ?>
          <option value="<?= $row['id'] ?>"><?= $row['class_name'] ?></option>
        <?php endforeach; ?>
      </select><br><br>

      <input type="hidden" name="report_type" value="class_only">
      <button type="submit">Generate Excel</button>
    </form>
  </div>

</body>
</html>
