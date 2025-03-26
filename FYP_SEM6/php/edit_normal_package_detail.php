<?php
// Start the session if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['detail_name'];
    $day = $_POST['day_time'];
    $description = $_POST['detail_description'];

    $stmt = $conn->prepare("UPDATE normal_package_detail SET detail_name = :name, day_time = :day, detail_description = :description WHERE id = :id");

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':day', $day);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        file_put_contents("logs.txt", "Edited package detail ID $id at " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
        echo "<p style='color: black;'>Detail updated successfully.</p>";
    } else {
        echo "<p style='color: red;'>Error updating detail.</p>";
    }

    // Handle file upload if a new picture is provided
    if (isset($_FILES['detail_picture']) && $_FILES['detail_picture']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["detail_picture"]["name"]);
        
        if (move_uploaded_file($_FILES["detail_picture"]["tmp_name"], $target_file)) {
            // Update the detail picture in the database
            $stmt = $conn->prepare("UPDATE normal_package_detail SET detail_picture = :picture WHERE id = :id");
            $stmt->bindParam(':picture', $target_file);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
    }
}

$conn = null;
?>
