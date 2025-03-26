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
  <title>List Customize Package Orders - Travel Chill</title>
  <link rel="stylesheet" href="/FYP_SEM6/css/list2.css">
</head>
<body>

<h2>List of Customize Package Orders</h2>

  <?php
  require 'dbconnection.php';

  $success_message = '';
  $error_message = '';

  // Delete record
  if (isset($_GET['delete'])) {
      $id = $_GET['delete'];
      $stmt = $conn->prepare("DELETE FROM customize_package_order WHERE id = :id");
      $stmt->bindParam(':id', $id);
      if ($stmt->execute()) {
          file_put_contents("logs.txt", "Deleted Customize Package Orders ID $id at " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
          $success_message = "Customize package orders deleted successfully.";
      } else {
          $error_message = "Error deleting customize package orders.";
      }
  }


  // Fetch records
  $stmt = $conn->prepare("SELECT cpo.id, u.full_name, cpo.customize_package_id,cpo.country, cpo.city, cpo.total_price, cpo.total_days, cpo.order_status
                          FROM customize_package_order cpo
                          JOIN user u ON cpo.user_id = u.id");
  $stmt->execute();
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if ($results) {

      echo "<table>
              <tr>
                  <th>Order ID</th>
                  <th>User Name</th>
                  <th>Package IDs</th>
                  <th>Country</th>
                  <th>City</th>
                  <th>Total Price</th>
                  <th>Total Days</th>
                  <th>Status</th>
                  <th>Actions</th>
              </tr>";

      foreach ($results as $row) {
          echo "<tr>
                  <td>{$row['id']}</td>
                  <td>{$row['full_name']}</td>
                  <td>{$row['customize_package_id']}</td>
                  <td>{$row['country']}</td>
                  <td>{$row['city']}</td>
                  <td>RM {$row['total_price']}</td>
                  <td>{$row['total_days']} days</td>
                  <td>{$row['order_status']}</td>
                  <td><a href='?delete={$row['id']}' class='delete-link'>Delete</a></td>
              </tr>";
      }
      echo "</table>";
  } else {
      echo "<p>No orders found.</p>";
  }

  ?>

</body>
</html>
