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
    <title>Booking History - Travel Chill</title>
    <link rel="stylesheet" href="/FYP_SEM6/css/booking_history.css">
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

<h2>Booking History</h2>

<?php

if (!isset($_SESSION['user_info']) || !isset($_SESSION['user_info']['user_id'])) {
    // 如果用户未登录，重定向到登录页面
    header("Location: login_user.php");
    exit();
}

require 'dbconnection.php';

// 获取当前登录用户的ID
$user_id = $_SESSION['user_info']['user_id'];

// Fetch normal package bookings for the current user
$stmt_normal = $conn->prepare("
    SELECT npp.id AS booking_id, np.package_name AS package_name, npp.total_price AS price, DATEDIFF(np.package_end_date, np.package_date) + 1 AS days, 'Normal' AS package_type
    FROM normal_package_payment npp
    JOIN normal_package np ON npp.package_id = np.id
    WHERE npp.user_id = :user_id
");
$stmt_normal->bindParam(':user_id', $user_id);
$stmt_normal->execute();
$normal_bookings = $stmt_normal->fetchAll(PDO::FETCH_ASSOC);

// Fetch customize package bookings for the current user
$stmt_customize = $conn->prepare("
    SELECT cpo.id AS booking_id, cpo.country AS package_name, cpo.total_price AS price, cpo.total_days AS days, 'Customize' AS package_type
    FROM customize_package_order cpo
    WHERE cpo.user_id = :user_id AND cpo.order_status = 'Paid'
");
$stmt_customize->bindParam(':user_id', $user_id);
$stmt_customize->execute();
$customize_bookings = $stmt_customize->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- Display Normal Package Bookings -->
<h3>Normal Package</h3>
<?php
if ($normal_bookings) {
    echo "<table>
            <tr>
                <th>Booking ID</th>
                <th>Package Name</th>
                <th>Price</th>
                <th>Days</th>
                <th></th>
            </tr>";

    foreach ($normal_bookings as $booking) {
        echo "<tr>
                <td>{$booking['booking_id']}</td>
                <td>{$booking['package_name']}</td>
                <td>RM {$booking['price']}</td>
                <td>{$booking['days']} days</td>
                <td><a href='booking_details.php?type={$booking['package_type']}&id={$booking['booking_id']}' class='view-button'>View</a></td>
            </tr>";
    }

    echo "</table>";
} else {
    echo "<p style='text-align: center;'>No Normal Package booking history found.</p>";
}
?>

<!-- Display Customize Package Bookings -->
<h3>Customize Package</h3>
<?php
if ($customize_bookings) {
    echo "<table>
            <tr>
                <th>Booking ID</th>
                <th>Package Name</th>
                <th>Price</th>
                <th>Days</th>
                <th></th>
            </tr>";

    foreach ($customize_bookings as $booking) {
        echo "<tr>
                <td>{$booking['booking_id']}</td>
                <td>{$booking['package_name']}</td>
                <td>RM {$booking['price']}</td>
                <td>{$booking['days']} days</td>
                <td><a href='booking_details.php?type={$booking['package_type']}&id={$booking['booking_id']}' class='view-button'>View</a></td>
            </tr>";
    }

    echo "</table>";
} else {
    echo "<p style='text-align: center;'>No Customize Package booking history found.</p>";
}
?>

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
</html>
