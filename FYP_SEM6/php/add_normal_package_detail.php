<?php

require 'dbconnection.php';
session_start();
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : ''; 

$day_time = "";
$day_timeErr = "";
$detail_name = "";
$detail_nameErr = "";
$detail_description = "";
$detail_descriptionErr = "";
$detail_picture = "";
$detail_pictureErr = "";
$package_id = "";
$package_idErr = "";
$new_inserted_record_msg = "";

// 接收传递的 package_id 参数
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['package_id'])) {
    $package_id = $_GET['package_id'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_type = isset($_POST["form_type_input"]) ? $_POST["form_type_input"] : '';

    if ($form_type == "detail_info") {
        $all_valid_input = true;

        if (empty($_POST["day_time"])) {
            $day_timeErr = "Day is required!";
            $all_valid_input = false;
        } else {
            $day_time = $_POST["day_time"];
        }

        if (empty($_POST["detail_name_input"])) {
            $detail_nameErr = "Detail Name is required!";
            $all_valid_input = false;
        } else {
            $detail_name = $_POST["detail_name_input"];
        }

        if (empty($_POST["detail_description_input"])) {
            $detail_descriptionErr = "Detail Description is required!";
            $all_valid_input = false;
        } else {
            $detail_description = $_POST["detail_description_input"];
        }

        if (empty($_POST["package_id_input"])) {
            $package_idErr = "Package ID is required!";
            $all_valid_input = false;
        } else {
            $package_id = $_POST["package_id_input"];
        }

        $detail_picture = "";
        if ($_FILES["detail_picture_input"]["error"] == 0) {
            $upload_file_results = upload_file_function("detail_picture_input");
            if (!empty($upload_file_results["error"])) {
                $detail_pictureErr = $upload_file_results["error"];
                $all_valid_input = false;
            } else {
                $detail_picture = $upload_file_results["url"];
            }
        } else {
            $detail_pictureErr = "Detail Picture is required!";
            $all_valid_input = false;
        }

        if ($all_valid_input) {
            $stmt = $conn->prepare("INSERT INTO normal_package_detail (day_time, detail_name, detail_description, detail_picture, package_id)
                                    VALUES (:day_time, :detail_name, :detail_description, :detail_picture, :package_id)");

            $stmt->bindParam(':day_time', $day_time);
            $stmt->bindParam(':detail_name', $detail_name);
            $stmt->bindParam(':detail_description', $detail_description);
            $stmt->bindParam(':detail_picture', $detail_picture);
            $stmt->bindParam(':package_id', $package_id);

            if ($stmt->execute()) {
                $new_inserted_record_msg = "Add Detail Successfully!!";
            } else {
                echo "Error: " . $stmt->errorInfo()[2];
            }
        }
    }
}

function upload_file_function($file_input_name) {
    $err_message = "";
    $file_url = "";
    $target_dir = "pictures/"; // 确保这个目录存在且可写
    $target_file = $target_dir . basename($_FILES[$file_input_name]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // 检查文件是否为图像
    $check = getimagesize($_FILES[$file_input_name]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $err_message = "File is not an image.";
        $uploadOk = 0;
    }

    // 检查文件是否已存在
    if (file_exists($target_file)) {
        $err_message = "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // 检查文件大小
    if ($_FILES[$file_input_name]["size"] > 5000000) { // 5MB 限制
        $err_message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // 允许的文件格式
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $err_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // 检查 $uploadOk 是否设置为 0
    if ($uploadOk == 0) {
        $err_message = "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $target_file)) {
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/FYP_SEM6/css/add_normal_package_detail.css">
    <title>Add Package Detail - Travel Chill</title>
</head>
<body>
  <div class="container">
    <nav class="nav collapsible">
      <i class="nav__toggler fa-solid fa-bars"></i>
      <ul class="nav__list collapsible__content">
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="add_normal_package" class="hidden-button">Add Package</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="add_customize_package" class="hidden-button">Add Customize Package</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_normal_package" class="hidden-button">Normal Package</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_normal_package_payment" class="hidden-button">Normal Package Payment</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_customize_package" class="hidden-button">Customize Package</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_user_customize_detail" class="hidden-button">User Customize Detail</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_customize_package_payment" class="hidden-button">Customize Payment</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_customize_package_order" class="hidden-button">Customize Package Order</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="feedback_list" class="hidden-button">Feedback</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_user" class="hidden-button">User List</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="logs" class="hidden-button">Logs</button>
          </form>
        </li>
      </ul>
    </nav>

    <div class="content">
      <div class="header-container">
        <h3>Super Admin: <?php echo htmlspecialchars($full_name); ?></h3>
        <button class="logout_button1"><a href="logout_super_admin.php">Logout</a></button>
      </div>

      <h2>Normal Package Detail Info</h2>
      <h3 style="color: black;"><?php echo $new_inserted_record_msg; ?></h3>
      <h3>Add New Detail</h3>

      <p><span style="color:red">* required field</span></p>

      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
      <div class="form-group">
            <label for="package_id">Package ID:</label>
            <input type="number" id="package_id" name="package_id_input" min="1" value="<?php echo htmlspecialchars($package_id); ?>" >
            <span style="color:red">* <?php echo $package_idErr; ?></span>
        </div>
        <br>

        <div class="form-group">
            <label for="detail_name">Detail Name:</label>
            <input type="text" id="detail_name" name="detail_name_input" value="">
            <span style="color:red">* <?php echo $detail_nameErr; ?></span>
        </div>
        <br>

        <div class="form-group">
            <label for="day_time">Day Time:</label>
            <input type="number" id="day_time" name="day_time" min="1" max="12" value="">
            <span style="color:red">* <?php echo $day_timeErr; ?></span>
        </div>
        <br>

        <div class="form-group">
            <label for="detail_description">Detail Description:</label>
            <textarea id="detail_description" name="detail_description_input" cols="172"></textarea>
            <span style="color:red"><?php echo $detail_descriptionErr; ?></span>
        </div>
        <br>

        <div class="form-group">
            <label for="detail_picture">Detail Picture:</label>
            <input type="file" id="detail_picture" name="detail_picture_input" accept="image/*">
            <span style="color:red">* <?php echo $detail_pictureErr; ?></span>
        </div>
        <br>

          <input type="hidden" name="form_type_input" value="detail_info">
          <button type="submit">Submit</button>
      </form>
    </div> <!-- End of content -->
  </div> <!-- End of container -->
</body>
</html>
