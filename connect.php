<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "jemis-art";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    echo "<script>console.log('Connected to database');</script>";
} catch (PDOException $e) {
    $error = $e->getMessage();
    $error = str_replace(["'", '"'], "", $error);
    echo "<script>console.log('Database connection failed: $error');</script>";
    exit();
}