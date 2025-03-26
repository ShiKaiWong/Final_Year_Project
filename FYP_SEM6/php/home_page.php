<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
$showLoginModal = false;
if (!isset($_SESSION['user_info']) || !isset($_SESSION['user_info']['user_id'])) {
    $showLoginModal = true; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/FYP_SEM6/css/HomePage2.css">
  <title>Travel Chill</title>

</head>
<body>

  
  <div class="modal" style="display: <?php echo $showLoginModal ? 'flex' : 'none'; ?>;">
    <div class="modal-content">
      <h2>You need to log in to access this page</h2>
      <p>Please log in to continue using the site.</p>
      <a href="login_user.php">Go to Login</a>
    </div>
  </div>

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

  <?php  
  // Get the full name from session
  $full_name = isset($_SESSION['user_info']['full_name']) ? $_SESSION['user_info']['full_name'] : '';
  if ($full_name) {
      echo '<h1>Welcome to Travel Chill, ' . htmlspecialchars($full_name) . '</h1>';
  }
  ?>
  <h2>Let's See Your Travel Trip Plan.</h2>
  <div class="container grid grid--1x2">
    <img src="/FYP_SEM6/images/zzz.jpg" class="picture_image2" alt="welcome" />
    <div class="text-container">
      <h3>Travel Chill offers an unparalleled travel experience.Whether you're seeking adventure,relaxation,or a bit of both,we ensure that your journey is filled with unforgettable moments.From breathtaking landscapes to vibrant cultures,Travel Chill promises a memorable and enriching trip.</h3>
    </div>
  </div>

  <h2>Recommend Tours</h2>

  <div class="cards-container">
  <?php
  require 'dbconnection.php';


        $stmt = $conn->prepare("SELECT id, package_name, package_date, package_style, package_price, package_picture FROM normal_package LIMIT 3");
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

  <h3>Popular Tour</h3> 

  <div class="cards-container2">
    <?php
      try {
        $stmt = $conn->prepare("SELECT id, package_name, package_date, package_style, package_price, package_picture FROM normal_package LIMIT 3 OFFSET 3");
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
      } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
      }

      $conn = null; // Close the connection
    ?>
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
</html>
