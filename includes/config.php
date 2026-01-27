<?php
/**
 * Configuration file for the LETI Ahangama website.
 * Contains global constants and settings.
 */

// Define the base URL if needed (optional for late-stage dynamic pathing)
// Environment Settings
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // Local Configuration
    define('BASE_URL', 'http://localhost/leti-web/');
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'leti_db');
} else {
    // Production Configuration (Real World)
    // TODO: Update these values with your actual production details
    define('BASE_URL', 'https://leti.pdscharuka.xyz.lk/');
    define('DB_HOST', 'localhost'); // Usually localhost even on production, but check your provider
    define('DB_USER', 'pdscharu_leti');
    define('DB_PASS', 'leti@1979');
    define('DB_NAME', 'pdscharu_leti_db');
}

// Page titles helper
$siteName = "Light Engineering Training Institute Ahangama";

// Utility function to get full page title
function getPageTitle($pageTitle)
{
    global $siteName;
    return $pageTitle . " - " . $siteName;
}

// Database Connection
function getDbConnection()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    if ($conn->query($sql) === TRUE) {
        $conn->select_db(DB_NAME);
    } else {
        die("Error creating database: " . $conn->error);
    }

    return $conn;
}
?>