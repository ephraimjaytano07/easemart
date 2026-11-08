<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ease Mart — Cashier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- MOBILE HEADER -->
    <div class="mobile-header">
        <img src="assets/images/banner-transparent-bg.png" alt="Ease Mart">
        <div class="hamburger" onclick="toggleSidebar()">☰</div>
    </div>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="logo-section">
            <img src="assets/images/banner-transparent-bg.png" alt="Ease Mart" class="main-logo">
        </div>
        <a class="nav-item active" href="index.php">
            <i class="fas fa-cash-register"></i> Cashier
        </a>
        <a class="nav-item" href="inventory.php">
            <i class="fas fa-boxes-stacked"></i> Inventory
        </a>
        <a class="nav-item" href="transactions.php">
            <i class="fas fa-file-invoice"></i> Transactions
        </a>
        <div class="user-footer">
            <div class="user-info">
                <?php echo htmlspecialchars($_SESSION['user']) . " | " . htmlspecialchars($_SESSION['role']); ?>
            </div>
            <a href="logout.php" class="logout-btn" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>

    <!-- MAIN -->
    <div class="main-container">
        <div id="cashier" class="tab-view active">
            <div class="pos-layout">
                <div class="product-grid" id="pos-products"></div>

                <div class="cart-section">
                    <div class="cart">
                        <h3>🛒 Your Cart</h3>
                        <div id="cart-list" class="cart-list"></div>
                    </div>
                    <div class="summary">
                        <h3 class="summary-title">Payment Summary</h3>
                        <p>Subtotal: <strong id="subtotal-val">PHP 0.00</strong></p>
                        <p>Tax (12%): <strong id="tax-val">PHP 0.00</strong></p>
                        <p>Total: <strong id="total-val">PHP 0.00</strong></p>
                        <button class="btn-green-payment" onclick="openModal()">Process Payment</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warning Modal -->
        <div class="modal fade" id="warningModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4" style="border-radius:20px;border:none;">
                    <div class="modal-body">
                        <i class="bi bi-exclamation-circle-fill" style="font-size:4rem;color:#ffc107;"></i>
                        <h3 class="mt-3 fw-bold" id="warning-title">Notice</h3>
                        <p id="warning-message" class="text-muted"></p>
                        <button type="button" class="btn mt-3 px-4" data-bs-dismiss="modal"
                            style="background:#6a994e;color:#fff;border-radius:25px;">Understood</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- PAYMENT MODAL -->
        <div id="paymentModal" class="modal-overlay">
            <div class="modal-box">
                <h2>Confirm Payment</h2>
                <div id="modal-cart-list"></div>
                <div class="modal-summary">
                    <p>Total: <strong id="modal-total">PHP 0.00</strong></p>
                </div>
                <div class="summary">
                    <input type="text" id="cust_name" placeholder="Customer Name" required>
                    <input type="number" id="amt_paid" placeholder="Amount Paid" required>
                </div>
                <div class="modal-actions">
                    <button class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button class="btn-proceed" onclick="confirmCheckout()">Proceed</button>
                </div>
            </div>
        </div>

        <!-- RECEIPT MODAL -->
        <div id="receiptModal" class="modal-overlay">
            <div class="receipt-box">
                <div class="receipt-header">
                    <h3>Receipt</h3>
                    <span class="close-receipt" onclick="closeReceipt()">&times;</span>
                </div>
                <div class="receipt-logo">
                    <img src="assets/images/banner-transparent-bg.png" alt="Ease Mart Logo">
                </div>
                <div class="receipt-section">
                    <h4>Products:</h4>
                    <div id="receipt-products"></div>
                </div>
                <div class="receipt-section">
                    <h4>Payment Summary:</h4>
                    <p>Subtotal: <span id="receipt-subtotal"></span></p>
                    <p>Tax (12%): <span id="receipt-tax"></span></p>
                    <p>Total: <span id="receipt-total"></span></p>
                    <p>Amount Paid: <span id="receipt-paid"></span></p>
                    <p>Change: <span id="receipt-change"></span></p>
                </div>
                <div class="receipt-section">
                    <h4>Customer:</h4>
                    <p id="receipt-customer"></p>
                </div>
                <div class="receipt-section">
                    <h4>Processed By:</h4>
                    <p><?php echo htmlspecialchars($_SESSION['user']); ?></p>
                </div>
                <button class="receipt-btn" onclick="closeReceipt()">Close</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
