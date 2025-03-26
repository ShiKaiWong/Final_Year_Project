<?php
// Start the session only if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require 'dbconnection.php';

// Define cities for each country
$citiesByCountry = [
    'China' => ['Beijing', 'Guangdong'],
    'Japan' => ['Tokyo', 'Osaka']
];

$selected_country = isset($_POST['country']) ? $_POST['country'] : '';
$selected_city = isset($_POST['city']) ? $_POST['city'] : '';
$selected_days = isset($_POST['days']) ? $_POST['days'] : '';
$current_day = isset($_POST['current_day']) ? $_POST['current_day'] : 1;

// Initialize session variables if not set
if (!isset($_SESSION['total_price'])) {
    $_SESSION['total_price'] = 0;
}
if (!isset($_SESSION['selected_packages'])) {
    $_SESSION['selected_packages'] = [];
}

// Handle "Next" button click
if (isset($_POST['next'])) {
    if (!empty($_POST['package_ids'])) {
        foreach ($_POST['package_ids'] as $package_id) {
            $stmt = $conn->prepare("SELECT price FROM customize_package WHERE id = :package_id");
            $stmt->bindParam(':package_id', $package_id);
            $stmt->execute();
            $package = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['total_price'] += $package['price']; // 累积总价
        }
        $_SESSION['selected_packages'][$current_day] = $_POST['package_ids'];
    }
    $current_day++;
}

// Handle "Back" button click
if (isset($_POST['back'])) {
    if ($current_day > 1) {
        // Decrement the current day first
        $current_day--;
        
        // Clear the total price and selected packages for the day we are going back to
        if (isset($_SESSION['selected_packages'][$current_day])) {
            foreach ($_SESSION['selected_packages'][$current_day] as $package_id) {
                $stmt = $conn->prepare("SELECT price FROM customize_package WHERE id = :package_id");
                $stmt->bindParam(':package_id', $package_id);
                $stmt->execute();
                $package = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['total_price'] -= $package['price'];
            }
            unset($_SESSION['selected_packages'][$current_day]);
        }
    }
}

// Handle "Submit" button click and redirect to payment
if (isset($_POST['submit'])) {
    if (!empty($_POST['package_ids'])) {
        foreach ($_POST['package_ids'] as $package_id) {
            $stmt = $conn->prepare("SELECT price FROM customize_package WHERE id = :package_id");
            $stmt->bindParam(':package_id', $package_id);
            $stmt->execute();
            $package = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['total_price'] += $package['price']; // 累积总价
        }
        $_SESSION['selected_packages'][$current_day] = $_POST['package_ids'];
    }
    header("Location: customize_package_calculate.php");
    exit();
}
?>

<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/FYP_SEM6/css/customize_package2.css">
  <title>Customize and Calculate Price - Travel Chill</title>
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
      // Check if user is logged in
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

  <h2>Customize Your Package</h2>

  <form method="post" action="">
    <label for="country" class="select_form">Select Country:</label>
    <select id="country" class="select_form" name="country" onchange="this.form.submit()">
        <option value="">--Select a country--</option>
        <option value="China" <?php echo $selected_country == 'China' ? 'selected' : ''; ?>>China</option>
        <option value="Japan" <?php echo $selected_country == 'Japan' ? 'selected' : ''; ?>>Japan</option>
    </select>
    <br>

    <?php if ($selected_country): ?>
        <label for="city" class="select_form">Select City:</label>
        <select id="city" class="select_form" name="city" onchange="this.form.submit()">
            <option value="">--Select a city--</option>
            <?php foreach ($citiesByCountry[$selected_country] as $city): ?>
                <option value="<?php echo $city; ?>" <?php echo $selected_city == $city ? 'selected' : ''; ?>><?php echo $city; ?></option>
            <?php endforeach; ?>
        </select>
        <br>

        <?php if ($selected_city): ?>
            <label for="days" class="select_form">Select Days:</label>
            <select id="days" class="select_form" name="days" onchange="this.form.submit()">
                <option value="">--Select number of days--</option>
                <?php for ($i = 1; $i <= 7; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo $selected_days == $i ? 'selected' : ''; ?>><?php echo $i; ?> day<?php echo $i > 1 ? 's' : ''; ?></option>
                <?php endfor; ?>
            </select>
            <br>
        <?php endif; ?>
    <?php endif; ?>
  </form>
  <br>

  <?php
  if ($selected_country && $selected_city && $selected_days) {
      $stmt = $conn->prepare("SELECT * FROM customize_package WHERE country = :country AND city = :city AND day_time = :current_day ORDER BY day_time");
      $stmt->bindParam(':country', $selected_country);
      $stmt->bindParam(':city', $selected_city);
      $stmt->bindParam(':current_day', $current_day, PDO::PARAM_INT);
      $stmt->execute();
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($results) {
          echo "<form method='post' action=''>";
          echo "<input type='hidden' name='country' value='$selected_country'>";
          echo "<input type='hidden' name='city' value='$selected_city'>";
          echo "<input type='hidden' name='days' value='$selected_days'>";
          echo "<input type='hidden' name='current_day' value='$current_day'>";
          echo "<div class='package-container'>";
          foreach ($results as $row) {
              echo "<div class='day-package'>
                      <img src='{$row['picture_url']}' alt='Package Image'>
                      <div class='day-package-details'>
                          <h4>Day {$row['day_time']}: {$row['package_name']}</h4>
                          <p>{$row['customize_description']}</p>
                          <p >Price: RM<span class='package-price'>{$row['price']}</span></p>
                      </div>
                      <div class='checkbox-container'>
                          <input type='checkbox' name='package_ids[]' value='{$row['id']}' onclick='calculateTotal()'>
                      </div>
                    </div>";
          }
          echo "</div>";

        // Display total price above the buttons
        echo "<div class='total-container' style='text-align:center; font-weight:bold;'>Total Price: RM<span id='total-price'>" . number_format($_SESSION['total_price'], 2) . "</span></div>";

        echo "<div class='button-container'>";
        if ($current_day > 1) {
            echo "<input type='submit' class='normal_button back_button' name='back' value='Back'>";
        } else {
            echo "<div class='spacer'></div>";
        }

        if ($current_day < $selected_days) {
            echo "<input type='submit' class='normal_button' name='next' value='Next' onclick='return validateNextStep()'>";
        } else {
            echo "<input type='submit' class='normal_button' name='submit' value='Submit'>";
        }
        echo "</div>";


        

          echo "</form>";
      }
  }
  ?>

  <footer>
    <div class="footer-container">
      <div class="footer-content">
        <p>&copy; 2024 Travel Chill. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <script>
 function calculateTotal() {
    const prices = document.querySelectorAll('.package-price');
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    let total = <?php echo $_SESSION['total_price']; ?>; // 从之前的总价开始累积

    checkboxes.forEach((checkbox, index) => {
        if (checkbox.checked) {
            total += parseFloat(prices[index].textContent);
        }
    });

    document.getElementById('total-price').textContent = total.toFixed(2);
}

function validateNextStep() {
    const checkboxes = document.querySelectorAll('input[name="package_ids[]"]:checked');
    
    if (checkboxes.length === 0) {
        alert("Please select at least one package to proceed to the next step.");
        return false; // Prevent form submission
    }
    
    return true; // Allow form submission
}


  </script>

</body>
</html>
