<?php
require 'dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['package_name'];
    $date = $_POST['package_date'];
    $end_date = $_POST['package_end_date'];
    $style = $_POST['package_style'];
    $price = $_POST['package_price'];
    $picture = $_FILES['detail_picture']['name'];

    // 检查是否上传了新图片
    if (!empty($picture)) {
        $target_dir = "pictures/";
        $target_file = $target_dir . basename($picture);
        move_uploaded_file($_FILES["detail_picture"]["tmp_name"], $target_file);
        $picture_url = $target_file;
    } else {
        // 如果没有上传新图片，则保留旧图片
        $stmt = $conn->prepare("SELECT package_picture FROM normal_package WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $picture_url = $row['package_picture'];
    }

    // 更新包裹
    $stmt = $conn->prepare("UPDATE normal_package SET package_name = :name, package_date = :date, package_end_date = :end_date, package_style = :style, package_price = :price, package_picture = :picture WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':style', $style);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':picture', $picture_url);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $logMessage = "Edited package detail ID $id at " . date("Y-m-d H:i:s") . "\n";
        file_put_contents("logs.txt", $logMessage, FILE_APPEND);
    } else {
        echo "<p style='color: red;'>Error updating detail.</p>";
    }
    

    if ($stmt->execute()) {
        echo "<p style='color: black;'>Edit successfully!</p>";
    } else {
        echo "<p style='color: red;'>Edit error!</p>";
    }
}



$conn = null;
?>
