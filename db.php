<?php
// db.php
$servername = "localhost";
$username = "root";
$password = "";  // change if needed
$dbname = "GIKONKO_TSS";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
