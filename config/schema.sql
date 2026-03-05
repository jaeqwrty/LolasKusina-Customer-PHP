-- Lola's Kusina Database Schema

CREATE DATABASE IF NOT EXISTS lolas_kusina;
USE lolas_kusina;

-- Packages Table
CREATE TABLE IF NOT EXISTS packages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100),
    persons_served VARCHAR(50),
    rating DECIMAL(2, 1) DEFAULT 0,
    reviews_count INT DEFAULT 0,
    sales_count INT DEFAULT 0,
    is_bestseller BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Package Items Table (items included in each package)
CREATE TABLE IF NOT EXISTS package_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    package_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    item_description TEXT,
    quantity VARCHAR(50),
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
);

-- Menu Items Table (for building custom packages)
CREATE TABLE IF NOT EXISTS menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    category ENUM('main_dish', 'side_dish', 'dessert', 'beverage') NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Customers Table
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    password_hash VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    customer_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    delivery_datetime DATETIME NOT NULL,
    payment_method ENUM('cod', 'gcash', 'bank') NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    delivery_fee DECIMAL(10, 2) DEFAULT 50.00,
    discount DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'delivering', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    item_type ENUM('package', 'menu_item') NOT NULL,
    item_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    package_id INT,
    customer_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- Insert Sample Packages
INSERT INTO packages (name, description, price, image, category, persons_served, rating, reviews_count, sales_count, is_bestseller) VALUES
('Paborito Package', 'Good for 6-7 pax', 2500.00, 'paborito-package.jpg', 'all_packages', '6-7 pax', 4.8, 20, 150, TRUE),
('Family Fiesta', 'Good for 10-12 pax', 4200.00, 'family-fiesta.jpg', 'all_packages', '10-12 pax', 4.9, 35, 200, TRUE),
('Salo-Salo Special', 'Good for 15-20 pax', 6500.00, 'salo-salo.jpg', 'all_packages', '15-20 pax', 4.7, 18, 95, FALSE);

-- Insert Sample Package Items
INSERT INTO package_items (package_id, item_name, item_description, quantity) VALUES
(1, 'Pancit Canton', 'Stir-Fried Yellow Noodles', 'Good for 6-7'),
(1, 'Lumpiang Shanghai', 'Mini Spring Rolls', '25 pieces'),
(1, 'Lechon Kawali', 'Crispy Fried Pork Belly', '1 kg'),
(1, 'Steamed Rice', 'Plain White Rice', 'Good for 6-7'),
(2, 'Chicken Inasal', 'Grilled Marinated Chicken', '8 pieces'),
(2, 'Kare-Kare', 'Oxtail in Peanut Sauce', 'Good for 10-12'),
(2, 'Lumpia Shanghai', 'Mini Spring Rolls', '50 pieces'),
(2, 'Garlic Rice', 'Fried Rice with Garlic', 'Good for 10-12'),
(2, 'Halo-Halo', 'Mixed Dessert', '12 servings');

-- Insert Sample Menu Items
INSERT INTO menu_items (name, description, price, image, category) VALUES
('Lechon Kawali', 'Crispy Fried Pork Belly', 450.00, 'lechon-kawali.jpg', 'main_dish'),
('Chicken Inasal', 'Grilled Marinated Chicken', 380.00, 'chicken-inasal.jpg', 'main_dish'),
('Kare-Kare', 'Oxtail in Peanut Sauce', 520.00, 'kare-kare.jpg', 'main_dish'),
('Pata (Crispy)', 'Deep Fried Pork Leg', 680.00, 'pata.jpg', 'main_dish'),
('Beef Caldereta', 'Beef in Tomato Sauce', 480.00, 'caldereta.jpg', 'main_dish'),
('Pancit Canton', 'Stir-Fried Noodles', 200.00, 'pancit-canton.jpg', 'side_dish'),
('Lumpia Shanghai', 'Mini Spring Rolls', 180.00, 'lumpia.jpg', 'side_dish'),
('Steamed Rice', 'Plain White Rice', 150.00, 'rice.jpg', 'side_dish'),
('Garlic Rice', 'Fried Rice with Garlic', 180.00, 'garlic-rice.jpg', 'side_dish'),
('Halo-Halo', 'Mixed Dessert', 85.00, 'halo-halo.jpg', 'dessert'),
('Buko Pandan', 'Coconut Pandan Dessert', 75.00, 'buko-pandan.jpg', 'dessert'),
('Leche Flan', 'Caramel Custard', 90.00, 'leche-flan.jpg', 'dessert');
