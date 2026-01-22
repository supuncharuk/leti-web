<?php
require_once __DIR__ . '/../includes/config.php';

$conn = getDbConnection();

// Users Table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_role ENUM('admin', 'editor') DEFAULT 'editor',
    user_status ENUM('0', '1') DEFAULT '1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'users' created successfully.<br>";
} else {
    echo "Error creating 'users' table: " . $conn->error . "<br>";
}

// News Categories Table
$sql = "CREATE TABLE IF NOT EXISTS news_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    added_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'news_categories' created successfully.<br>";

    // Insert default categories if empty
    $check = $conn->query("SELECT id FROM news_categories LIMIT 1");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO news_categories (name) VALUES ('Academic'), ('Events'), ('Sports'), ('Announcements')");
    }
} else {
    echo "Error creating 'news_categories' table: " . $conn->error . "<br>";
}

// Courses Table
$sql = "CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    course_type ENUM('Full Time', 'Part Time'),
    image VARCHAR(255),
    intro_text TEXT,
    modules LONGTEXT,
    career_opportunities LONGTEXT,
    duration VARCHAR(100),
    nvq_level VARCHAR(100),
    medium VARCHAR(50),
    intake VARCHAR(100),
    fee VARCHAR(100),
    entry_requirements LONGTEXT,
    added_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'courses' created successfully.<br>";
} else {
    echo "Error creating 'courses' table: " . $conn->error . "<br>";
}

// News Table
$sql = "CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category_id INT,
    publish_date DATE,
    image VARCHAR(255),
    content LONGTEXT,
    added_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE SET NULL
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'news' created successfully.<br>";
} else {
    echo "Error creating 'news' table: " . $conn->error . "<br>";
}

// Create a default admin if none exists
$check = $conn->query("SELECT id FROM users LIMIT 1");
if ($check->num_rows == 0) {
    $username = 'admin';
    $password = password_hash('1234', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, user_role, user_status) VALUES (?, ?, 'admin', '1')");
    $stmt->bind_param("ss", $username, $password);
    if ($stmt->execute()) {
        echo "Default admin created (user: admin, pass: 1234). Please change this immediately!<br>";
    }
}

echo "Database initialization complete.";
$conn->close();
?>