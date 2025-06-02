-- create the database
CREATE DATABASE IF NOT EXISTS attendance_schema;
use attendance_schema;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(50) NOT NULL,
    lname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Student', 'Teacher') NOT NULL
);



CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    teacher_id INT,
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);


CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    UNIQUE (student_id, class_id)
);

CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Present', 'Absent', 'Late') NOT NULL,
    recorded_by INT, -- teacher id
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE (student_id, class_id, date)
);


-- What I used for testing

-- Insert teacher
-- INSERT INTO users (id, fname, lname, email, password, role)
-- VALUES (1, 'Jasmin', 'Ruiz', 'jasmin@example.com', 'password', 'Teacher');

-- Insert student
-- INSERT INTO users (id, fname, lname, email, password, role)
-- VALUES (2, 'Daniel', 'Casco', 'Daniel@example.com', 'password', 'Student');

-- Insert class linked to the teacher
-- INSERT INTO classes (id, class_name, teacher_id)
-- VALUES (1, 'Web Tech', 1);

-- Enroll the student into the class
-- INSERT INTO enrollments (student_id, class_id)
-- VALUES (2, 1);


