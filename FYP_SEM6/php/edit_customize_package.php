<?php
require 'dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $country = $_POST['country'];
    $day_time = $_POST['day_time'];
    $package_name = $_POST['package_name'];
    $customize_description = $_POST['customize_description'];
    $price = $_POST['price'];
    $picture = $_FILES['picture_url']['name'];
    $city = $_POST['city'];

    // Check if a new picture was uploaded
    if (!empty($picture)) {
        $target_dir = "pictures/";
        $target_file = $target_dir . basename($picture);
        if (move_uploaded_file($_FILES["picture_url"]["tmp_name"], $target_file)) {
            $picture_url = $target_file;
        } else {
            echo "Error uploading file.";
            exit;
        }
    } else {
        // If no new picture was uploaded, keep the old picture
        $stmt = $conn->prepare("SELECT picture_url FROM customize_package WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $picture_url = $row['picture_url'];
    }

    // Update the package
    $stmt = $conn->prepare("UPDATE customize_package SET country = :country, day_time = :day_time, package_name = :package_name, customize_description = :customize_description, price = :price, picture_url = :picture_url , city = :city WHERE id = :id");
    $stmt->bindParam(':country', $country);
    $stmt->bindParam(':day_time', $day_time);
    $stmt->bindParam(':package_name', $package_name);
    $stmt->bindParam(':customize_description', $customize_description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':picture_url', $picture_url);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':city', $city);

    if ($stmt->execute()) {
        file_put_contents("logs.txt", "Edited detail ID $id at " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
        echo "success";
    } else {
        echo "error";
    }
    $conn = null;
}
?>
