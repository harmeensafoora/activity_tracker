<?php
// includes/db_connect.php
$host = "localhost";
$dbname = "academic_tracker";
$username = "root";
$password = "";

try {
    $dbh = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
?>
