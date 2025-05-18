<?php
session_start();

require_once 'config.php'; // Database configuration file

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SESSION['name'] = $user['name'];

if (!$conn) {
    die("Error: Database connection failed!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $security_answer = mysqli_real_escape_string($conn, $_POST['security_answer']);

    // Fetch user details (Ensure column names match the database)
    $query = "SELECT id, name, password, failed_attempts, lock_until, mobile, security_question, security_answer 
              FROM users WHERE email = '$email' LIMIT 1";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("SQL Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) === 0) {
        die("Error: No matching user found in database!");
    }

    // Fetch user data correctly
    $user = mysqli_fetch_assoc($result);

    // Ensure the user data is valid
    if (!$user || !isset($user['id'])) {
        die("Error: User ID is missing!");
    }

    // Extract values safely
    $stored_password = $user['password'] ?? null;
    $stored_mobile = trim($user['mobile']) ?? null;
    $stored_security_answer = $user['security_answer'] ?? null;
    $failed_attempts = $user['failed_attempts'] ?? 0;
    $lock_until = $user['lock_until'] ?? null;

    // Check if account is locked
    if ($lock_until && strtotime($lock_until) > time()) {
        echo "<script>alert('Account locked! Try again later.'); window.history.back();</script>";
        exit;
    }

    // Verify password
    if (!password_verify(trim($password), $stored_password)) {
        $failed_attempts++;
        $lock_until = ($failed_attempts >= 5) ? date("Y-m-d H:i:s", strtotime("+30 minutes")) : NULL;

        mysqli_query($conn, "UPDATE users SET failed_attempts = $failed_attempts, lock_until = " . ($lock_until ? "'$lock_until'" : "NULL") . " WHERE id = {$user['id']}");

        $message = ($failed_attempts >= 5) ? "Too many failed attempts. Account locked for 30 minutes!" : "Invalid password. Attempts left: " . (5 - $failed_attempts);
        echo "<script>alert('$message'); window.history.back();</script>";
        exit;
    }

    // Verify mobile number
    if (trim($mobile) !== $stored_mobile) {
        echo "<script>alert('Incorrect mobile number.'); window.history.back();</script>";
        exit;
    }

    // Verify security answer
    if (!password_verify(trim($security_answer), $stored_security_answer)) {
        echo "<script>alert('Incorrect security answer.'); window.history.back();</script>";
        exit;
    }

    // Reset failed attempts on successful login
    mysqli_query($conn, "UPDATE users SET failed_attempts = 0, lock_until = NULL WHERE id = {$user['id']}");
    
// Record login activity
$log_query = "INSERT INTO logins (user_id) VALUES (?)";
$log_stmt = mysqli_prepare($conn, $log_query);
mysqli_stmt_bind_param($log_stmt, "i", $user['id']);
mysqli_stmt_execute($log_stmt);

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];

    header("Location: dashboard.php");
    exit;
}
?>
