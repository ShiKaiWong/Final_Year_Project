<?php
// Start the session if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Logs - Travel Chill</title>
  <link rel="stylesheet" href="/FYP_SEM6/css/list1.css">
</head>
<body>


<h2>System Logs</h2>

<div id="logs_container">
    <?php
    $logFile = 'logs.txt'; // Path to your logs.txt file

    if (file_exists($logFile)) {
        // Read the log file line by line
        $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!empty($logs)) {
            echo "<table>
                    <thead>
                        <tr>
                            <th>Log Entry</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($logs as $log) {
                // Display the full log entry in the message column
                echo "<tr>
                        <td>{$log}</td>
                    </tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No logs found.</p>";
        }
    } else {
        echo "<p>The log file does not exist.</p>";
    }
    ?>
</div>

</body>
</html>
