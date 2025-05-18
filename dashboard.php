<?php
session_start();

require_once 'config.php'; // your DB connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
if (!$user_id) {
    die("Error: User ID is missing from session.");
}

// Fetch user and account details
$query_account = "SELECT users.name, accounts.account_number, accounts.account_type, accounts.balance, accounts.id AS account_id
                            FROM users
                            JOIN accounts ON users.id = accounts.user_id
                            WHERE users.id = '$user_id'
                            LIMIT 1";
$result_account = mysqli_query($conn, $query_account);

if (!$result_account) {
    die("SQL Error in account query: " . mysqli_error($conn));
}

$num_rows = mysqli_num_rows($result_account);

if ($num_rows === 0) {
    die("Error: No matching account found!");
}

$user_data = mysqli_fetch_assoc($result_account);

// Fetch recent transactions
if (!$user_data) {
    die("Error: Account details missing!");
}
$account_id = $user_data['account_id'];

$transactions_query = "SELECT * FROM transactions WHERE account_id = '$account_id' ORDER BY created_at DESC LIMIT 5";
$transactions_result = mysqli_query($conn, $transactions_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            height: 100vh;
            margin: 0;
        }
        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            transition: width 0.3s;
        }
        .sidebar h1 {
            font-size: 1.5em;
            margin-bottom: 30px; /* Adjusted margin */
            text-align: center;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            flex-grow: 1;
        }
        .sidebar ul li {
            margin: 10px 0; /* Reduced margin for less space */
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .sidebar ul li a {
            text-decoration: none;
            color: white;
            display: block; /* Make the whole li clickable */
            width: 100%;
        }
        .sidebar ul li:hover {
            background-color: #34495e;
            transform: scale(1.05);
        }
        .sidebar ul li i {
            margin-right: 8px;
        }
        .sidebar ul li:last-child {
            margin-bottom: 0;
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            position: relative;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
         .stats {
            display: flex;
            justify-content: center;
            margin: 30px 0;
        }
        .stat {
            background: rgb(218, 221, 223);
            border-radius: 10px;
            padding: 25px;
            width: 250px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .stat:hover {
            transform: translateY(-5px);
        }
        .stat strong {
            font-size: 1.5em;
            color: #2c3e50;
        }

        .recent-transaction {
            margin-top: 20px;
        }
        .recent-transaction table {
            width: 100%;
            border-collapse: collapse;
        }
        .recent-transaction th, .recent-transaction td {
            padding: 10px;
            border: 1px solid #bdc3c7;
            text-align: left;
        }

        /* Profile Icon Style */
        .profile-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 50px;
            color: #34495e;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .profile-icon:hover {
            color: #3498db;
        }

        /* Logout Modal Styles */
        #logoutModal {
            display: none; /* Initially hidden */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
        }

        #logoutModal .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 25px;
            border: none;
            width: 400px; /* Fixed width */
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        #logoutModal p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        #logoutModal button {
            padding: 12px 25px;
            margin: 10px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.3s;
        }

        #logoutModal button:hover {
            transform: translateY(-2px);
        }

        #logoutModal button:first-child {
            background-color: #e74c3c; /* Red for Yes/Logout */
            color: white;
        }
        
        #logoutModal button:first-child:hover {
            background-color: #c0392b;
        }

        #logoutModal button:last-child {
            background-color: #7f8c8d; /* Grey for Cancel */
            color: white;
        }
        
        #logoutModal button:last-child:hover {
            background-color: #6c7a7d;
        }



        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                width: 100px;
                padding: 15px;
            }
            .sidebar h1 {
                font-size: 1.2em;
            }
            .sidebar ul li {
                font-size: 14px;
            }
            .main-content {
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
            }
        }

        /* Style for the modal action buttons */
        .modal-footer .btn-primary {
            background-color: #2c3e50 !important;
            border-color: #2c3e50 !important;
        }

        .modal-footer .btn-primary:hover {
            background-color: #34495e !important;
            border-color: #34495e !important;
        }
        .modal-header .modal-title {
  color: #2c3e50 !important;
}
    </style>
</head>
<body>

