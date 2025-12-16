<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'wisata_simple');
define('DB_USER', 'root');
define('DB_PASS', '');

// Base URL
define('BASE_URL', 'http://localhost/wisata-simple');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Include database
require_once 'database.php';

// Functions
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
}

function generateKodeBooking() {
    return 'WB' . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

function getUserTelepon() {
    return $_SESSION['user_telepon'] ?? '';
}

function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

// Logout function
function logout() {
    session_destroy();
    redirect('../index.php');
}
?>