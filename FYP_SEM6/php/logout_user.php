<?php
session_start();

// Check if user_info is set and retrieve full_name, otherwise set as empty string
$full_name = isset($_SESSION['user_info']['full_name']) ? $_SESSION['user_info']['full_name'] : '';

// Set timezone and get the current logout time
date_default_timezone_set("Asia/Kuala_Lumpur");
$logoutDateTime = date("Y-m-d H:i:s");
$_SESSION["logout_datetime"] = $logoutDateTime;  // Optionally log the logout time in session for later use

// Log the logout action before destroying the session
$logFileName = "logs.txt";
$logFile = fopen($logFileName, "a") or die("Unable to open log file!");
$logMessage = "User $full_name logged out at $logoutDateTime" . PHP_EOL;
fwrite($logFile, $logMessage);
fclose($logFile);

// Clear the user cookie
setcookie("user", "", time() - 3600);

// Destroy the session
session_destroy();

// Redirect to the login page
header('Location: login_user.php');
exit(); // Stop script execution after the redirect
?>
