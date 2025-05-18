<?php
$host = 'localhost'; // Hostname
$username = 'root';  // MySQL username
$password = "";      // MySQL password (default is empty for XAMPP)
$dbname = 'piyubank_db'; // Database name

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
