<?php
require_once 'config_admin.php';

// Set admin credentials
$name = "Super Admin";
$email = "admin@piyubank.com";
$raw_password = "Admin123!";
$password = password_hash($raw_password, PASSWORD_DEFAULT); // hash securely
$role = "super_admin";

// Security answers (plaintext for now, hash if needed)
$security_a1 = "Juanita";
$security_a2 = "Brownie";
$security_a3 = "January";

// Avoid duplicate insert
$check = mysqli_query($conn, "SELECT * FROM admins WHERE email = '$email'");
if (mysqli_num_rows($check) > 0) {
    echo "❌ Admin already exists!";
    exit;
}

// Insert admin account
$query = "INSERT INTO admins (name, email, password, role, security_q1, security_a1, security_q2, security_a2, security_q3, security_a3) 
VALUES ('$name', '$email', '$password', '$role', 
        'What is the name of my grandmother?', '$security_a1',
        'What is the name of my dog?', '$security_a2',
        'What month is my mother’s birthday?', '$security_a3')";

if (mysqli_query($conn, $query)) {
    echo "✅ Admin account created with password: $raw_password";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}
?>