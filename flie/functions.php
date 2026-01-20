<?php
require_once 'config.php';

// Get race categories
function getCategories() {
    $pdo = getConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM RACE_CATEGORY ORDER BY distance_km");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return getSampleCategories();
        }
    }
    return getSampleCategories();
}

// Get price rates
function getPriceRates() {
    $pdo = getConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->query("
                SELECT category_id, group_type, amount 
                FROM PRICE_RATE 
                ORDER BY category_id, group_type
            ");
            $rates = [];
            while ($row = $stmt->fetch()) {
                $rates[$row['category_id']][$row['group_type']] = $row['amount'];
            }
            return $rates;
        } catch (PDOException $e) {
            return getSamplePrices();
        }
    }
    return getSamplePrices();
}

// Get shipping options
function getShippingOptions() {
    $pdo = getConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM SHIPPING_OPTION ORDER BY cost");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return getSampleShipping();
        }
    }
    return getSampleShipping();
}

// Register new participant
function registerParticipant($data) {
    $pdo = getConnection();
    
    // For demo mode without database
    if (!$pdo) {
        return [
            'success' => true,
            'bib_number' => generateBibNumber($data['category']),
            'message' => 'ลงทะเบียนสำเร็จ (Demo Mode)',
            'reg_id' => rand(1000, 9999)
        ];
    }
    
    try {
        $pdo->beginTransaction();
        
        // Check if runner exists
        $stmt = $pdo->prepare("SELECT runner_id FROM RUNNER WHERE citizen_id = ? OR email = ?");
        $stmt->execute([$data['citizen_id'], $data['email']]);
        $existingRunner = $stmt->fetch();
        
        if ($existingRunner) {
            $runnerId = $existingRunner['runner_id'];
        } else {
            // Create new runner
            $stmt = $pdo->prepare("
                INSERT INTO RUNNER (first_name, last_name, date_of_birth, gender, citizen_id, 
                                  phone, email, address, is_disabled) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['date_of_birth'],
                $data['gender'],
                $data['citizen_id'],
                $data['phone'],
                $data['email'],
                $data['address'],
                $data['is_disabled'] ? 1 : 0
            ]);
            $runnerId = $pdo->lastInsertId();
        }
        
        // Determine price type
        $age = calculateAge($data['date_of_birth']);
        $priceType = 'Standard';
        if ($data['is_disabled']) {
            $priceType = 'Disabled';
        } elseif ($age >= 60) {
            $priceType = 'Senior';
        }
        
        // Get price ID
        $stmt = $pdo->prepare("
            SELECT price_id FROM PRICE_RATE 
            WHERE category_id = ? AND group_type = ?
        ");
        $stmt->execute([$data['category'], $priceType]);
        $priceId = $stmt->fetchColumn();
        
        // Generate bib number
        $bibNumber = generateBibNumber($data['category']);
        
        // Create registration
        $stmt = $pdo->prepare("
            INSERT INTO REGISTRATION (runner_id, category_id, price_id, shipping_id, 
                                    shirt_size, bib_number, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'Pending')
        ");
        $stmt->execute([
            $runnerId,
            $data['category'],
            $priceId,
            $data['shipping'],
            $data['shirt_size'],
            $bibNumber
        ]);
        $regId = $pdo->lastInsertId();
        
        $pdo->commit();
        
        return [
            'success' => true,
            'bib_number' => $bibNumber,
            'reg_id' => $regId,
            'message' => 'ลงทะเบียนสำเร็จ'
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ];
    }
}

// Check registration status
function checkRegistrationStatus($search) {
    $pdo = getConnection();
    
    // Demo data for when database is not available
    $sampleData = [
        '1234567890123' => [
            'first_name' => 'สมชาย',
            'last_name' => 'วิ่งเร็ว',
            'email' => 'somchai@email.com',
            'category_name' => 'Half Marathon',
            'bib_number' => 'HM001',
            'reg_status' => 'Paid',
            'reg_date' => '2026-01-08',
            'total_amount' => 1200
        ],
        'somchai@email.com' => [
            'first_name' => 'สมชาย',
            'last_name' => 'วิ่งเร็ว',
            'email' => 'somchai@email.com',
            'category_name' => 'Half Marathon',
            'bib_number' => 'HM001',
            'reg_status' => 'Paid',
            'reg_date' => '2026-01-08',
            'total_amount' => 1200
        ]
    ];
    
    if (!$pdo) {
        return isset($sampleData[$search]) ? $sampleData[$search] : null;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                r.reg_id, r.bib_number, r.status as reg_status, r.reg_date,
                ru.first_name, ru.last_name, ru.email,
                rc.name as category_name,
                p.total_amount, p.status as payment_status
            FROM REGISTRATION r
            JOIN RUNNER ru ON r.runner_id = ru.runner_id
            JOIN RACE_CATEGORY rc ON r.category_id = rc.category_id
            LEFT JOIN PAYMENT p ON r.reg_id = p.reg_id
            WHERE ru.citizen_id = ? OR ru.email = ?
        ");
        
        $stmt->execute([$search, $search]);
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        return null;
    }
}

// Calculate registration price
function calculatePrice($categoryId, $dateOfBirth, $isDisabled, $shippingId) {
    $priceRates = getPriceRates();
    $shippingOptions = getShippingOptions();
    
    // Determine price type
    $priceType = 'Standard';
    if ($isDisabled) {
        $priceType = 'Disabled';
    } elseif ($dateOfBirth) {
        $age = calculateAge($dateOfBirth);
        if ($age >= 60) {
            $priceType = 'Senior';
        }
    }
    
    $registrationFee = $priceRates[$categoryId][$priceType] ?? 0;
    
    $shippingFee = 0;
    foreach ($shippingOptions as $option) {
        if ($option['id'] == $shippingId) {
            $shippingFee = $option['cost'];
            break;
        }
    }
    
    return [
        'registration_fee' => $registrationFee,
        'shipping_fee' => $shippingFee,
        'total' => $registrationFee + $shippingFee
    ];
}
?>