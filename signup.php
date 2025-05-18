<?php
session_start();
require_once 'config.php'; // Database configuration file

// Function to check password strength
function validatePasswordStrength($password) {
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter.";
    }
    if (!preg_match('/[a-z]/', $password)) {
        return "Password must contain at least one lowercase letter.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        return "Password must contain at least one number.";
    }
    if (!preg_match('/[\W]/', $password)) {
        return "Password must contain at least one special character (@, #, $, etc.).";
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $security_question = mysqli_real_escape_string($conn, $_POST['security_question']);
    $security_answer = mysqli_real_escape_string($conn, $_POST['security_answer']);

    // Validate password strength
    $passwordValidation = validatePasswordStrength($password);
    if ($passwordValidation !== true) {
        echo "<script>alert('$passwordValidation'); window.history.back();</script>";
        exit;
    }

    // Hash password & security answer before storing
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $hashed_security_answer = password_hash($security_answer, PASSWORD_BCRYPT);

    // Check if email already exists
    $check_email = "SELECT id FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $check_email);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Email already registered!'); window.history.back();</script>";
        exit;
    }

    // Insert new user into database
    $query = "INSERT INTO users (name, email, password, mobile, security_question, security_answer) 
              VALUES ('$name', '$email', '$hashed_password', '$mobile', '$security_question', '$hashed_security_answer')";
    
    if (mysqli_query($conn, $query)) {
        // Get the newly created user's ID
        $new_user_id = mysqli_insert_id($conn);

        // Generate a unique account number
        $generated_account_number = rand(0, 9999999999);

        // Insert an account linked to this user
        $insert_account_query = "INSERT INTO accounts (user_id, account_number, account_type, balance) 
                         VALUES ($new_user_id, '$generated_account_number', 'Savings', 0)";
        mysqli_query($conn, $insert_account_query);

        echo "<script>alert('Registration successful! Account created successfully.'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Error registering. Please try again later.'); window.history.back();</script>";
    }
}
?>
