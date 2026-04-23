<?php
// ─────────────────────────────────────────
//  DATABASE CONFIGURATION
//  Change these values to match your setup
// ─────────────────────────────────────────
define('DB_HOST', 'localhost:3307'); // Change port if needed (default 3306)
define('DB_USER', 'root');
define('DB_PASS', '');               // Leave empty for default XAMPP
define('DB_NAME', 'daily_planner_v2');

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
