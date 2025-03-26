<?php
// Start the session only if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'dbconnection.php';

$package_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$package_price = 0;

if ($package_id > 0) {
    $stmt = $conn->prepare("SELECT package_price FROM normal_package WHERE id = ?");
    $stmt->execute([$package_id]);
    $package_price = $stmt->fetchColumn();
}

// Retrieve full name from session info
$full_name = isset($_SESSION['user_info']['full_name']) ? $_SESSION['user_info']['full_name'] : '';
$user_id = isset($_SESSION['user_info']['user_id']) ? $_SESSION['user_info']['user_id'] : 0;  // Assume you need user ID somewhere

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/FYP_SEM6/css/customize_package_calculate.css">
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

  <div class="container">
    <h2>Payment</h2>

    <form method="post" action="normal_package_payment.php">
      <label for="full_name">Full Name:</label>
      <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" readonly>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="amount">Amount:</label>
      <input type="text" id="amount" name="amount" value="<?php echo htmlspecialchars($package_price); ?>" readonly>

      <label for="payment_method">Payment Method:</label>
      <select id="payment_method" name="payment_method" required>
        <option value="credit_card">Credit Card</option>
        <option value="touch_and_go">Touch And Go</option>
        <option value="online_banking">Online Banking</option>
      </select>

      <input type="hidden" name="package_id" value="<?php echo htmlspecialchars($package_id); ?>">
      <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($package_price); ?>">

      <input type="submit" class="normal_button" value="Pay Now">
    </form>
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
