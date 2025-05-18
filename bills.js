
document.getElementById('submitBillPayment').addEventListener('click', function() {
    const accountNumber = document.getElementById('accountNumber').value;
    const accountName = document.getElementById('accountName').value;
    const billType = document.getElementById('billType').value;
    const paymentAmount = document.getElementById('paymentAmount').value;

    fetch('process_bill_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            accountNumber: accountNumber,
            accountName: accountName,
            billType: billType,
            paymentAmount: paymentAmount
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Reload to update balance maybe
        } else {
            document.getElementById('error-message').style.display = 'block';
            document.getElementById('error-message').innerText = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

