# Student Attendance Tracker

A **Web-Based Attendance Management System** designed to simplify and streamline the attendance process for teachers, students, and administrators. This project was developed as a final submission for the Web Technologies course.

## 🧩 Project Overview

The system allows teachers to mark attendance, track student participation trends, and generate dynamic reports. Students can log in to view their attendance records, while administrators can monitor and analyze data through an interactive dashboard.

> This project meets all required components of the course final, including front-end and back-end development, MySQL integration, user authentication, and a unique feature (PDF report generation).

---

## 🛠️ Tech Stack

### 🔹 Front-End
- HTML5, CSS3
- JavaScript
- (Optional: React)

### 🔹 Back-End
- PHP (server-side logic)
- MySQL (relational database)

### 🔹 Authentication
- Register & Login with hashed passwords (using `password_hash()` and `password_verify()` in PHP)

---

## 📋 Core Features

### 👩‍🏫 Teacher Functionality
- Mark student attendance for each class/session
- View real-time attendance records
- Generate class-wise and student-wise reports (PDF format)

### 👨‍🎓 Student Functionality
- View personal attendance history
- Track attendance percentages per subject

### 📊 Admin/Analytics
- Secure admin login
- Attendance record management
- Performance tracking through dynamic analytics

---

## 🌟 Unique Features

> **PDF Report Generation**
- Teachers and admins can generate downloadable PDF/Excel attendance reports with just one click.
- Dark Mode
- OTP Authentication
- Feature implemented using `TCPDF` PHP library.

---

## 🔒 Authentication Flow

- Secure registration with hashed passwords
- Login system validates credentials via PHP and MySQL
- Session management to protect user pages

---

## 🧱 Database Schema

The MySQL database contains the following core tables:
- `users` – Stores user credentials and roles (teacher/student/admin)
- `attendance` – Stores daily attendance records
- `subjects` – List of classes/subjects
- `students` – Student information linked to subjects

> 📄 SQL file included in submission: `database.sql`

---

## 🗂️ File Structure

```plaintext
├── index.php                # Home Page
├── login.php                # Login Page
├── register.php             # Registration Page
├── dashboard/               # Dashboard for Teachers/Admins
│   ├── teacher.php          # Teacher Dashboard
│   ├── admin.php            # Admin Dashboard
├── reports/                 # Report Generation
│   ├── generate_pdf.php     # PDF Report Generator
|   ├── generate_excel.php   # Excel Report Generator  
├── includes/                # Reusable PHP Functions
│   ├── db_connect.php       # Database Connection
│   ├── auth.php             # Authentication Utilities
|   ├── otp_mailer.php       # Email Sender
|   ├── otp_validatior.php   # OTP Verification 
├── assets/                  # CSS, JS, and Images
│   ├── styles.css           # Global Styles
│   ├── scripts.js           # Global Scripts
├── database.sql             # Database Initialization Script
└── README.md                # Project Documentation
```

---

## 🚀 Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Jassie2003/Student-Attendance-Tracker.git
   cd student-attendance-tracker
   ```

2. **Set Up the Database**
   - Import the `database.sql` file into your MySQL server.
   - Update the database credentials in `includes/db_connect.php`.
   - Update the $port variable in all of the files in the `Reports` folder to the port that you're MySQL server is running on.

3. **Configure the Server**
   - Ensure you have a local server running (e.g., XAMPP, WAMP, or MAMP).
   - Place the project folder in the server's root directory (e.g., `htdocs` for XAMPP).

4. **Install Dependencies**
   - If using `TCPDF`, download and include the library in the `includes` folder.

5. **Run the Application**
   - Open your browser and navigate to `http://localhost/student-attendance-tracker`.

---

## 📖 Usage

1. **Admin**
   - Log in using predefined admin credentials.
   - Add teachers and students to the system.
   - Monitor attendance reports.

2. **Teacher**
   - Log in to mark attendance.
   - Generate PDF reports for classes or individual students.

3. **Student**
   - Log in to view attendance records and subject-wise percentages.

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:
1. Fork the repository.
2. Create a new branch (`feature/your-feature`).
3. Commit your changes.
4. Submit a pull request.

---

## 📝 License

This project is licensed under the MIT License. See the `LICENSE` file for details.

---

## 📧 Contact

For any questions or feedback, please contact:
- **Name**: Jasmin Ruiz
- **Email**: jar2bp@mtmail.mtsu.edu
- **GitHub**: [Jassie2003](https://github.com/Jassie2003)
