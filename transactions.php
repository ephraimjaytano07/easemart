<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
require_once 'config/db.php';

$query = "SELECT transaction_id, customer_name, total_amount, amount_paid, amount_change, transaction_date
          FROM tbl_transactions
          ORDER BY transaction_date DESC, transaction_id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ease Mart — Transactions</title>
    <link rel="stylesheet" href="assets/css/transactions.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="page-wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-top">
            <div class="brand">
                <img src="assets/images/banner-transparent-bg.png" alt="Ease Mart" class="brand-logo">
            </div>
            <nav class="sidebar-nav">
                <a href="index.php">
                    <i class="fas fa-cash-register"></i>
                    <span>Cashier</span>
                </a>
                <a href="inventory.php">
                    <i class="fas fa-boxes-stacked"></i>
                    <span>Inventory</span>
                </a>
                <a href="transactions.php" class="active">
                    <i class="fas fa-file-invoice"></i>
                    <span>Transactions</span>
                </a>
            </nav>
        </div>
        <div class="sidebar-footer">
            <span><?php echo htmlspecialchars($_SESSION['user']) . " | " . htmlspecialchars($_SESSION['role']); ?></span>
            <a href="logout.php" class="logout-btn" title="Logout">
                <i class="fas fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- MOBILE HEADER -->
        <div class="mobile-header">
            <button id="menuToggle" class="burger-btn" type="button" aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
            <img src="assets/images/banner-transparent-bg.png" class="mobile-logo" alt="Ease Mart">
        </div>

        <!-- TABLE CARD -->
        <div class="table-card">
            <div class="table-top">
                <h2>Completed Transactions</h2>
                <input type="text" id="searchInput" placeholder="Search customer or transaction ID...">
            </div>

            <div class="table-responsive">
                <table id="transactionsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Total Amount</th>
                            <th>Amount Paid</th>
                            <th>Change</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['transaction_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td>PHP <?php echo number_format($row['total_amount'], 2); ?></td>
                                    <td>PHP <?php echo number_format($row['amount_paid'], 2); ?></td>
                                    <td>PHP <?php echo number_format($row['amount_change'], 2); ?></td>
                                    <td><?php echo date("m/d/Y h:i A", strtotime($row['transaction_date'])); ?></td>
                                    <td>
                                        <div class="action-cell">
                                            <button class="view-btn open-modal"
                                                data-id="<?php echo $row['transaction_id']; ?>"
                                                data-customer="<?php echo htmlspecialchars($row['customer_name']); ?>"
                                                data-total="<?php echo number_format($row['total_amount'], 2); ?>"
                                                data-paid="<?php echo number_format($row['amount_paid'], 2); ?>"
                                                data-change="<?php echo number_format($row['amount_change'], 2); ?>"
                                                data-date="<?php echo date("m/d/Y h:i A", strtotime($row['transaction_date'])); ?>"
                                                type="button" title="View Transaction">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="print-btn open-receipt"
                                                data-id="<?php echo $row['transaction_id']; ?>"
                                                type="button" title="Print Receipt">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="empty-state">No transactions found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- MOBILE OVERLAY -->
<div id="overlay" class="overlay"></div>

<!-- TRANSACTION DETAILS MODAL -->
<div class="modal-overlay" id="transactionModal">
    <div class="modal-box">
        <button class="close-modal" id="closeModal" type="button">&times;</button>
        <div class="modal-header">
            <img src="assets/images/banner-transparent-bg.png" alt="Ease Mart" class="modal-logo">
            <h3>Transaction Details</h3>
        </div>
        <div class="modal-body">
            <p><strong>Transaction ID:</strong> #<span id="modalTransactionId"></span></p>
            <p><strong>Customer Name:</strong> <span id="modalCustomerName"></span></p>
            <p><strong>Total Amount:</strong> PHP <span id="modalTotalAmount"></span></p>
            <p><strong>Amount Paid:</strong> PHP <span id="modalAmountPaid"></span></p>
            <p><strong>Change:</strong> PHP <span id="modalChange"></span></p>
            <p><strong>Date Processed:</strong> <span id="modalDate"></span></p>
        </div>
    </div>
</div>

<!-- RECEIPT MODAL -->
<div class="modal-overlay" id="receiptModal">
    <div class="modal-box receipt-modal">
        <button class="close-modal" id="closeReceipt" type="button">&times;</button>
        <div id="receiptContent"></div>
        <div class="modal-actions">
            <button id="printReceiptBtn" class="proceed-btn" type="button">
                <i class="fas fa-print"></i> Print Receipt
            </button>
        </div>
    </div>
</div>

<script src="assets/js/transactions.js"></script>
</body>
</html>
