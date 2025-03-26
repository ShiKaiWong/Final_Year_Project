<?php
session_start();

// Check if super_admin_info is set and retrieve full_name, otherwise set as empty string
$full_name = isset($_SESSION['super_admin_info']['full_name']) ? $_SESSION['super_admin_info']['full_name'] : '';

date_default_timezone_set("Asia/Kuala_Lumpur");
$logoutDateTime = date("Y-m-d H:i:s");
$_SESSION["logout_datetime"] = $logoutDateTime;  // Optionally log the logout time in session for later use

// Clear the user cookie
setcookie("SuperAdmin", "", time() - 3600);

// Log the logout action
$logFileName = "logs.txt";
try {
    $logFile = fopen($logFileName, "a");
    $logMessage = "Super Admin $full_name logged out at {$logoutDateTime}" . PHP_EOL;
    fwrite($logFile, $logMessage);
    fclose($logFile);
} catch (Exception $e) {
    // Handle file open/write errors
    error_log("Error writing to log file: " . $e->getMessage());
}

// Prepare to clear session
session_unset(); // Remove all session variables

// Destroy the session
session_destroy(); // It's a good practice to destroy the session completely if it's a logout action.

// Redirect to login.php and stop script execution
header('Location: login_super_admin.php');
exit;
?>