<div class="sidebar">
    <br>
    <h1><strong>PiyuBank</strong></h1>

    <br>
    <ul>
        <li><a href="#" style="text-decoration: none; color: white;"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="#transferModal" data-bs-toggle="modal" style="text-decoration: none; color: white;"><i class="fas fa-exchange-alt"></i> Transfer Money</a></li>
        <li><a href="#billModal" data-bs-toggle="modal" style="text-decoration: none; color: white;"><i class="fas fa-file-invoice-dollar"></i> Pay Bills</a></li>
        <li><a href="#depositModal" data-bs-toggle="modal" style="text-decoration: none; color: white;"><i class="fas fa-money-check-alt"></i> Deposit Money</a></li>
        <li><a href="#withdrawModal" data-bs-toggle="modal" style="text-decoration: none; color: white;"><i class="fas fa-credit-card"></i> Withdraw Money</a></li>
    </ul>

    <div style="margin-top: auto;">
        <ul>
            <li><a href="#" onclick="openLogoutModal()" style="text-decoration: none; color: white;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</div>

<div class="main-content">
   <br>
    <a href="profile.php" style="text-decoration: none; color: inherit;"><i class="fas fa-user-circle profile-icon"></i></a>
<br>
    <div class="container my-5">
        <div class="text-center mb-5">
            <h2><strong>Welcome</strong>, <?php echo htmlspecialchars($user_data['name']); ?>!</h2>
            <p class="text-muted">Account Number: <strong><?php echo $user_data['account_number']; ?></strong></p>
        </div>
    </div>

    <div class="stats">
        <div class="stat"><strong>₱<?php echo number_format($user_data['balance'], 2); ?></strong><br>Balance</div>
    </div>

    <div class="recent-transaction">
        <h3>Recent Transactions</h3>
        <?php if (mysqli_num_rows($transactions_result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($tx = mysqli_fetch_assoc($transactions_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tx['type']); ?></td>
                            <td>₱<?php echo number_format($tx['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($tx['description']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($tx['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No recent transactions.</p>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <form action="transfer.php" method="POST" class="p-3 px-md-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold text-primary" id="transferModalLabel">
                    </i>Transfer Money
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="recipient_account" class="form-label fw-medium">Recipient Account Number</label>
                        <input type="text" class="form-control" id="recipient_account" name="recipient_account" required placeholder="Enter account number">
                    </div>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="amount" class="form-label fw-medium">Amount (₱)</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required placeholder="Enter amount"
                               data-bs-toggle="tooltip" data-bs-placement="top" title="Enter the amount to send">
                    </div>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="description" class="form-label fw-medium">Description <span class="text-muted">(optional)</span></label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="e.g. Rent for May">
                    </div>
                    <input type="hidden" name="sender_account_id" value="<?php echo $user_data['account_id']; ?>">
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill">
                    </i>Send Money
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="billModal" tabindex="-1" aria-labelledby="billModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <form action="pay_bill.php" method="POST" class="p-3 px-md-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold text-primary" id="billModalLabel">
                    </i>Pay Bills
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="sender_account" class="form-label fw-medium">Your Account Number</label>
                        <input type="text" class="form-control" id="sender_account" name="sender_account" required placeholder="Enter your account number">
                    </div>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="billType" class="form-label fw-medium">Bill Type</label>
                        <select class="form-control" id="billType" name="billType" required>
                            <option value="">Select</option>
                            <option value="Electricity">Electricity</option>
                            <option value="Water">Water</option>
                            <option value="Internet">Internet</option>
                        </select>
                    </div>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="amount" class="form-label fw-medium">Amount (₱)</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required placeholder="Enter amount">
                    </div>

                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="description" class="form-label fw-medium">Description</label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="Enter a note about this payment">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill">
                    </i>Pay Bill
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <form action="deposit.php" method="POST" class="p-3 px-md-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold text-primary" id="depositModalLabel">
                    </i>Deposit Funds
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="account_number" class="form-label fw-medium">Account Number</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" required placeholder="Enter account number">
                    </div>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="password" class="form-label fw-medium">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password">
                    </div>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="amount" class="form-label fw-medium">Amount (₱)</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required placeholder="Enter amount">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill">
                    </i>Deposit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <form action="withdraw.php" method="POST" class="p-3 px-md-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold text-primary" id="withdrawModalLabel">
                    </i>Withdraw Funds
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="account_number" class="form-label fw-medium">Account Number</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" required placeholder="Enter account number">
                    </div>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="password" class="form-label fw-medium">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password">
                    </div>
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <label for="amount" class="form-label fw-medium">Amount (₱)</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required placeholder="Enter amount">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill">
                    </i>Withdraw
                    </button>
                </div>
            </form>
        </div>
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
        window.location.href = 'logout.php'; // Redirect to your logout script
    }
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