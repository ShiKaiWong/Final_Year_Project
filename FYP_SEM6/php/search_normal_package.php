<?php
$package_name = isset($_GET['package_name']) ? $_GET['package_name'] : '';

require 'dbconnection.php';

$filter_sql = ""; // Initialize the variable
if (!empty($package_name)) {
    $sql = "SELECT * FROM normal_package WHERE package_name LIKE :package_name ORDER BY package_name";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':package_name', '%' . $package_name . '%');
} else {
    $sql = "SELECT * FROM normal_package ORDER BY package_name";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$response = "";

$response .= "
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Date</th>
                <th>Style</th>
                <th>Price</th>
                <th>Picture</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $response .= "<tr>";
    $response .= "<td>" . $row['id'] . "</td>";
    $response .= "<td>" . $row['package_name'] . "</td>";
    $response .= "<td>" . $row['package_date'] . "</td>";
    $response .= "<td>" . $row['package_style'] . "</td>";
    $response .= "<td>RM " . $row['package_price'] . "</td>";
    $response .= "<td>";
    if (!empty($row['package_picture'])) {
        $response .= "<img src='" . $row['package_picture'] . "' style='width:70px; height:70px; object-position:center;'>";
    }
    $response .= "</td>";
    $response .= "<td>
                    <a href='?delete=" . $row['id'] . "' class='delete-link'>Delete</a>
                    <a href='#' onclick='editPackage(" . $row['id'] . ", \"" . $row['package_name'] . "\", \"" . $row['package_date'] . "\", \"" . $row['package_style'] . "\", \"" . $row['package_price'] . "\", \"" . $row['package_picture'] . "\")' class='edit-link'>Edit</a>
                  </td>";
    $response .= "</tr>";
}

$response .= "</tbody></table>";

echo $response;

$conn = null;
?>

<!-- JavaScript Function to Handle Edit -->
<script>
function editPackage(id, name, date, style, price, picture) {
    document.getElementById('package_id').value = id;
    document.getElementById('package_name').value = name;
    document.getElementById('package_date').value = date;
    document.getElementById('package_style').value = style;
    document.getElementById('package_price').value = price;
    document.getElementById('detail_picture').value = picture;

    // Scroll to the edit form if it's present on the page
    if (document.getElementById('editForm')) {
        document.getElementById('editForm').scrollIntoView();
    }
}
</script>
