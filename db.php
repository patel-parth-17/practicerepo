<?php
/**
 * Lumina Bookstore - Database Connection & XAMPP Integration Helper
 */

$host = "localhost";
$dbname = "bookstore_db";
$username = "root";
$password = "";
$conn = null;
$db_error = null;
$db_connected = false;

try {
    // First try connecting directly to the bookstore_db database
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 3
        ]
    );
    $db_connected = true;
} catch (PDOException $e) {
    // If database doesn't exist, try connecting to server and auto-creating it
    try {
        $rootConn = new PDO(
            "mysql:host=$host;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3
            ]
        );
        
        // Auto-create database if possible
        $rootConn->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        
        // Reconnect with the new database
        $conn = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

        // Run schema.sql if tables are not yet created
        $tableCheck = $conn->query("SHOW TABLES LIKE 'books'")->fetchAll();
        if (empty($tableCheck)) {
            $schemaFile = __DIR__ . '/schema.sql';
            if (file_exists($schemaFile)) {
                $sql = file_get_contents($schemaFile);
                $conn->exec($sql);
            }
        }
        $db_connected = true;
    } catch (PDOException $ex) {
        // Safe graceful fallback: don't crash the frontend if MySQL is offline
        $conn = null;
        $db_error = $ex->getMessage();
        $db_connected = false;
        error_log("Database connection notice: " . $db_error);
    }
}

function getDb() {
    global $conn;
    return $conn;
}

function isDbConnected() {
    global $db_connected;
    return $db_connected;
}
?>
