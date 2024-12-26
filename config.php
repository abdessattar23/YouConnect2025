<?php
define('DB_FILE', __DIR__ . '/database.sqlite');

try {
    $conn = new SQLite3(DB_FILE);
    
    // Create tables if they don't exist
    $sql = file_get_contents(__DIR__ . '/database.sql');
    $conn->exec($sql);
    
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
