<?php
// JavaScript for Marathon Registration System
header('Content-Type: application/javascript');

$javascript = "
// Marathon Registration Website JavaScript

// Sample data (in real application, this would come from API)
const raceCategories = [
    {
        id: 1,
        name: 'Mini Marathon',
        distance: 10.5,
        startTime: '06:00',
        timeLimit: '02:30',
        giveaway: 'เสื้อ + เหรียญ',
        icon: 'fas fa-walking',
        color: 'success'
    },
    {
        id: 2,
        name: 'Half Marathon',
        distance: 21.1,
        startTime: '05:30',
        timeLimit: '03:30',
        giveaway: 'เสื้อ + เหรียญ + ใบประกาศ',
        icon: 'fas fa-running',
        color: 'warning'
    },
    {
        id: 3,
        name: 'Full Marathon',
        distance: 42.2,
        startTime: '05:00',
        timeLimit: '07:00',
        giveaway: 'เสื้อ + เหรียญ + ใบประกาศ + ถ้วยรางวัล',
        icon: 'fas fa-medal',
        color: 'danger'
    }
];

// Initialize the website
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Citizen ID formatting
    const citizenIdInput = document.querySelector('input[name=\"citizen_id\"]');
    if (citizenIdInput) {
        citizenIdInput.addEventListener('input', formatCitizenId);
    }
    
    // Smooth scrolling for navigation
    document.querySelectorAll('a[href^=\"#\"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
}

// Format citizen ID input
function formatCitizenId(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 13) value = value.slice(0, 13);
    e.target.value = value;
}

// Calculate age from date of birth
function calculateAge(dateOfBirth) {
    const today = new Date();
    const birthDate = new Date(dateOfBirth);
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return age;
}

// Validate registration form
function validateForm(form) {
    const citizenId = form.querySelector('input[name=\"citizen_id\"]');
    const dateOfBirth = form.querySelector('input[name=\"date_of_birth\"]');
    const email = form.querySelector('input[name=\"email\"]');
    
    // Check citizen ID format
    if (citizenId && citizenId.value.length !== 13) {
        showAlert('danger', 'กรุณากรอกเลขบัตรประชาชน 13 หลัก');
        return false;
    }
    
    // Check age (minimum 15 years old)
    if (dateOfBirth && dateOfBirth.value) {
        const age = calculateAge(dateOfBirth.value);
        if (age < 15) {
            showAlert('danger', 'ผู้สมัครต้องมีอายุอย่างน้อย 15 ปี');
            return false;
        }
    }
    
    // Check email format
    if (email && email.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value)) {
            showAlert('danger', 'กรุณากรอกอีเมลให้ถูกต้อง');
            return false;
        }
    }
    
    return true;
}

// Show alert message
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-\${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        \${message}
        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>
    `;
    
    // Insert at top of page
    document.body.insertBefore(alertDiv, document.body.firstChild);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Utility functions
function formatThaiDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Loading animation
function showLoading(button) {
    const originalText = button.innerHTML;
    button.innerHTML = '<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span> กำลังประมวลผล...';
    button.disabled = true;
    
    return function() {
        button.innerHTML = originalText;
        button.disabled = false;
    };
}

// Price calculation (if needed for dynamic updates)
function calculatePrice(categoryId, dateOfBirth, isDisabled, shippingId) {
    // This would typically call an API endpoint
    // For now, just return sample calculation
    const basePrices = {
        1: { standard: 800, senior: 600, disabled: 400 },
        2: { standard: 1200, senior: 900, disabled: 600 },
        3: { standard: 1800, senior: 1350, disabled: 900 }
    };
    
    const shippingCosts = {
        1: 0,
        2: 50,
        3: 150
    };
    
    let priceType = 'standard';
    if (isDisabled) {
        priceType = 'disabled';
    } else if (dateOfBirth) {
        const age = calculateAge(dateOfBirth);
        if (age >= 60) {
            priceType = 'senior';
        }
    }
    
    const registrationFee = basePrices[categoryId][priceType] || 0;
    const shippingFee = shippingCosts[shippingId] || 0;
    
    return {
        registration_fee: registrationFee,
        shipping_fee: shippingFee,
        total: registrationFee + shippingFee
    };
}
";

echo $javascript;
?>