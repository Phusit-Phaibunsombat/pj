<?php
/*
Marathon Registration System Database Schema
Created: January 9, 2026
*/

$schema_sql = "
-- 1. RUNNER (ผู้สมัคร) - Runner information
CREATE TABLE RUNNER (
    runner_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('M', 'F') NOT NULL,
    citizen_id VARCHAR(13) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    address TEXT NOT NULL,
    is_disabled BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. RACE_CATEGORY (ประเภทการแข่งขัน) - Race categories like Mini Marathon, Half Marathon
CREATE TABLE RACE_CATEGORY (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    distance_km DECIMAL(5,2) NOT NULL,
    start_time TIME NOT NULL,
    time_limit TIME NOT NULL,
    giveaway_type VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. AGE_GROUP (กลุ่มอายุ) - Age groups for each category
CREATE TABLE AGE_GROUP (
    group_id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    gender ENUM('M', 'F') NOT NULL,
    min_age INT NOT NULL,
    max_age INT NOT NULL,
    label VARCHAR(50) NOT NULL, -- e.g., \"18-29\", \"30-39\"
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES RACE_CATEGORY(category_id) ON DELETE CASCADE,
    INDEX idx_category_gender (category_id, gender)
);

-- 4. PRICE_RATE (อัตราค่าสมัคร) - Registration fees by participant type
CREATE TABLE PRICE_RATE (
    price_id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    group_type ENUM('Standard', 'Senior', 'Disabled') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES RACE_CATEGORY(category_id) ON DELETE CASCADE,
    INDEX idx_category_type (category_id, group_type)
);

-- 5. SHIPPING_OPTION (การจัดส่ง) - Shipping options for giveaways
CREATE TABLE SHIPPING_OPTION (
    shipping_id INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('EMS', 'Pickup') NOT NULL,
    cost DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    detail TEXT, -- Address for EMS or pickup location details
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. REGISTRATION (การสมัคร) - Registration records
CREATE TABLE REGISTRATION (
    reg_id INT PRIMARY KEY AUTO_INCREMENT,
    runner_id INT NOT NULL,
    category_id INT NOT NULL,
    price_id INT NOT NULL,
    shipping_id INT NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    shirt_size ENUM('XS', 'S', 'M', 'L', 'XL', 'XXL') NOT NULL,
    bib_number VARCHAR(10) UNIQUE,
    status ENUM('Pending', 'Paid', 'Canceled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (runner_id) REFERENCES RUNNER(runner_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES RACE_CATEGORY(category_id) ON DELETE RESTRICT,
    FOREIGN KEY (price_id) REFERENCES PRICE_RATE(price_id) ON DELETE RESTRICT,
    FOREIGN KEY (shipping_id) REFERENCES SHIPPING_OPTION(shipping_id) ON DELETE RESTRICT,
    
    INDEX idx_runner (runner_id),
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_reg_date (reg_date)
);

-- 7. PAYMENT (การชำระเงิน) - Payment records
CREATE TABLE PAYMENT (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    reg_id INT NOT NULL UNIQUE,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_time TIMESTAMP NULL,
    payment_method ENUM('Credit Card', 'Bank Transfer', 'Cash', 'QR Code') NOT NULL,
    status ENUM('Success', 'Failed', 'Pending') DEFAULT 'Pending',
    transaction_ref VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (reg_id) REFERENCES REGISTRATION(reg_id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_payment_time (payment_time)
);

-- Additional indexes for performance
CREATE INDEX idx_runner_email ON RUNNER(email);
CREATE INDEX idx_runner_citizen_id ON RUNNER(citizen_id);
CREATE INDEX idx_registration_bib ON REGISTRATION(bib_number);

-- Views for common queries
-- View: Registration details with runner and category info
CREATE VIEW v_registration_details AS
SELECT 
    r.reg_id,
    r.bib_number,
    r.status as reg_status,
    r.shirt_size,
    r.reg_date,
    ru.first_name,
    ru.last_name,
    ru.email,
    ru.phone,
    rc.name as category_name,
    rc.distance_km,
    pr.amount as registration_fee,
    pr.group_type,
    so.type as shipping_type,
    so.cost as shipping_cost,
    p.status as payment_status,
    p.total_amount,
    p.payment_time
FROM REGISTRATION r
JOIN RUNNER ru ON r.runner_id = ru.runner_id
JOIN RACE_CATEGORY rc ON r.category_id = rc.category_id
JOIN PRICE_RATE pr ON r.price_id = pr.price_id
JOIN SHIPPING_OPTION so ON r.shipping_id = so.shipping_id
LEFT JOIN PAYMENT p ON r.reg_id = p.reg_id;

-- View: Category statistics
CREATE VIEW v_category_stats AS
SELECT 
    rc.category_id,
    rc.name as category_name,
    rc.distance_km,
    COUNT(r.reg_id) as total_registrations,
    COUNT(CASE WHEN r.status = 'Paid' THEN 1 END) as paid_registrations,
    COUNT(CASE WHEN r.status = 'Pending' THEN 1 END) as pending_registrations,
    SUM(CASE WHEN p.status = 'Success' THEN p.total_amount ELSE 0 END) as total_revenue
FROM RACE_CATEGORY rc
LEFT JOIN REGISTRATION r ON rc.category_id = r.category_id
LEFT JOIN PAYMENT p ON r.reg_id = p.reg_id
GROUP BY rc.category_id, rc.name, rc.distance_km;
";

// Function to execute schema
function createSchema($pdo) {
    try {
        $statements = explode(';', $schema_sql);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// If this file is accessed directly, show the schema
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    header('Content-Type: text/plain');
    echo $schema_sql;
}
?>