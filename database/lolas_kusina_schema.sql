-- =================================================================================
-- Lola's Kusina Order System - Database Schema (SOLID / Highly Normalized)
-- Target RDBMS: MySQL (InnoDB)
-- Timezone: Asia/Manila (UTC+8) - As per NFR-T03
-- =================================================================================

-- Create the database and select it to prevent Error Code: 1046
CREATE DATABASE IF NOT EXISTS lolas_kusina_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; -- Handles Filipino names with 'Ñ'
USE lolas_kusina_db;

SET time_zone = '+08:00';

-- =================================================================================
-- 1. USERS TABLE (Customers, Admins, Resellers)
-- Single Responsibility: Core Authentication and Base User Profile
-- =================================================================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('admin', 'reseller', 'customer') NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    phone_number VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255), 
    
    reset_token VARCHAR(255) NULL, -- Enterprise Auth: Secure password recovery (FR-C17)
    reset_token_expires_at DATETIME NULL,
    
    is_active BOOLEAN DEFAULT TRUE, -- Allows soft-deleting/banning accounts without breaking logs
    last_login_at TIMESTAMP NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_role (role) -- Speeds up queries like "Show me all resellers"
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 1.5 GUEST PROFILES TABLE
-- Single Responsibility: Store details for non-registered checkout customers
-- =================================================================================
CREATE TABLE guest_profiles (
    guest_id INT AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(150) NOT NULL,
    guest_phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 2. RESELLER PROFILES TABLE
-- Single Responsibility: Reseller-specific financial and tracking data
-- =================================================================================
CREATE TABLE reseller_profiles (
    reseller_id INT PRIMARY KEY,
    referral_code VARCHAR(50) UNIQUE NOT NULL,
    commission_type ENUM('percentage', 'fixed', 'hybrid') NOT NULL DEFAULT 'percentage',
    commission_percentage DECIMAL(5, 2) DEFAULT 0.00, -- FR-O09: Hybrid math (percentage part)
    commission_fixed DECIMAL(10, 2) DEFAULT 0.00, -- FR-O09: Hybrid math (fixed part)
    downpayment_override DECIMAL(5, 2) NULL, -- FR-C07: Reseller-specific policy
    wallet_balance DECIMAL(10, 2) DEFAULT 0.00,
    gcash_name VARCHAR(100) NULL, 
    gcash_number VARCHAR(20) NULL, 
    FOREIGN KEY (reseller_id) REFERENCES users(user_id) ON DELETE CASCADE,
    
    INDEX idx_referral_code (referral_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 3. MENU ITEMS TABLE (Packages & Trays)
-- Single Responsibility: Store product catalog baseline data
-- =================================================================================
CREATE TABLE menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_type ENUM('package', 'tray') NOT NULL,
    category VARCHAR(50) NULL, -- FR-C03: UI Grouping (e.g., 'Pork', 'Seafood')
    name VARCHAR(150) NOT NULL,
    description TEXT,
    base_price DECIMAL(10, 2) NOT NULL,
    image_path VARCHAR(255), 
    is_featured BOOLEAN DEFAULT FALSE, -- FR-C01: Browse Featured Packages
    is_available BOOLEAN DEFAULT TRUE, -- Temporarily out of stock
    is_archived BOOLEAN DEFAULT FALSE, -- Soft delete for FR-O11 Analytics integrity
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 3.5 PACKAGE INCLUSIONS TABLE
-- Single Responsibility: Map default trays inside a predefined package
-- =================================================================================
CREATE TABLE package_inclusions (
    inclusion_id INT AUTO_INCREMENT PRIMARY KEY,
    package_id INT NOT NULL,
    tray_id INT NOT NULL,
    default_quantity INT NOT NULL DEFAULT 1,
    
    FOREIGN KEY (package_id) REFERENCES menu_items(item_id) ON DELETE CASCADE,
    FOREIGN KEY (tray_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 4. ORDERS TABLE (CORE)
-- Single Responsibility: Manage the overarching order lifecycle and relationships
-- =================================================================================
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    reference_number VARCHAR(20) UNIQUE NOT NULL, 
    customer_id INT NULL, 
    guest_id INT NULL, 
    reseller_id INT NULL, 
    
    scheduled_datetime DATETIME NOT NULL, 
    
    status ENUM(
        'Pending Approval', 
        'Approved', 
        'In Preparation', 
        'Out for Delivery', 
        'Ready for Pickup', 
        'Completed', 
        'Cancelled', 
        'Rejected'
    ) DEFAULT 'Pending Approval',
    
    customer_order_notes TEXT NULL, -- e.g., "Don't ring the doorbell"
    admin_remarks TEXT NULL, -- Internal notes for rejection reasons
    is_archived BOOLEAN DEFAULT FALSE, -- FR-O12: Clean dashboard vs Analytics
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- SOLID/Integrity Fix: RESTRICT protects order history even if users are deactivated
    FOREIGN KEY (customer_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (guest_id) REFERENCES guest_profiles(guest_id) ON DELETE RESTRICT,
    FOREIGN KEY (reseller_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    
    INDEX idx_order_status (status),
    INDEX idx_scheduled_datetime (scheduled_datetime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 4.1 ORDER PAYMENTS TABLE
-- Single Responsibility: Handle all financial data for an order
-- =================================================================================
CREATE TABLE order_payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNIQUE NOT NULL,
    
    subtotal DECIMAL(10, 2) NOT NULL,
    delivery_fee DECIMAL(10, 2) DEFAULT 0.00,
    grand_total DECIMAL(10, 2) NOT NULL,
    downpayment_required DECIMAL(10, 2) NOT NULL,
    amount_paid DECIMAL(10, 2) DEFAULT 0.00, 
    
    payment_status ENUM('Unpaid', 'Downpayment Paid', 'Fully Paid', 'Refunded') DEFAULT 'Unpaid',
    
    payment_method VARCHAR(50) NULL, -- 'GCash', 'Cash'
    customer_reference_no VARCHAR(100) NULL, -- Manual entry for verification
    receipt_image_path VARCHAR(255) NULL, 
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    
    INDEX idx_payment_status (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 4.2 ORDER FULFILLMENTS TABLE
-- Single Responsibility: Handle logistics (address, map pins, distance, riders)
-- =================================================================================
CREATE TABLE order_fulfillments (
    fulfillment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNIQUE NOT NULL,
    
    fulfillment_type ENUM('delivery', 'pickup') NOT NULL,
    delivery_address TEXT NULL,
    delivery_city VARCHAR(100) NULL, 
    delivery_barangay VARCHAR(100) NULL, -- FR-O12 Search by area
    delivery_instructions TEXT NULL, -- Landmarks for local riders
    map_pinned_lat DECIMAL(10, 8) NULL, 
    map_pinned_lng DECIMAL(11, 8) NULL,
    distance_km DECIMAL(6, 2) NULL, 
    
    rider_name VARCHAR(100) NULL, 
    rider_phone VARCHAR(20) NULL, 
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    
    INDEX idx_delivery_barangay (delivery_barangay)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 4.5 ORDER STATUS HISTORY
-- Single Responsibility: Maintain a permanent timeline for the order tracker (FR-O05)
-- =================================================================================
CREATE TABLE order_status_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status ENUM(
        'Pending Approval', 'Approved', 'In Preparation', 
        'Out for Delivery', 'Ready for Pickup', 'Completed', 
        'Cancelled', 'Rejected'
    ) NOT NULL,
    remarks TEXT NULL, -- Optional update for the customer
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    INDEX idx_history_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 5. ORDER ITEMS TABLE (Cart contents)
-- Single Responsibility: Track individual items/packages ordered
-- =================================================================================
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price_at_time DECIMAL(10, 2) NOT NULL, -- Locks price for historical accuracy
    customization_notes TEXT NULL, 
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 5.1 ORDER ITEM INCLUSIONS TABLE (Customized Package Contents)
-- Single Responsibility: Allow exact querying for the Kitchen List (FR-O07)
-- =================================================================================
CREATE TABLE order_item_inclusions (
    order_inclusion_id INT AUTO_INCREMENT PRIMARY KEY,
    order_item_id INT NOT NULL,
    tray_id INT NOT NULL,
    quantity INT NOT NULL, -- Tracks swaps or extra trays
    price_adjustment DECIMAL(10, 2) DEFAULT 0.00, -- Handles premium tray swaps
    
    FOREIGN KEY (order_item_id) REFERENCES order_items(order_item_id) ON DELETE CASCADE,
    FOREIGN KEY (tray_id) REFERENCES menu_items(item_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 6. COMMISSIONS TABLE
-- Single Responsibility: Track individual commission payouts
-- =================================================================================
CREATE TABLE commissions (
    commission_id INT AUTO_INCREMENT PRIMARY KEY,
    reseller_id INT NOT NULL,
    order_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    paid_at TIMESTAMP NULL, 
    settlement_reference VARCHAR(100) NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (reseller_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    
    INDEX idx_commission_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 6.5 SMS QUEUE TABLE
-- Single Responsibility: Handle asynchronous SMS processing with retries (NFR-P04)
-- =================================================================================
CREATE TABLE sms_queue (
    sms_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    retry_count INT DEFAULT 0, 
    gateway_reference_id VARCHAR(100) NULL, -- Semaphore API tracking
    fail_reason TEXT NULL, -- Debugging for Jullian (T012)
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    INDEX idx_sms_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 7. REVIEWS TABLE (Testimonials)
-- Single Responsibility: Store feedback (FR-C18 & FR-O04)
-- =================================================================================
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNIQUE NULL, 
    customer_id INT NULL, 
    guest_reviewer_name VARCHAR(150) NULL, 
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    proof_image_path VARCHAR(255) NULL,
    is_approved BOOLEAN DEFAULT FALSE, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 8. AUDIT LOGS TABLE
-- Single Responsibility: Immutable accountability logs (NFR-S06)
-- =================================================================================
CREATE TABLE audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, 
    action VARCHAR(100) NOT NULL, 
    description TEXT NOT NULL, 
    ip_address VARCHAR(45) NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 9. SYSTEM SETTINGS TABLE
-- Single Responsibility: Global configurations (NFR-T01 Pricing Logic)
-- =================================================================================
CREATE TABLE system_settings (
    setting_key VARCHAR(50) PRIMARY KEY, 
    setting_value VARCHAR(255) NOT NULL,
    setting_type ENUM('string', 'number', 'boolean') DEFAULT 'string', -- UI Rendering hint
    description TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;