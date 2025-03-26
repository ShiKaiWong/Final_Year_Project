<?php
session_start();
$full_name = isset($_SESSION['super_admin_info']['full_name']) ? $_SESSION['super_admin_info']['full_name'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $_SESSION['current_page'] = $action; // Store the current page state in session
    
    if ($action == 'edit_package') {
        require 'dbconnection.php';

        // Define the upload function
        function upload_file_function() {
            $err_message = "";
            $file_url = "";
            $target_dir = "pictures/"; // Ensure this directory exists and is writable
            $target_file = $target_dir . basename($_FILES["detail_picture"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is an actual image or fake image
            $check = getimagesize($_FILES["detail_picture"]["tmp_name"]);
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
            if ($_FILES["detail_picture"]["size"] > 5000000) { // 5MB limit
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
                if (move_uploaded_file($_FILES["detail_picture"]["tmp_name"], $target_file)) {
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

        $id = $_POST['id'];
        $name = $_POST['package_name'];
        $date = $_POST['package_date'];
        $style = $_POST['package_style'];
        $price = $_POST['package_price'];

        // Handle file upload
        $upload_result = upload_file_function();
        $picture = $upload_result['url'];

        if (!empty($upload_result['error'])) {
            $error_msg = $upload_result['error'];
        } else {
            $stmt = $conn->prepare("UPDATE normal_package SET package_name = :name, package_date = :date, package_style = :style, package_price = :price, package_picture = :picture WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':style', $style);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':picture', $picture);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                $success_msg = "Package updated successfully.";
            } else {
                $error_msg = "Error updating package.";
            }
        }

        $_SESSION['current_page'] = 'list_normal_package'; // Stay on the list page after editing
    }



    elseif ($action == 'delete_feedback') {
      require 'dbconnection.php';

      if (isset($_POST['feedback_id'])) {
          $feedback_id = $_POST['feedback_id'];
          $stmt = $conn->prepare("DELETE FROM feedback_list WHERE id = :id");
          $stmt->bindParam(':id', $feedback_id);
          
          if ($stmt->execute()) {
              $success_msg = "Feedback deleted successfully.";
          } else {
              $error_msg = "Error deleting feedback.";
          }
      }

      $_SESSION['current_page'] = 'feedback_list'; // Stay on the feedback list page after deletion
  }
}

$current_page = isset($_SESSION['current_page']) ? $_SESSION['current_page'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/FYP_SEM6/css/SuperAdmin1.css">
  <title>Dash Board</title>
</head>
<body>
  <div class="container">
    <nav class="nav collapsible">
      <i class="nav__toggler fa-solid fa-bars"></i>
      <ul class="nav__list collapsible__content">

        <li class="nav__item">
          <form method="post" action="">
            <button type="submit" name="action" value="add_normal_package" class="hidden-button">Add Package</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="">
            <button type="submit" name="action" value="add_customize_package" class="hidden-button">Add Customize Package</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="">
            <button type="submit" name="action" value="list_normal_package" class="hidden-button">Normal Package</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="">
            <button type="submit" name="action" value="list_normal_package_payment" class="hidden-button">View Normal Package Payment</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="">
            <button type="submit" name="action" value="list_customize_package" class="hidden-button">Customize Package</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="">
            <button type="submit" name="action" value="list_user_customize_detail" class="hidden-button">User Customize Detail</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="">
            <button type="submit" name="action" value="list_customize_package_payment" class="hidden-button">View Customize Payment</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="">
            <button type="submit" name="action" value="list_customize_package_order" class="hidden-button">Customize Package Order</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="">
            <button type="submit" name="action" value="feedback_list" class="hidden-button">Feedback</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="">
            <button type="submit" name="action" value="list_user" class="hidden-button">User List</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="">
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
      
    <?php

    if ($current_page == 'add_normal_package') {
        include 'add_normal_package.php';
    } 
    elseif ($current_page == 'add_customize_package') {
        include 'add_customize_package.php';
    } 
    elseif ($current_page == 'list_normal_package') {
        include 'list_normal_package.php';
    } 
    
    elseif ($current_page == 'list_normal_package_detail') {
      include 'list_normal_package_detail.php';
    } 
    elseif ($current_page == 'list_normal_package_payment') {
      include 'list_normal_package_payment.php';
    } 
    elseif ($current_page == 'list_customize_package') {
        include 'list_customize_package.php';
    }
    elseif ($current_page == 'list_user_customize_detail') {
        include 'list_user_customize_detail.php';
    }
    elseif ($current_page == 'list_customize_package_payment') {
        include 'list_customize_package_payment.php';
    }
    elseif ($current_page == 'list_customize_package_order') {
      include 'list_customize_package_order.php';
    } 
    elseif ($current_page == 'feedback_list') {
        include 'feedback_list.php';
    } 
    elseif ($current_page == 'list_user') {
      include 'list_user.php';
  } 
    elseif ($current_page == 'logs') {
      include('logs.php');

    } 
    else {
      echo "$full_name, Welcome To Super Admin Dash Board.<br>";
    }
    ?>
    </div>
  </div>
</body>
</html>
