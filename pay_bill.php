<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $sender_account_number = mysqli_real_escape_string($conn, $_POST['sender_account']);
    $sender_query = "SELECT id, balance FROM accounts WHERE account_number = '$sender_account_number' LIMIT 1";
    $sender_result = mysqli_query($conn, $sender_query);
    
    if (mysqli_num_rows($sender_result) === 0) {
        echo "<script>alert('Account not found. Please enter a valid account number.'); window.history.back();</script>";
        exit;
    }
    
    $sender_data = mysqli_fetch_assoc($sender_result);
    $sender_account_id = $sender_data['id'];
    
    $amount = (float) $_POST['amount'];
    
    $billType = mysqli_real_escape_string($conn, $_POST['billType']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Validate amount
    if ($amount <= 0) {
        echo "<script>alert('Invalid payment amount.'); window.history.back();</script>";
        exit;
    }

    // Check sender balance
    $sender_query = "SELECT balance FROM accounts WHERE id = '$sender_account_id' LIMIT 1";
    $sender_result = mysqli_query($conn, $sender_query);
    $sender_data = mysqli_fetch_assoc($sender_result);

    if ($sender_data['balance'] < $amount) {
        echo "<script>alert('Insufficient balance.'); window.history.back();</script>";
        exit;
    }

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // Deduct from sender's own account
        $deduct_query = "UPDATE accounts SET balance = balance - '$amount' WHERE id = '$sender_account_id'";
        mysqli_query($conn, $deduct_query);
        
        // Record transaction in the transactions table
        $sender_tx = "INSERT INTO transactions (account_id, type, amount, bill_type, description) 
                      VALUES ('$sender_account_id', 'Payment', '-$amount', '$billType', 'Paid $billType bill: $description')";
        mysqli_query($conn, $sender_tx);

        // Commit transaction
        mysqli_commit($conn);

        $modalType = "success";
        $modalMessage = "Bill payment successful!";
        $redirectUrl = "dashboard.php";
    } catch (Exception $e) {
        mysqli_rollback($conn);

        $modalType = "error";
        $modalMessage = "Payment failed. Please try again.";
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