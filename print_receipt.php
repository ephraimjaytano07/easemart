<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
require_once 'config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid transaction ID."); }

$id   = intval($_GET['id']);
$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_transactions WHERE transaction_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) === 0) { die("Transaction not found."); }
$t = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo $t['transaction_id']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:30px; }
        .receipt { width:360px; margin:0 auto; background:#fff; padding:24px; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,.12); }
        .receipt-header { text-align:center; margin-bottom:20px; }
        .receipt-header img { max-width:180px; margin-bottom:10px; }
        .receipt-header h2 { margin:0; font-size:22px; color:#548d35; }
        .receipt-header p  { margin:4px 0 0; color:#666; font-size:14px; }
        .row { display:flex; justify-content:space-between; gap:16px; font-size:15px; line-height:1.9; color:#333; }
        .divider { border-top:1px dashed #bbb; margin:14px 0; }
        .print-note { text-align:center; font-size:13px; color:#666; margin-top:18px; }
        .print-btn { display:block; width:100%; margin-top:20px; padding:12px; border:none; border-radius:8px; background:#6aa84f; color:#fff; font-size:15px; cursor:pointer; }
        @media print { body{background:#fff;padding:0;} .receipt{box-shadow:none;width:100%;} .no-print{display:none;} }
    </style>
</head>
<body>
<div class="receipt">
    <div class="receipt-header">
        <img src="assets/images/banner-transparent-bg.png" alt="Ease Mart">
        <h2>EASE MART</h2>
        <p>Fresh. Fast. Friendly.</p>
    </div>
    <div class="row"><span>Transaction ID:</span><strong>#<?php echo $t['transaction_id']; ?></strong></div>
    <div class="row"><span>Customer:</span><strong><?php echo htmlspecialchars($t['customer_name']); ?></strong></div>
    <div class="divider"></div>
    <div class="row"><span>Total Amount:</span><strong>PHP <?php echo number_format($t['total_amount'], 2); ?></strong></div>
    <div class="row"><span>Amount Paid:</span><strong>PHP <?php echo number_format($t['amount_paid'], 2); ?></strong></div>
    <div class="row"><span>Change:</span><strong>PHP <?php echo number_format($t['amount_change'], 2); ?></strong></div>
    <div class="divider"></div>
    <div class="row"><span>Date:</span><strong><?php echo date("m/d/Y h:i A", strtotime($t['transaction_date'])); ?></strong></div>
    <p class="print-note">Thank you for shopping at Ease Mart.</p>
    <button class="print-btn no-print" onclick="window.print()">Print Receipt</button>
</div>
</body>
</html>
