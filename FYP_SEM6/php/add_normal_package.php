<?php
require 'dbconnection.php';

$package_name = "";
$package_nameErr = "";
$package_date = "";
$package_dateErr = "";
$package_end_date = "";
$package_end_dateErr = "";
$package_style = "";
$package_styleErr = "";
$package_price = "";
$package_priceErr = "";
$package_picture = "";
$package_pictureErr = "";
$new_inserted_record_msg = "";


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $form_type = isset($_POST["form_type_input"]) ? $_POST["form_type_input"] : '';

        if ($form_type == "package_info") {
            $all_valid_input = true;

            if (empty($_POST["package_name_input"])) {
                $package_nameErr = "Package Name is required!";
                $all_valid_input = false;
            } else {
                $package_name = $_POST["package_name_input"];
            }

            if (empty($_POST["package_date_input"])) {
                $package_dateErr = "Package Date is required!";
                $all_valid_input = false;
            } else {
                $package_date = $_POST["package_date_input"];
            }

            if (empty($_POST["package_end_date_input"])) {
                $package_end_dateErr = "Package End Date is required!";
                $all_valid_input = false;
            } else {
                $package_end_date = $_POST["package_end_date_input"];
            }

            if (empty($_POST["package_style_input"])) {
                $package_styleErr = "Package Style is required!";
                $all_valid_input = false;
            } else {
                $package_style = $_POST["package_style_input"];
            }

            if (empty($_POST["package_price_input"])) {
                $package_priceErr = "Package Price is required!";
                $all_valid_input = false;
            } else {
                $package_price = $_POST["package_price_input"];
            }

            $package_picture = "";
            if ($_FILES["package_picture_input"]["error"] == 0) {
                $upload_file_results = upload_file_function();
                if (!empty($upload_file_results["error"])) {
                    $package_pictureErr = $upload_file_results["error"];
                    $all_valid_input = false;
                } else {
                    $package_picture = $upload_file_results["url"];
                }
            } else {
                $package_pictureErr = "Package Picture is required!";
                $all_valid_input = false;
            }

            if ($all_valid_input) {
                $stmt = $conn->prepare("INSERT INTO normal_package (package_name, package_date, package_end_date, package_style, package_price, package_picture)
                                        VALUES (:package_name, :package_date, :package_end_date, :package_style, :package_price, :package_picture)");

                $stmt->bindParam(':package_name', $package_name);
                $stmt->bindParam(':package_date', $package_date);
                $stmt->bindParam(':package_end_date', $package_end_date);
                $stmt->bindParam(':package_style', $package_style);
                $stmt->bindParam(':package_price', $package_price);
                $stmt->bindParam(':package_picture', $package_picture);

                if ($stmt->execute()) {
                    $new_inserted_record_msg = "Add Package Successfully!!";
                } else {
                    echo "Error: " . $stmt->errorInfo()[2];
                }
            }
        }
    }


function upload_file_function() {
    $err_message = "";
    $file_url = "";
    $target_dir = "pictures/"; // Ensure this directory exists and is writable
    $target_file = $target_dir . basename($_FILES["package_picture_input"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["package_picture_input"]["tmp_name"]);
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

    // Check file size
    if ($_FILES["package_picture_input"]["size"] > 5000000) { // 5MB limit
        $err_message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $err_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $err_message = "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["package_picture_input"]["tmp_name"], $target_file)) {
            $file_url = $target_file;
           //for http search
           // $file_url = "http://" . $_SERVER["HTTP_HOST"] . "/FYP_SEM6/php" . "/" . $target_file;
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/FYP_SEM6/css/add_customize1.css">
    <title>Add Package - Travel Chill</title>
</head>
<body>


    <h2>Package Info</h2>
    <h3 style="color: black;"><?php echo $new_inserted_record_msg; ?></h3>
    <h3>Add New Package</h3>

    <p><span style="color:red">* required field</span></p>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="package_name">Package Name:</label>
            <input type="text" id="package_name" name="package_name_input" value="">
            <span style="color:red">* <?php echo $package_nameErr; ?></span>
        </div>
        <br>

        <div class="form-group">
            <label for="package_date">Package Date:</label>
            <input type="date" id="package_date" name="package_date_input" value="">
            <span style="color:red">* <?php echo $package_dateErr; ?></span>
        </div>
        <br>

        <div class="form-group">
            <label for="package_end_date">Package End Date:</label>
            <input type="date" id="package_end_date" name="package_end_date_input" value="">
            <span style="color:red">* <?php echo $package_end_dateErr; ?></span>
        </div>
        <br>

        <div class="form-group">
            <label for="package_style">Package Style:</label>
            <input type="text" id="package_style" name="package_style_input" value="">
            <span style="color:red">* <?php echo $package_styleErr; ?></span>
        </div>
        <br>

        <div class="form-group">
            <label for="package_price">Package Price:</label>
            <input type="text" id="package_price" name="package_price_input" value="">
            <span style="color:red">* <?php echo $package_priceErr; ?></span>
        </div>
        <br>

        <div class="form-group">
            <label for="package_picture">Package Picture:</label>
            <input type="file" id="package_picture" name="package_picture_input" accept="image/*">
            <span style="color:red">* <?php echo $package_pictureErr; ?></span>
        </div>
        <br>

        <input type="hidden" name="form_type_input" value="package_info">
        <button type="submit">Submit</button>
    </form>



</body>
</html>
