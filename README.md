# Nexus Appointments

**Author:** Michael Ahenkan-Yeboah
**Track:** Track 1C - Full Stack Web Development

## Description of the Application
Nexus Appointments is a full-stack web application designed to solve the practical problem of service scheduling for a solo business. It provides a seamless booking platform where clients can authenticate, browse available services (such as consultations and premium packages), and manage their schedules. 

The application features full CRUD functionality and a protected user dashboard, allowing registered clients to create new bookings, read/view their upcoming schedule, and cancel (soft-delete) existing appointments.

##  Architecture & Tech Choices
To ensure a lightweight, secure, and easily deployable application, I made the following architectural decisions:

*   **Frontend (HTML5, CSS3, Vanilla PHP):** I opted for semantic HTML and custom vanilla CSS rather than a heavy framework like Bootstrap or Tailwind. This ensures the codebase remains clean, easy to read, and fully responsive across all devices without unnecessary bloat.
*   **Backend (PHP 8 & PDO):** I built the RESTful backend using PHP. I chose PHP's native session-based authentication (`$_SESSION`) over JWT because it provides robust, built-in security for tracking logged-in users that is highly efficient for a server-rendered application. Data is handled securely using PDO prepared statements to prevent SQL injection.
*   **Database (MySQL):** A relational database was chosen to handle the strict relationship between users and their appointments (using foreign keys). Cancelled appointments utilize a "soft delete" (updating a status column to 'cancelled') rather than a hard delete, preserving data integrity and audit history for the business.

##  Local Setup Instructions
To run this application locally on your machine, please follow these steps:

**1. Environment Setup**
* Ensure you have a local server environment installed (such as XAMPP, WAMP, or Laragon).
* Clone or download this repository and place the `nexus_appointments` folder directly inside your server's root web directory (e.g., `C:\xampp\htdocs\nexus_appointments`).

**2. Database Configuration**
* Start your Apache and MySQL modules.
* Open phpMyAdmin (`http://localhost/phpmyadmin`).
* Create a new database named `nexus_appointments`.
* Run the following SQL commands to create the required tables:

```sql
CREATE TABLE users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE appointments (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED,
    service_name VARCHAR(100) NOT NULL,
    booking_date DATETIME NOT NULL,
    status VARCHAR(20) DEFAULT 'confirmed',
    FOREIGN KEY (user_id) REFERENCES users(id)
);
