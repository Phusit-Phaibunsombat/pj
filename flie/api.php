<?php
// Marathon Registration API
// This file provides backend functionality for the registration system

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost';
$dbname = 'marathon_registration';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get request method and endpoint
$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));

// Route requests
switch($method) {
    case 'GET':
        handleGet($segments, $pdo);
        break;
    case 'POST':
        handlePost($segments, $pdo);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

// Handle GET requests
function handleGet($segments, $pdo) {
    $endpoint = $segments[1] ?? '';
    
    switch($endpoint) {
        case 'categories':
            getCategories($pdo);
            break;
        case 'prices':
            getPrices($pdo);
            break;
        case 'shipping':
            getShippingOptions($pdo);
            break;
        case 'status':
            getRegistrationStatus($pdo);
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
            break;
    }
}

// Handle POST requests
function handlePost($segments, $pdo) {
    $endpoint = $segments[1] ?? '';
    
    switch($endpoint) {
        case 'register':
            createRegistration($pdo);
            break;
        case 'payment':
            processPayment($pdo);
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
            break;
    }
}

// Get race categories
function getCategories($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM RACE_CATEGORY ORDER BY distance_km");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categories);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch categories: ' . $e->getMessage()]);
    }
}

// Get price rates
function getPrices($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT pr.*, rc.name as category_name 
            FROM PRICE_RATE pr 
            JOIN RACE_CATEGORY rc ON pr.category_id = rc.category_id 
            ORDER BY pr.category_id, pr.group_type
        ");
        $prices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($prices);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch prices: ' . $e->getMessage()]);
    }
}

// Get shipping options
function getShippingOptions($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM SHIPPING_OPTION ORDER BY cost");
        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($options);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch shipping options: ' . $e->getMessage()]);
    }
}

// Get registration status
function getRegistrationStatus($pdo) {
    $search = $_GET['search'] ?? '';
    
    if (empty($search)) {
        http_response_code(400);
        echo json_encode(['error' => 'Search parameter required']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
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
            LEFT JOIN PAYMENT p ON r.reg_id = p.reg_id
            WHERE ru.citizen_id = ? OR ru.email = ?
        ");
        
        $stmt->execute([$search, $search]);
        $registration = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($registration) {
            echo json_encode($registration);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Registration not found']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch registration: ' . $e->getMessage()]);
    }
}

// Create new registration
function createRegistration($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['firstName', 'lastName', 'dateOfBirth', 'gender', 'citizenId', 
                'phone', 'email', 'address', 'category', 'shirtSize', 'shipping'];
    
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field '$field' is required"]);
            return;
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        // Check if runner already exists
        $stmt = $pdo->prepare("SELECT runner_id FROM RUNNER WHERE citizen_id = ? OR email = ?");
        $stmt->execute([$input['citizenId'], $input['email']]);
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
                $input['firstName'],
                $input['lastName'],
                $input['dateOfBirth'],
                $input['gender'],
                $input['citizenId'],
                $input['phone'],
                $input['email'],
                $input['address'],
                $input['isDisabled'] ?? false
            ]);
            $runnerId = $pdo->lastInsertId();
        }
        
        // Determine price type
        $age = calculateAge($input['dateOfBirth']);
        $priceType = 'Standard';
        if ($input['isDisabled'] ?? false) {
            $priceType = 'Disabled';
        } elseif ($age >= 60) {
            $priceType = 'Senior';
        }
        
        // Get price ID
        $stmt = $pdo->prepare("
            SELECT price_id FROM PRICE_RATE 
            WHERE category_id = ? AND group_type = ?
        ");
        $stmt->execute([$input['category'], $priceType]);
        $priceId = $stmt->fetchColumn();
        
        if (!$priceId) {
            throw new Exception('Price not found for selected category and type');
        }
        
        // Generate bib number
        $categoryPrefix = getCategoryPrefix($input['category']);
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM REGISTRATION 
            WHERE category_id = ?
        ");
        $stmt->execute([$input['category']]);
        $count = $stmt->fetchColumn() + 1;
        $bibNumber = $categoryPrefix . str_pad($count, 3, '0', STR_PAD_LEFT);
        
        // Create registration
        $stmt = $pdo->prepare("
            INSERT INTO REGISTRATION (runner_id, category_id, price_id, shipping_id, 
                                    shirt_size, bib_number, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'Pending')
        ");
        $stmt->execute([
            $runnerId,
            $input['category'],
            $priceId,
            $input['shipping'],
            $input['shirtSize'],
            $bibNumber
        ]);
        $regId = $pdo->lastInsertId();
        
        // Calculate total amount
        $stmt = $pdo->prepare("
            SELECT pr.amount + so.cost as total_amount
            FROM PRICE_RATE pr, SHIPPING_OPTION so
            WHERE pr.price_id = ? AND so.shipping_id = ?
        ");
        $stmt->execute([$priceId, $input['shipping']]);
        $totalAmount = $stmt->fetchColumn();
        
        // Create pending payment record
        $stmt = $pdo->prepare("
            INSERT INTO PAYMENT (reg_id, total_amount, payment_method, status) 
            VALUES (?, ?, 'Pending', 'Pending')
        ");
        $stmt->execute([$regId, $totalAmount]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'registration_id' => $regId,
            'bib_number' => $bibNumber,
            'total_amount' => $totalAmount,
            'message' => 'Registration created successfully'
        ]);
        
    } catch(Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
    }
}

// Process payment
function processPayment($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $required = ['reg_id', 'payment_method'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field '$field' is required"]);
            return;
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        // Update payment
        $stmt = $pdo->prepare("
            UPDATE PAYMENT 
            SET payment_time = NOW(), payment_method = ?, status = 'Success',
                transaction_ref = ?
            WHERE reg_id = ?
        ");
        $transactionRef = 'TXN' . time() . rand(1000, 9999);
        $stmt->execute([$input['payment_method'], $transactionRef, $input['reg_id']]);
        
        // Update registration status
        $stmt = $pdo->prepare("UPDATE REGISTRATION SET status = 'Paid' WHERE reg_id = ?");
        $stmt->execute([$input['reg_id']]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'transaction_ref' => $transactionRef,
            'message' => 'Payment processed successfully'
        ]);
        
    } catch(Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Payment processing failed: ' . $e->getMessage()]);
    }
}

// Utility functions
function calculateAge($dateOfBirth) {
    $today = new DateTime();
    $birthDate = new DateTime($dateOfBirth);
    return $today->diff($birthDate)->y;
}

function getCategoryPrefix($categoryId) {
    $prefixes = [1 => 'MM', 2 => 'HM', 3 => 'FM'];
    return $prefixes[$categoryId] ?? 'XX';
}
?>