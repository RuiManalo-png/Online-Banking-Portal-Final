<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT u.name, u.email, u.mobile, a.account_number 
          FROM users u 
          JOIN accounts a ON u.id = a.user_id 
          WHERE u.id = ?";

$stmt = mysqli_prepare($conn, $query);
if ($stmt === false) {
    die("Error preparing the SQL statement: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($result);

if (!$user_data) {
    die("Error: User data not found.");
}

$update_success = false;
if (isset($_SESSION['update_success']) && $_SESSION['update_success']) {
    $update_success = true;
    unset($_SESSION['update_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: #f6f7fa;
      font-family: 'Poppins', sans-serif;
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .profile-container {
      width:70ch;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f6f7fa;
    }
    .profile-card {
      background: #fff;
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(44, 62, 80, 0.14);
      width: 100%;
      padding: 2.5rem 2rem 2rem 2rem;
      margin: 2rem 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }
    .back-link {
      position: absolute;
      left: 1.5rem;
      top: 1.2rem;
      font-size: 1rem;
      color: #222f3e;
      text-decoration: none;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.4rem;
      transition: color 0.2s;
    }
    .back-link:hover {
      color: #2c3e50;
      text-decoration: underline;
    }
    .profile-avatar {
      width: 90px;
      height: 90px;
      background:rgba(44,62,80,0.12);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.2rem auto;
      box-shadow: 0 4px 16px rgba(44,62,80,0.12);
      border: 2px solid #fff;
    }
    .profile-avatar i {
      font-size: 3rem;
      color: #2c3e50;
    }
    .profile-title {
      font-size: 2rem;
      font-weight: 700;
      color: #222f3e;
      margin-bottom: 2rem;
      text-align: center;
      letter-spacing: 0.01em;
    }
    form {
      width: 100%;
    }
    .form-label {
      font-weight: 600;
      color: #1a202c;
      margin-bottom: 0.3rem;
      font-size: 1rem;
      display: block;
    }
    .input-group {
      position: relative;
      margin-bottom: 1.3rem;
    }
    .input-group input {
      width: 72ch;
      padding: 0.85rem 1rem 0.85rem 2.8rem;
      border-radius: 0.7rem;
      border: none;
      background: #f4f6fb;
      font-size: 1rem;
      color: #222f3e;
      font-weight: 500;
      outline: none;
      transition: box-shadow 0.2s, border 0.2s;
      box-shadow: 0 1px 3px rgba(44,62,80,0.04);
    }
    .input-group input:focus {
      box-shadow: 0 0 0 2px #2c3e5022;
      border: none;
      background: #f9fafb;
    }
    .input-group input[readonly] {
      color: #7b8794;
      background: #f1f5f9;
      font-weight: 400;
      cursor: default;
    }
    .input-group .input-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #7b8794;
      font-size: 1.2rem;
      pointer-events: none;
    }
    .input-group .copy-btn {
      position: absolute;
      right: 0.8rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #7b8794;
      font-size: 1.23rem;
      cursor: pointer;
      transition: color 0.2s;
      padding: 0.2rem;
      border-radius: 0.3rem;
    }
    .input-group .copy-btn:hover {
      color: #2c3e50;
      background: #eaf4fb;
    }
    .input-help {
      font-size: 0.87rem;
      color: #7b8794;
      margin-top: -0.4rem;
      margin-bottom: 0.5rem;
      font-style: italic;
      margin-left: 2.1rem;
    }
    .actions {
      display: flex;
      gap: 1rem;
      margin-top: 1.8rem;
      width: 100%;
    }
    .btn-save {
      flex: 1;
      border-radius: 0.7rem;
      font-size: 1.08rem;
      font-weight: 700;
      padding: 0.9rem 0;
      border: 2px solid #222f3e;
      cursor: pointer;
      transition: background 0.18s, color 0.18s;
      text-align: center;
      text-decoration: none;
      letter-spacing: 0.01em;
    }
    .btn-cancel {
      background: #fff;
      color: #222f3e;
      border: 2px solid #222f3e;
    }
    .btn-cancel:hover {
      background: #f4f6fb;
      color: #111a2c;
    }
    .btn-save {
      background: #222f3e;
      color: #fff;
      border: 2px solid #222f3e;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }
    .btn-save:hover {
      background: #34405b;
      color: #fff;
    }
    .alert-success {
      border-radius: 0.7rem;
      padding: 1rem 1.25rem;
      margin-bottom: 1.5rem;
      font-weight: 600;
      font-size: 1rem;
      box-shadow: 0 2px 8px rgba(44,62,80,0.08);
      text-align: center;
      background: #d1fae5;
      color: #065f46;
      border: 1px solid #10b981;
      max-width: 400px;
      margin-left: auto;
      margin-right: auto;
    }
    @media (max-width: 600px) {
      .profile-card {
        padding: 1.3rem 0.5rem;
        max-width: 98vw;
      }
      .profile-title {
        font-size: 1.3rem;
      }
      .profile-avatar {
        width: 70px;
        height: 70px;
        font-size: 1.5rem;
      }
    }

  </style>
  <script>
  function copyAccountNumber() {
    const accountInput = document.getElementById('account');
    navigator.clipboard.writeText(accountInput.value).then(() => {
      const toast = document.getElementById('copy-toast');
      toast.style.display = 'block';
      setTimeout(() => {
        toast.style.display = 'none';
      }, 500);
    });
  }
</script>

</head>
<body>
<div class="profile-container">
  <div class="profile-card">
    <a href="dashboard.php" class="back-link">
      <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
    <div class="profile-avatar" style="margin-top:2.5rem;">
      <i class="bi bi-person-circle"></i>
    </div>
    <div class="profile-title"><?php echo htmlspecialchars($user_data['name']); ?></div>
    <?php if ($update_success): ?>
      <div class="alert-success">
        <i class="bi bi-check-circle-fill me-2"></i>
        Profile updated successfully!
      </div>
    <?php endif; ?>
    <form method="POST" action="update_profile.php" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <label for="name" class="form-label">Full Name</label>
      <div class="input-group">
        <i class="bi bi-person input-icon"></i>
        <input 
          type="text" 
          id="name" 
          name="name" 
          value="<?php echo htmlspecialchars($user_data['name']); ?>" 
          required 
          minlength="2" 
          maxlength="100"
        >
      </div>
      <label for="email" class="form-label">Email Address</label>
      <div class="input-group">
        <i class="bi bi-envelope input-icon"></i>
        <input 
          type="email" 
          id="email" 
          name="email" 
          value="<?php echo htmlspecialchars($user_data['email']); ?>" 
          required
        >
      </div>
      <label for="mobile" class="form-label">Mobile Number</label>
      <div class="input-group">
        <i class="bi bi-telephone input-icon"></i>
        <input 
          type="tel" 
          id="mobile" 
          name="mobile" 
          value="<?php echo htmlspecialchars($user_data['mobile']); ?>" 
          pattern="\+?[0-9]{10,15}" 
          required
        >
      </div>
      <div class="input-help">Format: +63 X XXXX XXXX</div>
      <label for="account" class="form-label">Account Number</label>
      <div class="input-group">
        <i class="bi bi-credit-card-fill input-icon"></i>
        <input 
          type="text" 
          id="account" 
          name="account" 
          value="<?php echo htmlspecialchars($user_data['account_number']); ?>" 
          readonly
        >
        <button type="button" class="copy-btn" onclick="copyAccountNumber()" title="Copy">
          <i class="bi bi-clipboard"></i>
        </button>
      </div>
      <div class="actions">
        <button type="submit" class="btn-save"><i class="bi bi-check-circle"></i> Save Changes</button>
            </div>
    </form>
  </div>
</div>
<div id="copy-toast" style="
  position: fixed;
  border-radius: 8px;
  display: none;
  z-index: 9999;
  font-size: 14px;
  border-radius: 0.7rem;
      padding: 1rem 1.25rem;
      margin-bottom: 1.5rem;
      font-weight: 600;
      font-size: 1rem;
      box-shadow: 0 2px 8px rgba(44,62,80,0.08);
      text-align: center;
      background: #2c3e50;
      color:rgb(234, 250, 246);
      max-width: 400px;
">
  Account number copied!
</div>

</body>
</html>
