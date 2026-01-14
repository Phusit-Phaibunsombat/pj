<?php
/*
Useful Queries for Marathon Registration System
*/

$useful_queries = [
    'all_registrations' => "
        -- Get all registrations with complete details
        SELECT * FROM v_registration_details 
        ORDER BY reg_date DESC;
    ",
    
    'category_statistics' => "
        -- Get category statistics
        SELECT * FROM v_category_stats;
    ",
    
    'paid_registrations_by_category' => "
        -- Find all paid registrations for a specific category
        SELECT 
            r.bib_number,
            ru.first_name,
            ru.last_name,
            ru.email,
            rc.name as category,
            r.shirt_size,
            p.total_amount,
            p.payment_time
        FROM REGISTRATION r
        JOIN RUNNER ru ON r.runner_id = ru.runner_id
        JOIN RACE_CATEGORY rc ON r.category_id = rc.category_id
        JOIN PAYMENT p ON r.reg_id = p.reg_id
        WHERE r.status = 'Paid' 
          AND rc.name = 'Half Marathon'
        ORDER BY r.bib_number;
    ",
    
    'pending_payments' => "
        -- Get pending payments (registrations without successful payment)
        SELECT 
            r.reg_id,
            r.bib_number,
            ru.first_name,
            ru.last_name,
            ru.email,
            rc.name as category,
            pr.amount + so.cost as total_due,
            r.reg_date
        FROM REGISTRATION r
        JOIN RUNNER ru ON r.runner_id = ru.runner_id
        JOIN RACE_CATEGORY rc ON r.category_id = rc.category_id
        JOIN PRICE_RATE pr ON r.price_id = pr.price_id
        JOIN SHIPPING_OPTION so ON r.shipping_id = so.shipping_id
        LEFT JOIN PAYMENT p ON r.reg_id = p.reg_id
        WHERE r.status = 'Pending' 
           OR p.status != 'Success' 
           OR p.payment_id IS NULL
        ORDER BY r.reg_date;
    ",
    
    'age_group_analysis' => "
        -- Get runners by age group for a specific category
        SELECT 
            ag.label as age_group,
            ag.gender,
            COUNT(r.reg_id) as registration_count
        FROM AGE_GROUP ag
        LEFT JOIN REGISTRATION reg ON ag.category_id = reg.category_id
        LEFT JOIN RUNNER ru ON reg.runner_id = ru.runner_id 
            AND ru.gender = ag.gender
            AND TIMESTAMPDIFF(YEAR, ru.date_of_birth, CURDATE()) BETWEEN ag.min_age AND ag.max_age
        WHERE ag.category_id = 2  -- Half Marathon
        GROUP BY ag.group_id, ag.label, ag.gender
        ORDER BY ag.gender, ag.min_age;
    ",
    
    'revenue_report' => "
        -- Revenue report by category and payment method
        SELECT 
            rc.name as category,
            p.payment_method,
            COUNT(p.payment_id) as transaction_count,
            SUM(p.total_amount) as total_revenue
        FROM PAYMENT p
        JOIN REGISTRATION r ON p.reg_id = r.reg_id
        JOIN RACE_CATEGORY rc ON r.category_id = rc.category_id
        WHERE p.status = 'Success'
        GROUP BY rc.category_id, rc.name, p.payment_method
        ORDER BY rc.name, total_revenue DESC;
    ",
    
    'duplicate_registrations' => "
        -- Find duplicate registrations (same runner in same category)
        SELECT 
            ru.first_name,
            ru.last_name,
            ru.email,
            rc.name as category,
            COUNT(r.reg_id) as registration_count
        FROM REGISTRATION r
        JOIN RUNNER ru ON r.runner_id = ru.runner_id
        JOIN RACE_CATEGORY rc ON r.category_id = rc.category_id
        GROUP BY r.runner_id, r.category_id
        HAVING COUNT(r.reg_id) > 1;
    ",
    
    'shipping_summary' => "
        -- Shipping summary
        SELECT 
            so.type as shipping_type,
            COUNT(r.reg_id) as total_registrations,
            SUM(so.cost) as total_shipping_revenue
        FROM SHIPPING_OPTION so
        JOIN REGISTRATION r ON so.shipping_id = r.shipping_id
        JOIN PAYMENT p ON r.reg_id = p.reg_id
        WHERE p.status = 'Success'
        GROUP BY so.shipping_id, so.type
        ORDER BY total_registrations DESC;
    ",
    
    'daily_registration_report' => "
        -- Daily registration report
        SELECT 
            DATE(r.reg_date) as registration_date,
            COUNT(r.reg_id) as total_registrations,
            COUNT(CASE WHEN r.status = 'Paid' THEN 1 END) as paid_registrations,
            SUM(CASE WHEN p.status = 'Success' THEN p.total_amount ELSE 0 END) as daily_revenue
        FROM REGISTRATION r
        LEFT JOIN PAYMENT p ON r.reg_id = p.reg_id
        GROUP BY DATE(r.reg_date)
        ORDER BY registration_date DESC;
    ",
    
    'disabled_runners_summary' => "
        -- Runners with disabilities summary
        SELECT 
            rc.name as category,
            COUNT(r.reg_id) as disabled_registrations,
            COUNT(CASE WHEN r.status = 'Paid' THEN 1 END) as paid_disabled_registrations
        FROM REGISTRATION r
        JOIN RUNNER ru ON r.runner_id = ru.runner_id
        JOIN RACE_CATEGORY rc ON r.category_id = rc.category_id
        WHERE ru.is_disabled = TRUE
        GROUP BY rc.category_id, rc.name
        ORDER BY disabled_registrations DESC;
    "
];

// Function to execute a specific query
function executeQuery($pdo, $queryName) {
    global $useful_queries;
    
    if (!isset($useful_queries[$queryName])) {
        return false;
    }
    
    try {
        $stmt = $pdo->query($useful_queries[$queryName]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

// Function to get all available queries
function getAvailableQueries() {
    global $useful_queries;
    return array_keys($useful_queries);
}

// If this file is accessed directly, show available queries
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    echo "<h1>Available Queries</h1>";
    echo "<ul>";
    foreach (array_keys($useful_queries) as $queryName) {
        echo "<li><strong>$queryName</strong></li>";
    }
    echo "</ul>";
    
    echo "<h2>Query Details</h2>";
    foreach ($useful_queries as $name => $query) {
        echo "<h3>$name</h3>";
        echo "<pre>" . htmlspecialchars(trim($query)) . "</pre>";
    }
}
?>