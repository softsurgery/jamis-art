<?php

require_once "lib/Env.php";

Env::load(__DIR__ . '/.env');

$host = Env::get('DB_HOST');
$user = Env::get('DB_USER');
$pass = Env::get('DB_PASS');
$db = Env::get('DB_NAME');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // echo "<script>console.log('Connected to database');</script>";
} catch (PDOException $e) {
    $error = $e->getMessage();
    $error = str_replace(["'", '"'], "", $error);
    echo "<script>console.log('Database connection failed: $error');</script>";
    exit();
}