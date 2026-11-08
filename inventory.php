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
    <title>Ease Mart — Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/transactions.css">
    <style>
        /* ── Inventory-specific overrides ── */
        .table-top { justify-content: space-between; flex-wrap: wrap; gap: 12px; }

        .add-btn {
            background: #6aa84f;
            color: #fff;
            border: none;
            padding: 11px 20px;
            border-radius: 10px;
            font-size: 0.95rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            transition: background 0.2s;
        }
        .add-btn:hover { background: #4f772d; }

        #toastContainer {
            position: fixed; top: 20px; right: 20px; z-index: 9999;
            display: flex; flex-direction: column; gap: 10px;
        }
        .custom-toast {
            min-width: 240px; padding: 13px 18px; border-radius: 10px;
            color: #fff; font-size: 0.88rem; display: flex; align-items: center;
            gap: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: toastIn 0.3s ease;
        }
        .custom-toast.toast-success { background: #619B47; }
        .custom-toast.toast-cancel  { background: #dc3545; }
        @keyframes toastIn { from { opacity:0; transform:translateX(60px); } to { opacity:1; transform:translateX(0); } }

        .modal-illustration {
            width: 130px; height: 130px; object-fit: contain;
            display: block; margin: 0 auto 10px;
        }

        .badge { font-size: 0.8rem; padding: 5px 10px; }
        .bg-success { background-color: #619B47 !important; }
    </style>
</head>
<body>

<div id="toastContainer"></div>

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
                <a href="inventory.php" class="active">
                    <i class="fas fa-boxes-stacked"></i>
                    <span>Inventory</span>
                </a>
                <a href="transactions.php">
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
                <h2>Product Inventory</h2>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    <input type="text" id="searchInput" placeholder="Search product...">
                    <button class="add-btn" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="inventoryTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <tr><td colspan="6" class="empty-state">Loading products…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- MOBILE OVERLAY -->
<div id="overlay" class="overlay"></div>

<!-- ── ADD MODAL ── -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="addCloseX"></button>
            </div>
            <div class="modal-body">
                <form action="add_product.php" method="POST" enctype="multipart/form-data" id="addProductForm">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (₱)</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" min="0" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="product_image" class="form-control" accept="image/*">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="addCancelBtn">Cancel</button>
                        <button type="button" class="btn btn-success" id="addSaveBtn">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ── EDIT MODAL ── -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="editCloseX"></button>
            </div>
            <div class="modal-body">
                <form action="edit_product.php" method="POST" enctype="multipart/form-data" id="editProductForm">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product_name" id="edit_product_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (₱)</label>
                        <input type="number" step="0.01" min="0" name="price" id="edit_price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" min="0" name="quantity" id="edit_quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="product_image" id="edit_product_image" class="form-control" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image.</small>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="editCancelBtn">Cancel</button>
                        <button type="button" class="btn btn-success" id="editSaveBtn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ── DELETE MODAL ── -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="deleteCloseX"></button>
            </div>
            <div class="modal-body text-center">
                <img src="assets/images/delete.png" alt="Delete" class="modal-illustration">
                <p class="mt-2">This action cannot be undone. Continue?</p>
                <form action="delete_product.php" method="POST" id="deleteProductForm">
                    <input type="hidden" name="product_id" id="deleteProductID">
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="deleteCancelBtn">Cancel</button>
                        <button type="button" class="btn btn-danger" id="deleteConfirmBtn">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ── CONFIRM MODAL ── -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmTitle">Confirm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="assets/images/confirm.jpg" alt="Confirm" class="modal-illustration">
                <p id="confirmMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="confirmNoBtn">No, Cancel</button>
                <button type="button" class="btn btn-success btn-sm" id="confirmYesBtn">Yes, Proceed</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/edit-product.js"></script>
<script src="assets/js/delete-product.js"></script>
<script src="assets/js/fetch-product.js"></script>
<script src="assets/js/validationform.js"></script>
<script>
    /* sidebar toggle */
    const menuToggle = document.getElementById('menuToggle');
    const sidebar    = document.getElementById('sidebar');
    const overlay    = document.getElementById('overlay');
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('show');
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('show');
        });
    }
    document.querySelectorAll('.sidebar-nav a').forEach(link => {
        link.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('show');
        });
    });
</script>
</body>
</html>
