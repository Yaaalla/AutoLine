-- AutoLux Database Schema

CREATE DATABASE IF NOT EXISTS autolux_db;
USE autolux_db;

-- Cars Table
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    price_per_day DECIMAL(10, 2) NOT NULL,
    transmission ENUM('Auto', 'Manual') DEFAULT 'Auto',
    seats INT NOT NULL,
    fuel_type VARCHAR(20) NOT NULL,
    image_path TEXT NOT NULL,
    status ENUM('available', 'reserved', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    pickup_date DATETIME NOT NULL,
    return_date DATETIME NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);

-- Car Images Table
CREATE TABLE IF NOT EXISTS car_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    image_path TEXT NOT NULL,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert initial admin (password: admin123)
-- In production, passwords should be hashed using password_hash()
INSERT INTO admins (username, password) VALUES ('admin', 'admin123');

-- Insert initial fleet data
INSERT INTO cars (brand, model, price_per_day, transmission, seats, fuel_type, image_path, status) VALUES 
('Lamborghini', 'Urus', 1200.00, 'Auto', 5, 'Gasoline', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAXdro0R7MfcgPJmF_Eh2iGp6GgJsDaSxD7xRaLX0Aou6sNnC7NMdVOgI6OJ6PHb5jukhojRoVdfnyheecYtrKgiWdqodDyPqnDVC8XDXCOzdMMlGci4QrFiyFawZ19uuHGsF3aXKNqsta4O5nsMaUzxjg9-gOdZL3SPUb9WZH6iv9xfjaVXcYEcKafbFNSluW3j0FlxBZEP1O_rXYbp_9PF0asLRU9qUb4G9LIAUrOYPK7gjz8fT325sBFt9L6clKVnxQTzuJhIzrK', 'available'),
('Porsche', '911 GT3', 850.00, 'Auto', 2, 'Gasoline', 'https://lh3.googleusercontent.com/aida-public/AB6AXuC0l_sRZMoxb7F493pwLvY7LEpkz5ZLkYpJ0tYhkYlb-c5mXvMg0zW-RClU0KN7O07WWSXUjt2T5tn7ITqcOlArZHuYVLnxhn85qyVlxRkRpyI4ewzWY5Ds9tjWKsqE8nY_YjuHCaEDJGT6hH6bp0qppUTeU4igN5BaSiTRsAS3W5i1LicKqEytfvWNkjbsAlYfDfIOJw-nRXCNW5L9RndA175-vqMdRmi-BqNGNaOXbE_9qD6luz83Q02_frILKprAQF9svwabbh5R', 'available'),
('BMW', 'M8 Comp', 750.00, 'Auto', 4, 'Gasoline', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBP68avjsWcDAswRS9Y9s3d8Ho8IUkaWElhmJHuGdbHLVLRxa3Z8omTVR7N7MZPiUoTrFxD6fFxub1a94aK-obJWlsDfbf-uW1PVA-5QVQeTGpA-n6QXY3IJGV1MjiFNoqcboHu9x-eB-rgMHmOHMSxopQmMbkXca0dbU3pHsPYv-8DfxXfBX2nO4iS681GDfHjO2l3axKX0QFir5RYDqlLWnp1Udi3O_dBS5mZ5auJi5CQ3ZG1GkZtEvCu_vUZTr6pWRTn43ISj2qc', 'available'),
('Range Rover', 'Autobiography', 600.00, 'Auto', 5, 'Hybrid', 'https://lh3.googleusercontent.com/aida-public/AB6AXuB4X5xQhlkG8Kiy3aBbH6jtjU1deDjvtdE5QtnktwpHjTijY-ru5j6DsNYkdbuUUkzif9opjWK-xVDAsicbRzw-ZrVAGfBtnNqC4rRbQ8QXlOYBCTcNSUcu2oKPiCN9MyvVx_1pFUDaz6_8h0YhHaRJvuRr0ruQTErlNvKM0tRITBQ8QrazwbUK97Bzn3yysQWEavoQP0p-KikcbPm9R2UwAryEFqi6F6rCQx1FrZJjWsrBZL4Un2J9MOtv1vqD3Pk-KD8fy7rzBf1a', 'available');

-- Settings Table
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Activity Logs Table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);

-- Insert initial settings
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
('site_name', 'AutoLux'),
('contact_email', 'admin@autolux.com'),
('social_fb', 'https://facebook.com/autolux'),
('social_ig', 'https://instagram.com/autolux');
