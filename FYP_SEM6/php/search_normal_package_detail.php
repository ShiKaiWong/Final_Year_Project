<?php
$detail_name = isset($_GET['detail_name']) ? $_GET['detail_name'] : '';

require 'dbconnection.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';

$sql = "SELECT * FROM normal_package_detail WHERE id LIKE :query OR detail_name LIKE :query ORDER BY id";
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

<!-- JavaScript Function to Handle Edit -->
<script>
function editPackageDetail(id, name, day, description, picture) {
    document.getElementById('detail_id').value = id;
    document.getElementById('detail_name').value = name;
    document.getElementById('day_time').value = day;
    document.getElementById('detail_description').value = description;
    document.getElementById('detail_picture').value = picture;

    // Scroll to the edit form if it's present on the page
    if (document.getElementById('editForm')) {
        document.getElementById('editForm').scrollIntoView();
    }
}
</script>
