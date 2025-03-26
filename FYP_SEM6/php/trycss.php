<link rel="stylesheet" href="/FYP_SEM6/css/try.css">
<!-- <body>

  <div class="container">
    <h2>Login</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    User Name: <input type="text" name="user_name_input" value="<?php echo $user_name; ?>">
    <span style="color:red"><?php echo $user_name_err; ?></span>
    <br><br>

    User Password: <input type="password" name="user_password_input" value="<?php echo $user_password; ?>">
    <span style="color:red"><?php echo $user_password_err; ?></span>
    <br><br>

    <span style="color:red"><?php echo $login_error; ?></span>
    <br><br>

    <input type="submit" name="submit" value="Login">
    <br><br>

</form>

    <a href="register_page.php" class="logout_button">Tap Here To Register</a>
  </div>

</body> -->


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/FYP_SEM6/css/customize_package.css">
  <title>Customize and Calculate Price - Travel Chill</title>
</head>
<body>
  <nav class="nav collapsible">
    <img src="/FYP_SEM6/images/logo.png" class="picture_image" alt="Logo" />
    <i class="nav__toggler fa-solid fa-bars"></i>
    <ul class="nav__list collapsible__content">
      <li class="nav__item"><a href="home_page.php">Home</a></li>
      <li class="nav__item"><a href="package.php">Package</a></li>
      <li class="nav__item"><a href="customize_package.php">Customize</a></li>
      <li class="nav__item"><a href="contact_us.php">Contact Us / About Us</a></li>
      <li class="nav__item"><a href="login_user.php">Logout</a></li>
    </ul>
  </nav>

  <h2>Customize Your Package</h2>

  <?php
  // Define cities for each country
  $citiesByCountry = [
    'China' => ['Beijing', 'Shanghai', 'Guangzhou'],
    'Malaysia' => ['Penang', 'Melaka', 'Kuching'],
    'Japan' => ['Tokyo', 'Osaka', 'Kyoto']
  ];

  $selected_country = isset($_POST['country']) ? $_POST['country'] : '';
  $selected_city = isset($_POST['city']) ? $_POST['city'] : '';
  $selected_package_ids = isset($_POST['package_ids']) ? $_POST['package_ids'] : [];
  ?>

  <form method="post">
    <label for="country" class="select_form">Select Country:</label>
    <select id="country" class="select_form" name="country" onchange="this.form.submit()">
      <option value="">--Select a country--</option>
      <option value="China" <?php echo $selected_country == 'China' ? 'selected' : ''; ?>>China</option>
      <option value="Malaysia" <?php echo $selected_country == 'Malaysia' ? 'selected' : ''; ?>>Malaysia</option>
      <option value="Japan" <?php echo $selected_country == 'Japan' ? 'selected' : ''; ?>>Japan</option>
    </select>
    <br>

    <?php if ($selected_country): ?>
      <?php if (!empty($citiesByCountry[$selected_country])): ?>
        <label for="city" class="select_form">Select City:</label>
        <select id="city" class="select_form" name="city" onchange="this.form.submit()">
          <option value="">--Select a city--</option>
          <?php foreach ($citiesByCountry[$selected_country] as $city): ?>
            <option value="<?php echo $city; ?>" <?php echo $selected_city == $city ? 'selected' : ''; ?>><?php echo $city; ?></option>
          <?php endforeach; ?>
        </select>
        <br>
      <?php else: ?>
        <p>No city available for the selected country.</p>
      <?php endif; ?>
    <?php endif; ?>
  </form>
  <br>

  <?php
  $servername = "127.0.0.1:3307";
  $username = "shikai";
  $password = "secef017";
  $dbname = "fyp_sem6";

  try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      if ($selected_country && $selected_city) {
          $stmt = $conn->prepare("SELECT * FROM customize_package WHERE country = :country AND city = :city ORDER BY day_time");
          $stmt->bindParam(':country', $selected_country);
          $stmt->bindParam(':city', $selected_city);
          $stmt->execute();
          $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($results) {
              echo "<form method='post' action='customize_package_calculate.php'>";
              echo "<div class='package-container'>";
              foreach ($results as $row) {
                  $isChecked = in_array($row['id'], $selected_package_ids) ? 'checked' : '';
                  echo "<div class='day-package'>
                          <img src='{$row['picture_url']}' alt='Package Image'>
                          <div class='day-package-details'>
                              <h4>Day {$row['day_time']}: {$row['package_name']}</h4>
                              <p>{$row['customize_description']}</p>
                              <p>Price: RM<span class='package-price'>{$row['price']}</span></p>
                          </div>
                          <div class='checkbox-container'>
                              <input type='checkbox' name='package_ids[]' value='{$row['id']}' $isChecked>
                          </div>
                        </div>";
              }
              echo "</div>";
              echo "<div class='total-container'>Total Price: RM<span id='total-price'>0.00</span></div>";
              echo "<input type='submit' class='normal_button' value='Submit'>";
              echo "</form>";
          } else {
              echo "<p>No packages available for the selected country and city.</p>";
          }
      }

  } catch (PDOException $e) {
      echo "Connection failed: " . htmlspecialchars($e->getMessage());
  }
  ?>

  <?php include 'Asg_footer.php'; ?>

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
    let total = 0;

    checkboxes.forEach((checkbox, index) => {
      if (checkbox.checked) {
        total += parseFloat(prices[index].textContent);
      }
    });

    document.getElementById('total-price').textContent = total.toFixed(2);
  }

  // Initialize total price on page load and bind change event to checkboxes
  window.onload = function() {
    calculateTotal();
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
      checkbox.addEventListener('change', calculateTotal);
    });
  }
