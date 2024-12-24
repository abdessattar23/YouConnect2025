CREATE DATABASE IF NOT EXISTS party_tickets;
USE party_tickets;

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ticket_number VARCHAR(50) NOT NULL UNIQUE,
    UNIQUE KEY unique_booking (email, full_name)
);
