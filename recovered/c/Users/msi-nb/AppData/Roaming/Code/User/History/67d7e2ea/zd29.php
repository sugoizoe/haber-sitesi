<?php
// db.php — PDO-based MySQL connection helper
// Configure these variables to match your environment.
$db_host = '127.0.0.1';
$db_name = 'bpr201_news';
$db_user = 'root';
$db_pass = ''; // set your MySQL password
$db_charset = 'utf8mb4';

$dsn = "mysql:host={$db_host};dbname={$db_name};charset={$db_charset}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // In production, avoid echoing raw errors — log them instead.
    http_response_code(500);
    echo "Database connection failed: " . htmlspecialchars($e->getMessage());
    exit;
}

// Helper to get the PDO instance (optional)
function getPDO()
{
    global $pdo;
    return $pdo;
}
