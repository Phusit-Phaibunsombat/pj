<?php
/*
Sample Data for Marathon Registration System
This file contains sample data to test the database schema
*/

$sample_data_sql = "
-- Insert sample race categories
INSERT INTO RACE_CATEGORY (name, distance_km, start_time, time_limit, giveaway_type) VALUES
('Mini Marathon', 10.5, '06:00:00', '02:30:00', 'T-Shirt + Medal'),
('Half Marathon', 21.1, '05:30:00', '03:30:00', 'T-Shirt + Medal + Finisher Certificate'),
('Full Marathon', 42.2, '05:00:00', '07:00:00', 'T-Shirt + Medal + Finisher Certificate + Trophy');

-- Insert age groups for each category
INSERT INTO AGE_GROUP (category_id, gender, min_age, max_age, label) VALUES
-- Mini Marathon age groups
(1, 'M', 18, 29, '18-29'),
(1, 'M', 30, 39, '30-39'),
(1, 'M', 40, 49, '40-49'),
(1, 'M', 50, 99, '50+'),
(1, 'F', 18, 29, '18-29'),
(1, 'F', 30, 39, '30-39'),
(1, 'F', 40, 49, '40-49'),
(1, 'F', 50, 99, '50+'),
-- Half Marathon age groups
(2, 'M', 18, 29, '18-29'),
(2, 'M', 30, 39, '30-39'),
(2, 'M', 40, 49, '40-49'),
(2, 'M', 50, 99, '50+'),
(2, 'F', 18, 29, '18-29'),
(2, 'F', 30, 39, '30-39'),
(2, 'F', 40, 49, '40-49'),
(2, 'F', 50, 99, '50+'),
-- Full Marathon age groups
(3, 'M', 18, 29, '18-29'),
(3, 'M', 30, 39, '30-39'),
(3, 'M', 40, 49, '40-49'),
(3, 'M', 50, 99, '50+'),
(3, 'F', 18, 29, '18-29'),
(3, 'F', 30, 39, '30-39'),
(3, 'F', 40, 49, '40-49'),
(3, 'F', 50, 99, '50+');

-- Insert price rates
INSERT INTO PRICE_RATE (category_id, group_type, amount) VALUES
-- Mini Marathon prices
(1, 'Standard', 800.00),
(1, 'Senior', 600.00),
(1, 'Disabled', 400.00),
-- Half Marathon prices
(2, 'Standard', 1200.00),
(2, 'Senior', 900.00),
(2, 'Disabled', 600.00),
-- Full Marathon prices
(3, 'Standard', 1800.00),
(3, 'Senior', 1350.00),
(3, 'Disabled', 900.00);

-- Insert shipping options
INSERT INTO SHIPPING_OPTION (type, cost, detail) VALUES
('Pickup', 0.00, 'Central World Shopping Center - Ground Floor, Event Counter'),
('EMS', 50.00, 'EMS delivery to registered address within Thailand'),
('EMS', 150.00, 'EMS express delivery to registered address within Thailand');

-- Insert sample runners
INSERT INTO RUNNER (first_name, last_name, date_of_birth, gender, citizen_id, phone, email, address, is_disabled) VALUES
('สมชาย', 'วิ่งเร็ว', '1990-05-15', 'M', '1234567890123', '0812345678', 'somchai.runner@email.com', '123 ถนนสุขุมวิท กรุงเทพฯ 10110', FALSE),
('สมหญิง', 'วิ่งดี', '1985-08-22', 'F', '1234567890124', '0823456789', 'somying.runner@email.com', '456 ถนนพหลโยธิน กรุงเทพฯ 10400', FALSE),
('วิชัย', 'นักวิ่ง', '1975-12-10', 'M', '1234567890125', '0834567890', 'wichai.runner@email.com', '789 ถนนรัชดาภิเษก กรุงเทพฯ 10320', FALSE),
('มาลี', 'รักวิ่ง', '1995-03-08', 'F', '1234567890126', '0845678901', 'malee.runner@email.com', '321 ถนนเพชรบุรี กรุงเทพฯ 10400', TRUE);

-- Insert sample registrations
INSERT INTO REGISTRATION (runner_id, category_id, price_id, shipping_id, shirt_size, bib_number, status) VALUES
(1, 2, 4, 1, 'M', 'HM001', 'Paid'),
(2, 1, 1, 2, 'S', 'MM001', 'Paid'),
(3, 3, 7, 1, 'L', 'FM001', 'Pending'),
(4, 1, 3, 3, 'M', 'MM002', 'Paid');

-- Insert sample payments
INSERT INTO PAYMENT (reg_id, total_amount, payment_time, payment_method, status, transaction_ref) VALUES
(1, 1200.00, '2026-01-08 14:30:00', 'Credit Card', 'Success', 'TXN001234567'),
(2, 850.00, '2026-01-08 16:45:00', 'QR Code', 'Success', 'QR001234568'),
(4, 550.00, '2026-01-09 09:15:00', 'Bank Transfer', 'Success', 'BT001234569');
";

// Function to insert sample data
function insertSampleData($pdo) {
    try {
        $statements = explode(';', $sample_data_sql);
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

// If this file is accessed directly, show the sample data
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    header('Content-Type: text/plain');
    echo $sample_data_sql;
}
?>