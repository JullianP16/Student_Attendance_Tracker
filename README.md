# Student Attendance Tracker — Project Showcase by Jullian Phan

This is a public showcase of my contributions to a web-based Attendance Management System built as a final project for a Web Technologies course.

The system supports OTP-secured login, student/teacher/admin roles, dynamic attendance tracking, PDF/Excel export, and a clean UI with dark mode support.

> 📌 Note: The original full repo is private and maintained by a team. This public version highlights the specific features and files I developed or co-developed.

---

## 🔧 Features I Built or Contributed To

### 🔐 OTP Authentication System
- Developed one-time password (OTP) email verification during login
- Secure credential handling via PHP sessions and PHPMailer

### 📄 Report Generation
- Created exportable PDF and Excel attendance reports
- Used the TCPDF PHP library for styling and formatting output

### 🌙 Dark Mode Toggle
- Designed and implemented a JavaScript-based dark mode toggle with `localStorage` support

### 💡 SQL Database Design & Queries
- Designed the full **MySQL database schema** to support users, roles, subjects, and attendance logs
- Wrote all custom **SQL queries** for:
  - Secure registration and login
  - OTP validation
  - Attendance tracking by class, student, or date
  - Dynamic report generation for PDF and Excel
- Implemented foreign keys, indexing, and normalization for optimized performance

> 📄 SQL file included in `/SQL/db.sql`

---

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Libraries:** TCPDF (PDF generation), PHPMailer (OTP email)

---


