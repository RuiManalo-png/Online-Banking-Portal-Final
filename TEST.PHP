<?php
// The raw password you want to hash
$raw_password = "Admin123!";

// Hash the password using bcrypt (default algorithm in PHP)
$hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

// Display the hashed password
echo "Hashed Password: " . $hashed_password;
?>