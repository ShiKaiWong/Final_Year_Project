<?php
// Start the session only if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'dbconnection.php';

// Function to handle errors and provide feedback
function handleError($message) {
    echo "<p>$message</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History - Travel Chill</title>
    <link rel="stylesheet" href="/FYP_SEM6/css/booking_detail4.css">
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
<main class="container">
<?php
if (isset($_GET['type']) && isset($_GET['id'])) {
    $package_type = $_GET['type'];
    $booking_id = $_GET['id'];

    if ($package_type === 'Normal') {
        try {
            // Fetch normal package details
            $stmt = $conn->prepare("
                SELECT np.package_name, np.package_date, np.package_end_date, np.package_style, np.package_price, np.id AS package_id
                FROM normal_package np
                JOIN normal_package_payment npp ON npp.package_id = np.id
                WHERE npp.id = :booking_id
            ");
            $stmt->bindParam(':booking_id', $booking_id);
            $stmt->execute();
            $details = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($details) {
                echo "<h2>{$details['package_name']}</h2>";
                echo "<div class='package-details'>
                        <div><strong>Start Date:</strong> {$details['package_date']}</div>
                        <div><strong>End Date:</strong> {$details['package_end_date']}</div>
                        <div><strong>Style:</strong> {$details['package_style']}</div>
                        <div><strong>Price:</strong> RM {$details['package_price']}</div>
                      </div>";
                

                // Fetch normal package detail information using package_id
                $stmt_details = $conn->prepare("
                    SELECT day_time, detail_name, detail_description, detail_picture
                    FROM normal_package_detail
                    WHERE package_id = :package_id
                    ORDER BY day_time ASC
                ");
                $stmt_details->bindParam(':package_id', $details['package_id']);
                $stmt_details->execute();
                $package_details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

                echo "<h4>Package Details:</h4>";
                echo "<ul class='package-list'>";
                if ($package_details) {
                    foreach ($package_details as $detail) {
                        echo "<li class='package-detail'>
                                <img src='{$detail['detail_picture']}' alt='{$detail['detail_name']}' />
                                <div class='package-detail-text'>
                                    <p><strong>Day {$detail['day_time']}:</strong> {$detail['detail_name']}</p>
                                    <p>{$detail['detail_description']}</p>
                                </div>
                              </li>";
                    }
                } else {
                    echo "<p>No details found for this package.</p>";
                }
                echo "</ul>";

            } else {
                handleError("Booking details not found.");
            }

        } catch (PDOException $e) {
            handleError("An error occurred: " . $e->getMessage());
        }

    } elseif ($package_type === 'Customize') {
        try {
            // Fetch customize package details
            $stmt = $conn->prepare("
                SELECT cpo.country, cpo.city, cpo.total_price, cpo.total_days, cpo.customize_package_id
                FROM customize_package_order cpo
                WHERE cpo.id = :booking_id
            ");
            $stmt->bindParam(':booking_id', $booking_id);
            $stmt->execute();
            $details = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($details) {
                echo "<h3>{$details['country']}</h3>";
                echo "<div class='package-details'>
                        <div><strong>City:</strong> {$details['city']}</div>
                        <div><strong>Days:</strong> {$details['total_days']} days</div>
                        <div><strong>Total Price:</strong> RM {$details['total_price']}</div>
                      </div>";
                      echo "<br>";

                echo "<ul class='package-list'>";
                // Fetch details for each selected package
                $package_ids = explode(',', $details['customize_package_id']);
                foreach ($package_ids as $package_id) {
                    $stmt_package = $conn->prepare("
                        SELECT package_name, customize_description, price, picture_url, day_time
                        FROM customize_package
                        WHERE id = :package_id
                    ");
                    $stmt_package->bindParam(':package_id', $package_id);
                    $stmt_package->execute();
                    $package_details = $stmt_package->fetch(PDO::FETCH_ASSOC);

                    if ($package_details) {
                        echo "<li class='package-detail'>
                                <img src='{$package_details['picture_url']}' alt='{$package_details['package_name']}' />
                                <div class='package-detail-text'>
                                    <p><strong>Day {$package_details['day_time']}:</strong> {$package_details['package_name']}</p>
                                    <p>{$package_details['customize_description']}</p>
                                    <p><strong>Price:</strong> RM {$package_details['price']}</p>
                                </div>
                              </li>";
                    }
                }
                echo "</ul>";
            } else {
                handleError("Booking details not found.");
            }
        } catch (PDOException $e) {
            handleError("An error occurred: " . $e->getMessage());
        }
    } else {
        handleError("Invalid package type.");
    }
} else {
    handleError("Required parameters are missing.");
}
?>

<footer>
<div class="footer-container">
    <div class="footer-content">
        <p>&copy; 2024 Travel Chill. All rights reserved.</p>
    </div>
</div>
</footer>

</body>
</html>
