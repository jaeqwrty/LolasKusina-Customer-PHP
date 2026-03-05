-- =================================================================================
-- Lola's Kusina Order System - Database Schema
-- Timezone: Asia/Manila (UTC+8) - As per NFR-T03
-- =================================================================================

-- Create the database and select it to prevent Error Code: 1046
CREATE DATABASE IF NOT EXISTS lolas_kusina_db;
USE lolas_kusina_db;

SET time_zone = '+08:00';

-- =================================================================================
-- 1. USERS TABLE (Customers, Admins, Resellers)
-- Satisfies FR-001 (Admin Access), FR-C17 (Optional Customer Login)
-- =================================================================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('admin', 'reseller', 'customer') NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    phone_number VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255), -- Nullable for guest customers who don't create accounts
    last_login_at TIMESTAMP NULL, -- Security tracking for admin/reseller sessions (NFR-S02)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 2. RESELLER PROFILES TABLE
-- Satisfies FR-008 (Referral Link), FR-009 (Configurable Commission Rules)
-- =================================================================================
CREATE TABLE reseller_profiles (
    reseller_id INT PRIMARY KEY,
    referral_code VARCHAR(50) UNIQUE NOT NULL,
    commission_type ENUM('percentage', 'fixed', 'hybrid') NOT NULL DEFAULT 'percentage',
    commission_value DECIMAL(10, 2) NOT NULL, -- e.g., 10.00 for 10% or 50.00 for 50 PHP
    wallet_balance DECIMAL(10, 2) DEFAULT 0.00,
    gcash_name VARCHAR(100) NULL, -- Needed by admin to process the commission settlement
    gcash_number VARCHAR(20) NULL, -- Where the admin sends the payout
    FOREIGN KEY (reseller_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 3. MENU ITEMS TABLE (Packages & Trays)
-- Satisfies FR-C01 (Featured Packages), FR-002 (Package & Tray Management)
-- =================================================================================
CREATE TABLE menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_type ENUM('package', 'tray') NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    base_price DECIMAL(10, 2) NOT NULL,
    image_path VARCHAR(255), -- Stores path to file under 200KB (NFR-P02)
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 3.5 PACKAGE INCLUSIONS TABLE
-- Satisfies FR-C02 (Customize Existing Package inclusions)
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
-- 4. ORDERS TABLE
-- Satisfies FR-C08 (Ref Number), FR-C05 (Delivery/Pickup), FR-C06 (Distance Pricing), 
-- FR-005 (Workflow Management), FR-003 (Manual Entry)
-- =================================================================================
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    reference_number VARCHAR(20) UNIQUE NOT NULL, -- Alphanumeric ref number
    customer_id INT NULL, -- NULL if guest checkout
    guest_name VARCHAR(150) NULL, -- Used if customer_id is NULL
    guest_phone VARCHAR(20) NULL, -- Used if customer_id is NULL
    reseller_id INT NULL, -- Tracks which reseller referred this order
    
    order_type ENUM('delivery', 'pickup') NOT NULL,
    delivery_address TEXT NULL,
    map_pinned_lat DECIMAL(10, 8) NULL, -- Google Maps pinning (FR-C14)
    map_pinned_lng DECIMAL(11, 8) NULL,
    distance_km DECIMAL(6, 2) NULL, -- Used to compute delivery fee
    
    scheduled_datetime DATETIME NOT NULL, -- Target delivery or pickup date/time (FR-O06)
    
    rider_name VARCHAR(100) NULL, -- Needed for Out for Delivery SMS (FR-C12)
    rider_phone VARCHAR(20) NULL, -- Needed for Out for Delivery SMS (FR-C12)
    
    subtotal DECIMAL(10, 2) NOT NULL,
    delivery_fee DECIMAL(10, 2) DEFAULT 0.00,
    grand_total DECIMAL(10, 2) NOT NULL,
    downpayment_required DECIMAL(10, 2) NOT NULL,
    amount_paid DECIMAL(10, 2) DEFAULT 0.00, -- Admin inputs this after verifying the receipt
    receipt_image_path VARCHAR(255) NULL, -- Proof of payment upload
    
    status ENUM(
        'Pending Approval', 
        'Approved', 
        'In Preparation', 
        'Out for Delivery', 
        'Ready for Pickup', 
        'Completed', 
        'Cancelled', -- Added to handle customer cancellations
        'Rejected',  -- Added to handle invalid receipts/spam
        'Archived'
    ) DEFAULT 'Pending Approval',
    
    admin_remarks TEXT NULL, -- Internal notes (e.g., "Rejected: Blurry GCash receipt")
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (reseller_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 5. ORDER ITEMS TABLE (Cart contents)
-- Satisfies FR-C02 & FR-C03 (Customization & Build Your Own)
-- =================================================================================
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price_at_time DECIMAL(10, 2) NOT NULL, -- Locks the price in case it changes later
    customization_notes TEXT NULL, -- E.g., "No pork", "Extra spicy"
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 6. COMMISSIONS TABLE
-- Satisfies FR-010 (Reseller Performance Tracking)
-- =================================================================================
CREATE TABLE commissions (
    commission_id INT AUTO_INCREMENT PRIMARY KEY,
    reseller_id INT NOT NULL,
    order_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'paid') DEFAULT 'pending',
    paid_at TIMESTAMP NULL, -- FR-O10 Tracks exactly when the commission was settled
    settlement_reference VARCHAR(100) NULL, -- e.g., GCash reference number for the payout
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (reseller_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 6.5 SMS QUEUE TABLE
-- Satisfies NFR-P04 (SMS Reliability & 3 Retry Attempts) suitable for Shared Hosting
-- =================================================================================
CREATE TABLE sms_queue (
    sms_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    retry_count INT DEFAULT 0, -- Tracks up to 3 retries (NFR-P04)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 7. REVIEWS TABLE (Testimonials)
-- Satisfies FR-C18 (Authenticated Review Submission), FR-014 (Moderation)
-- =================================================================================
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNIQUE NULL, -- Nullable to allow admin to upload offline testimonials (FR-O04)
    customer_id INT NULL, -- Nullable for offline testimonials
    guest_reviewer_name VARCHAR(150) NULL, -- Used if customer_id is NULL
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    proof_image_path VARCHAR(255) NULL,
    is_approved BOOLEAN DEFAULT FALSE, -- Admin moderation control
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 8. AUDIT LOGS TABLE
-- Satisfies FR-013 & NFR-S06 (Immutable timestamped logs of admin actions)
-- =================================================================================
CREATE TABLE audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- Admin who performed the action
    action VARCHAR(100) NOT NULL, -- e.g., "Updated Order Status", "Changed Menu Price"
    description TEXT NOT NULL, -- Detailed description of the change
    ip_address VARCHAR(45) NULL, -- Tracks admin IP for better security tracing (NFR-S06)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Immutable, no update timestamp
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================================
-- 9. SYSTEM SETTINGS TABLE
-- Satisfies FR-C07 (Default owner policy for downpayments)
-- =================================================================================
CREATE TABLE system_settings (
    setting_key VARCHAR(50) PRIMARY KEY, -- e.g., 'default_downpayment_percentage'
    setting_value VARCHAR(255) NOT NULL,
    description TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;