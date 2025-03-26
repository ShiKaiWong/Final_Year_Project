<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User List - Travel Chill</title>
  <link rel="stylesheet" href="/FYP_SEM6/css/list2.css">
</head>
<body>
  <h2>User List</h2>

  <?php
  require 'dbconnection.php';

  // Delete user
  if (isset($_GET['delete'])) {
      $id = $_GET['delete'];
      $stmt = $conn->prepare("DELETE FROM user WHERE id = :id");
      $stmt->bindParam(':id', $id);

      if ($stmt->execute()) {
          echo "<p>User with ID $id has been deleted.</p>";
          file_put_contents("logs.txt", "Deleted user ID $id at " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
      }
  }

  // Fetch user records
  $stmt = $conn->prepare("SELECT id, full_name, phone_number, email, user_name FROM user");
  $stmt->execute();
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if ($results) {
      echo "<table>
              <tr>
                  <th>ID</th>
                  <th>Full Name</th>
                  <th>Phone Number</th>
                  <th>Email</th>
                  <th>User Name</th>
                  <th>Actions</th>
              </tr>";

      foreach ($results as $row) {
          echo "<tr>
                  <td>{$row['id']}</td>
                  <td>{$row['full_name']}</td>
                  <td>{$row['phone_number']}</td>
                  <td>{$row['email']}</td>
                  <td>{$row['user_name']}</td>
                  <td><a href='?delete={$row['id']}' class='delete-link'>Delete</a></td>
              </tr>";
      }
      echo "</table>";
  } else {
      echo "<p>No user records found.</p>";
  }

  ?>

</body>
</html>
