<?php
$host = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$database = "piyubankadmin_db";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Error: Database connection failed!");
}
?>