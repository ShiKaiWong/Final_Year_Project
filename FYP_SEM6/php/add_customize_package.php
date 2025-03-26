<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/FYP_SEM6/css/add_customize1.css">
  <title>Add Customize Package - Travel Chill</title>
</head>
<body>


  <h2>Add Customize Package</h2>

<?php
// Check if a session is already active before starting a new one
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require 'dbconnection.php';

$success_msg = "";
$error_msg = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve and sanitize POST data
        $country = isset($_POST['country']) ? $_POST['country'] : '';
        $day_time = isset($_POST['day_time']) ? $_POST['day_time'] : '';
        $package_name = isset($_POST['package_name']) ? $_POST['package_name'] : '';
        $customize_description = isset($_POST['customize_description']) ? $_POST['customize_description'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : '';
        $city = isset($_POST['city']) ? $_POST['city'] : '';
        $picture_url = "";

        // Validate required fields
        if (empty($country) || empty($day_time) || empty($package_name) || empty($price) || empty($city)) {
            $error_msg = "All fields are required.";
        } else {
            // Handle file upload
            if ($_FILES["picture_url"]["error"] == 0) {
                $upload_file_results = upload_file_function();
                if (!empty($upload_file_results["error"])) {
                    $error_msg = $upload_file_results["error"];
                } else {
                    $picture_url = $upload_file_results["url"];
                }
            } else {
                $error_msg = "Package Picture is required!";
            }

            // Insert data if no error
            if (empty($error_msg)) {
                $stmt = $conn->prepare("INSERT INTO customize_package (country, day_time, package_name, customize_description, price, picture_url, city)
                                        VALUES (:country, :day_time, :package_name, :customize_description, :price, :picture_url, :city)");
                $stmt->bindParam(':country', $country);
                $stmt->bindParam(':day_time', $day_time);
                $stmt->bindParam(':package_name', $package_name);
                $stmt->bindParam(':customize_description', $customize_description);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':picture_url', $picture_url);
                $stmt->bindParam(':city', $city);

                if ($stmt->execute()) {
                    $success_msg = "Package added successfully!";
                } else {
                    $error_info = $stmt->errorInfo();
                    $error_msg = "Error executing statement: " . $error_info[2];
                }
            }
        }
    }


function upload_file_function() {
    $err_message = "";
    $file_url = "";
    $target_dir = "pictures/"; // Ensure this directory exists and is writable
    $target_file = $target_dir . basename($_FILES["picture_url"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["picture_url"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $err_message = "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $err_message = "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size (5MB limit)
    if ($_FILES["picture_url"]["size"] > 5000000) {
        $err_message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow only certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $err_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Attempt to upload the file
    if ($uploadOk == 0) {
        $err_message = "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["picture_url"]["tmp_name"], $target_file)) {
            $file_url = $target_file;
        } else {
            $err_message = "Sorry, there was an error uploading your file.";
        }
    }

    return array(
        "url" => $file_url,
        "error" => $err_message,
    );
}
?>

<?php if ($success_msg): ?>
    <p style="color: black;"><?php echo $success_msg; ?></p>
<?php endif; ?>

<?php if ($error_msg): ?>
    <p style="color: red;"><?php echo $error_msg; ?></p>
<?php endif; ?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
    <div class="form-group">
        <label for="country">Country:</label>
        <input type="text" id="country" name="country" required>
    </div>
    <div class="form-group">
        <label for="day_time">Day:</label>
        <input type="number" id="day_time" name="day_time" min="1" max="12" required>
    </div>
    <div class="form-group">
        <label for="package_name">Package Name:</label>
        <input type="text" id="package_name" name="package_name" required>
    </div>
    <div class="form-group">
        <label for="customize_description">Description:</label>
        <textarea id="customize_description" name="customize_description" cols="172"></textarea>
    </div>
    <div class="form-group">
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required>
    </div>
    <div class="form-group">
        <label for="picture_url">Package Picture:</label>
        <input type="file" id="picture_url" name="picture_url" accept="image/*" required>
    </div>
    <div class="form-group">
        <label for="city">City:</label>
        <input type="text" id="city" name="city" required>
    </div>
    <button type="submit">Add Package</button>
</form>

</body>
</html>
