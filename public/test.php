<?php
$servername = "localhost";
$username = "scottgmo";
$password = "H0tdufc_25";
$dbname = "scottgmo_tw_qb_integration";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
$last_name = 'Doe '.mt_rand();
$sql = "INSERT INTO members (first_name, last_name, role)
VALUES ('John', 'Doe', 'testrole')";

if ($conn->query($sql) === TRUE) {
echo "New record created successfully";
} else {
echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?> 