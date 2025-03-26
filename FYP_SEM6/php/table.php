<?php
  require 'dbconnection.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=fyp_sem6", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create table for super_admin
    $sql = "CREATE TABLE IF NOT EXISTS super_admin (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(60) NOT NULL,
        phone_number VARCHAR(25) NOT NULL,
        email VARCHAR(50) NOT NULL,
        user_name VARCHAR(30) NOT NULL,
        user_password VARCHAR(255) NOT NULL
    )";
    $conn->exec($sql);
    echo "Table super_admin created successfully<br>";

    // Create table for user
    $sql = "CREATE TABLE IF NOT EXISTS user (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(60) NOT NULL,
        phone_number VARCHAR(25) NOT NULL,
        email VARCHAR(50) NOT NULL,
        user_name VARCHAR(30) NOT NULL,
        user_password VARCHAR(255) NOT NULL
    )";
    $conn->exec($sql);
    echo "Table user created successfully<br>";
        
    // Create table for feedback_list
    $sql = "CREATE TABLE IF NOT EXISTS feedback_list (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        interested VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user_id INT(11) UNSIGNED NOT NULL,
        FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE
    )";
    $conn->exec($sql);
    echo "Table feedback_list created successfully<br>";

    // Create table for normal_package
    $sql = "CREATE TABLE IF NOT EXISTS normal_package (
        id INT AUTO_INCREMENT PRIMARY KEY,
        package_name VARCHAR(255) NOT NULL,
        package_date DATE NOT NULL,
        package_end_date DATE NOT NULL,
        package_style VARCHAR(255) NOT NULL,
        package_price DECIMAL(10, 2) NOT NULL,
        package_picture VARCHAR(255) DEFAULT NULL
    )";
    $conn->exec($sql);
    echo "Table normal_package created successfully<br>";

    // Create table for normal_package_detail
    $sql = "CREATE TABLE IF NOT EXISTS normal_package_detail (
        id INT AUTO_INCREMENT PRIMARY KEY,
        day_time INT,
        detail_name VARCHAR(255) NOT NULL,
        detail_picture VARCHAR(255) DEFAULT NULL,
        detail_description TEXT DEFAULT NULL,
        package_id INT NOT NULL,
        FOREIGN KEY (package_id) REFERENCES normal_package(id) ON DELETE CASCADE ON UPDATE CASCADE
    )";
    $conn->exec($sql);
    echo "Table normal_package_detail created successfully<br>";

    // Create table for normal_package_payment
    $sql = "CREATE TABLE IF NOT EXISTS normal_package_payment (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) UNSIGNED NOT NULL,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        total_price DECIMAL(10, 2) NOT NULL,
        payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        package_id INT NOT NULL,
        FOREIGN KEY (package_id) REFERENCES normal_package(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE
    )";
    $conn->exec($sql);
    echo "Table normal_package_payment created successfully<br>";

    // Create table for customize_package
    $sql = "CREATE TABLE IF NOT EXISTS customize_package (
        id INT AUTO_INCREMENT PRIMARY KEY,
        country VARCHAR(255) NOT NULL,
        day_time INT NOT NULL,
        package_name VARCHAR(255) NOT NULL,
        customize_description TEXT DEFAULT NULL,
        price DECIMAL(10, 2) NOT NULL,
        picture_url VARCHAR(255) DEFAULT NULL,
        city VARCHAR(255) DEFAULT NULL
    )";
    $conn->exec($sql);
    echo "Table customize_package created successfully<br>";

    // Create table for customize_package_payment
    $sql = "CREATE TABLE IF NOT EXISTS customize_package_payment (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        package_ids TEXT NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        total_price DECIMAL(10, 2) NOT NULL,
        payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user_id INT(11) UNSIGNED NOT NULL,
        FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE
    )";
    $conn->exec($sql);
    echo "Table customize_package_payment created successfully<br>";

    // Create table for user_customize_detail
    $sql = "CREATE TABLE IF NOT EXISTS user_customize_detail (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) UNSIGNED NOT NULL,
        customize_package_ids TEXT NOT NULL,
        total_days INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE
    )";
    $conn->exec($sql);
    echo "Table user_customize_detail created successfully<br>";

    // Create table for customize_package_order
    $sql = "CREATE TABLE IF NOT EXISTS customize_package_order (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) UNSIGNED NOT NULL,
        customize_package_id TEXT NOT NULL,
        country VARCHAR(255) NOT NULL,
        city VARCHAR(255) DEFAULT NULL,
        total_price DECIMAL(10, 2) NOT NULL,
        total_days INT NOT NULL,
        order_status VARCHAR(50) DEFAULT 'Unpaid',
        FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE
    )";
    $conn->exec($sql);
    echo "Table customize_package_order created successfully<br>";








} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
