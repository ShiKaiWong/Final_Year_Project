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
  <link rel="stylesheet" href="/FYP_SEM6/css/normal_package2.css">
  <title>Travel Chill</title>
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

  <div class="package-header">
      <h1>Package</h1>
      <form class="search-form" action="normal_package_country_search.php" method="GET">
          <input type="text" name="country" placeholder="Search country..." class="search-input">
          <button type="submit" class="search-button">Search</button>
      </form>
  </div>

  <div class="cards-container2">
    <?php
        require 'dbconnection.php';

        // Fetch package details
        $stmt = $conn->prepare("SELECT id, package_name, package_date, package_style, package_price, package_picture FROM normal_package");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

        foreach ($stmt->fetchAll() as $row) {
            echo '<div class="card">';
            echo '<img src="' . $row["package_picture"] . '" alt="' . $row["package_name"] . '">';
            echo '<div class="card-content">';
            echo '<div class="info">';
            echo '<p class="destination">Destination</p>';
            echo '<p class="price">Price</p>';
            echo '</div>';
            echo '<div class="details">';
            echo '<p class="destination-value">' . $row["package_name"] . '</p>';
            echo '<p class="price-value">RM' . $row["package_price"] . '</p>';
            echo '</div>';
            echo '</div>';
            echo '<a href="normal_package_detail.php?id=' . $row["id"] . '"><button class="view-detail">View Detail</button></a>';
            echo '</div>';
        }

    ?>
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
