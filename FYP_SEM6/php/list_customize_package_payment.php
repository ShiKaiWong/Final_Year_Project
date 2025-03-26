<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>List Customize Package Payments - Travel Chill</title>
  <link rel="stylesheet" href="/FYP_SEM6/css/list2.css">
</head>
<body>
  <h2>User Payment List</h2>

  <?php
  require 'dbconnection.php';

  $success_message = '';
  $error_message = '';

      // Delete record
      if (isset($_GET['delete'])) {
          $id = $_GET['delete'];
          $stmt = $conn->prepare("DELETE FROM customize_package_payment WHERE id = :id");
          $stmt->bindParam(':id', $id);
          if ($stmt->execute()) {
            file_put_contents("logs.txt", "Deleted User Payment ID $id at " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
            $success_message = "Customize package payment deleted successfully.";
        }
        else{
            $error_message = "Error deleting customize package payment.";
        }
      }

      // Fetch records
      $stmt = $conn->prepare("SELECT * FROM customize_package_payment");
      $stmt->execute();
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($results) {
          echo "<table>
                  <tr>
                      <th>ID</th>
                      <th>Full Name</th>
                      <th>User ID</th>
                      <th>Email</th>
                      <th>Payment Method</th>
                      <th>Package IDs</th>
                      <th>Total Price</th>
                      <th>Payment Date</th>
                      <th>Actions</th>
                  </tr>";

          foreach ($results as $row) {
              echo "<tr>
                      <td>{$row['id']}</td>
                      <td>{$row['full_name']}</td>
                      <td>{$row['user_id']}</td>
                      <td>{$row['email']}</td>
                      <td>{$row['payment_method']}</td>
                      <td>{$row['package_ids']}</td>
                      <td>RM {$row['total_price']}</td>
                      <td>{$row['payment_date']}</td>
                      <td><a href='?delete={$row['id']}' class='delete-link'>Delete</a></td>
                  </tr>";
          }
          echo "</table>";
      } else {
          echo "<p>No records found.</p>";
      }

  ?>

</body>
</html>
