<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_account_id = mysqli_real_escape_string($conn, $_POST['sender_account_id']);
    $recipient_account_number = mysqli_real_escape_string($conn, $_POST['recipient_account']);
    $amount = (float) $_POST['amount'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    if ($amount <= 0) {
        echo "<script>alert('Invalid transfer amount.'); window.history.back();</script>";
        exit;
    }

    $recipient_query = "SELECT id FROM accounts WHERE account_number = '$recipient_account_number' LIMIT 1";
    $recipient_result = mysqli_query($conn, $recipient_query);

    if (mysqli_num_rows($recipient_result) === 0) {
        echo "<script>alert('Recipient account not found.'); window.history.back();</script>";
        exit;
    }

    $recipient_data = mysqli_fetch_assoc($recipient_result);
    $recipient_account_id = $recipient_data['id'];

    $sender_query = "SELECT balance FROM accounts WHERE id = '$sender_account_id' LIMIT 1";
    $sender_result = mysqli_query($conn, $sender_query);
    $sender_data = mysqli_fetch_assoc($sender_result);

    if ($sender_data['balance'] < $amount) {
        echo "<script>alert('Insufficient balance.'); window.history.back();</script>";
        exit;
    }

    mysqli_begin_transaction($conn);

    try {
        mysqli_query($conn, "UPDATE accounts SET balance = balance - '$amount' WHERE id = '$sender_account_id'");
        mysqli_query($conn, "UPDATE accounts SET balance = balance + '$amount' WHERE id = '$recipient_account_id'");

        mysqli_query($conn, "INSERT INTO transactions (account_id, type, amount, description) VALUES ('$sender_account_id', 'Transfer', '-$amount', 'Transfer to Account $recipient_account_number: $description')");
        mysqli_query($conn, "INSERT INTO transactions (account_id, type, amount, description) VALUES ('$recipient_account_id', 'Deposit', '$amount', 'Received from Transfer: $description')");

        mysqli_commit($conn);

        $modalType = "success";
        $modalMessage = "Transfer successful!";
        $redirectUrl = "dashboard.php";
    } catch (Exception $e) {
        mysqli_rollback($conn);

        $modalType = "error";
        $modalMessage = "Transfer failed. Please try again.";
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