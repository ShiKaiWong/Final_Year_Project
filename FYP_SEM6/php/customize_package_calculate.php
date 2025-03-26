<?php
// Start the session only if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'dbconnection.php';

$total_price = isset($_SESSION['total_price']) ? $_SESSION['total_price'] : 0;
$selected_package_ids = isset($_SESSION['selected_packages']) ? $_SESSION['selected_packages'] : [];

// Retrieve and calculate the total days and price based on selected package IDs
$unique_days = [];
$total_price = 0;

foreach ($selected_package_ids as $day_packages) {
    foreach ($day_packages as $package_id) {
        $stmt = $conn->prepare("SELECT id, day_time, price FROM customize_package WHERE id = :package_id");
        $stmt->bindParam(':package_id', $package_id);
        $stmt->execute();
        $package = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_price += $package['price'];
        // Add day_time to the unique_days array
        if (!in_array($package['day_time'], $unique_days)) {
            $unique_days[] = $package['day_time'];
        }
    }
}

// The total days should be the count of unique days
$total_days = count($unique_days);

$combined_package_ids = implode(',', array_merge(...$selected_package_ids));
$user_id = isset($_SESSION['user_info']['user_id']) ? $_SESSION['user_info']['user_id'] : null;
$full_name = isset($_SESSION['user_info']['full_name']) ? $_SESSION['user_info']['full_name'] : '';


if (is_null($user_id)) {
  // 提示用户登录
  echo "Error: You must be logged in to customize packages.";
  exit;
}
// Insert into `user_customize_detail`
$stmt_insert = $conn->prepare("INSERT INTO user_customize_detail (user_id, customize_package_ids, total_days) VALUES (:user_id, :customize_package_ids, :total_days)");
$stmt_insert->bindParam(':user_id', $user_id);
$stmt_insert->bindParam(':customize_package_ids', $combined_package_ids);
$stmt_insert->bindParam(':total_days', $total_days);
$stmt_insert->execute();

$_SESSION['total_price'] = 0;
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

  <div class="container">
    <h2>Payment</h2>

    <form method="post" action="customize_package_payment.php">
      <label for="full_name">Full Name:</label>
      <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" readonly>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="amount">Amount:</label>
      <input type="text" id="amount" name="amount" value="RM <?php echo htmlspecialchars($total_price); ?>" readonly>

      <label for="payment_method">Payment Method:</label>
      <select id="payment_method" name="payment_method" required>
        <option value="credit_card">Credit Card</option>
        <option value="touch_and_go">Touch And Go</option>
        <option value="online_banking">Online Banking</option>
      </select>

      <input type="hidden" name="package_ids" value="<?php echo htmlspecialchars($combined_package_ids); ?>">
      <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">

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
