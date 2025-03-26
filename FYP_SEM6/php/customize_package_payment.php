<?php
// Start the session only if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require 'dbconnection.php';

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $full_name = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $payment_method = $_POST['payment_method'] ?? '';
        $package_ids = explode(',', $_POST['package_ids'] ?? '');
        $total_price = $_POST['total_price'] ?? 0;
        $user_id = $_SESSION['user_info']['user_id'] ?? null;


        

        // Initialize variables to store total days, country, and city information
        $unique_days = [];
        $country = '';
        $city = '';

        // Calculate unique days and fetch other details from selected packages
        foreach ($package_ids as $package_id) {
            $stmt_package = $conn->prepare("SELECT country, city, day_time FROM customize_package WHERE id = ?");
            $stmt_package->execute([$package_id]);
            $package = $stmt_package->fetch(PDO::FETCH_ASSOC);

            if ($package) {
                if (!in_array($package['day_time'], $unique_days)) {
                    $unique_days[] = $package['day_time']; // Collect unique days
                }
                $country = $package['country'];
                $city = $package['city'];
            }
        }

        // The total days should be the count of unique days
        $total_days = count($unique_days);

        // Convert the package IDs array to a comma-separated string
        $package_ids_string = implode(',', $package_ids);

        // Insert payment information into customize_package_payment table
        $stmt_payment = $conn->prepare("INSERT INTO customize_package_payment (full_name, email, payment_method, package_ids, total_price, payment_date,user_id) VALUES (?, ?, ?, ?, ?, NOW(),?)");
        $stmt_payment->execute([$full_name, $email, $payment_method, $package_ids_string, $total_price,$user_id]);

        // Insert order information into customize_package_order table
        $stmt_order = $conn->prepare("INSERT INTO customize_package_order (user_id, customize_package_id, country, city, total_price, total_days, order_status) VALUES (?, ?, ?, ?, ?, ?, 'Paid')");
        $stmt_order->execute([$user_id, $package_ids_string, $country, $city, $total_price, $total_days]);


        echo '<div class="container">';
        echo '<h2>Payment Successfully!!!</h2>';
        echo '<a href="home_page.php" class="back-button">Back to Home</a>';
        echo '</div>';
    

        
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/FYP_SEM6/css/customize_package_payment.css">
  <title>Payment - Travel Chill</title>
</head>
<body>
<nav class="nav collapsible">
    <img src="/FYP_SEM6/images/logo.png" class="picture_image" alt="Logo" />
    <i class="nav__toggler fa-solid fa-bars"></i>
    <ul class="nav__list collapsible__content">
      <li class="nav__item"><a href="home_page.php">Home</a></li>
      <li class="nav__item"><a href="normal_package1.php">Package</a></li>
      <li class="nav__item"><a href="customize_package.php">Customize</a></li>
      <li class="nav__item"><a href="contact_us.php">Contact Us / About Us</a></li>
      <li class="nav__item"><a href="booking_history.php">History</a></li>
      <?php
      if(isset($_SESSION['user_info']) && isset($_SESSION['user_info']['user_id'])) {

        // If user is logged in, show the Logout button
        echo '<li class="nav__item"><a href="logout_user.php">Logout</a></li>';
    } else {
        // If user is not logged in, show the Sign Up button
        echo '<li class="nav__item"><a href="login_user.php">Sign Up</a></li>';
    }
      ?>
    </ul>
  </nav>


  <footer>
    <div class="footer-container">
      <div class="footer-content">
        <p>&copy; 2024 Travel Chill. All rights reserved.</p>
      </div>
    </div>
  </footer>
</body>
</html>
