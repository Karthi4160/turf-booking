DROP DATABASE IF EXISTS turf_db;
CREATE DATABASE turf_db;
USE turf_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(15) UNIQUE,
    wallet_balance INT DEFAULT 0
);

CREATE TABLE turfs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    location VARCHAR(255),
    price_football INT,
    price_cricket INT
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    turf_id INT,
    sport ENUM('football', 'cricket'),
    booking_date DATE,
    slot_time VARCHAR(50),
    total_amount INT,
    status ENUM('confirmed', 'cancelled') DEFAULT 'confirmed',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (turf_id) REFERENCES turfs(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    amount INT,
    transaction_id VARCHAR(100),
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

CREATE TABLE blocked_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    turf_id INT,
    block_date DATE,
    slot_time VARCHAR(50)
);

INSERT INTO turfs (name, location, price_football, price_cricket) VALUES 
('Green Park Arena', 'Downtown', 1200, 900),
('Victory Turf', 'Suburbs', 1000, 800);
