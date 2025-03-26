<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>List Customize Packages - Travel Chill</title>
  <link rel="stylesheet" href="/FYP_SEM6/css/list2.css">
  <style>

  </style>
</head>
<body>

<h2>List of Customize Packages</h2>
Search by package name or id: <br>
<input type="text" onkeyup="searchPackage(this.value)" value="">
<br><br>

<div id="package_table_list">
    <?php
    require 'dbconnection.php';
    $full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : ''; 

    // Handle deletion via GET parameter
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $stmt = $conn->prepare("DELETE FROM customize_package WHERE id = :id");
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            file_put_contents("logs.txt", "Deleted package ID $id at " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
        }
    }

    // Fetch and display package records
    $stmt = $conn->prepare("SELECT * FROM customize_package ORDER BY id");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Country</th>
                    <th>Day</th>
                    <th>Package Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Picture</th>
                    <th>City</th>
                    <th>Actions</th>
                </tr>";

        foreach ($results as $row) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['country']}</td>
                    <td>{$row['day_time']}</td>
                    <td>{$row['package_name']}</td>
                    <td>{$row['customize_description']}</td>
                    <td>RM {$row['price']}</td>
                    <td><img src='{$row['picture_url']}' alt='Package Image'></td>
                    <td>{$row['city']}</td>
                    <td>
                        <a href='?delete={$row['id']}' class='delete-link'>Delete</a>
                        <a href='#' onclick='editPackage({$row['id']}, \"".htmlspecialchars($row['country'], ENT_QUOTES)."\" , \"".htmlspecialchars($row['day_time'], ENT_QUOTES)."\" , \"".htmlspecialchars($row['package_name'], ENT_QUOTES)."\" , \"".htmlspecialchars($row['customize_description'], ENT_QUOTES)."\" , \"".htmlspecialchars($row['price'], ENT_QUOTES)."\" , \"".htmlspecialchars($row['picture_url'], ENT_QUOTES)."\" ,  \"".htmlspecialchars($row['city'], ENT_QUOTES)."\")' class='edit-link'>Edit</a>
                    </td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No records found.</p>";
    }

    $conn = null;
    ?>
</div>

<h2>Edit Customize Package</h2>
<div id="message"></div>
<form id="editForm" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" id="package_id">
    <label for="country">Country:</label><br>
    <input type="text" name="country" id="country"><br><br>
    
    <label for="day_time">Day:</label><br>
    <input type="number" name="day_time" id="day_time"><br><br>
    
    <label for="package_name">Package Name:</label><br>
    <input type="text" name="package_name" id="package_name"><br><br>
    
    <label for="customize_description">Description:</label><br>
    <textarea name="customize_description" id="customize_description"></textarea><br><br>
    
    <label for="price">Price:</label><br>
    <input type="text" name="price" id="price"><br><br>
    
    <label for="picture_url">Upload Picture:</label><br>
    <input type="file" name="picture_url" id="picture_url"><br><br>

    <label for="city">City:</label><br>
    <input type="text" name="city" id="city"><br><br>
    
    <input type="submit" value="Save Changes">
</form>

<script>
function searchPackage(query){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function(){
        if (this.readyState == 4 && this.status == 200){ // Server returns success response
            document.getElementById("package_table_list").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET","search_customize_package.php?query=" + encodeURIComponent(query), true);
    xmlhttp.send();
}

function editPackage(id, country, day_time, package_name, customize_description, price, picture_url,city) {
    // Fill the form with data
    document.getElementById('package_id').value = id;
    document.getElementById('country').value = country;
    document.getElementById('day_time').value = day_time;
    document.getElementById('package_name').value = package_name;
    document.getElementById('customize_description').value = customize_description;
    document.getElementById('price').value = price;
    document.getElementById('city').value = city;

    // The file input cannot be set programmatically due to security reasons
    // document.getElementById('picture_url').value = picture_url;

    // Scroll down to the edit form
    document.getElementById('editForm').scrollIntoView();
}

document.getElementById('editForm').onsubmit = function(event) {
    event.preventDefault(); // Prevent the default form submission

    var formData = new FormData(this);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Display the response message
            var messageDiv = document.getElementById('message');
            if (this.responseText.includes("success")) {
                messageDiv.innerHTML = "<p class='success'>Detail updated successfully.</p>";
            } else {
                messageDiv.innerHTML = "<p class='error'>Error updating detail.</p>";
            }
            // Optionally, clear the form fields or update the table
            document.getElementById('editForm').reset();
        }
    };

    xmlhttp.open("POST", "edit_customize_package.php", true);
    xmlhttp.send(formData);
};
</script>
</body>
</html>
