<?php
// CSS Styles for Marathon Registration System
header('Content-Type: text/css');

$css = "
:root {
    --primary-color: #000000;
    --secondary-color: #666666;
    --accent-color: #333333;
    --light-gray: #f8f9fa;
    --medium-gray: #e9ecef;
    --dark-gray: #495057;
    --white: #ffffff;
    --black: #000000;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    background-color: var(--white);
    color: var(--black);
}

/* Navigation */
.navbar {
    background-color: var(--black) !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.navbar-brand {
    font-weight: bold;
    font-size: 1.5rem;
    color: var(--white) !important;
}

.navbar-nav .nav-link {
    color: var(--white) !important;
    font-weight: 500;
    transition: color 0.3s ease;
}

.navbar-nav .nav-link:hover {
    color: var(--medium-gray) !important;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #000000 0%, #333333 50%, #666666 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"25\" cy=\"25\" r=\"1\" fill=\"rgba(255,255,255,0.05)\"/><circle cx=\"75\" cy=\"75\" r=\"1\" fill=\"rgba(255,255,255,0.05)\"/><circle cx=\"50\" cy=\"10\" r=\"0.5\" fill=\"rgba(255,255,255,0.03)\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>');
    opacity: 0.3;
}

.hero-image {
    text-align: center;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

/* Cards */
.card {
    border: 2px solid var(--medium-gray);
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    background-color: var(--white);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    border-color: var(--black);
}

.card-header {
    background-color: var(--black) !important;
    color: var(--white) !important;
    border-radius: 13px 13px 0 0 !important;
    border: none;
}

.card-header.bg-success {
    background-color: var(--accent-color) !important;
}

/* Category Cards */
.category-card {
    height: 100%;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid var(--medium-gray);
}

.category-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    border-color: var(--black);
}

.category-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: var(--black);
}

/* Badges */
.badge.bg-success { background-color: var(--black) !important; }
.badge.bg-warning { background-color: var(--dark-gray) !important; }
.badge.bg-danger { background-color: var(--accent-color) !important; }

/* Buttons */
.btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 12px 30px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-primary {
    background-color: var(--black);
    border-color: var(--black);
    color: var(--white);
}

.btn-primary:hover {
    background-color: var(--accent-color);
    border-color: var(--accent-color);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.btn-success {
    background-color: var(--accent-color);
    border-color: var(--accent-color);
    color: var(--white);
}

.btn-success:hover {
    background-color: var(--black);
    border-color: var(--black);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.btn-warning {
    background-color: var(--white);
    border-color: var(--black);
    color: var(--black);
}

.btn-warning:hover {
    background-color: var(--black);
    border-color: var(--black);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.btn-lg {
    padding: 15px 40px;
    font-size: 1.1rem;
}

/* Forms */
.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid var(--medium-gray);
    padding: 12px 15px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    background-color: var(--white);
}

.form-control:focus, .form-select:focus {
    border-color: var(--black);
    box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
    background-color: var(--white);
}

.form-label {
    font-weight: 600;
    color: var(--black);
    margin-bottom: 8px;
}

.form-check-input:checked {
    background-color: var(--black);
    border-color: var(--black);
}

/* Alerts */
.alert {
    border-radius: 10px;
    border: 2px solid;
    font-weight: 500;
}

.alert-success {
    background-color: var(--light-gray);
    border-color: var(--black);
    color: var(--black);
}

.alert-danger {
    background-color: var(--light-gray);
    border-color: var(--accent-color);
    color: var(--black);
}

.alert-warning {
    background-color: var(--light-gray);
    border-color: var(--dark-gray);
    color: var(--black);
}

/* Status Cards */
.status-card {
    border-left: 4px solid var(--black);
    background: linear-gradient(135deg, var(--light-gray) 0%, var(--medium-gray) 100%);
}

.status-pending { border-left-color: var(--dark-gray); }
.status-paid { border-left-color: var(--black); }
.status-canceled { border-left-color: var(--accent-color); }

/* Background sections */
.bg-light {
    background-color: var(--light-gray) !important;
}

/* Footer */
footer.bg-dark {
    background-color: var(--black) !important;
}

/* Text colors */
.text-success { color: var(--black) !important; }
.text-warning { color: var(--dark-gray) !important; }
.text-danger { color: var(--accent-color) !important; }
.text-primary { color: var(--black) !important; }

/* Responsive Design */
@media (max-width: 768px) {
    .hero-section { 
        text-align: center; 
        padding: 2rem 0; 
    }
    
    .display-4 { 
        font-size: 2.5rem; 
    }
    
    .btn-lg {
        padding: 12px 30px;
        font-size: 1rem;
    }
    
    .category-icon { 
        font-size: 2rem; 
    }
}

/* Smooth Scrolling */
html { 
    scroll-behavior: smooth; 
}

section { 
    scroll-margin-top: 80px; 
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: var(--light-gray);
}

::-webkit-scrollbar-thumb {
    background: var(--black);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--accent-color);
}
";

echo $css;
?>