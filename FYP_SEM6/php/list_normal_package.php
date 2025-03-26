<?php
// Start the session only if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<link rel="stylesheet" href="/FYP_SEM6/css/list2.css">



<h2>Normal Package List</h2>
Search by package name: <br>
<input type="text" onkeyup="searchPackage(this.value)" value="">
<br><br>

<div id="package_table_list">
    <?php
    require 'dbconnection.php';

    date_default_timezone_set("Asia/Kuala_Lumpur");
    $logoutDateTime = date('d-m-Y H:i:s');
    $_SESSION["logout_datetime"] = $logoutDateTime;

    // Handle deletion via GET parameter
    if (isset($_GET['delete'])) {
        $package_id = $_GET['delete'];

        // First delete related records from the normal_package_detail table
        $stmt = $conn->prepare("DELETE FROM normal_package_detail WHERE package_id = :package_id");
        $stmt->bindParam(':package_id', $package_id);
        $stmt->execute();

        // Then delete the record from the normal_package table
        $stmt = $conn->prepare("DELETE FROM normal_package WHERE id = :package_id");
        $stmt->bindParam(':package_id', $package_id);
        if ($stmt->execute()) {
            file_put_contents("logs.txt", "Deleted normal package with ID $package_id at " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
        }
    }

    // Fetch and display package records
    $stmt = $conn->prepare("SELECT * FROM normal_package ORDER BY id");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        echo "
              <table>
                  <thead>
                      <tr>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Date</th>
                          <th>End Date</th>
                          <th>Style</th>
                          <th>Price</th>
                          <th>Picture</th>
                          <th>Action</th>
                      </tr>
                  </thead>
                  <tbody>";

        foreach ($results as $row) {
            // Convert the dates to YYYY-MM-DD format for the date input
            $package_date = date('Y-m-d', strtotime($row['package_date']));
            $package_end_date = date('Y-m-d', strtotime($row['package_end_date']));

            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['package_name']}</td>
                    <td>{$row['package_date']}</td>
                    <td>{$row['package_end_date']}</td>
                    <td>{$row['package_style']}</td>
                    <td>RM {$row['package_price']}</td>
                    <td>";
            if (!empty($row["package_picture"])) {
                echo "<img src='{$row["package_picture"]}' style='width:70px; height:70px; object-position:center;'>";
            }
            echo "</td>
                    <td>
                        <a href='?delete={$row['id']}' class='delete-link'>Delete</a>
                        <a href='#' onclick='editPackage({$row['id']}, \"{$row['package_name']}\", \"$package_date\", \"$package_end_date\", \"{$row['package_style']}\", \"{$row['package_price']}\", \"{$row['package_picture']}\")' class='edit-link'>Edit</a>
                        <a href='list_normal_package_detail.php?id={$row['id']}' class='edit-link'>View</a>
                    </td>

                </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No packages found.</p>";
    }

    $conn = null;
    ?>
</div>

<h2>Edit Package</h2>
<form id="editForm" method="post" action="edit_normal_package.php" enctype="multipart/form-data">
    <input type="hidden" name="id" id="package_id">
    <label for="package_name">Name:</label><br>
    <input type="text" name="package_name" id="package_name"><br><br>
    
    <label for="package_date">Date:</label><br>
    <input type="date" name="package_date" id="package_date"><br><br>

    <label for="package_end_date">End Date:</label><br>
    <input type="date" name="package_end_date" id="package_end_date"><br><br>
    
    <label for="package_style">Style:</label><br>
    <input type="text" name="package_style" id="package_style"><br><br>
    
    <label for="package_price">Price:</label><br>
    <input type="text" name="package_price" id="package_price"><br><br>
    
    <label for="detail_picture">Upload Picture:</label><br>
    <input type="file" name="detail_picture" id="detail_picture"><br><br>
    
    <input type="submit" value="Save Changes">
</form>

<script>
function searchPackage(str){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function(){
        if (this.readyState == 4 && this.status == 200){ // Server returns success response
            document.getElementById("package_table_list").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET","search_normal_package.php?package_name="+str,true);
    xmlhttp.send();
}

function editPackage(id, name, date, endDate, style, price, picture) {
    document.getElementById('package_id').value = id;
    document.getElementById('package_name').value = name;
    document.getElementById('package_date').value = date;
    document.getElementById('package_end_date').value = endDate; // Ensure end date is set
    document.getElementById('package_style').value = style;
    document.getElementById('package_price').value = price;
    document.getElementById('detail_picture').value = picture;

    // Scroll down to the edit form
    document.getElementById('editForm').scrollIntoView();
}

document.getElementById('editForm').addEventListener('submit', function(event) {
    event.preventDefault(); // 阻止表单正常提交

    var formData = new FormData(this);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'edit_normal_package.php', true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            document.getElementById('editForm').insertAdjacentHTML('beforebegin', xhr.responseText);
            document.getElementById('editForm').reset(); // 在成功更新后重置表单字段
        } else {
            alert('An error occurred during the update process。');
        }
    };

    xhr.send(formData);
});
</script>
