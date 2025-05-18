<?php

require_once 'config_admin.php';

$email = mysqli_real_escape_string($conn, $_GET['email']);
$q1 = strtolower(trim($_GET['q1'])); 
$q2 = strtolower(trim($_GET['q2'])); 
$q3 = strtolower(trim($_GET['q3']));


$security_query = "SELECT id, name, security_a1, security_a2, security_a3 FROM piyubankadmin_db.admins WHERE email = '$email'";

$result = mysqli_query($conn, $security_query);
$admin = mysqli_fetch_assoc($result);

var_dump($q1, $admin['security_a1']);



if (strtolower($q1) === strtolower($admin['security_a1']) && 
    strtolower($q2) === strtolower($admin['security_a2']) && 
    strtolower($q3) === strtolower($admin['security_a3'])) {

    // ✅ Store admin details in session before redirecting
    session_start();
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];

    // ✅ Redirect using PHP header
    header("Location: admin_dashboard.php");
    exit;
} else {
    echo "<script>alert('Incorrect security answers!'); window.history.back();</script>";
    exit;
}

 

?>