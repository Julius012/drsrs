-- Create the database
CREATE DATABASE IF NOT EXISTS UserManagement;

-- Use the created database
USE UserManagement;

-- Create the users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('staff', 'attachee', 'guest') NOT NULL,
    institution VARCHAR(255),
    academic_year VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role ENUM("Admin","Attachee","Mentor")
) ENGINE=InnoDB;

-- Create the reports table for attachees
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    report_type ENUM('weekly', 'final') NOT NULL,
    report_file VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;
