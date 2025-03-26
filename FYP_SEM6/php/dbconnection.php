<?php
$servername = "127.0.0.1:3307";
$username = "shikai";
$password = "secef017";
$dbname = "fyp_sem6";


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . htmlspecialchars($e->getMessage());
}
?>