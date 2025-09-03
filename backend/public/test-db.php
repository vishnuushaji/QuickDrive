<?php
$host = 'ep-blue-fire-adpperjk-pooler.c-2.us-east-1.aws.neon.tech';
$db = 'neondb';
$user = 'neondb_owner';
$pass = 'npg_0CUOjSHGfdh1';

try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Database connected successfully!\n";
    
    // Check tables
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema='public'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(', ', $tables) . "\n";
    
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}