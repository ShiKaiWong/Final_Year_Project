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
    // Validate full name
    if (empty($_POST["full_name_input"])) {
        $full_name_err = "*";
    } else {
        $full_name = $_POST["full_name_input"];
    }

    // Validate phone number
    if (empty($_POST["phone_number_input"])) {
        $phone_number_err = "*";
    } else {
        $phone_number = $_POST["phone_number_input"];
    }

    // Validate email
    if (empty($_POST["email_input"])) {
        $email_err = "*";
    } else {
        $email = $_POST["email_input"];
    }

    // Validate user name
    if (empty($_POST["user_name_input"])) {
        $user_name_err = "*";
    } else {
        $user_name = $_POST["user_name_input"];
    }

    // Validate user password
    if (empty($_POST["user_password_input"])) {
        $user_password_err = "*";
    } else {
        $user_password = $_POST["user_password_input"];
    }

    // Check if all fields are valid
    if (empty($full_name_err) && empty($phone_number_err) && empty($email_err) && empty($user_name_err) && empty($user_password_err)) {
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO super_admin (full_name, phone_number, email, user_name, user_password) VALUES (:full_name, :phone_number, :email, :user_name, :user_password)");
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_name', $user_name);
        $stmt->bindParam(':user_password', $user_password);

        if ($stmt->execute()) {
            // Store super admin info in session under super_admin_info
            $_SESSION['super_admin_info'] = [
                'full_name' => $full_name,
                'phone_number' => $phone_number,
                'email' => $email,
                'user_id' => $conn->lastInsertId() // Capture the last inserted ID
            ];
            header('Location: login_super_admin.php'); // Redirect to login page
            exit;
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
}

$conn = null;
?>
<header>
<link rel="stylesheet" href="/FYP_SEM6/css/LoginAndRegister.css">
<title>Super Admin Register</title>
</header>

<body>

    <div class="container">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h2 class="space" >Super Admin Register</h2>
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
</body>