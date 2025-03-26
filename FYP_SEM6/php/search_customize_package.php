<?php
require 'dbconnection.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';

$sql = "SELECT * FROM customize_package WHERE id LIKE :query OR package_name LIKE :query OR country LIKE :query ORDER BY id";
$stmt = $conn->prepare($sql);
$searchQuery = "%$query%";
$stmt->bindParam(':query', $searchQuery);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($results) {
    echo "
          <table>
              <thead>
                  <tr>
                      <th>ID</th>
                      <th>Country</th>
                      <th>Day</th>
                      <th>Package Name</th>
                      <th>Description</th>
                      <th>Price</th>
                      <th>Picture</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tbody>";

    foreach ($results as $row) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['country']}</td>
                <td>{$row['day_time']}</td>
                <td>{$row['package_name']}</td>
                <td>{$row['customize_description']}</td>
                <td>RM {$row['price']}</td>
                <td>";
        if (!empty($row["picture_url"])) {
            echo "<img src='{$row["picture_url"]}' style='width:70px; height:70px; object-fit:cover;'>";
        }
        echo "</td>
                <td>
                    <a href='?delete={$row['id']}' class='delete-link'>Delete</a>
                    <a href='#' onclick='editPackage({$row['id']}, \"{$row['country']}\", \"{$row['day_time']}\", \"{$row['package_name']}\", \"{$row['customize_description']}\", \"{$row['price']}\", \"{$row['picture_url']}\")' class='edit-link'>Edit</a>
                </td>
            </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>No customize packages found.</p>";
}

$conn = null;
?>
