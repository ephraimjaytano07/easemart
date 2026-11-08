<?php
session_start();
if (!isset($_SESSION['user'])) { http_response_code(401); exit(); }
require_once 'config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { exit("Invalid request"); }

$id   = intval($_GET['id']);
$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_transactions WHERE transaction_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) { exit("Transaction not found"); }
$row = mysqli_fetch_assoc($result);
?>
<div class="receipt">
    <div class="receipt-header">
        <img src="assets/images/banner-transparent-bg.png" alt="Ease Mart" style="width:160px;display:block;margin:0 auto 8px;">
        <h2 style="color:#548d35;text-align:center;">EASE MART</h2>
        <p style="text-align:center;color:#666;font-size:13px;">Fresh. Fast. Friendly.</p>
    </div>
    <hr>
    <div class="receipt-body">
        <p><strong>Transaction #:</strong> <?php echo $row['transaction_id']; ?></p>
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($row['customer_name']); ?></p>
        <hr>
        <p><strong>Total:</strong> PHP <?php echo number_format($row['total_amount'], 2); ?></p>
        <p><strong>Paid:</strong> PHP <?php echo number_format($row['amount_paid'], 2); ?></p>
        <p><strong>Change:</strong> PHP <?php echo number_format($row['amount_change'], 2); ?></p>
        <hr>
        <p><strong>Date:</strong> <?php echo date("m/d/Y h:i A", strtotime($row['transaction_date'])); ?></p>
    </div>
    <p class="receipt-footer" style="text-align:center;color:#666;font-size:13px;margin-top:12px;">Thank you for shopping!</p>
</div>
