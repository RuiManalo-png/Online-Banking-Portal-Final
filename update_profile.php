<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch original user data before updating
    $query = "SELECT name, email, mobile, security_question FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $old_data = mysqli_fetch_assoc($result);

    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $security_question = mysqli_real_escape_string($conn, $_POST['security_question']);

    // Log changes before updating
    $fields = ['name', 'email', 'mobile', 'security_question'];
    foreach ($fields as $field) {
        if ($old_data[$field] !== $_POST[$field]) {
            $old_value = $old_data[$field];
            $new_value = $_POST[$field];

            $log_query = "INSERT INTO profile_changes (user_id, field_changed, old_value, new_value) VALUES (?, ?, ?, ?)";
            $log_stmt = mysqli_prepare($conn, $log_query);
            mysqli_stmt_bind_param($log_stmt, "isss", $user_id, $field, $old_value, $new_value);
            mysqli_stmt_execute($log_stmt);
        }
    }

    // Update profile in the database
    $update_query = "UPDATE users SET name=?, email=?, mobile=?, security_question=? WHERE id=?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "ssssi", $name, $email, $mobile, $security_question, $user_id);

    if (mysqli_stmt_execute($update_stmt)) {
        $_SESSION['name'] = $name; // Update session with new name
        $_SESSION['update_success'] = true;
        header("Location: profile.php");
        exit;
    } else {
        // Show styled error page with Bootstrap
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8" />
          <meta name="viewport" content="width=device-width, initial-scale=1" />
          <title>Error Updating Profile</title>
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
        </head>
        <body style="background-color: #f8fafc; padding: 2rem;">
          <div class="container d-flex justify-content-center align-items-center" style="height: 80vh;">
            <div class="alert alert-danger text-center" style="max-width: 500px; width: 100%;">
              <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Update Failed</h4>
              <p>There was an error updating your profile. Please try again later.</p>
              <a href="profile.php" class="btn btn-primary">Back to Profile</a>
            </div>
          </div>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
        exit;
    }
}

mysqli_close($conn);
?>
