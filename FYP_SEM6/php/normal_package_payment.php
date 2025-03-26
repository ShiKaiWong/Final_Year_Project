<?php
// Start the session only if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/FYP_SEM6/css/normal_package_payment1.css">
  <title>Payment - Travel Chill</title>
</head>
<body>
  <nav class="nav collapsible">
    <img src="/FYP_SEM6/images/logo.png" class="picture_image" alt="Logo" />
    <i class="nav__toggler fa-solid fa-bars"></i>
    <ul class="nav__list collapsible__content">
      <li class="nav__item"><a href="home_page.php">Home</a></li>
      <li class="nav__item"><a href="normal_package.php">Package</a></li>
      <li class="nav__item"><a href="customize_package.php">Customize</a></li>
      <li class="nav__item"><a href="contact_us.php">Contact Us / About Us</a></li>
      <li class="nav__item"><a href="booking_history.php">History</a></li>
      <?php
      if (isset($_SESSION['user_info']['user_id'])) {
        // If user is logged in, show the Logout button
        echo '<li class="nav__item"><a href="logout_user.php">Logout</a></li>';
    } else {
        // If user is not logged in, show the Sign Up button
        echo '<li class="nav__item"><a href="login_user.php">Sign Up</a></li>';
    }
      ?>
    </ul>
  </nav>

<?php


require 'dbconnection.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $user_id = $_SESSION['user_info']['user_id'];
      $full_name = $_POST['full_name'];
      $email = $_POST['email'];
      $payment_method = $_POST['payment_method'];
      $total_price = $_POST['total_price'];
      $package_id = $_POST['package_id']; // Ensure this is coming from your form
  
      $stmt = $conn->prepare("INSERT INTO normal_package_payment (user_id,full_name, email, payment_method, total_price, package_id) VALUES (?,?, ?, ?, ?, ?)");
      $stmt->execute([$user_id,$full_name, $email, $payment_method, $total_price, $package_id]);

  }
  
?>


  <div class="container">
    <h2>Payment Successfully!!!</h2>
    <a href="home_page.php" class="back-button">Back to Home</a>
  </div>

  <footer>
    <div class="footer-container">
      <div class="footer-content">
        <p>&copy; 2024 Travel Chill. All rights reserved.</p>
      </div>
    </div>
  </footer>
</body>
</html>
