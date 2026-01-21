<?php

// Database connection datails
$servername = "localhost";  // Cloud IP
$username   = "std6630202813";          // Username
$password   = "bai2U&qe@1";          // Password
$dbname     = "it_std6630202813";       // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch data from the table
$sql = "SELECT id, zone, zone_info, risk_disaster FROM regions";
$result = $conn->query($sql);

//Display results
if ($result->num_rows > 0) {
    echo "<table border='1'>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["zone"] . "</td>";
        echo "<td>" . $row["zone_info"] . "</td>";
        echo "<td>" . $row["risk_disaster"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No data found.";
}

// Close the connection

$conn->close();
?>