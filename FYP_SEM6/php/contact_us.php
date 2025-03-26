<?php
// Start the session only if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'dbconnection.php';

// Initialize variables to avoid undefined variable warnings
$full_name = "";
$email = "";
$phone_number = "";
$interested = "";
$message = "";
$errors = [];

// Check if user ID is set in the session and fetch user data from the database
$user_id = isset($_SESSION['user_info']['user_id']) ? $_SESSION['user_info']['user_id'] : null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT full_name, email, phone_number FROM user WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $full_name = $result['full_name'];
        $email = $result['email'];
        $phone_number = $result['phone_number'];
    }
}

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'] ?? $full_name;
    $email = $_POST['email'] ?? $email;
    $phone_number = $_POST['phone_number'] ?? $phone_number;
    $interested = $_POST['interested'] ?? '';
    $message = $_POST['message'] ?? '';
    $user_id = $_SESSION['user_info']['user_id'] ?? null;

    // Validate fields
    if (empty($full_name) || empty($email) || empty($phone_number) || empty($interested) || empty($message)) {
        $errors[] = "Please fill in all the required fields.";
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // If no errors, proceed with the database insertion
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO feedback_list (full_name, email, phone_number, interested, message,user_id) VALUES (:full_name, :email, :phone_number, :interested, :message, :user_id)");
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':interested', $interested);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $_SESSION['message'] = "Send successfully!!!";
        header("Location: contact_us.php");
        exit();
    }
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/FYP_SEM6/css/contact_us.css">
    <title>Contact Us</title>
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
        echo '<li class="nav__item"><a href="logout_user.php">Logout</a></li>';
      } else {
        echo '<li class="nav__item"><a href="login_user.php">Sign Up</a></li>';
      }
      ?>
    </ul>
  </nav>

<section class="contact-section">
    <div class="contact-header">
        <h1>Contact Us</h1>
    </div>
    <form action="" method="POST" class="contact-form">
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
        </div>
        <div class="form-group">
            <label for="interested">Interested In:</label>
            <input list="choices" id="interested" name="interested" />
            <datalist id="choices">
                <option value="Feedback"></option>
                <option value="Other"></option>
            </datalist>
        </div>
        <div class="form-group">
            <label for="message">Your message:</label>
            <textarea id="message" name="message"></textarea>
        </div>
        <button type="submit" class="btn btn--outline btn--block2">Send</button>
    </form>

    <!-- Display validation errors -->
    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach ($errors as $error): ?>
                <p class='error-message'><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php
    if (isset($_SESSION['message'])) {
        echo "</br>";
        echo "<p class='success-message'>" . $_SESSION['message'] . "</p>";
        unset($_SESSION['message']);
    }
    ?>

    <div class="map-section">
        <h2>The Travel Chill Location In Map</h2>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.699125583434!2d3.4283575747845316!3d6.432681424212483!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103bf57c7a618537%3A0x3689d19908a5d097!2sLEVART%20TOURS%20LTD!5e0!3m2!1sen!2smy!4v1697186884568!5m2!1sen!2smy"
            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>

        <h2>Phone Number: 012-3456789</h2>
    </div>
</section>

<footer>
    <div class="footer-container">
        <div class="footer-content">
            <p>&copy; 2024 Travel Chill. All rights reserved.</p>
        </div>
    </div>
</footer>
</body>
</html>