</script>

</body>
</html>





















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

  <link rel="stylesheet" href="/FYP_SEM6/css/list2.css">
  <title>List View Detail Packages</title>
</head>
<body>
    


<h2>Normal Package Detail List</h2>
Search by ID or package name: <br>
<input type="text" onkeyup="searchPackage(this.value)" value="">
<br><br>

<div id="package_detail_table_list">
    <?php
    require 'dbconnection.php';

    date_default_timezone_set("Asia/Kuala_Lumpur");
    $logoutDateTime = date('d-m-Y H:i:s');
    $_SESSION["logout_datetime"] = $logoutDateTime;

    // Handle deletion via GET parameter
    if (isset($_GET['delete'])) {
        $detail_id = $_GET['delete'];
        $stmt = $conn->prepare("DELETE FROM normal_package_detail WHERE id = :detail_id");
        $stmt->bindParam(':detail_id', $detail_id);
        if ($stmt->execute()) {
            file_put_contents("logs.txt", "Deleted normal package detail ID $detail_id at " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
           
        } 
    }

    // Fetch package detail records
    $stmt = $conn->prepare("SELECT * FROM normal_package_detail ORDER BY id");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        echo "
              <table>
                  <thead>
                      <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Day</th>
                          <th>Description</th>
                          <th>Picture</th>
                          <th>Action</th>
                      </tr>
                  </thead>
                  <tbody>";

        foreach ($results as $row) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['detail_name']}</td>
                    <td>{$row['day_time']}</td>
                    <td>{$row['detail_description']}</td>
                    <td>";
            if (!empty($row["detail_picture"])) {
                echo "<img src='{$row["detail_picture"]}' style='width:70px; height:70px; object-fit:cover;'>";
            }
            echo "</td>
                    <td>
                        <a href='?delete={$row['id']}' class='delete-link'>Delete</a>
                        <a href='#' onclick='editDetail({$row['id']}, \"{$row['detail_name']}\", \"{$row['day_time']}\", \"{$row['detail_description']}\", \"{$row['detail_picture']}\")' class='edit-link'>Edit</a>
                    </td>
                </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No detail packages found.</p>";
    }

    $conn = null;
    ?>
</div>

<h2>Edit Detail</h2>
<form id="editDetailForm" method="post" action="edit_normal_package_detail.php" enctype="multipart/form-data">
    <input type="hidden" name="id" id="detail_id">
    <label for="detail_name">Name:</label><br>
    <input type="text" name="detail_name" id="detail_name"><br><br>
    
    <label for="day_time">Day:</label><br>
    <input type="number" name="day_time" id="day_time"><br><br>
    
    <label for="detail_description">Description:</label><br>
    <textarea name="detail_description" id="detail_description"></textarea><br><br>
    
    <label for="detail_picture">Upload Picture:</label><br>
    <input type="file" name="detail_picture" id="detail_picture"><br><br>
    
    <input type="submit" value="Save Changes">
</form>

<script>
function searchPackage(str) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("package_detail_table_list").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "search_normal_package_detail.php?query=" + str, true);
    xmlhttp.send();
}

function editDetail(id, name, day, description, picture) {
    document.getElementById('detail_id').value = id;
    document.getElementById('detail_name').value = name;
    document.getElementById('day_time').value = day;
    document.getElementById('detail_description').value = description;
    document.getElementById('detail_picture').value = picture;
    
    // Scroll down to the edit form
    document.getElementById('editDetailForm').scrollIntoView();
}

document.getElementById('editDetailForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent normal form submission

    var formData = new FormData(this);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'edit_normal_package_detail.php', true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            document.getElementById('editDetailForm').insertAdjacentHTML('beforebegin', xhr.responseText);
            document.getElementById('editDetailForm').reset(); // Reset form fields after a successful update
        } else {
            alert('Error occurred during update.');
        }
    };

    xhr.send(formData);
});
</script>

</body>
</html>
