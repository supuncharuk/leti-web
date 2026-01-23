<?php
/**
 * Configuration file for the LETI Ahangama website.
 * Contains global constants and settings.
 */

// Define the base URL if needed (optional for late-stage dynamic pathing)
define('BASE_URL', 'http://localhost/leti-web/');

// Page titles helper
$siteName = "Light Engineering Training Institute Ahangama";

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'leti_db');

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