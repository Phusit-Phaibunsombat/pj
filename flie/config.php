<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'marathon_registration');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application settings
define('SITE_NAME', 'Bangkok Marathon 2026');
define('SITE_URL', 'http://localhost:8000');

// Database connection
function getConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            // For demo purposes, return null if database is not available
            return null;
        }
    }
    
    return $pdo;
}

// Sample data for demo mode (when database is not available)
function getSampleCategories() {
    return [
        ['id' => 1, 'name' => 'Mini Marathon', 'distance_km' => 10.5, 'start_time' => '06:00:00', 'time_limit' => '02:30:00', 'giveaway_type' => 'เสื้อ + เหรียญ'],
        ['id' => 2, 'name' => 'Half Marathon', 'distance_km' => 21.1, 'start_time' => '05:30:00', 'time_limit' => '03:30:00', 'giveaway_type' => 'เสื้อ + เหรียญ + ใบประกาศ'],
        ['id' => 3, 'name' => 'Full Marathon', 'distance_km' => 42.2, 'start_time' => '05:00:00', 'time_limit' => '07:00:00', 'giveaway_type' => 'เสื้อ + เหรียญ + ใบประกาศ + ถ้วยรางวัล']
    ];
}

function getSamplePrices() {
    return [
        1 => ['Standard' => 800, 'Senior' => 600, 'Disabled' => 400],
        2 => ['Standard' => 1200, 'Senior' => 900, 'Disabled' => 600],
        3 => ['Standard' => 1800, 'Senior' => 1350, 'Disabled' => 900]
    ];
}

function getSampleShipping() {
    return [
        ['id' => 1, 'type' => 'Pickup', 'cost' => 0, 'detail' => 'รับที่งาน - Central World ชั้น G'],
        ['id' => 2, 'type' => 'EMS', 'cost' => 50, 'detail' => 'EMS ส่งถึงบ้าน (ปกติ)'],
        ['id' => 3, 'type' => 'EMS', 'cost' => 150, 'detail' => 'EMS ส่งถึงบ้าน (Express)']
    ];
}

// Utility functions
function calculateAge($dateOfBirth) {
    $today = new DateTime();
    $birthDate = new DateTime($dateOfBirth);
    return $today->diff($birthDate)->y;
}

function formatThaiDate($date) {
    $months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[date('n', $timestamp)];
    $year = date('Y', $timestamp) + 543;
    
    return "$day $month $year";
}

function generateBibNumber($categoryId) {
    $prefixes = [1 => 'MM', 2 => 'HM', 3 => 'FM'];
    $prefix = $prefixes[$categoryId] ?? 'XX';
    return $prefix . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}
?>