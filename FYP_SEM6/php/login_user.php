<?php
session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");
$logoutDateTime = date("Y-m-d H:i:s");

function writeToLogFile($action) {
    $logFileName = "logs.txt";
    $currentTime = $action . PHP_EOL;

    // Open the log file in append mode
    $logFile = fopen($logFileName, "a") or die("Unable to open log file!");

    // Write the log message to the log file
    fwrite($logFile, $currentTime);

    
    // Close the log file
    fclose($logFile);
}

require 'dbconnection.php';

    $user_name_err = $user_password_err = $login_error = "";
    $user_name = $user_password = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

        // Check if both user name and password are provided
        if (!empty($user_name) && !empty($user_password)) {
            // Use prepared statements to prevent SQL injection
            $sql = "SELECT * FROM user WHERE user_name = :user_name AND user_password = :user_password";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_name', $user_name);
            $stmt->bindParam(':user_password', $user_password);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result === false) {
                $login_error = "Incorrect User Name or User Password.";
            } else {
                $_SESSION['user_info'] = [
                    "full_name" => $result["full_name"],
                    "user_id" => $result["id"]
                ];

                writeToLogFile("User Login: " . $_SESSION['user_info']['full_name'] . " at " . $logoutDateTime);
                header('Location: home_page.php');
                exit;
            }
        }
    }
?>
<header>
<link rel="stylesheet" href="/FYP_SEM6/css/LoginAndRegisters.css">
<title>User Login</title>
</header>
<body>
<div class="container">
  <h2>Login</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

    User Name: <?php echo $user_name_err; ?>
    <input type="text" name="user_name_input" value="<?php echo htmlspecialchars($user_name); ?>">

    User Password: <?php echo $user_password_err; ?>
    <input type="password" name="user_password_input" value="<?php echo htmlspecialchars($user_password); ?>">
    
    <span style="color:red" class="space"><?php echo $login_error; ?></span>

    <input type="submit" name="submit" value="Login">

</form>
<a href="register_page.php" class="register_button">Tap Here To Register</a>

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


<?php
$conn = null; // Close connection with DB
?>
