<?php
// Demo page to show sample data and test functionality
require_once 'functions.php';

echo "<h1>Marathon Registration System - Demo</h1>";

echo "<h2>Sample Categories:</h2>";
$categories = getCategories();
echo "<pre>" . print_r($categories, true) . "</pre>";

echo "<h2>Sample Price Rates:</h2>";
$prices = getPriceRates();
echo "<pre>" . print_r($prices, true) . "</pre>";

echo "<h2>Sample Shipping Options:</h2>";
$shipping = getShippingOptions();
echo "<pre>" . print_r($shipping, true) . "</pre>";

echo "<h2>Test Registration Status Check:</h2>";
$testSearch = '1234567890123';
$result = checkRegistrationStatus($testSearch);
echo "<p>Searching for: $testSearch</p>";
echo "<pre>" . print_r($result, true) . "</pre>";

echo "<h2>Test Price Calculation:</h2>";
$price = calculatePrice(2, '1990-05-15', false, 1);
echo "<p>Half Marathon, Age 35, Not Disabled, Pickup</p>";
echo "<pre>" . print_r($price, true) . "</pre>";

echo "<hr>";
echo "<p><a href='index.php'>← กลับไปหน้าหลัก</a></p>";
?>