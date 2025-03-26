<?php
session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");
require 'dbconnection.php';

$full_name = "";
$full_name_err = "";
$user_name_err = "";
$user_name = "";
$user_password = "";
$user_password_err = "";
$phone_number = "";
$phone_number_err = "";
$email = "";
$email_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    if (empty($_POST["full_name_input"])) {
        $full_name_err = "*";
    } else {
        $full_name = $_POST["full_name_input"];
    }
    if (empty($_POST["phone_number_input"])) {
        $phone_number_err = "*";
    } else {
        $phone_number = $_POST["phone_number_input"];
    }
    if (empty($_POST["email_input"])) {
        $email_err = "*";
    } else {
        $email = $_POST["email_input"];
    }
    if (empty($_POST["user_name_input"])) {
        $user_name_err = "*";
    } else {
        $user_name = $_POST["user_name_input"];
    }
    if (empty($_POST["user_password_input"])) {
        $user_password_err = "*";
    } else {
        $user_password = $_POST["user_password_input"];
    }

    // Insert into database if no errors
    if (empty($full_name_err) && empty($phone_number_err) && empty($email_err) && empty($user_name_err) && empty($user_password_err)) {
        $stmt = $conn->prepare("INSERT INTO user (full_name, phone_number, email, user_name, user_password) VALUES (:full_name, :phone_number, :email, :user_name, :user_password)");
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_name', $user_name);
        $stmt->bindParam(':user_password', $user_password);

        if ($stmt->execute()) {
            // Store user info in session
            $_SESSION['user_info'] = [
                'full_name' => $full_name,
                'phone_number' => $phone_number,
                'email' => $email,
                'user_id' => $conn->lastInsertId() // Get the last inserted ID
            ];
            header('Location: login_user.php');
            exit;
        } else {
            echo "Error: " . $stmt->errorInfo();
        }
    }
}

$conn = null;
?>


<header>
<link rel="stylesheet" href="/FYP_SEM6/css/LoginAndRegisters.css">
<title>User Register</title>
</header>

<body>

    <div class="container">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h2 class="space" >User Registration</h2>
        Full Name: <?php echo $full_name_err; ?>
        <input type="text" name="full_name_input" value="<?php echo $full_name; ?>">
        
        Phone Number: <?php echo $phone_number_err; ?>
        <input type="text" name="phone_number_input" value="<?php echo $phone_number; ?>">
        
        Email: <?php echo $email_err; ?>
        <input type="text" name="email_input" value="<?php echo $email; ?>">


        User Name: <?php echo $user_name_err; ?>
        <input type="text" name="user_name_input" value="<?php echo $user_name; ?>">
        

        User Password: <?php echo $user_password_err;?>
        <input type="password" name="user_password_input" value="<?php echo $user_password; ?>">
       
        <input type="submit" name="submit" value="Register">
        </form>
    </div>

    <footer>
    <div class="footer">
    <div class="footer-container">
      <div class="footer-content">
        <p>&copy; 2024 Travel Chill. All rights reserved.</p>
      </div>
    </div>
    </div>
  </footer>
</body>