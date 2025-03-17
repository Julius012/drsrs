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
    user_type ENUM('admin', 'supervisor', 'attachee') NOT NULL,
    institution VARCHAR(255),
    academic_year VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role ENUM("Admin","Supervisor","Attachee")
) ENGINE=InnoDB;


-- Create the reports table for reports
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    report_type ENUM('weekly', 'final') NOT NULL,
    report_data LONGBLOB NOT NULL,  -- Stores the actual file
    file_name VARCHAR(255) NOT NULL, -- Stores original file name
    file_type VARCHAR(50) NOT NULL,  -- Stores MIME type (e.g., application/pdf)
    approved TINYINT(1) DEFAULT NULL, -- 0 = Pending, 1 = Approved, Initially NULL
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


-- Create the notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    report_id INT NOT NULL,
    status TINYINT(1) NOT NULL, -- 1 = Approved, 0 = Rejected
    message VARCHAR(255) NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE
) ENGINE=InnoDB;
