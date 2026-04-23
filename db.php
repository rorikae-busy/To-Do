<?php

//  DATABASE CONFIGURATION

define('DB_HOST', 'localhost:3307');
define('DB_USER', 'root');
define('DB_PASS', '');              
define('DB_NAME', 'to_do_management_db');

function getDB() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'DB Error: ' . $e->getMessage()]);
        exit;
    }
}
?>
