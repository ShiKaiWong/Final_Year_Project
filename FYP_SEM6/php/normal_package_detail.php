<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/FYP_SEM6/css/normal_package_detail1.css">
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

  <div class="container">
    <div class="details-list">
      <?php
      require 'dbconnection.php';

          if (isset($_GET['id'])) {
              $id = $_GET['id'];

              // Query the normal_package table
              $packageStmt = $conn->prepare("SELECT * FROM normal_package WHERE id = :id");
              $packageStmt->bindParam(':id', $id);
              $packageStmt->execute();
              $package = $packageStmt->fetch(PDO::FETCH_ASSOC);

              if ($package) {
                  echo '<h2>' . htmlspecialchars($package['package_name']) . '</h2>';

                  // Query the normal_package_detail table using package_id
                  $detailStmt = $conn->prepare("SELECT * FROM normal_package_detail WHERE package_id = :package_id ORDER BY day_time");
                  $detailStmt->bindParam(':package_id', $id);
                  $detailStmt->execute();
                  $results = $detailStmt->fetchAll(PDO::FETCH_ASSOC);

                  if ($results) {
                      foreach ($results as $row) {
                          echo '<div class="detail-item">';
                          if (!empty($row['detail_picture'])) {
                              echo '<img src="' . htmlspecialchars($row['detail_picture']) . '" alt="' . htmlspecialchars($row['detail_name']) . '">';
                          }
                          echo '<div class="day-detail">';
                          echo '<h2>Day ' . htmlspecialchars($row['day_time']) . ': ' . htmlspecialchars($row['detail_name']) . '</h2>';
                          echo '<p>' . htmlspecialchars($row['detail_description']) . '</p>';
                          echo '</div></div>';
                      }
                  } else {
                      echo '<p>No details found for the given package ID.</p>';
                  }
              } else {
                  echo '<p>No package found for the given ID.</p>';
              }
          } else {
              echo '<p>No ID provided.</p>';
          }

      $conn = null; // Close the connection
      ?>
    </div>
    
    <aside class="booking-info">
      <?php 
      if ($package) {
          echo '<div>';
          echo 'Travel Date: ' . htmlspecialchars($package['package_date']) . ' - ' . htmlspecialchars($package['package_end_date']);
          echo '<p>Destination: ' . htmlspecialchars($package['package_name']) . '</p>';
          echo '<p>Price: RM' . htmlspecialchars($package['package_price']) . '</p>';

          // You can set a default or placeholder value for days here
          $total_days = $results ? count($results) : 0; // Assuming each entry in results corresponds to a day
          echo '<p>Day: ' . htmlspecialchars($total_days) . ' days</p>';
          echo '</div>';
      } else {
          echo '<p>No package found for the given ID.</p>';
      }
      ?>

      <a href="normal_package_calculate.php?id=<?php echo urlencode($package['id']); ?>"><button>Book Now</button></a>
    </aside>
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
