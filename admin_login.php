<?php
session_start();
require_once 'config_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $lock_check_query = "SELECT id, name, password, role, failed_attempts, locked_until, security_a1, security_a2, security_a3 FROM piyubankadmin_db.admins WHERE email = '$email' LIMIT 1";
    $lock_check_result = mysqli_query($conn, $lock_check_query);

    if (!$lock_check_result) {
        die("❌ SQL Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($lock_check_result) > 0) {
        $admin = mysqli_fetch_assoc($lock_check_result);

        if ($admin['locked_until'] !== NULL && strtotime($admin['locked_until']) > time()) {
            $remaining = ceil((strtotime($admin['locked_until']) - time()) / 60);
            showAlert('Account Locked', "Your account is locked. Try again in $remaining minute(s).", 'error');
        }

        if (password_verify($password, $admin['password'])) {
            echo "
            <!DOCTYPE html>
            <html>
            <head>
                <title>Security Check</title>
                <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
            <script>
                Swal.fire({
                    title: 'Security Check',
                    html:
                        '<div class=\"mb-3\">' +
                        '<label class=\"form-label\">What is the name of your grandmother?</label>' +
                        '<input id=\"q1\" class=\"form-control\">' +
                        '</div>' +
                        '<div class=\"mb-3\">' +
                        '<label class=\"form-label\">What is the name of your dog?</label>' +
                        '<input id=\"q2\" class=\"form-control\">' +
                        '</div>' +
                        '<div class=\"mb-3\">' +
                        '<label class=\"form-label\">What month is your mother\\'s birthday?</label>' +
                        '<input id=\"q3\" class=\"form-control\">' +
                        '</div>',
                    confirmButtonText: 'Submit',
                    focusConfirm: false,
                    preConfirm: () => {
                        const q1 = document.getElementById('q1').value;
                        const q2 = document.getElementById('q2').value;
                        const q3 = document.getElementById('q3').value;
                        if (!q1 || !q2 || !q3) {
                            Swal.showValidationMessage('Please answer all questions');
                            return false;
                        }
                        const url = 'verify_security.php?email=" . urlencode($email) . "&q1=' + encodeURIComponent(q1) + '&q2=' + encodeURIComponent(q2) + '&q3=' + encodeURIComponent(q3);
                        window.location.href = url;
                    }
                });
            </script>
            </body>
            </html>";
            exit;
        } else {
            $new_attempts = $admin['failed_attempts'] + 1;
            $lock_time = NULL;

            if ($new_attempts >= 5) {
                $lock_minutes = 15;
                $lock_time = date("Y-m-d H:i:s", strtotime("+$lock_minutes minutes"));
                $lock_sql = "UPDATE piyubankadmin_db.admins SET failed_attempts = '$new_attempts', locked_until = '$lock_time' WHERE email = '$email'";
            } else {
                $lock_sql = "UPDATE piyubankadmin_db.admins SET failed_attempts = '$new_attempts' WHERE email = '$email'";
            }

            mysqli_query($conn, $lock_sql);
            $remaining = max(0, 5 - $new_attempts);
            $msg = ($remaining > 0) ? "Incorrect password! Attempts remaining: $remaining" : "Account locked for 15 minutes due to multiple failed attempts.";
            showAlert('Login Failed', $msg, 'error');
        }
    } else {
        showAlert('Admin Not Found', 'No admin found with that email.', 'warning');
    }
}

function showAlert($title, $text, $icon) {
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>$title</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: '$icon',
            title: '$title',
            text: '$text'
        }).then(() => {
            window.history.back();
        });
    </script>
    </body>
    </html>";
    exit;
}
?>
