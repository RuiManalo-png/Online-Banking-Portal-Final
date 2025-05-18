<?php
session_start();
require_once 'config_admin.php';

// Redirect if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
} else {
    // Regenerate session ID to prevent fixation attacks
    session_regenerate_id(true);
}

// Check DB connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepared statement: total users
$stmt = $conn->prepare("SELECT COUNT(*) AS total_users FROM piyubank_db.users");
$stmt->execute();
$result = $stmt->get_result();
$total_users_result = $result->fetch_assoc();
$stmt->close();

// Prepared statement: today's transactions
$stmt = $conn->prepare("SELECT COUNT(*) AS today FROM piyubank_db.transactions WHERE DATE(created_at) = CURDATE()");
$stmt->execute();
$result = $stmt->get_result();
$transactions_today = $result->fetch_assoc();
$stmt->close();

// Prepared statement: recent logins (last 5)
$stmt = $conn->prepare("SELECT u.name, l.login_time FROM piyubank_db.logins l 
                       JOIN piyubank_db.users u ON l.user_id = u.id 
                       ORDER BY l.login_time DESC LIMIT 5");
$stmt->execute();
$logins_result = $stmt->get_result();
$stmt->close();

// Prepared statement: recent users (last 5)
$stmt = $conn->prepare("SELECT u.name, u.email, a.account_number, a.balance 
                       FROM piyubank_db.users u 
                       LEFT JOIN piyubank_db.accounts a ON u.id = a.user_id 
                       LIMIT 5");
$stmt->execute();
$users_result = $stmt->get_result();
$stmt->close();

// Close DB connection explicitly
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>PIYUBANK Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="dashboard.css" />
</head>
<body>
<div class="dashboard-container">
   <aside class="sidebar">
    <h2 class="logo">PIYUBANK</h2>
    <div class="profile-box">
        <button id="openProfileModal" aria-haspopup="dialog" aria-controls="profileModal">Admin Profile</button>
    </div>
    

    <!-- Push this logout box to bottom -->
    <div class="logout-box">
            <a href="#" onclick="openLogoutModal()" style="text-decoration: none; color: white;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="support-box">
        <p>Need Help?</p>
        <a href="#" id="contactSupportBtn" aria-haspopup="dialog" aria-controls="contactModal">Contact Support</a>
    </div>
</aside>


    <main class="main-content">
        <header>
            <h1>ADMIN DASHBOARD</h1>
        </header>

        <section class="stats-section">
            <div class="stat-card" role="region" aria-label="Total Users">
                <h3>Total Users</h3>
                <p><?= htmlspecialchars($total_users_result['total_users'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="stat-card" role="region" aria-label="Today's Transactions">
                <h3>Today's Transactions</h3>
                <p><?= htmlspecialchars($transactions_today['today'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </section>

        <section class="data-section">
            <div class="data-box" role="region" aria-label="Recent Logins">
                <h3>Recent Logins</h3>
                <ul>
                    <?php while ($row = $logins_result->fetch_assoc()) : ?>
                        <li><?= strtoupper(htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8')) ?> – <?= htmlspecialchars($row['login_time'], ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <div class="data-box" role="region" aria-label="Recent Users">
                <h3>Recent Users</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Account #</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $users_result->fetch_assoc()) : ?>
                            <tr>
                                <td><?= strtoupper(htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8')) ?></td>
                                <td><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['account_number'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                                <td>₱<?= number_format($row['balance'], 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<!-- Admin Profile Modal -->
<div id="profileModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="profileModalTitle" tabindex="-1">
    <div class="modal-content">
        <button class="close-btn" id="closeProfileModal" aria-label="Close profile modal">&times;</button>
        <h2 id="profileModalTitle">Admin Profile</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['admin_email'] ?? 'email@example.com', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Role:</strong> Administrator</p>
    </div>
</div>

<!-- Contact Support Modal -->
<div id="contactModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="contactModalTitle" tabindex="-1">
  <div class="modal-content">
    <button class="close-btn" id="closeModal" aria-label="Close contact modal">&times;</button>
    <h2 id="contactModalTitle">Contact Support</h2>
    <form action="mailto:support@piyubank.com" method="post" enctype="text/plain">
      <label for="name">Your Name:</label>
      <input type="text" id="name" name="name" required />

      <label for="email">Your Email:</label>
      <input type="email" id="email" name="email" required />

      <label for="message">Message:</label>
      <textarea id="message" name="message" rows="4" required></textarea>

      <button type="submit">Send Email</button>
    </form>
    <div class="contact-info">
      <p><strong>Email:</strong> support@piyubank.com</p>
      <p><strong>Phone:</strong> +63 912 345 6789</p>
    </div>
  </div>
</div>

<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('open');
}

// Modal logic for Contact Support
const contactModal = document.getElementById('contactModal');
const openContactBtn = document.getElementById('contactSupportBtn');
const closeContactBtn = document.getElementById('closeModal');

openContactBtn.onclick = () => contactModal.style.display = 'block';
closeContactBtn.onclick = () => contactModal.style.display = 'none';

// Modal logic for Admin Profile
const profileModal = document.getElementById('profileModal');
const openProfileBtn = document.getElementById('openProfileModal');
const closeProfileBtn = document.getElementById('closeProfileModal');

openProfileBtn.onclick = () => profileModal.style.display = 'block';
closeProfileBtn.onclick = () => profileModal.style.display = 'none';

// Combined window onclick handler for modals
window.onclick = e => {
    if (e.target === contactModal) contactModal.style.display = 'none';
    if (e.target === profileModal) profileModal.style.display = 'none';
};
</script>
<div id="logoutModal" class="modal" style="display: none;">
    <div class="modal-content">
        <p>Are you sure you want to log out?</p>
        <button onclick="confirmLogout()">Yes</button>
        <button onclick="closeLogoutModal()">Cancel</button>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openLogoutModal() {
        document.getElementById('logoutModal').style.display = 'block';
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }

    function confirmLogout() {
        window.location.href = 'index.html'; // Redirects to index.html
    }
</script>
</body>
</html>
