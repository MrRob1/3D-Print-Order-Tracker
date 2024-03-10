<?php
// Database configuration settings
define('DB_HOST', 'localhost'); // Database host, usually it's "localhost"
define('DB_USERNAME', 'USERNAME HERE'); // Your database username
define('DB_PASSWORD', 'PASSWORD'); // Your database password
define('DB_NAME', 'DB NAME HERE'); // Your database name

// Attempt to connect to MySQL database
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
