<?php
require 'dbconnection.php';

// Delete record
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM feedback_list WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute(); // This line is necessary to execute the delete statement
}

// Fetch records
$stmt = $conn->prepare("SELECT * FROM feedback_list");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($results) {
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>User ID</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Interested In</th>
                <th>Message</th>
                <th>Actions</th>
            </tr>";

    foreach ($results as $row) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['user_id']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone_number']}</td>
                <td>{$row['interested']}</td>
                <td>{$row['message']}</td>
                <td><a href='?delete={$row['id']}' class='delete-link'>Delete</a></td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No records found.</p>";
}

?>
