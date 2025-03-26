<?php
// Start the session only if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$full_name = isset($_SESSION['super_admin_info']['full_name']) ? $_SESSION['super_admin_info']['full_name'] : '';
require 'dbconnection.php';

// Get the normal_package ID from the URL
$package_id = isset($_GET['id']) ? $_GET['id'] : '';

if (!$package_id) {
    echo "No package ID provided!";
    exit;
}

// Handle deletion via GET parameter
if (isset($_GET['delete'])) {
    $detail_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM normal_package_detail WHERE id = :detail_id AND package_id = :package_id");
    $stmt->bindParam(':detail_id', $detail_id);
    $stmt->bindParam(':package_id', $package_id);
    if ($stmt->execute()) {
      file_put_contents("logs.txt", "Deleted normal package detail ID $detail_id from package ID $package_id at " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
    } 
}

// Fetch the details for the given normal_package ID
$stmt = $conn->prepare("SELECT * FROM normal_package_detail WHERE package_id = :package_id ORDER BY id");
$stmt->bindParam(':package_id', $package_id);
$stmt->execute();
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/FYP_SEM6/css/SuperAdmin1.css"> <!-- Add this line to include the styles for the sidebar -->
  <title>Normal Package Detail List</title>
</head>
<body>
  <div class="container">
    <nav class="nav collapsible">
      <i class="nav__toggler fa-solid fa-bars"></i>
      <ul class="nav__list collapsible__content">
      <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="add_normal_package" class="hidden-button">Add Package</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="add_customize_package" class="hidden-button">Add Customize Package</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_normal_package" class="hidden-button">Normal Package</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_normal_package_payment" class="hidden-button">Normal Package Payment</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_customize_package" class="hidden-button">Customize Package</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_user_customize_detail" class="hidden-button">User Customize Detail</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_customize_package_payment" class="hidden-button">Customize Payment</button>
          </form>
        </li>
        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_customize_package_order" class="hidden-button">Customize Package Order</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="feedback_list" class="hidden-button">Feedback</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="list_user" class="hidden-button">User List</button>
          </form>
        </li>

        <li class="nav__item">
          <form method="post" action="super_admin.php">
            <button type="submit" name="action" value="logs" class="hidden-button">Logs</button>
          </form>
        </li>
      </ul>
    </nav>
      </ul>
    </nav>

    <div class="content">
      <div class="header-container">
        <h3>Super Admin: <?php echo htmlspecialchars($full_name); ?></h3>
        <button class="logout_button1"><a href="logout_super_admin.php">Logout</a></button>
      </div>

      <h2>Normal Package Detail List for Package ID: <?php echo htmlspecialchars($package_id); ?></h2>
      Search by ID or package name: <br>
      <input type="text" onkeyup="searchPackage(this.value)" value="">
      <br><br>

      <div id="package_detail_table_list">
          <?php if ($details): ?>
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
                  <tbody>
                      <?php foreach ($details as $row): ?>
                          <tr>
                              <td><?php echo $row['id']; ?></td>
                              <td><?php echo htmlspecialchars($row['detail_name']); ?></td>
                              <td><?php echo htmlspecialchars($row['day_time']); ?></td>
                              <td><?php echo htmlspecialchars($row['detail_description']); ?></td>
                              <td>
                                  <?php if (!empty($row['detail_picture'])): ?>
                                      <img src="<?php echo htmlspecialchars($row['detail_picture']); ?>" style="width:70px; height:70px; object-fit:cover;">
                                  <?php endif; ?>
                              </td>
                              <td>
                                  <a href="?delete=<?php echo $row['id']; ?>&id=<?php echo $package_id; ?>" class='delete-link'>Delete</a>
                                  <a href="#" onclick='editDetail(<?php echo json_encode($row); ?>)' class='edit-link'>Edit</a>
                              </td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
              </table>
          <?php else: ?>
              <p>No details found for this package.</p>
          <?php endif; ?>
      </div>

      <br/>
      <!-- Correctly pass the package_id to the add_normal_package_detail.php page -->
      <a href="add_normal_package_detail.php?package_id=<?php echo $package_id; ?>">Add Detail</a>

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

      function editDetail(detail) {
          document.getElementById('detail_id').value = detail.id;
          document.getElementById('detail_name').value = detail.detail_name;
          document.getElementById('day_time').value = detail.day_time;
          document.getElementById('detail_description').value = detail.detail_description;
          
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

    </div> <!-- End of content -->
  </div> <!-- End of container -->
</body>
</html>
