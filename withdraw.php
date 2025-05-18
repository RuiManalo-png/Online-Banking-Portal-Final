<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_number = mysqli_real_escape_string($conn, $_POST['account_number']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $amount = (float) $_POST['amount'];

    // Validate amount
    if ($amount <= 0) {
        echo "<script>alert('Invalid withdrawal amount.'); window.history.back();</script>";
        exit;
    }

    // Fetch account details from `accounts` and password from `users`
    $query = "SELECT a.id, a.balance, u.password FROM accounts a 
    JOIN users u ON a.user_id = u.id  -- Ensure correct columns are used
    WHERE a.account_number = '$account_number' LIMIT 1";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 0) {
        echo "<script>alert('Account not found.'); window.history.back();</script>";
        exit;
    }

    $data = mysqli_fetch_assoc($result);
    $account_id = $data['id'];
    $balance = $data['balance'];
    $stored_password = $data['password'];
    
    
    // Verify password
    if (!password_verify($password, $stored_password)) {
        echo "<script>alert('Incorrect password.'); window.history.back();</script>";
        exit;
    }
    // Check sufficient balance
    if ($balance < $amount) {
        echo "<script>alert('Insufficient balance.'); window.history.back();</script>";
        exit;
    }

    // Begin transaction for withdrawal
    mysqli_begin_transaction($conn);
    
    try {
        $withdraw_query = "UPDATE accounts SET balance = balance - '$amount' WHERE id = '$account_id'";
        mysqli_query($conn, $withdraw_query);

        // Record transaction
        $transaction_query = "INSERT INTO transactions (account_id, type, amount, description) 
                            VALUES ('$account_id', 'Withdraw', '-$amount', 'Withdrawal made')";
        mysqli_query($conn, $transaction_query);

        mysqli_commit($conn);
        
        $modalType = "success";
        $modalMessage = "Withdrawal successful!";
        $redirectUrl = "dashboard.php";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        
        $modalType = "error";
        $modalMessage = "Withdrawal failed. Please try again.";
        $redirectUrl = "javascript:window.history.back();";
    }
    
    echo "
    <style>
      .modal-bg {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.7);
        display: flex; justify-content: center; align-items: center;
        z-index: 9999;
      }
      .modal-box {
        background: white;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        font-family: 'Segoe UI', Arial, sans-serif;
        text-align: center;
        max-width: 400px;
        width: 90%;
        color: black;
      }
      .modal-message {
        font-size: 18px;
        line-height: 1.5;
        font-weight: 500;
        color: #2c3e50;
        margin: 10px 0;
      }
      .modal-box button {
        margin-top: 22px;
        padding: 10px 26px;
        background: #2c3e50;
        border: none;
        color: white;
        font-weight: 500;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s ease;
        box-shadow: 0 2px 5px rgba(44, 62, 80, 0.2);
      }
      .modal-box button:hover {
        background: #3d5166;
      }
    </style>

    <div class='modal-bg' id='modal'>
      <div class='modal-box'>
        <div class='modal-message'>$modalMessage</div>
        <button onclick='closeModal()'>OK</button>
      </div>
    </div>

    <script>
      function closeModal() {
        document.getElementById('modal').style.display = 'none';
        window.location.href = '$redirectUrl';
      }
    </script>
    ";
}
?>