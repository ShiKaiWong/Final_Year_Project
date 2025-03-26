<?php
// Start the session only if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : ''; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List User Customize Details - Travel Chill</title>
    <link rel="stylesheet" href="/FYP_SEM6/css/list2.css">
</head>
<body>

<h2>List of User Customize Details</h2>

<?php
require 'dbconnection.php';


    // Delete record
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $stmt = $conn->prepare("DELETE FROM user_customize_detail WHERE id = :id");
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            file_put_contents("logs.txt", "Deleted user customize detail ID $id at " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
        }
    }

    // Fetch records from user_customize_detail
    $stmt = $conn->prepare("
        SELECT ucd.id, u.full_name, ucd.customize_package_ids, ucd.total_days
        FROM user_customize_detail ucd
        JOIN user u ON ucd.user_id = u.id
    ");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Customize Package IDs</th>
                    <th>Total Days</th>
                    <th>Actions</th>
                </tr>";

        foreach ($results as $row) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['full_name']}</td>
                    <td>{$row['customize_package_ids']}</td>
                    <td>{$row['total_days']}</td>
                    <td><a href='?delete={$row['id']}' class='delete-link'>Delete</a></td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No records found.</p>";
    }


$conn = null;
?>

</body>
</html>
